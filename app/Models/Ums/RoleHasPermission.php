<?php

namespace App\Models\UMS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleHasPermission extends Model
{
    // กำหนดชื่อตาราง
    protected $table = 'role_has_permissions';

    // เนื่องจากเป็น Pivot Table ที่มี Composite Primary Key (role_id, permission_id)
    // เราจึงตั้งค่า $incrementing เป็น false
    public $incrementing = false;

    // ตารางนี้ไม่จำเป็นต้องมี timestamps ก็ได้ แต่ถ้ามีให้ใส่ไว้ครับ
    public $timestamps = false;

    protected $fillable = [
        'role_id',
        'permission_id'
    ];

    /**
     * Relationship: เชื่อมกลับไปหา Role
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    /**
     * Relationship: เชื่อมกลับไปหา Permission
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'permission_id', 'id');
    }
}