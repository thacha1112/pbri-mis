<?php

namespace App\Http\Controllers\UMS;

use App\Http\Controllers\Controller;
use App\Models\Ums\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('ums.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('ums.roles.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:roles', 'display_name' => 'required']);
        Role::create($request->all());
        return redirect()->route('roles.index')->with('success', 'สร้างบทบาทสำเร็จ');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return view('ums.roles.edit', compact('role'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->update($request->all());
        return redirect()->route('roles.index')->with('success', 'อัปเดตบทบาทสำเร็จ');
    }
}