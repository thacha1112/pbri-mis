<?php

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubActivityBudget extends Model
{
    protected $table = 'plan_sub_activity_budgets';
    protected $fillable = ['sub_activity_id', 'activity_budget_id', 'allocated_amount', 'pr_amount', 'po_amount'];

    // ความสัมพันธ์กลับไปยังกิจกรรมหลัก (งบก้อนแม่)
    public function parentBudget(): BelongsTo
    {
        return $this->belongsTo(ActivityBudget::class, 'activity_budget_id');
    }

    // ประวัติการเบิกจ่าย
    public function payments(): HasMany
    {
        return $this->hasMany(SubActivityPayment::class, 'sub_activity_budget_id');
    }

    // Helper: ดึงยอดรวมที่เบิกจ่ายแล้ว
    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }

    // เพิ่มฟังก์ชันนี้เพื่อให้สามารถใช้ $budget->subActivity ได้
    public function subActivity()
    {
        // สมมติว่าในตาราง sub_activity_budgets มีคอลัมน์ sub_activity_id 
        // ที่เชื่อมไปยังตาราง project_sub_activities
        return $this->belongsTo(\App\Models\Plan\ProjectSubActivity::class, 'sub_activity_id');
    }
    
}