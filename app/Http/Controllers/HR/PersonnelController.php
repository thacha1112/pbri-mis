<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Common\Personnel;
use App\Models\Common\Department;

class PersonnelController extends Controller
{
    // 1. หน้าแสดงรายการตารางหลัก
    public function index()
    {
        $personnels = Personnel::with('department')->orderBy('id', 'desc')->get();
        return view('hr.personnels.index', compact('personnels'));
    }

    // 2. หน้าเปิดฟอร์มเพิ่มข้อมูลใหม่
    public function create()
    {
        $departments = Department::where('status', 'active')->get();
        return view('hr.personnels.create', compact('departments'));
    }

    // 3. ฟังก์ชันบันทินข้อมูลเพิ่ม
    public function store(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'email' => 'nullable|email|max:155',
            'department_id' => 'required|exists:departments,id'
        ], [
            'firstname.required' => 'กรุณากรอกชื่อจริง',
            'lastname.required' => 'กรุณากรอกนามสกุล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'department_id.required' => 'กรุณาเลือกหน่วยงานสังกัด'
        ]);

        Personnel::create($request->all());

        return redirect('hr/personnels')->with('success', 'เพิ่มข้อมูลบุคลากรเรียบร้อยแล้ว');
    }

    // 4. หน้าเปิดฟอร์มแก้ไขข้อมูล
    public function edit($id)
    {
        $personnel = Personnel::findOrFail($id);
        $departments = Department::where('status', 'active')->get();
        return view('hr.personnels.edit', compact('personnel', 'departments'));
    }

    // 5. ฟังก์ชันบันทึกข้อมูลอัปเดต
    public function update(Request $request, $id)
    {
        $request->validate([
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'email' => 'nullable|email|max:155',
            'department_id' => 'required|exists:departments,id'
        ]);

        $personnel = Personnel::findOrFail($id);
        $personnel->update($request->all());

        return redirect('hr/personnels')->with('success', 'อัปเดตข้อมูลบุคลากรสำเร็จ');
    }

    // 6. ฟังก์ชันลบข้อมูล (ยังใช้ AJAX เพื่อความเนียนในการ fade ตาราง)
    public function destroy($id)
    {
        Personnel::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'ลบข้อมูลบุคลากรเรียบร้อยแล้ว']);
    }
}
