<?php

namespace App\Models\Assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetStatus extends Model
{
    // กำหนดชื่อตาราง (คาดว่าเป็น asset_statuses ตามหลักพหูพจน์ หรือ asset_status)
    protected $table = 'asset_statuses';

    protected $fillable = [
        'name',       // เช่น ใช้งานอยู่, ชำรุด, จำหน่ายแล้ว
        'description'
    ];

    /**
     * Relationship: สถานะหนึ่งสถานะ มีครุภัณฑ์ได้หลายรายการ
     */
    public function assetItems(): HasMany
    {
        return $this->hasMany(AssetItem::class, 'status_id', 'id');
    }
}