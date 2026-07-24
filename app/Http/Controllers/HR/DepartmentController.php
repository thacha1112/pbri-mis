<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Common\Department;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::with('parent', 'children');

        // 🟢 ตรวจสอบว่ามีการเลือกฟิลเตอร์หน่วยงานสูงสุดหรือไม่
        if ($request->has('parent_id') && $request->parent_id != 'all' && $request->parent_id != '') {
            $parentId = $request->parent_id;
            // ดึงเฉพาะหน่วยงานสูงสุดที่เลือก และหน่วยงานย่อยที่มี parent_id ตรงกัน
            $query->where(function($q) use ($parentId) {
                $q->where('id', $parentId)
                  ->orWhere('parent_id', $parentId);
            });
        }

        $departments = $query->orderBy('parent_id', 'asc')->orderBy('id', 'desc')->get();
        
        // สำหรับแสดงใน Dropdown ฟิลเตอร์ และ Modal
        $parentDepartments = Department::whereNull('parent_id')->where('status', 'active')->get(); 

        return view('hr.departments.index', compact('departments', 'parentDepartments'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        Department::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id ?: null,
            'status' => $request->status ?? 'active'
        ]);

        return response()->json(['success' => true, 'message' => 'บันทึกหน่วยงานสำเร็จ']);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $department = Department::findOrFail($id);

        // ป้องกันไม่ให้เลือกตัวเองเป็นหน่วยงานหลัก
        if ($request->parent_id == $id) {
            return response()->json(['success' => false, 'errors' => [['หน่วยงานหลักต้องไม่ใช่ตัวเอง']]], 422);
        }

        $department->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id ?: null,
            'status' => $request->status
        ]);

        return response()->json(['success' => true, 'message' => 'อัปเดตข้อมูลสำเร็จ']);
    }

    public function destroy($id)
    {
        Department::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'ลบหน่วยงานเรียบร้อยแล้ว']);
    }
}
