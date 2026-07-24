<form id="editPaymentForm">
    @csrf @method('PUT')
    <input type="hidden" name="payment_id" value="{{ $payment->id }}">
    <div class="row g-3">
        <div class="col-12">
            <label class="form-label fw-bold">จำนวนเงินที่เบิก (บาท)</label>
            <input type="text" class="form-control budget-input" name="amount_display" 
                   value="{{ number_format($payment->amount, 2) }}" oninput="formatBudgetInput(this)" required>
            <input type="hidden" name="amount" class="budget-hidden" value="{{ $payment->amount }}">
        </div>
        <div class="col-12">
            <label class="form-label fw-bold">วันที่เบิก</label>
            <input type="date" class="form-control" name="payment_date" value="{{ $payment->payment_date }}" required>
        </div>
    </div>
</form>
<div class="modal-footer"><button type="button" class="btn btn-primary" id="btnUpdatePayment">บันทึกการแก้ไข</button></div>