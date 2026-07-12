<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $fillable = ['personnals_id'];

    public function personnal()
    {
        return $this->belongsTo(\App\Models\Common\Personnel::class, 'personnals_id', 'id');
    }

    // สร้าง Accessor ให้เรียก $user->email ได้เหมือนปกติ
    public function getEmailAttribute()
    {
        return $this->personnal ? $this->personnal->email : null;
    }

    // เพิ่ม Accessor สำหรับชื่อ
    public function getNameAttribute()
    {
        // สมมติว่าในตาราง Personnel มี field ชื่อ 'firstname' และ 'lastname'
        // ปรับแก้ตามชื่อ field จริงในตาราง Personnel ของคุณนะครับ
        return $this->personnal ? ($this->personnal->firstname . ' ' . $this->personnal->lastname) : 'ไม่มีชื่อ';
    }

    public function roles()
    {
        return $this->belongsToMany(
            \App\Models\Ums\Role::class, // ระบุ Path ของ Model Role (ตัวเล็กตามที่คุณตั้งไว้)
            'user_has_roles',             // ชื่อตาราง Pivot
            'user_id',                    // Foreign Key ของ User ในตาราง Pivot
            'role_id'                     // Foreign Key ของ Role ในตาราง Pivot
        );
    }

    public function hasRole($roleId)
    {
        // ตรวจสอบจาก pivot table โดยตรงผ่านคอลัมน์ role_id
        // ใช้การเปรียบเทียบค่า id ที่ส่งเข้ามา
        return $this->roles->contains('id', $roleId);
    }

    // แนะนำให้เพิ่มอันนี้ไว้ด้วย สำหรับกรณีต้องการเช็คหลาย Role ในคราวเดียว
    public function hasAnyRoleIds(array $roleIds)
    {
        // ตรวจสอบว่า User มี Role ใด Role หนึ่งในรายการ ID ที่ส่งมาหรือไม่
        return $this->roles()->whereIn('role_id', $roleIds)->exists();
    }

}