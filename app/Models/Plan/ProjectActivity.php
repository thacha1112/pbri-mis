<?php

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectActivity extends Model
{
    protected $table = 'plan_project_activities';

    protected $fillable = [
        'project_id',
        'name',
        'objectives',
        'indicators',
        'target_group',
        'outputs',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    // แหล่งเงินที่ถูกแบ่งมาให้กิจกรรมหลักนี้
    public function activityBudgets(): HasMany
    {
        return $this->hasMany(ActivityBudget::class, 'activity_id');
    }

    // กิจกรรมย่อยภายใต้กิจกรรมหลักนี้
    public function subActivities(): HasMany
    {
        return $this->hasMany(ProjectSubActivity::class, 'activity_id');
    }

    // เพิ่มฟังก์ชันนี้เข้าไปครับ
    public function budgets()
    {
        // อ้างอิงตาราง plan_activity_budgets 
        // โดยมี foreign key คือ activity_id
        return $this->hasMany(ActivityBudget::class, 'activity_id');
    }

   
}
