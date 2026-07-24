<?php

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Common\FiscalYear;
use App\Models\Common\Personnel;
use App\Models\Common\Department;

class Project extends Model
{
    protected $table = 'plan_projects';

    protected $fillable = [
        'project_code',
        'name',
        'fiscal_year_id',
        'personnel_id',
        'department_id',
        'project_method_id',
        'construction_type_id',
        'overseas_type_id',
        'mission_id',
        'strategic_issue_id',
        'goal_id',
        'strategy_id',
        'start_date',
        'end_date',
        'background_rationale',
        'objectives',
        'target_group',
        'indicators',
        'outputs' ,
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // --- ความสัมพันธ์ข้อมูลทั่วไป ---
    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id');
    }
    public function personnel(): BelongsTo
    {
        return $this->belongsTo(Personnel::class, 'personnel_id');
    }
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function method(): BelongsTo
    {
        return $this->belongsTo(ProjectMethod::class, 'project_method_id');
    }
    public function constructionType(): BelongsTo
    {
        return $this->belongsTo(ConstructionType::class, 'construction_type_id');
    }
    public function overseasType(): BelongsTo
    {
        return $this->belongsTo(OverseasType::class, 'overseas_type_id');
    }

    // --- ความสัมพันธ์ฝั่งยุทธศาสตร์ ---
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class, 'mission_id');
    }
    public function strategicIssue(): BelongsTo
    {
        return $this->belongsTo(StrategicIssue::class, 'strategic_issue_id');
    }
    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class, 'goal_id');
    }
    public function strategy(): BelongsTo
    {
        return $this->belongsTo(Strategy::class, 'strategy_id');
    }

    // --- แหล่งเงินที่โครงการได้รับจัดสรร (Many-to-Many ผ่าน Bridge Model) ---
    public function budgetSources(): BelongsToMany
    {
        return $this->belongsToMany(BudgetSource::class, 'plan_project_budget_sources', 'project_id', 'budget_source_id')
            ->withPivot('id', 'allocated_amount')
            ->withTimestamps();
    }

  

    // --- กิจกรรมย่อยภายใต้โครงการ ---
    public function activities(): HasMany
    {
        return $this->hasMany(ProjectActivity::class, 'project_id');
    }

    public function projectBudgetSources() {
        return $this->hasMany(ProjectBudgetSource::class, 'project_id');
    }

    public function getTotalAllocatedBudgetAttribute()
    {
        // ดึงผลรวมของ allocated_amount ของโปรเจกต์นี้
        return $this->projectBudgetSources()->sum('allocated_amount');
    }

    public function activityBudgets(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        // โครงการมีหลายกิจกรรม (activities) 
        // และกิจกรรมมีหลายงบประมาณ (activity_budgets)
        return $this->hasManyThrough(
            \App\Models\Plan\ActivityBudget::class, // ตารางปลายทาง
            \App\Models\Plan\ProjectActivity::class,       // ตารางกลาง
            'project_id',                                  // FK ของ Project ในตาราง Activities
            'activity_id',                                 // FK ของ Activity ในตาราง Budgets
            'id',                                          // PK ของ Project
            'id'                                           // PK ของ Activity
        );
    }

    public function selectedStrategies()
    {
        return $this->hasMany(ProjectStrategy::class);
    }
    
}
