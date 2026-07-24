<form id="paymentForm">
    @csrf
    <div class="row g-3">
        <div class="col-12">
            <label class="form-label fw-bold small">แหล่งเงินที่ต้องการตัดยอด</label>
            <select class="form-select" name="sub_activity_budget_id" required>
                @foreach($subActivity->subActivityBudgets as $budget)
                    @php
                        // ใช้ยอด effective_amount (PO หรือ PR)
                        $used = $budget->payments->sum('amount');
                        $available = $budget->effective_amount - $used;
                    @endphp
                    <option value="{{ $budget->id }}">
                        {{ $budget->parentBudget->projectBudgetSource->budgetSource->name }} 
                        (คงเหลือ: {{ number_format($available, 2) }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-12">
            <label class="form-label fw-bold small">วันที่เบิกจ่าย</label>
            <input type="date" class="form-control" name="payment_date" value="{{ date('Y-m-d') }}" required>
        </div>
        
        <div class="col-md-12">
            <label class="form-label fw-bold small">จำนวนเงินที่ต้องการเบิก (บาท)</label>
            <input type="text" class="form-control budget-input" name="amount_display" 
                placeholder="0.00" oninput="formatBudgetInput(this)" required>
            <input type="hidden" name="amount" class="budget-hidden" value="0">
        </div>
    </div>
</form>

{{-- ส่วนที่ขาดไป: ปุ่มบันทึกใน Modal Footer --}}
<div class="modal-footer mt-3">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
    <button type="button" class="btn btn-primary" id="btnSavePayment">ยืนยันการเบิกจ่าย</button>
</div>