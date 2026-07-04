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
        $categories = BudgetCategory::with('program.budgetSource')->orderBy('id', 'desc')->get();
        $programs = Program::with('budgetSource')->where('status', 'active')->get(); // ดึงไปกรองในกล่องป๊อปอัพ
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
