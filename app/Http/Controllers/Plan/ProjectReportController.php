<?php

namespace App\Http\Controllers\Plan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProjectReportService;
use App\Models\Common\FiscalYear;
use App\Models\Common\Department;

// 🟢 แก้ไขบรรทัดนี้ ให้มี \Plan เพิ่มเข้ามา
use App\Exports\Plan\ProjectSummaryExport;
use Maatwebsite\Excel\Facades\Excel;

class ProjectReportController extends Controller
{
    protected ProjectReportService $reportService;

    public function __construct(ProjectReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['fiscal_year_id', 'parent_department_id']);

        // ประมวลผลรายงาน
        $report = $this->reportService->generateSummaryReport($filters);

        // ข้อมูลสำหรับ Dropdown Filter
        $fiscalYears = FiscalYear::orderBy('year', 'desc')->get();
        $parentDepartments = Department::whereNull('parent_id')->where('status', 'active')->get();

       return view('plan.reports.project_summary', [
            'reportData' => $report['data'],
            'totals' => $report['totals'],
            'fiscalYears' => $fiscalYears,
            'parentDepartments' => $parentDepartments,
            'filters' => $filters
        ]);
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['fiscal_year_id', 'parent_department_id']);
        $report = $this->reportService->generateSummaryReport($filters);
        
        $fileName = 'project_summary_' . date('Ymd_His') . '.xlsx';

        // สามารถเรียกใช้ new ProjectSummaryExport() ได้ตามปกติ
        return Excel::download(new ProjectSummaryExport($report['data'], $report['totals']), $fileName);
    }
}