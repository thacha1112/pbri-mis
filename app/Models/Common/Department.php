<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';

    protected $fillable = [
        'name',
        'parent_id',
        'status'
    ];

    // ความสัมพันธ์: ดึงหน่วยงานหลัก (Parent)
    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    // ความสัมพันธ์: ดึงหน่วยงานย่อยในสังกัด (Children / Sub-departments)
    public function children()
    {
        return $this->hasMany(Department::class, 'parent_id')->orderBy('name', 'asc');
    }

    // ความสัมพันธ์: ดึงบุคลากรที่สังกัดหน่วยงานนี้
    public function personnels()
    {
        return $this->hasMany(Personnel::class, 'department_id');
    }
}
