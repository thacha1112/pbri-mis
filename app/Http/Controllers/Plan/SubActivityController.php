<?php

namespace App\Http\Controllers\Plan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan\ProjectSubActivity;
use App\Models\Plan\SubActivityBudget;
use App\Models\Plan\SubActivityPayment;
use App\Models\Plan\ActivityBudget;
use App\Models\Plan\ProjectActivity;
use Illuminate\Support\Facades\DB;

class SubActivityController extends Controller
{
    // ดึงกิจกรรมย่อยทั้งหมดของกิจกรรมหลัก
   public function getSubActivityForm($activityId)
    {
        // โหลด budgets และ budgetSource ของงบนั้นๆ มาด้วย
        $activity = \App\Models\Plan\ProjectActivity::with(['budgets.budgetSource', 'budgets.subActivityBudgets'])
            ->findOrFail($activityId);
            
        return view('plan.projects.tabs._sub_activity_modal_form', compact('activity'));
    }

    public function getSubActivities($activityId)
    {
        $subActivities = ProjectSubActivity::with(['budgets.payments'])
            ->where('activity_id', $activityId)
            ->get();

        // ปรับโครงสร้างข้อมูลก่อนส่งออก (Transform)
        return $subActivities->map(function ($sub) {
            return [
                'id' => $sub->id,
                'name' => $sub->name,
                // รวมยอดงบประมาณที่ได้รับจากทุกแหล่ง
                'total_allocated' => $sub->budgets->sum('allocated_amount'),
                // รวมยอดที่จ่ายไปแล้ว
                'total_paid' => $sub->budgets->flatMap->payments->sum('amount'),
                'budgets' => $sub->budgets // เก็บ array เดิมไว้เผื่อใช้ในส่วนเบิกจ่าย
            ];
        });
    }

    // บันทึกกิจกรรมย่อย
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'allocated_amount' => 'required|numeric|min:0'
        ]);

        // 1. หาว่ากิจกรรมหลักมีงบจัดสรรมาเท่าไหร่ (Master Budget)
        $activity = ProjectActivity::findOrFail($request->activity_id);

       // ถ้ากิจกรรมหลักมีงบเดียว
        $parentBudget = ActivityBudget::where('activity_id', $activity->id)->firstOrFail();
        
        // 2. หายอดรวมที่ถูกใช้ไปแล้วในกิจกรรมย่อยอื่น ๆ ภายใต้ Master Budget นี้
        $usedAmount = SubActivityBudget::where('activity_budget_id', $parentBudget->id)
                    ->sum('allocated_amount');

        // 3. ตรวจสอบว่ายอดใหม่ที่เพิ่มเข้าไปเกินงบที่มีไหม
        if (($usedAmount + $request->allocated_amount) > $parentBudget->amount) {
            return response()->json(['success' => false, 'message' => 'งบประมาณกิจกรรมย่อยเกินวงเงินที่กิจกรรมหลักได้รับ!'], 422);
        }

        DB::beginTransaction();
        try {
            $sub = ProjectSubActivity::create([
                'activity_id' => $request->activity_id,
                'name' => $request->name
            ]);

            // บันทึกงบประมาณที่กิจกรรมย่อยได้รับ (ผูกกับ activity_budget_id)
            SubActivityBudget::create([
                'sub_activity_id' => $sub->id,
                'activity_budget_id' => $request->activity_budget_id, // รหัสงบที่มาจากกิจกรรมหลัก
                'allocated_amount' => $request->allocated_amount
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'เพิ่มกิจกรรมย่อยสำเร็จ']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // บันทึกการเบิกจ่าย
    public function storePayment(Request $request, $subActivityBudgetId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $budget = SubActivityBudget::findOrFail($subActivityBudgetId);
        $totalPaid = $budget->payments()->sum('amount');
        $remaining = $budget->allocated_amount - $totalPaid;

        if ($request->amount > $remaining) {
            return response()->json(['message' => 'ยอดเบิกเกินงบประมาณที่จัดสรร'], 422);
        }

        $budget->payments()->create([
            'amount' => $request->amount,
            'payment_date' => now(), // หรือรับค่าจาก $request->date
        ]);

        return response()->json(['message' => 'บันทึกการเบิกจ่ายสำเร็จ']);
    }

    public function getPaymentForm($subActivityBudgetId)
    {
        // โหลดข้อมูลกิจกรรมย่อยและประวัติการเบิกจ่าย (payments)
        $budget = SubActivityBudget::with(['subActivity', 'payments'])
            ->findOrFail($subActivityBudgetId);

        return view('plan.projects.tabs._payment_modal_form', compact('budget'));
    }
    /**
     * ยกเลิกการเบิกจ่ายทั้งหมดของกิจกรรมย่อยนั้นๆ
     */
    public function cancelPayment($subActivityId)
    {
        // 1. ค้นหา SubActivityBudget ที่ผูกกับกิจกรรมย่อยนี้
        // (สมมติว่า 1 กิจกรรมย่อยมี 1 งบประมาณ ตามโค้ด store ของคุณ)
        $subBudget = SubActivityBudget::where('sub_activity_id', $subActivityId)->firstOrFail();

        DB::beginTransaction();
        try {
            // 2. ลบรายการเบิกจ่าย (Payments) ทั้งหมดที่ผูกกับงบนี้
            SubActivityPayment::where('sub_activity_budget_id', $subBudget->id)->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'ยกเลิกการเบิกจ่ายเรียบร้อยแล้ว']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }
}