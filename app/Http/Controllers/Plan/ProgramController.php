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
        $programs = Program::with('budgetSource')->orderBy('id', 'desc')->get();
        $sources = BudgetSource::where('status', 'active')->get(); // ดึงไปให้เลือกแหล่งเงินแม่
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
