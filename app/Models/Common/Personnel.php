<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    protected $table = 'personnels';

    protected $fillable = [
        'firstname',
        'lastname',
        'emp_code',
        'department_id',
        'position_title',
        'status',
        'email', // <-- เพิ่มฟิลด์นี้
    ];

    // คืนค่าชื่อ-นามสกุลแบบเต็ม (Accessor)
    public function getFullNameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }

    // ความสัมพันธ์: บุคลากรคนนี้อยู่หน่วยงานไหน
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
