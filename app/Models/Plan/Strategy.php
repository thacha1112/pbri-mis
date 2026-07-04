<?php

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Model;

class Strategy extends Model
{
    protected $table = 'plan_strategies';
    protected $fillable = ['goal_id', 'code', 'name', 'status'];

    // ดึงแม่: กลับไปหาเป้าประสงค์
    public function goal()
    {
        return $this->belongsTo(Goal::class, 'goal_id');
    }

    // 💡 อนาคตตารางโครงการ (Projects) จะมาทำ HasMany เชื่อมต่อที่จุดนี้ครับ
}
