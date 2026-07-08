<?php

namespace App\Models\Assets;

use Illuminate\Database\Eloquent\Model;

class AcquisitionMethod extends Model
{
    protected $table = 'acquisition_methods'; // ตรวจสอบชื่อตารางใน SQL ของคุณอีกครั้ง
    protected $fillable = ['name', 'description'];

    // ความสัมพันธ์: หนึ่งวิธีได้มา มีทรัพย์สินได้หลายรายการ
    public function assets()
    {
        return $this->hasMany(AssetItem::class,'acquisition_method_id');
    }

}