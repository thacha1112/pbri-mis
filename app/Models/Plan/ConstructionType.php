<?php

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConstructionType extends Model
{
    protected $table = 'plan_construction_types';

    protected $fillable = ['name', 'status'];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'construction_type_id');
    }
}
