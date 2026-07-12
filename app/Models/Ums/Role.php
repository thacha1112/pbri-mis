<?php

namespace App\Models\UMS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    // กำหนดชื่อตาราง
    protected $table = 'roles';

    // กำหนด Field ที่อนุญาตให้แก้ไข
    protected $fillable = [
        'name',
        'display_name',
        'description'
    ];

    /**
     * ความสัมพันธ์: Role หนึ่ง มีได้หลาย Permission
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
    }

    /**
     * ความสัมพันธ์: Role หนึ่ง มี User ได้หลายคน
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_has_roles', 'role_id', 'user_id');
    }
}