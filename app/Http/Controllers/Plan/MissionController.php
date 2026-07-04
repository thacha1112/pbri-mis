<?php

namespace App\Http\Controllers\Plan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan\Mission;
use App\Models\Common\FiscalYear;

class MissionController extends Controller
{
    public function index()
    {
        $missions = Mission::with('fiscalYear')->orderBy('id', 'desc')->get();
        $fiscalYears = FiscalYear::where('status', 'active')->orderBy('year', 'desc')->get();
        return view('plan.missions.index', compact('missions', 'fiscalYears'));
    }

    public function store(Request $request)
    {
        $request->validate(['fiscal_year_id' => 'required', 'name' => 'required|string']);
        Mission::create($request->all());
        return response()->json(['success' => true, 'message' => 'บันทึกพันธกิจสำเร็จ']);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['fiscal_year_id' => 'required', 'name' => 'required|string']);
        Mission::findOrFail($id)->update($request->all());
        return response()->json(['success' => true, 'message' => 'อัปเดตพันธกิจสำเร็จ']);
    }

    public function destroy($id)
    {
        Mission::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'ลบพันธกิจเรียบร้อยแล้ว']);
    }
}
