<?php

namespace App\Repositories;

use App\Models\Common\Department;
use App\Models\Plan\Project;
use Illuminate\Support\Facades\DB;

class ProjectReportRepository
{
    /**
     * ดึงข้อมูลสรุปตามหน่วยงาน พร้อมยอดเงินและสถานะ
     */
    public function getDepartmentSummary(array $filters)
    {
        // 1. ดึงข้อมูลหน่วยงานตาม Filter (หน่วยงานสูงสุด และหน่วยงานย่อย)
        $deptQuery = Department::query()->orderBy('parent_id')->orderBy('id');

        if (!empty($filters['parent_department_id']) && $filters['parent_department_id'] !== 'all') {
            $parentId = $filters['parent_department_id'];
            $deptQuery->where(function ($q) use ($parentId) {
                $q->where('id', $parentId)
                  ->orWhere('parent_id', $parentId);
            });
        }

        $departments = $deptQuery->get();
        $departmentIds = $departments->pluck('id')->toArray();

        // 2. Query Aggregate ข้อมูลโครงการ งบประมาณ และการเบิกจ่าย
        // ใช้ DB::table เพื่อประสิทธิภาพสูงสุดในการ JOIN ข้อมูลจำนวนมาก
        $stats = DB::table('plan_projects as p')
            ->whereIn('p.department_id', $departmentIds)
            ->when(!empty($filters['fiscal_year_id']) && $filters['fiscal_year_id'] !== 'all', function ($q) use ($filters) {
                $q->where('p.fiscal_year_id', $filters['fiscal_year_id']);
            })
            ->leftJoin('plan_project_budget_sources as pbs', 'p.id', '=', 'pbs.project_id')
            ->leftJoin('plan_budget_sources as bs', 'pbs.budget_source_id', '=', 'bs.id')
            // Join ไปหากิจกรรมและการเบิกจ่าย เพื่อหายอดที่เบิกจ่ายจริง (payment_type = 'payment')
            ->leftJoin('plan_activity_budgets as ab', 'pbs.id', '=', 'ab.project_budget_source_id')
            ->leftJoin('plan_activity_payments as ap', function($join) {
                $join->on('ab.id', '=', 'ap.activity_budget_id')
                     ->where('ap.payment_type', '=', 'payment');
            })
            ->select(
                'p.department_id',
                // นับสถานะโครงการ (ใช้วิธีนับ Project ID ที่ไม่ซ้ำกันตามสถานะ)
                DB::raw('COUNT(DISTINCT p.id) as total_projects'),
                DB::raw("COUNT(DISTINCT CASE WHEN p.status = 'completed' THEN p.id END) as completed_count"),
                DB::raw("COUNT(DISTINCT CASE WHEN p.status = 'inprogress' THEN p.id END) as processing_count"),
                DB::raw("COUNT(DISTINCT CASE WHEN p.status = 'pending' THEN p.id END) as waiting_count"),
                DB::raw("COUNT(DISTINCT CASE WHEN p.status = 'cancelled' THEN p.id END) as cancel_count"),
                
                // สรุปยอดเงินงบประมาณ
                DB::raw("SUM(CASE WHEN bs.name LIKE '%งบประมาณ%' THEN pbs.allocated_amount ELSE 0 END) as budget_allocated"),
                DB::raw("SUM(CASE WHEN bs.name LIKE '%งบประมาณ%' THEN ap.amount ELSE 0 END) as budget_paid"),
                
                // สรุปยอดเงินรายได้
                DB::raw("SUM(CASE WHEN bs.name LIKE '%รายได้%' THEN pbs.allocated_amount ELSE 0 END) as income_allocated"),
                DB::raw("SUM(CASE WHEN bs.name LIKE '%รายได้%' THEN ap.amount ELSE 0 END) as income_paid")
            )
            ->groupBy('p.department_id')
            ->get()
            ->keyBy('department_id');

        return [
            'departments' => $departments,
            'stats' => $stats
        ];
    }
}