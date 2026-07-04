<?php

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Model;

class SubActivityPayment extends Model
{
    protected $table = 'plan_sub_activity_payments';
    protected $fillable = ['sub_activity_budget_id', 'amount', 'payment_date', 'remarks', 'status'];
}