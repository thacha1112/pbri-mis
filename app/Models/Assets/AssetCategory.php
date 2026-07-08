<?php

namespace App\Models\Assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetCategory extends Model
{
    // กำหนดชื่อตารางให้ตรงกับในฐานข้อมูล
    protected $table = 'asset_categories';

    // กำหนด Field ที่อนุญาตให้บันทึกข้อมูล
    protected $fillable = [
        'code', 
        'name', 
        'description'
    ];

    /**
     * ความสัมพันธ์: หมวดหมู่หนึ่ง สามารถมีทรัพย์สินได้หลายรายการ
     */
    public function assets(): HasMany
    {
        return $this->hasMany(AssetItem::class, 'category_id', 'id');
    }
}