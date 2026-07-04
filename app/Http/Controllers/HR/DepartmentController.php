<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Common\Department;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with('parent')->orderBy('id', 'desc')->get();
        $parentDepartments = Department::whereNull('parent_id')->where('status', 'active')->get(); // สำหรับดึงไปเลือกหน่วยงานหลัก
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
