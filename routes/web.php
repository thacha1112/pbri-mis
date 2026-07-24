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

use App\Http\Controllers\Ums\RoleController;
use App\Http\Controllers\Ums\PermissionController;
use App\Http\Controllers\Ums\UserRoleController;
use App\Http\Controllers\Plan\DepartmentAllocationController;

use App\Http\Controllers\Plan\DisbursementController;

use App\Http\Controllers\Plan\ProjectReportController;



    // ไม่ต้องครอบ Middleware 'web' ซ้ำซ้อน
        Route::get('/login', function () { 
            return view('auth.login'); 
        })->name('login');

        // กลุ่มการล็อกอิน (ไม่ต้องผ่าน checklogin)
        Route::get('/authen/microsoft/redirect', [MicrosoftController::class, 'redirectToMicrosoft'])->name('login.microsoft');
        Route::get('/authen/microsoft/callback', [MicrosoftController::class, 'handleMicrosoftCallback']);
       

Route::middleware(['checklogin'])->group(function () {        
    // หน้าหลัก (Dashboard)

    Route::post('/logout', function () {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');

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
        
        Route::resource('department-allocations', DepartmentAllocationController::class);
        Route::get('get-data-by-year/{fyId}', [DepartmentAllocationController::class, 'getDataByYear']);
        // 1. API สำหรับ Cascade Dropdown (วางไว้ก่อน Resource)
        Route::get('get-sources-by-year/{fyId}', [\App\Http\Controllers\Plan\DepartmentAllocationController::class, 'getSourcesByYear']);
        Route::get('get-programs-by-source/{sourceId}', [\App\Http\Controllers\Plan\DepartmentAllocationController::class, 'getProgramsBySource']);
        Route::get('get-categories-by-program/{programId}', [\App\Http\Controllers\Plan\DepartmentAllocationController::class, 'getCategoriesByProgram']);
        
        // 📁 เส้นทางเพิ่มใหม่: ระบบบริหารจัดทำโครงการและแผนคลังงบประมาณรายปี
        // แทนที่ Route::resource บรรทัดเดียวด้วยชุดนี้ครับ
        // 1. Route สำหรับ Projects (ใช้ resource ให้เป็นประโยชน์)
        Route::resource('projects', App\Http\Controllers\Plan\ProjectController::class)
            ->names('plan.projects')
            ->except(['show']);

        // 2. Route เสริมของ Project (ที่ไม่ได้อยู่ในมาตรฐาน Resource)
        Route::prefix('projects')->name('plan.projects.')->group(function () {
            Route::post('{id}/update-alignment', [App\Http\Controllers\Plan\ProjectController::class, 'updateAlignment'])->name('update-alignment');
            Route::post('{id}/update-details', [App\Http\Controllers\Plan\ProjectController::class, 'updateDetails'])->name('update-details');
            Route::post('{id}/update-budget', [App\Http\Controllers\Plan\ProjectController::class, 'updateBudget'])->name('update-budget');
            Route::post('{id}/update-activities', [App\Http\Controllers\Plan\ProjectController::class, 'updateActivities'])->name('update-activities');
            Route::get('activity-form/{id?}', [App\Http\Controllers\Plan\ProjectController::class, 'getActivityForm'])->name('activity-form');
            Route::delete('activities/{id}', [App\Http\Controllers\Plan\ProjectController::class, 'destroyActivity'])->name('destroy-activity');
            // 🟢 เพิ่ม Route ใหม่ตรงนี้ สำหรับดึงสรุปงบประมาณ
            Route::get('{projectId}/budget-summary', [App\Http\Controllers\Plan\ProjectController::class, 'getProjectBudgetSummary']);
        });

       


        Route::resource('disbursements', DisbursementController::class)
            ->names('plan.disbursements')
            ->only(['index', 'show']);

        // 🟢 กลุ่มรายงานแผนงาน
        Route::prefix('reports')->name('plan.reports.')->group(function () {
            Route::get('/project-summary', [ProjectReportController::class, 'index'])->name('project_summary');
            Route::get('/project-summary/export-excel', [ProjectReportController::class, 'exportExcel'])->name('project_summary.export.excel');
        });

    });

        // 🟢 4. ระบบเบิกจ่ายงบประมาณ (Disbursements Module)
        /*Route::resource('disbursements', DisbursementController::class)
            ->names('plan.disbursements')
            ->only(['index', 'show']);*/

        Route::prefix('disbursements')->name('plan.disbursements.')->group(function () {
            Route::post('payments', [DisbursementController::class, 'storePayment'])->name('payments.store');
            Route::delete('payments/{id}', [DisbursementController::class, 'destroyPayment'])->name('payments.destroy');
        });

       

   

    Route::prefix('ums')->group(function () {
        Route::resource('roles', RoleController::class);
        Route::resource('user-roles', UserRoleController::class)->only(['index', 'edit', 'update']);
    });


});