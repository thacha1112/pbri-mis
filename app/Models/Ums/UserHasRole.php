<?php

namespace App\Models\UMS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User; // เชื่อมกับ Model User หลักของคุณ

class UserHasRole extends Model
{
    // กำหนดชื่อตาราง
    protected $table = 'user_has_roles';

    // เนื่องจากเป็น Pivot Table ที่มี Composite Primary Key (user_id, role_id)
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'role_id'
    ];

    /**
     * Relationship: เชื่อมไปหา User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relationship: เชื่อมไปหา Role
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
}