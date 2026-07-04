<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CheckPersonProfile
{
    public function handle(Request $request, Closure $next)
    {
        // 1. เช็คก่อนว่า Login หรือยัง
        if (Auth::check()) {
            $user = Auth::user();

            // 2. เช็คว่ามี person_id ในตาราง users หรือยัง 
            // และ person_id นั้นมีข้อมูลจริงๆ ในตาราง person (db: ums) หรือไม่
            $userExists = User::where('id',$user->id)
                            ->whereNull('citizen_id')->exists();

            if ($userExists) {
                // ถ้ายังไม่มี หรือ Join ไม่เจอ ให้ส่งไปหน้า Profile
                // เช็คด้วยว่าตอนนี้ไม่ได้อยู่ที่หน้า profile อยู่แล้ว (ป้องกัน Infinite Redirect)
                if (!$request->is('profile*')) {
                    return redirect()->route('profile.index')
                        ->with('warning', 'กรุณากรอกข้อมูลส่วนตัวให้ครบถ้วนก่อนใช้งานระบบ');
                }
            }
        }

        return $next($request);
    }
}