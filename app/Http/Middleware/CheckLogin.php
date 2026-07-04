<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // อย่าลืม Import Auth

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ถ้า User ยังไม่ได้เข้าสู่ระบบ (Guest)
        if (!Auth::check()) {
            // ให้เด้งกลับไปหน้า Login พร้อมส่งข้อความแจ้งเตือน
            return redirect()->route('login')->withErrors([
                'username' => 'กรุณาเข้าสู่ระบบก่อนใช้งาน',
            ]);
        }

        // ถ้าล็อกอินแล้ว ให้ทำงานต่อไปได้
        return $next($request);
    }
}
