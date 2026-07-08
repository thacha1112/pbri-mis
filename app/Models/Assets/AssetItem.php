<?php

namespace App\Models\Assets;

use Illuminate\Database\Eloquent\Model;
use App\Models\Common\Personnel; 
use App\Models\HR\Department;

class AssetItem extends Model
{
    // กำหนดชื่อตารางให้ตรงกับชื่อตารางใน SQL
    protected $table = 'asset_items'; 

    // กำหนด fillable ให้ครอบคลุมคอลัมน์สำคัญจาก SQL
    protected $fillable = [
        'reference_no', 
        'asset_no', 
        'asset_name', 
        'gfmis_no', 
        'category_id', 
        'department_id', 
        'division_id', 
        'specification', 
        'model', 
        'serial_no_1', 
        'serial_no_2', 
        'contract_no', 
        'funding_type_id', 
        'acquisition_method_id', 
        'quantity', 
        'unit_id', 
        'unit_price', 
        'total_price', 
        'useful_life', 
        'depreciation_rate', 
        'asset_code', 
        'fiscal_year', 
        'start_date', 
        'expire_date', 
        'vendor_id', 
        'location_id', 
        'status_id', 
        'usage_remark', 
        'disposal_remark', 
        'remark'
    ];

    /**
     * Relationship: หมวดหมู่ครุภัณฑ์
     */
    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id', 'id');
    }

    /**
     * Relationship: วิธีการได้มา
     */
    public function acquisitionMethod()
    {
        return $this->belongsTo(AcquisitionMethod::class, 'acquisition_method_id', 'id');
    }

    /**
     * Relationship: หน่วยงาน (สำนัก/คณะ)
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    /**
     * Relationship: สถานะครุภัณฑ์
     */
    public function status()
    {
        return $this->belongsTo(AssetStatus::class, 'status_id', 'id');
    }

    /**
     * Relationship: ประวัติการซ่อมบำรุง
     */
    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class, 'asset_id', 'id');
    }
}