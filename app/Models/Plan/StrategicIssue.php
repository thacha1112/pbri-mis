<?php

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Model;

class StrategicIssue extends Model
{
    protected $table = 'plan_strategic_issues';
    protected $fillable = ['mission_id', 'code', 'name', 'status'];

    // ดึงแม่: กลับไปหาพันธกิจ
    public function mission()
    {
        return $this->belongsTo(Mission::class, 'mission_id');
    }

    // ดึงลูก: เป้าประสงค์ภายใต้ยุทธศาสตร์นี้
    public function goals()
    {
        return $this->hasMany(Goal::class, 'strategic_issue_id');
    }
}
