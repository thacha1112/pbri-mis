<?php

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectBudgetSource extends Model
{
    protected $table = 'plan_project_budget_sources';

    protected $fillable = [
        'project_id', 
        'budget_source_id',
        'department_allocation_id', 
        'program_id', 
        'category_id', 
        'allocated_amount'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function budgetSource(): BelongsTo
    {
        return $this->belongsTo(BudgetSource::class, 'budget_source_id');
    }

    // --- เพิ่มความสัมพันธ์ส่วนนี้ครับ ---
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BudgetCategory::class, 'category_id');
    }
    // --------------------------------

    public function activityBudgets(): HasMany
    {
        return $this->hasMany(ActivityBudget::class, 'project_budget_source_id');
    }

    
}