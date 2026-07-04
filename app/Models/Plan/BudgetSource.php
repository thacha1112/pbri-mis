<?php

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Model;
use App\Models\Common\FiscalYear;

class BudgetSource extends Model
{
    protected $table = 'plan_budget_sources';
    protected $fillable = ['fiscal_year_id', 'name', 'status']; // <-- เพิ่ม fiscal_year_id เข้าไป

    // เชื่อมกลับไปหาปีงบประมาณหลักที่อยู่ในโฟลเดอร์ Common
    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id');
    }

    public function programs()
    {
        return $this->hasMany(Program::class, 'budget_source_id');
    }
}
