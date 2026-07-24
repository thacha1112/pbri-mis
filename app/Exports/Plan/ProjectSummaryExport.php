<?php

// 🟢 ปรับ Namespace ให้ตรงกับโฟลเดอร์ Plan
namespace App\Exports\Plan;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProjectSummaryExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $reportData;
    protected $totals;

    public function __construct($reportData, $totals)
    {
        $this->reportData = $reportData;
        $this->totals = $totals;
    }

    public function view(): View
    {
        // เรียกใช้ View ตามเดิม
        return view('plan.reports.exports.project_summary_excel', [
            'reportData' => $this->reportData,
            'totals' => $this->totals
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
            2    => ['font' => ['bold' => true]],
        ];
    }
}