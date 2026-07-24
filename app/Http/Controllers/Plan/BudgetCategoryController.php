<?php

namespace App\Http\Controllers\Plan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan\BudgetCategory;
use App\Models\Plan\Program;

class BudgetCategoryController extends Controller
{
    public function index()
    {
        // ดึงหมวดงบรายจ่าย พร้อมเรียงตามปีงบประมาณจากมากไปน้อย
        $categories = BudgetCategory::with(['program.budgetSource.fiscalYear'])
            ->join('plan_programs', 'plan_budget_categories.program_id', '=', 'plan_programs.id')
            ->join('plan_budget_sources', 'plan_programs.budget_source_id', '=', 'plan_budget_sources.id')
            ->join('fiscal_years', 'plan_budget_sources.fiscal_year_id', '=', 'fiscal_years.id')
            ->orderBy('fiscal_years.year', 'desc') // เรียงปีงบประมาณมากไปน้อย
            ->orderBy('plan_budget_categories.id', 'asc') // เรียง ID หมวดงบเป็นลำดับรอง
            ->select('plan_budget_categories.*') // เลือกเฉพาะข้อมูลจากตารางหมวดงบ
            ->get();

        // ดึงแผนงาน (Programs) ที่ใช้งานอยู่ เพื่อใช้ใน Select Modal
        $programs = Program::with('budgetSource.fiscalYear')
            ->where('status', 'active')
            ->get()
            ->sortByDesc(function($program) {
                return $program->budgetSource->fiscalYear->year;
            });

        return view('plan.budget_categories.index', compact('categories', 'programs'));
    }

    public function store(Request $request)
    {
        $request->validate(['program_id' => 'required', 'name' => 'required|string|max:255']);
        BudgetCategory::create($request->all());
        return response()->json(['success' => true, 'message' => 'บันทึกหมวดงบรายจ่ายสำเร็จ']);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['program_id' => 'required', 'name' => 'required|string|max:255']);
        BudgetCategory::findOrFail($id)->update($request->all());
        return response()->json(['success' => true, 'message' => 'อัปเดตข้อมูลหมวดงบรายจ่ายสำเร็จ']);
    }

    public function destroy($id)
    {
        BudgetCategory::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'ลบหมวดงบรายจ่ายเรียบร้อยแล้ว']);
    }
}
