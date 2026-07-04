<?php

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Model;

class BudgetCategory extends Model
{
    protected $table = 'plan_budget_categories';
    protected $fillable = ['program_id', 'name', 'status'];

    // ดึงกลับไปหาแผนงาน Level 1 (Parent)
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
}
