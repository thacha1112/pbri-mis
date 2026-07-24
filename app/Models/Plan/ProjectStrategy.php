<?php

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Model;
use App\Models\Plan\Mission;
use App\Models\Plan\StrategicIssue;
use App\Models\Plan\Goal;
use App\Models\Plan\Strategy;
use App\Models\Plan\Project; // สมมติว่า Project อยู่ใน namespace นี้

class ProjectStrategy extends Model
{
    // กำหนดชื่อตารางให้ชัดเจน (ถ้าชื่อไม่ตรงกับชื่อ Model ในรูปพหูพจน์)
    protected $table = 'project_strategies';

    // อนุญาตให้ Mass Assignment ได้
    protected $fillable = [
        'project_id',
        'mission_id',
        'strategic_issue_id',
        'goal_id',
        'strategy_id',
    ];

    // ========================================
    // RELATIONSHIPS (เชื่อมกลับไปยังตารางหลัก)
    // ========================================

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function mission()
    {
        return $this->belongsTo(Mission::class, 'mission_id');
    }

    public function strategicIssue()
    {
        return $this->belongsTo(StrategicIssue::class, 'strategic_issue_id');
    }

    public function goal()
    {
        return $this->belongsTo(Goal::class, 'goal_id');
    }

    public function strategy()
    {
        return $this->belongsTo(Strategy::class, 'strategy_id');
    }
}