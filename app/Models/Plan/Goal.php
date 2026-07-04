<?php

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $table = 'plan_goals';
    protected $fillable = ['strategic_issue_id', 'code', 'name', 'status'];

    // ดึงแม่: กลับไปหาประเด็นยุทธศาสตร์
    public function strategicIssue()
    {
        return $this->belongsTo(StrategicIssue::class, 'strategic_issue_id');
    }

    // ดึงลูก: กลยุทธ์ที่อยู่ภายใต้เป้าประสงค์นี้
    public function strategies()
    {
        return $this->hasMany(Strategy::class, 'goal_id');
    }
}
