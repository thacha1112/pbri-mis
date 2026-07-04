<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;

class FiscalYear extends Model
{
    // กำหนดชื่อตารางในกรณีที่ไม่ได้ใช้ระบบพหูพจน์สไตล์อังกฤษ
    protected $table = 'fiscal_years';

    // กำหนดฟิลด์ที่อนุญาตให้กรอกข้อมูลผ่าน Form/Mass Assignment
    protected $fillable = [
        'year',
        'status',
        'description'
    ];
}
