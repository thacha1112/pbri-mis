<?php

namespace App\Models\Assets;

use Illuminate\Database\Eloquent\Model;
use App\Models\Common\Personnel;

class MaintenanceLog extends Model
{
    // กำหนดชื่อตารางตาม SQL
    protected $table = 'maintenance_logs';

    protected $fillable = [
        'asset_id',
        'maintenance_date',
        'description',
        'cost',
        'technician_name',
        'personnel_id', // ผู้บันทึกหรือผู้รับผิดชอบในระบบ
        'remark'
    ];

    /**
     * Relationship: เป็นประวัติของครุภัณฑ์ชิ้นใด
     */
    public function asset()
    {
        return $this->belongsTo(AssetItem::class, 'asset_id', 'id');
    }

    /**
     * Relationship: ผู้ที่เกี่ยวข้องในการซ่อมบำรุง (บุคลากร)
     */
    public function personnel()
    {
        return $this->belongsTo(Personnel::class, 'personnel_id', 'id');
    }
}