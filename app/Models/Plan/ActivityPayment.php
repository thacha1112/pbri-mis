<?php

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Model;

class ActivityPayment extends Model
{
   protected $table = 'plan_activity_payments';
    protected $guarded = [];

    public function activityBudget() {
        return $this->belongsTo(ActivityBudget::class, 'activity_budget_id', 'id');
    }
}
