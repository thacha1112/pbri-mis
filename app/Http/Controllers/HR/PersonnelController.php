<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Common\Personnel;
use App\Models\Common\Department;
use App\Models\User;
use DB;

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
            'email' => 'nullable|email|max:155|unique:personnels,email',
            'department_id' => 'required|exists:departments,id'
        ], [
            'firstname.required' => 'กรุณากรอกชื่อจริง',
            'lastname.required' => 'กรุณากรอกนามสกุล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'department_id.required' => 'กรุณาเลือกหน่วยงานสังกัด',
            'email.unique' => 'อีเมลนี้มีอยู่ในระบบแล้ว กรุณาตรวจสอบอีกครั้ง'
        ]);

        DB::transaction(function () use ($request) {
        
            // ก. สร้างข้อมูลในตาราง personnel
            $personnel = Personnel::create($request->all());

            // ข. สร้าง User ที่ผูกกับ personnel นี้
            // สมมติว่าตาราง users มี field 'personnals_id' และ 'username' หรือ 'password'
           User::updateOrCreate(
                ['personnals_id' => $personnel->id], // เงื่อนไขในการค้นหา (ถ้าเจอ personals_id นี้ให้ update)
                [
                    'username' => $request->email,   // ข้อมูลที่จะให้ update หรือสร้าง
                    // 'password' => ... (ถ้าเป็น update อาจจะไม่ต้องใส่ password เพื่อไม่ให้ทับของเก่า)
                ]
            );
        });

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
