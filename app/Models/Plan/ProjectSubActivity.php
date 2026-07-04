<?php

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectSubActivity extends Model
{
    protected $table = 'plan_project_sub_activities';

    protected $fillable = ['activity_id', 'name'];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(ProjectActivity::class, 'activity_id');
    }

    // รายละเอียดจัดสรรเงินก้อนย่อยและสัญญากิจกรรมย่อยนี้
    public function subActivityBudgets(): HasMany
    {
        return $this->hasMany(SubActivityBudget::class, 'sub_activity_id');
    }

   

    // งบที่กิจกรรมย่อยได้รับ
    public function budgets()
    {
        return $this->hasMany(SubActivityBudget::class, 'sub_activity_id');
    }

    // ความสัมพันธ์กับงบย่อย (SubActivityBudget)
    public function subActivityBudget()
    {
        return $this->hasOne(SubActivityBudget::class, 'sub_activity_id');
    }

    // เข้าถึง BudgetSource โดยผ่าน SubActivityBudget
    public function budgetSource()
    {
        return $this->hasOneThrough(
            \App\Models\Plan\BudgetSource::class,      // Model ปลายทาง (ที่ต้องการ)
            \App\Models\Plan\SubActivityBudget::class, // Model ตัวกลาง
            'sub_activity_id',                         // Foreign key ของ Model ตัวกลางที่เชื่อมกับตัวเรา
            'id',                                      // Foreign key ของ Model ปลายทาง
            'id',                                      // Local key ของตัวเรา
            'budget_source_id'                         // Foreign key ในตารางกลางที่ชี้ไปหา Model ปลายทาง
        );
    }

    // ประวัติการเบิกจ่าย
    public function payments()
    {
        return $this->hasMany(SubActivityPayment::class, 'sub_activity_budget_id');
    }
   
}
