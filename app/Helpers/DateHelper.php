<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    // ฟังก์ชันสำหรับแปลงค่าเป็น ค.ศ. (ใช้ใน Controller)
    public static function toDbDate($dateInput)
    {
       if (empty($dateInput)) return null;

        try {
            // ใช้ createFromFormat เพื่อระบุชัดเจนว่าหน้าตาข้อมูลคือ dd/mm/yyyy
            $date = \Carbon\Carbon::createFromFormat('d/m/Y', $dateInput);
            
            // ถ้าปีที่ได้มาเป็น พ.ศ. (>= 2400) ให้ลบออก 543 เพื่อเป็น ค.ศ.
            if ($date->year >= 2400) {
                $date->subYears(543);
            }
            
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            // กรณี parse ด้วย dd/mm/yyyy ไม่ผ่าน อาจจะลอง parse แบบปกติเผื่อส่งเป็น Y-m-d มา
            try {
                return \Carbon\Carbon::parse($dateInput)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
    }

    // ฟังก์ชันสำหรับแปลงเป็น พ.ศ. (ใช้ใน View)
    public static function toThaiDate($date)
    {
        if (empty($date)) return '';
        $carbonDate = ($date instanceof Carbon) ? $date : Carbon::parse($date);
        
        if ($carbonDate->year < 2400) {
            return $carbonDate->addYears(543)->format('d/m/Y');
        }
        return $carbonDate->format('d/m/Y');
    }
}