<?php

namespace App\Services;

use App\Repositories\ProjectReportRepository;
use Illuminate\Support\Collection;

class ProjectReportService
{
    protected ProjectReportRepository $repository;

    public function __construct(ProjectReportRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * ประมวลผลและคำนวณข้อมูลสำหรับแสดงในตาราง
     */
    public function generateSummaryReport(array $filters): array
    {
        $data = $this->repository->getDepartmentSummary($filters);
        $departments = $data['departments'];
        $stats = $data['stats'];

        $reportData = collect();
        
        // ตัวแปรสำหรับ Footer สรุปยอดรวม
        $totals = [
            'total_allocated_all' => 0,
            'budget_allocated' => 0, 'budget_paid' => 0, 'budget_remaining' => 0,
            'income_allocated' => 0, 'income_paid' => 0, 'income_remaining' => 0,
            'total_allocated' => 0, 'total_paid' => 0, 'total_remaining' => 0,
            'total_projects' => 0, 'completed' => 0, 'processing' => 0, 'waiting' => 0, 'cancelled' => 0
        ];

        foreach ($departments as $dept) {
            $stat = $stats->get($dept->id);

            // ข้อมูลพื้นฐาน
            $budgetAllocated = $stat->budget_allocated ?? 0;
            $budgetPaid = $stat->budget_paid ?? 0;
            $budgetRemaining = $budgetAllocated - $budgetPaid;

            $incomeAllocated = $stat->income_allocated ?? 0;
            $incomePaid = $stat->income_paid ?? 0;
            $incomeRemaining = $incomeAllocated - $incomePaid;

            // ผลรวม
            $totalAllocated = $budgetAllocated + $incomeAllocated;
            $totalPaid = $budgetPaid + $incomePaid;
            $totalRemaining = $totalAllocated - $totalPaid;
            $percentPaid = $totalAllocated > 0 ? ($totalPaid / $totalAllocated) * 100 : 0;

            // สถานะ
            $totalProjects = $stat->total_projects ?? 0;
            $completed = $stat->completed_count ?? 0;
            $processing = $stat->processing_count ?? 0;
            $waiting = $stat->waiting_count ?? 0;
            $cancelled = $stat->cancel_count ?? 0;

            $reportData->push((object)[
                'department_id' => $dept->id,
                'department_name' => $dept->name,
                'parent_id' => $dept->parent_id,
                'is_parent' => is_null($dept->parent_id),
                
                'budget_allocated' => $budgetAllocated,
                'budget_paid' => $budgetPaid,
                'budget_remaining' => $budgetRemaining,
                
                'income_allocated' => $incomeAllocated,
                'income_paid' => $incomePaid,
                'income_remaining' => $incomeRemaining,
                
                'total_allocated' => $totalAllocated,
                'total_paid' => $totalPaid,
                'total_remaining' => $totalRemaining,
                'percent_paid' => $percentPaid,
                
                'total_projects' => $totalProjects,
                'completed' => $completed,
                'processing' => $processing,
                'waiting' => $waiting,
                'cancelled' => $cancelled,
            ]);

            // บวกยอดรวมลง Footer
            $totals['total_allocated_all'] += $totalAllocated;
            $totals['budget_allocated'] += $budgetAllocated;
            $totals['budget_paid'] += $budgetPaid;
            $totals['budget_remaining'] += $budgetRemaining;
            $totals['income_allocated'] += $incomeAllocated;
            $totals['income_paid'] += $incomePaid;
            $totals['income_remaining'] += $incomeRemaining;
            $totals['total_allocated'] += $totalAllocated;
            $totals['total_paid'] += $totalPaid;
            $totals['total_remaining'] += $totalRemaining;
            $totals['total_projects'] += $totalProjects;
            $totals['completed'] += $completed;
            $totals['processing'] += $processing;
            $totals['waiting'] += $waiting;
            $totals['cancelled'] += $cancelled;
        }

        // คำนวณเปอร์เซ็นต์รวม
        $totals['percent_paid'] = $totals['total_allocated'] > 0 
            ? ($totals['total_paid'] / $totals['total_allocated']) * 100 
            : 0;

        // จัดเรียงให้อยู่ในรูปแบบ Parent นำหน้า และ Child ตามมา
        $structuredData = $this->sortDepartmentHierarchy($reportData);

        return [
            'data' => $structuredData,
            'totals' => (object) $totals
        ];
    }

    /**
     * Helper สำหรับจัดเรียงข้อมูลให้อยู่ในโครงสร้าง Parent -> Children
     */
    private function sortDepartmentHierarchy(Collection $data): Collection
    {
        $parents = $data->where('is_parent', true);
        $sorted = collect();

        foreach ($parents as $parent) {
            $sorted->push($parent);
            $children = $data->where('parent_id', $parent->department_id);
            foreach ($children as $child) {
                $sorted->push($child);
            }
        }

        // หากมี Child ที่ Parent ไม่ได้ถูกดึงมา (กรณีแปลกประหลาด)
        $remaining = $data->whereNotIn('department_id', $sorted->pluck('department_id'));
        foreach($remaining as $rem) {
             $sorted->push($rem);
        }

        return $sorted;
    }
}