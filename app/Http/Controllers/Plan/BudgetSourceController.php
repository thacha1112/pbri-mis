<?php

namespace App\Http\Controllers\Plan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan\BudgetSource;
use App\Models\Common\FiscalYear;

class BudgetSourceController extends Controller
{
    public function index()
    {
        // ดึงแหล่งเงินพร้อมปีงบประมาณ
       $sources = BudgetSource::with('fiscalYear')
        ->join('fiscal_years', 'plan_budget_sources.fiscal_year_id', '=', 'fiscal_years.id')
        ->orderBy('fiscal_years.year', 'desc')
        ->orderBy('plan_budget_sources.id', 'ASC') // เรียง ID รองลงมาเพื่อความเสถียร
        ->select('plan_budget_sources.*') 
        ->get();

        // 2. ปีงบประมาณที่เปิดใช้งาน (เรียงตามปีจากมากไปน้อย)
        $fiscalYears = FiscalYear::where('status', 'active')
            ->orderBy('year', 'desc')
            ->get();

        return view('plan.budget_sources.index', compact('sources', 'fiscalYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fiscal_year_id' => 'required',
            'name' => 'required|string|max:255'
        ]);
        BudgetSource::create($request->all());
        return response()->json(['success' => true, 'message' => 'บันทึกแหล่งเงินงบประมาณสำเร็จ']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'fiscal_year_id' => 'required',
            'name' => 'required|string|max:255'
        ]);
        BudgetSource::findOrFail($id)->update($request->all());
        return response()->json(['success' => true, 'message' => 'อัปเดตข้อมูลแหล่งเงินสำเร็จ']);
    }

    public function destroy($id)
    {
        BudgetSource::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'ลบแหล่งเงินงบประมาณเรียบร้อยแล้ว']);
    }
}
