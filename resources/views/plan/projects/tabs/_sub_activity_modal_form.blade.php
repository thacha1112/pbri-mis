@php
    $subActivity = $subActivity ?? null; // ถ้าเป็นการแก้ไขให้ส่ง model มา
@endphp

<form id="subActivityForm">
    @csrf
    <input type="hidden" name="activity_id" value="{{ $activity->id }}">
    <input type="hidden" name="sub_activity_id" value="{{ $subActivity->id ?? '' }}">
    
    <div class="row g-3 p-3">
        <div class="col-12">
            <label class="form-label fw-bold text-primary">ชื่อกิจกรรมย่อย</label>
            <input type="text" class="form-control" name="name" value="{{ $subActivity->name ?? '' }}" required>
        </div>

        <div class="col-12 border-top pt-3">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-coins text-warning"></i> จัดสรรแหล่งเงินและบันทึก PO</h6>
            
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light text-center small">
                        <tr>
                            <th width="30%">แหล่งเงินจากกิจกรรมหลัก</th>
                            <th width="20%">งบคงเหลือ (ให้จัดสรรได้)</th>
                            <th width="25%">จำนวนเงินที่ขอ (PR)</th>
                            <th width="25%">จำนวนเงินอนุมัติ (PO)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activity->budgets as $budget)
                            @php
                                // คำนวณยอดที่ถูกใช้ไปแล้วของแหล่งเงินนี้ในกิจกรรมย่อย "อื่นๆ"
                                $usedByOthers = \App\Models\Plan\SubActivityBudget::where('activity_budget_id', $budget->id)
                                    ->when($subActivity, function($query) use ($subActivity) {
                                        return $query->where('sub_activity_id', '!=', $subActivity->id);
                                    })->get()->sum(function($sub) {
                                        return $sub->effective_amount; // ใช้ ยอด PO (ถ้ามี) หรือ PR
                                    });

                                // งบคงเหลือที่แท้จริง
                                $balance = $budget->amount - $usedByOthers;

                                // ดึงค่ายอดเดิมของกิจกรรมย่อยนี้ (ถ้าเป็นการ Edit)
                                $currentSubBudget = $subActivity ? $subActivity->subActivityBudgets->where('activity_budget_id', $budget->id)->first() : null;
                                $prVal = $currentSubBudget ? $currentSubBudget->allocated_amount : '';
                                $poVal = $currentSubBudget ? $currentSubBudget->po_amount : '';
                            @endphp
                            
                            <tr>
                                <td class="small fw-bold">{{ $budget->projectBudgetSource->budgetSource->name ?? 'ไม่ระบุ' }}</td>
                                <td class="text-end text-success fw-bold">{{ number_format($balance, 2) }}</td>
                                <td>
                                    <input type="text" class="form-control form-control-sm text-end budget-input" 
                                           name="budgets[{{ $budget->id }}][allocated_amount]" 
                                           value="{{ $prVal ? number_format($prVal, 2) : '' }}"
                                           placeholder="0.00" oninput="formatBudgetInput(this)">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm text-end budget-input border-warning" 
                                           name="budgets[{{ $budget->id }}][po_amount]" 
                                           value="{{ $poVal ? number_format($poVal, 2) : '' }}"
                                           placeholder="ระบุเมื่อมี PO" oninput="formatBudgetInput(this)">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-6 border-top pt-3">
            <label class="form-label fw-bold small text-muted">วันที่ได้รับอนุมัติ PO (ถ้ามี)</label>
            <input type="date" class="form-control" name="po_approved_date" 
                   value="{{ $subActivity?->po_approved_date ? \Carbon\Carbon::parse($subActivity->po_approved_date)->format('Y-m-d') : '' }}">
        </div>
    </div>
</form>

<div class="modal-footer mt-3 bg-light">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
    <button type="button" class="btn btn-success" id="btnSaveSubActivity">บันทึกข้อมูล</button>
</div>