<?php

namespace App\Http\Controllers\ums;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ums\Role;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function index(Request $request)
    {
        // รับค่าจาก input search
        $search = $request->input('search');
        $roles = \App\Models\ums\Role::all();
        // ดึงข้อมูลพร้อมการค้นหา
        $users = User::with(['roles', 'personnal'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('personnal', function ($q) use ($search) {
                    $q->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%");
                });
            })
            ->paginate(10);

        // ส่ง compact ไปทั้ง users และ search
        return view('ums.user-roles.index', compact('users', 'search','roles'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('ums.user-roles.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        // ใช้ sync เพื่อแทนที่ role เดิมด้วย role ที่เลือกใหม่ทั้งหมด
        $user->roles()->sync($request->roles);
        
        return redirect()->route('user-roles.index')->with('success', 'อัปเดตสิทธิ์ผู้ใช้เรียบร้อยแล้ว');
    }

   
}