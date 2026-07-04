<?php

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'plan_programs';
    protected $fillable = ['budget_source_id', 'name', 'status'];

    // ดึงกลับไปหาแหล่งเงินหลัก (Parent)
    public function budgetSource()
    {
        return $this->belongsTo(BudgetSource::class, 'budget_source_id');
    }

    // ดึงลูก: แผนงานนี้ มีได้หลายหมวดงบรายจ่าย (Level 2)
    public function budgetCategories()
    {
        return $this->hasMany(BudgetCategory::class, 'program_id');
    }
}
