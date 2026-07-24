<?php

namespace App\Http\Controllers\Plan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan\DepartmentAllocation;
use App\Models\Common\Department;
use App\Models\Common\FiscalYear;
use App\Models\Plan\BudgetSource;
use App\Models\Plan\Program;
use App\Models\Plan\BudgetCategory;

class DepartmentAllocationController extends Controller
{
    public function index(Request $request)
    {
        $fiscalYears = FiscalYear::where('status', 'active')->orderBy('year', 'desc')->get();
        $departments = Department::where('status', 'active')->get();
        
        // กำหนดค่าเริ่มต้นของปีงบประมาณ
        // ถ้ามีค่าจาก Request ให้ใช้ค่านั้น, ถ้าไม่มี (เข้าครั้งแรก) ให้ใช้ ID ของปีล่าสุด (อันแรกใน Collection)
        $selectedFiscalYearId = $request->fiscal_year_id ?? ($fiscalYears->first()->id ?? null);

        $allocations = DepartmentAllocation::with(['department', 'fiscalYear', 'budgetSource', 'program', 'category'])
            ->when($selectedFiscalYearId, fn($q) => $q->where('fiscal_year_id', $selectedFiscalYearId))
            ->when($request->department_id, fn($q) => $q->where('department_id', $request->department_id))
            ->orderBy('id', 'desc')
            ->get();

        // ส่ง $selectedFiscalYearId ไปให้ View ด้วย เพื่อให้ Dropdown แสดงค่าที่ถูกเลือกได้ถูกต้อง
        return view('plan.department_allocations.index', compact('allocations', 'fiscalYears', 'departments', 'selectedFiscalYearId'));
    }


    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'department_id'         => 'required|integer',
            'fiscal_year_id'        => 'required|integer',
            'source_fiscal_year_id' => 'required|integer',
            'budget_source_id'      => 'required|integer',
            'program_id'            => 'nullable|integer',
            'category_id'           => 'nullable|integer',
            'total_amount'          => 'required|numeric'
        ], [
            // ข้อความ Error
            'required' => 'กรุณากรอกข้อมูลในช่อง :attribute ให้ครบถ้วน',
            'integer'  => ':attribute ต้องเป็นตัวเลขจำนวนเต็ม',
            'numeric'  => ':attribute ต้องเป็นตัวเลข',
        ], [
            // ชื่อฟิลด์ภาษาไทย
            'department_id'         => 'หน่วยงาน',
            'fiscal_year_id'        => 'ปีงบประมาณโครงการ',
            'source_fiscal_year_id' => 'ปีของแหล่งเงินต้นทาง',
            'budget_source_id'      => 'แหล่งเงิน',
            'total_amount'          => 'ยอดจัดสรรรวม',
        ]);

        // 2. เตรียมข้อมูล (Data Sanitization)
        // การใช้ merge ช่วยจัดการกรณี field เป็นค่าว่าง ให้กลายเป็น null แทน
        $data = $request->only([
            'department_id', 
            'fiscal_year_id', 
            'source_fiscal_year_id', 
            'budget_source_id', 
            'program_id', 
            'category_id', 
            'total_amount'
        ]);

        // แปลงค่าว่างให้เป็น null สำหรับฟิลด์ที่ nullable
        $data['program_id']  = $request->program_id ?: null;
        $data['category_id'] = $request->category_id ?: null;

        // 3. บันทึกข้อมูล
        DepartmentAllocation::updateOrCreate(
            ['id' => $request->id], // ถ้ามี id จะเป็นการ update
            $data
        );

        // 4. Redirect กลับ
        return redirect()->route('department-allocations.index', [
            'fiscal_year_id' => $request->fiscal_year_id,
            'department_id'  => $request->department_id,
        ])->with('success', 'บันทึกข้อมูลการจัดสรรงบประมาณสำเร็จ');
    }

    public function edit($id)
    {
        $allocation = DepartmentAllocation::findOrFail($id);
        
        // ดึงค่าปีของแหล่งเงินต้นทางจากตาราง BudgetSource โดยตรง
        $budgetSource = BudgetSource::find($allocation->budget_source_id);
        $source_fiscal_year_id = $budgetSource ? $budgetSource->fiscal_year_id : null;

        // ดึงตัวเลือกตาม 'source_fiscal_year_id' ที่เราเพิ่งดึงมา
        $sources = BudgetSource::where('fiscal_year_id', $source_fiscal_year_id)->where('status', 'active')->get(['id', 'name']);
        $programs = Program::where('budget_source_id', $allocation->budget_source_id)->where('status', 'active')->get(['id', 'name']);
        $categories = BudgetCategory::where('program_id', $allocation->program_id)->where('status', 'active')->get(['id', 'name']);

        return response()->json(compact('allocation', 'sources', 'programs', 'categories', 'source_fiscal_year_id'));
    }

    public function destroy($id)
    {
        DepartmentAllocation::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'ลบข้อมูลสำเร็จ']);
    }

    // ================== API สำหรับ Cascade Dropdown ==================
    public function getSourcesByYear($fyId)
    {
        return response()->json(BudgetSource::where('fiscal_year_id', $fyId)->where('status', 'active')->get(['id', 'name']));
    }

    public function getProgramsBySource($sourceId)
    {
        return response()->json(Program::where('budget_source_id', $sourceId)->where('status', 'active')->get(['id', 'name']));
    }

    public function getCategoriesByProgram($programId)
    {
        return response()->json(BudgetCategory::where('program_id', $programId)->where('status', 'active')->get(['id', 'name']));
    }
}