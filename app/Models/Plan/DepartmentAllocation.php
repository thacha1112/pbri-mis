<?php

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Model;
use App\Models\Common\Department;
use App\Models\Common\FiscalYear;

class DepartmentAllocation extends Model
{
    protected $table = 'plan_department_allocations';
    protected $fillable = ['department_id', 'fiscal_year_id','source_fiscal_year_id', 'budget_source_id', 'program_id', 'category_id', 'total_amount'];

    public function department() { return $this->belongsTo(Department::class); }
    public function fiscalYear() { return $this->belongsTo(FiscalYear::class); }
    public function budgetSource() { return $this->belongsTo(BudgetSource::class); }
    public function program() { return $this->belongsTo(Program::class); }
    public function category() { return $this->belongsTo(BudgetCategory::class); }
}