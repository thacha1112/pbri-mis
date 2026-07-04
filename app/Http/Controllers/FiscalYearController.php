<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Common\FiscalYear; // <--- อัปเดต Path ให้เรียกผ่านโฟลเดอร์กลาง

class FiscalYearController extends Controller
{
    // หน้าแรกแสดงรายการปีงบประมาณทั้งหมด
    public function index()
    {
        $years = FiscalYear::orderBy('year', 'desc')->get();
        return view('fiscal_years.index', compact('years'));
    }

    // บันทึกข้อมูลปีงบประมาณใหม่ (AJAX)
    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|numeric|unique:fiscal_years,year',
        ], [
            'year.required' => 'กรุณากรอกปีงบประมาณ',
            'year.numeric' => 'กรุณากรอกเป็นตัวเลขเท่านั้น',
            'year.unique' => 'มีปีงบประมาณนี้ในระบบแล้ว',
        ]);

        FiscalYear::create([
            'year' => $request->year,
            'status' => $request->status ?? 'active',
            'description' => $request->description
        ]);

        return response()->json(['success' => true, 'message' => 'เพิ่มปีงบประมาณสำเร็จ']);
    }

    // แก้ไขข้อมูลปีงบประมาณ (AJAX)
    public function update(Request $request, $id)
    {
        $fiscalYear = FiscalYear::findOrFail($id);

        $request->validate([
            'year' => 'required|numeric|unique:fiscal_years,year,' . $id,
        ], [
            'year.required' => 'กรุณากรอกปีงบประมาณ',
            'year.numeric' => 'กรุณากรอกเป็นตัวเลขเท่านั้น',
            'year.unique' => 'มีปีงบประมาณนี้ในระบบแล้ว',
        ]);

        $fiscalYear->update([
            'year' => $request->year,
            'status' => $request->status,
            'description' => $request->description
        ]);

        return response()->json(['success' => true, 'message' => 'อัปเดตข้อมูลสำเร็จ']);
    }

    // ลบข้อมูลปีงบประมาณ (AJAX)
    public function destroy($id)
    {
        $fiscalYear = FiscalYear::findOrFail($id);
        $fiscalYear->delete();

        return response()->json(['success' => true, 'message' => 'ลบข้อมูลปีงบประมาณเรียบร้อยแล้ว']);
    }
}
