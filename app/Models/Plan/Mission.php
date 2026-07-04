<?php

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Model;
use App\Models\Common\FiscalYear;

class Mission extends Model
{
    protected $table = 'plan_missions';
    protected $fillable = ['fiscal_year_id', 'name', 'status'];

    // ผูกกลับไปหาปีงบประมาณที่อยู่ในโฟลเดอร์ Common
    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id');
    }

    // ดึงลูก: ประเด็นยุทธศาสตร์ที่อยู่ภายใต้พันธกิจนี้
    public function strategicIssues()
    {
        return $this->hasMany(StrategicIssue::class, 'mission_id');
    }
}
