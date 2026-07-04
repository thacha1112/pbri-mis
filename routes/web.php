<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FiscalYearController;
use App\Http\Controllers\HR\DepartmentController;
use App\Http\Controllers\HR\PersonnelController;
use App\Http\Controllers\Auth\MicrosoftController;

// ดึง Controllers ฝั่งระบบแผนเข้ามาใช้งาน
use App\Http\Controllers\Plan\MissionController;
use App\Http\Controllers\Plan\StrategicIssueController;
use App\Http\Controllers\Plan\GoalController;
use App\Http\Controllers\Plan\StrategyController;

// 💡 ดึงเพิ่ม: Controllers ฝั่งระบบบริหารแหล่งเงินและโครงสร้างงบประมาณ
use App\Http\Controllers\Plan\BudgetSourceController;
use App\Http\Controllers\Plan\ProgramController;
use App\Http\Controllers\Plan\BudgetCategoryController;
use App\Http\Controllers\Plan\ProjectController;
use App\Http\Controllers\Plan\SubActivityController;



    // ไม่ต้องครอบ Middleware 'web' ซ้ำซ้อน
        Route::get('/login', function () { 
            return view('auth.login'); 
        })->name('login');

        // กลุ่มการล็อกอิน (ไม่ต้องผ่าน checklogin)
        Route::get('/authen/microsoft/redirect', [MicrosoftController::class, 'redirectToMicrosoft'])->name('login.microsoft');
        Route::get('/authen/microsoft/callback', [MicrosoftController::class, 'handleMicrosoftCallback']);
        Route::post('/logout', function () {
            auth()->logout();
            session()->invalidate(); // ล้าง session
            session()->regenerateToken(); // ป้องกัน CSRF fix
            return redirect()->route('login');
        })->name('logout');

        
// หน้าหลัก (Dashboard)
Route::get('/', function () {
    return view('dashboard');
});

/**
 * ==========================================
 * โมดูลข้อมูลพื้นฐานระบบงาน (Common Configurations)
 * ==========================================
 */
Route::prefix('config')->group(function () {

    // จัดการข้อมูลปีงบประมาณ (สำหรับผูกทุกระบบงานใน MIS)
    Route::controller(FiscalYearController::class)->group(function () {
        Route::get('/fiscal-years', 'index');          // หน้าตารางข้อมูล
        Route::post('/fiscal-years', 'store');         // บันทึกเพิ่มข้อมูล
        Route::put('/fiscal-years/{id}', 'update');    // บันทึกแก้ไขข้อมูล
        Route::delete('/fiscal-years/{id}', 'destroy'); // ลบข้อมูล
    });

    // 💡 สามารถเพิ่ม Route ของตารางกลางอื่นๆ ในอนาคตต่อท้ายตรงนี้ได้เลย (เช่น ข้อมูลหน่วยงาน, ข้อมูลผู้สิทธิ์ระบบ)
});


// โมดูลบริหารงานบุคคล (HR Module)
Route::prefix('hr')->group(function () {
    // โครงสร้างหน่วยงาน
    Route::resource('departments', DepartmentController::class)->except(['create', 'edit', 'show']);
    // ข้อมูลบุคลากร (ปรับเป็นแบบแยกหน้าจัดการเต็มใบ)
    Route::resource('personnels', PersonnelController::class);
});

// 3. โมดูลยุทธศาสตร์และแผนโครงการ (Plan Module)
Route::prefix('plan')->group(function () {
    Route::resource('missions', MissionController::class)->except(['create', 'edit', 'show']);
    Route::resource('strategic-issues', StrategicIssueController::class)->except(['create', 'edit', 'show']);
    Route::resource('goals', GoalController::class)->except(['create', 'edit', 'show']);
    Route::resource('strategies', StrategyController::class)->except(['create', 'edit', 'show']);

    // 💵 เส้นทางเพิ่มใหม่: ระบบบริหารแหล่งเงินและโครงสร้างงบประมาณย่อย
    Route::resource('budget-sources', BudgetSourceController::class)->except(['create', 'edit', 'show']);
    Route::resource('programs', ProgramController::class)->except(['create', 'edit', 'show']);
    Route::resource('budget-categories', BudgetCategoryController::class)->except(['create', 'edit', 'show']);

    // 📁 เส้นทางเพิ่มใหม่: ระบบบริหารจัดทำโครงการและแผนคลังงบประมาณรายปี
   // แทนที่ Route::resource บรรทัดเดียวด้วยชุดนี้ครับ
    Route::get('projects', [App\Http\Controllers\Plan\ProjectController::class, 'index']);
    Route::get('projects/create', [App\Http\Controllers\Plan\ProjectController::class, 'create']);
    Route::post('projects', [App\Http\Controllers\Plan\ProjectController::class, 'store']);
    Route::get('projects/{id}/edit', [App\Http\Controllers\Plan\ProjectController::class, 'edit']);
    Route::put('projects/{id}', [App\Http\Controllers\Plan\ProjectController::class, 'update']);
    Route::delete('projects/{id}', [App\Http\Controllers\Plan\ProjectController::class, 'destroy']);

    // Route เสริมที่คุณมีอยู่แล้ว
    Route::post('projects/{id}/update-alignment', [App\Http\Controllers\Plan\ProjectController::class, 'updateAlignment']);
    Route::post('projects/{id}/update-details', [App\Http\Controllers\Plan\ProjectController::class, 'updateDetails']);
    Route::post('projects/{id}/update-budget', [App\Http\Controllers\Plan\ProjectController::class, 'updateBudget']);
    Route::post('projects/{id}/update-activities', [App\Http\Controllers\Plan\ProjectController::class, 'updateActivities']);
    Route::get('projects/activity-form/{id?}', [App\Http\Controllers\Plan\ProjectController::class, 'getActivityForm']);
    Route::delete('projects/activities/{id}', [App\Http\Controllers\Plan\ProjectController::class, 'destroyActivity']);
    
    // แนะนำ: ใช้ sub-activities นำหน้ากิจกรรมหลักเพื่อให้เรียกใช้ง่ายขึ้น
    Route::get('sub-activities/form/{activityId}', [App\Http\Controllers\Plan\SubActivityController::class, 'getSubActivityForm']);
    //Route::post('sub-activities/store/{activityId}', [App\Http\Controllers\Plan\SubActivityController::class, 'store']);
    Route::get('sub-activities/list/{activityId}', [App\Http\Controllers\Plan\SubActivityController::class, 'getSubActivities']);
    Route::get('payments/form/{subActivityBudgetId}', [App\Http\Controllers\Plan\SubActivityController::class, 'getPaymentForm']);
    Route::post('sub-activities/cancel-payment/{subActivityId}', [App\Http\Controllers\Plan\SubActivityController::class, 'cancelPayment']);
    Route::post('sub-activities/payments/{subActivityBudgetId}', [App\Http\Controllers\Plan\SubActivityController::class, 'storePayment']);
});

// วางไว้นอก Group prefix 'plan' ชั่วคราวเพื่อเช็คปัญหา
Route::post('api/sub-activities/store/{activityId}', [App\Http\Controllers\Plan\SubActivityController::class, 'store']);