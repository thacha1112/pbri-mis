<?php

namespace App\Models\Assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    // กำหนดชื่อตารางให้ตรงกับ SQL
    protected $table = 'units';

    // กำหนด fillable
    protected $fillable = [
        'name',       // เช่น ชุด, เครื่อง, อัน, แฟ้ม
        'description'
    ];

    /**
     * Relationship: หน่วยนับหนึ่งหน่วย สามารถมีครุภัณฑ์ที่นับหน่วยนี้ได้หลายรายการ
     */
    public function assetItems(): HasMany
    {
        return $this->hasMany(AssetItem::class, 'unit_id', 'id');
    }
}