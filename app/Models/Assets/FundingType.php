<?php

namespace App\Models\Assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FundingType extends Model
{
    // กำหนดชื่อตารางให้ตรงกับ SQL
    protected $table = 'funding_types';

    // กำหนด fillable ตามคอลัมน์มาตรฐาน
    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * ความสัมพันธ์: แหล่งเงินประเภทนี้ สามารถมีครุภัณฑ์ที่จัดซื้อได้หลายรายการ
     */
    public function assetItems(): HasMany
    {
        return $this->hasMany(AssetItem::class, 'funding_type_id', 'id');
    }
}