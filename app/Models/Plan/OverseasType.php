<?php

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OverseasType extends Model
{
    protected $table = 'plan_overseas_types';

    protected $fillable = ['name', 'status'];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'overseas_type_id');
    }
}
