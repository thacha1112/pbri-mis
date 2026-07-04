<form id="paymentForm">
    @csrf
    <input type="hidden" name="sub_activity_budget_id" value="{{ $budget->id }}">

    <div class="row g-3">
        <div class="col-12 bg-light p-3 rounded">
            <p class="mb-1 text-muted small">กิจกรรมย่อย: {{ $budget->subActivity->name }}</p>
            <h6 class="mb-0">งบประมาณที่จัดสรร: <span class="text-primary">{{ number_format($budget->allocated_amount, 2) }}</span> บาท</h6>
            <h6 class="mb-0">เบิกจ่ายแล้ว: <span class="text-danger">{{ number_format($budget->payments->sum('amount'), 2) }}</span> บาท</h6>
            <hr>
            <h6 class="mb-0 text-success">คงเหลือเบิกได้: <strong>{{ number_format($budget->allocated_amount - $budget->payments->sum('amount'), 2) }}</strong> บาท</h6>
        </div>

        <div class="col-md-12">
            <label class="form-label fw-bold small">วันที่เบิกจ่าย</label>
            <input type="date" class="form-control" name="payment_date" value="{{ date('Y-m-d') }}" required>
        </div>
         <div class="col-md-12">
            <label class="form-label fw-bold small">จำนวนเงินที่ต้องการเบิก (บาท)</label>
            <input type="number" class="form-control" name="amount" step="0.01" max="{{ $budget->allocated_amount - $budget->payments->sum('amount') }}" required>
        </div>

       
    </div>
</form>

<div class="modal-footer mt-3">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
    <button type="button" class="btn btn-primary" id="btnSavePayment">ยืนยันการเบิกจ่าย</button>
</div>