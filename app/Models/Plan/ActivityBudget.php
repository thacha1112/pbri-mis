<?php

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActivityBudget extends Model
{
    protected $table = 'plan_activity_budgets';

    protected $fillable = ['activity_id', 'project_budget_source_id', 'amount'];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(ProjectActivity::class, 'activity_id');
    }
    public function projectBudgetSource(): BelongsTo
    {
        return $this->belongsTo(ProjectBudgetSource::class, 'project_budget_source_id');
    }

   

    public function budgetSource()
    {
        // เปลี่ยน 'project_budget_source_id' ให้ตรงกับชื่อคอลัมน์จริงในตาราง plan_activity_budgets ของคุณ
        return $this->belongsTo(BudgetSource::class, 'project_budget_source_id');
    }

    // เพิ่มฟังก์ชันสำหรับดึงกิจกรรมย่อยด้วย เพื่อให้ใช้ $budget->subActivityBudgets ได้
    public function subActivityBudgets()
    {
        return $this->hasMany(SubActivityBudget::class, 'activity_budget_id');
    }
    
}
