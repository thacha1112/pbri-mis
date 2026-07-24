<?php

namespace App\Http\Controllers\Plan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan\Project;
use App\Models\Common\FiscalYear;
use App\Models\Common\Department;
use App\Models\Plan\ActivityPayment;
use App\Models\Plan\ActivityBudget;

class DisbursementController extends Controller
{
    public function index(Request $request)
    {
        // โหลดความสัมพันธ์ที่จำเป็นสำหรับการคำนวณยอดเงิน
        $query = Project::with([
            'fiscalYear',
            'department',
            'personnel',
            'projectBudgetSources',
            'activities.budgets.payments' // โหลดประวัติการเบิกจ่ายของแต่ละกิจกรรม
        ]);
        
        $user = auth()->user();

        // 1. ตรวจสอบสิทธิ์
        if (!$user->hasAnyRoleIds([1, 2])) {
            if ($user->personnal) {
                $query->where('department_id', $user->personnal->department_id);
            } else {
                $query->whereRaw('1=0'); 
            }
        } else {
            // 2. แอดมินสามารถกรองหน่วยงานได้
            if ($request->has('department_id') && $request->department_id != 'all') {
                $query->where('department_id', $request->department_id);
            }
        }

        // กรองด้วยปีงบประมาณ
        if ($request->has('fiscal_year_id') && $request->fiscal_year_id != 'all') {
            $query->where('fiscal_year_id', $request->fiscal_year_id);
        }

        $projects = $query->paginate(10)->withQueryString();
        
        $fiscalYears = FiscalYear::all();
        $departments = $user->hasAnyRoleIds([1, 2]) ? Department::all() : collect();

        return view('plan.disbursements.index', compact('projects', 'fiscalYears', 'departments'));
    }

    public function show($id)
    {
        $project = Project::with([
            'fiscalYear',
            'department',
            'personnel',
            'activities.budgets.projectBudgetSource.budgetSource.fiscalYear',
            'activities.budgets.projectBudgetSource.program',
            'activities.budgets.projectBudgetSource.category',
            'activities.budgets.payments' // ดึงประวัติการเบิกจ่ายของงบกิจกรรมนั้นๆ มาด้วย
        ])->findOrFail($id);

        // คำนวณภาพรวมของโครงการเฉพาะส่วนที่จัดสรรลงกิจกรรมแล้ว
        $totalAllocatedToActivities = 0;
        $totalPaid = 0;

        foreach ($project->activities as $act) {
            foreach ($act->budgets as $budget) {
                $totalAllocatedToActivities += $budget->amount;
                $totalPaid += $budget->payments->sum('amount');
            }
        }
        
        $balance = $totalAllocatedToActivities - $totalPaid;

        return view('plan.disbursements.show', compact('project', 'totalAllocatedToActivities', 'totalPaid', 'balance'));
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            'activity_budget_id' => 'required|integer',
            'payment_date'       => 'required|date',
            'payment_type'       => 'required|in:payment,borrow,transfer',
            'amount'             => 'required|numeric|min:0.01',
        ]);

        try {
            $budget = ActivityBudget::with('payments')->findOrFail($request->activity_budget_id);
            
            // 🟢 1. ตรวจสอบเงื่อนไข: หากมีประวัติแล้ว ประเภทต้องเหมือนกับรายการแรกเท่านั้น
            if ($budget->payments->count() > 0) {
                $firstPaymentType = $budget->payments->first()->payment_type;
                if ($request->payment_type !== $firstPaymentType) {
                    $typeName = [
                        'payment' => 'เบิกจ่ายจริง',
                        'borrow' => 'ยืมเงิน',
                        'transfer' => 'โอนเงิน'
                    ];
                    return response()->json([
                        'success' => false, 
                        'message' => 'หมวดเงินนี้ถูกกำหนดประเภทการเบิกจ่ายเป็น "' . ($typeName[$firstPaymentType] ?? $firstPaymentType) . '" แล้ว ต้องเลือกประเภทเดียวกันเท่านั้น'
                    ], 422);
                }
            }

            // 2. ตรวจสอบยอดเงินคงเหลือ
            $totalPaid = $budget->payments->sum('amount');
            $remaining = $budget->amount - $totalPaid;

            if ($request->amount > $remaining) {
                return response()->json(['success' => false, 'message' => 'ยอดทำรายการเกินกว่างบประมาณคงเหลือ'], 422);
            }

            ActivityPayment::create([
                'activity_budget_id' => $request->activity_budget_id,
                'payment_date'       => $request->payment_date,
                'payment_type'       => $request->payment_type,
                'amount'             => $request->amount,
                'description'        => $request->description
            ]);

            return response()->json(['success' => true, 'message' => 'บันทึกรายการสำเร็จ']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }

    /**
     * ลบประวัติการเบิกจ่าย
     */
    public function destroyPayment($id)
    {
        try {
            $payment = ActivityPayment::findOrFail($id);
            $payment->delete();
            
            return response()->json(['success' => true, 'message' => 'ลบรายการเบิกจ่ายสำเร็จ']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }
}