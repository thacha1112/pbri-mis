<?php

namespace App\Models\Assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    // กำหนดชื่อตารางให้ตรงกับ SQL
    protected $table = 'vendors';

    // กำหนด fillable ตามคอลัมน์มาตรฐานที่มักใช้ในระบบจัดซื้อ/พัสดุ
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'tax_id',
        'contact_person',
        'remark'
    ];

    /**
     * Relationship: ผู้จำหน่ายรายนี้ สามารถมีรายการครุภัณฑ์ที่จำหน่ายให้เราได้หลายรายการ
     */
    public function assetItems(): HasMany
    {
        return $this->hasMany(AssetItem::class, 'vendor_id', 'id');
    }
}