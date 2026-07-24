<?php

namespace App\Http\Controllers\Plan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan\Program;
use App\Models\Plan\BudgetSource;

class ProgramController extends Controller
{
    public function index()
    {
        // ดึงโปรแกรม โดย Eager Loading ไปถึง fiscalYear เพื่อใช้ในการเรียงลำดับ
        $programs = Program::with(['budgetSource.fiscalYear'])
            ->join('plan_budget_sources', 'plan_programs.budget_source_id', '=', 'plan_budget_sources.id')
            ->join('fiscal_years', 'plan_budget_sources.fiscal_year_id', '=', 'fiscal_years.id')
            ->orderBy('fiscal_years.year', 'desc') // เรียงตามปีจากมากไปน้อย
            ->orderBy('plan_programs.id', 'asc')  // เรียงตาม ID ของโปรแกรมเป็นลำดับรอง
            ->select('plan_programs.*')            // เลือกเฉพาะข้อมูลของตารางโปรแกรม
            ->get();

        // ดึงแหล่งเงินที่ active เพื่อใช้ในฟอร์ม
        $sources = BudgetSource::where('status', 'active')
            ->with('fiscalYear')
            ->get()
            ->sortByDesc(function($source) {
                return $source->fiscalYear->year; // เรียงแหล่งเงินใน select box ด้วย
            });

        return view('plan.programs.index', compact('programs', 'sources'));
    }

    public function store(Request $request)
    {
        $request->validate(['budget_source_id' => 'required', 'name' => 'required|string|max:255']);
        Program::create($request->all());
        return response()->json(['success' => true, 'message' => 'บันทึกแผนงานสำเร็จ']);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['budget_source_id' => 'required', 'name' => 'required|string|max:255']);
        Program::findOrFail($id)->update($request->all());
        return response()->json(['success' => true, 'message' => 'อัปเดตข้อมูลแผนงานสำเร็จ']);
    }

    public function destroy($id)
    {
        Program::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'ลบแผนงานเรียบร้อยแล้ว']);
    }
}
