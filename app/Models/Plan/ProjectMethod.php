<?php

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectMethod extends Model
{
    protected $table = 'plan_project_methods';

    protected $fillable = ['name', 'status'];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'project_method_id');
    }
}
