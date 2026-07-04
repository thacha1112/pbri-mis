<form id="subActivityForm">
    @csrf
    <input type="hidden" name="activity_id" value="{{ $activity->id }}">
    
    <div class="row g-3">
        <div class="col-12 bg-light p-2 rounded border">
            <small class="text-muted">กิจกรรมหลัก: {{ $activity->name ?? 'ไม่พบชื่อกิจกรรม' }}</small><br>
            <strong class="text-primary">
                งบประมาณรวม: {{ number_format($activity->budgets->sum('amount'), 2) }} บาท
            </strong>
        </div>

        <div class="col-12">
            <label class="form-label fw-bold small">ชื่อกิจกรรมย่อย</label>
            <input type="text" class="form-control" name="name" required>
        </div>

        <div class="col-12">
            <label class="form-label fw-bold small">แหล่งงบประมาณ</label>
            <select class="form-select" name="activity_budget_id" required>
                <option value="">-- เลือกแหล่งงบประมาณ --</option>
                @foreach($activity->budgets as $budget)
                    @php
                        // คำนวณงบคงเหลือ: งบที่ตั้งไว้ - งบที่กิจกรรมย่อยใช้ไปแล้ว
                        $used = $budget->subActivityBudgets->sum('allocated_amount');
                        $balance = $budget->amount - $used;
                        
                    @endphp
                    <option value="{{ $budget->id }}">
                       {{ $budget->projectBudgetSource->budgetSource->name ?? 'ไม่ระบุแหล่งเงิน' }}
                        (คงเหลือ: {{ number_format($balance, 2) }} บาท)
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-12">
            <label class="form-label fw-bold small">จัดสรรงบประมาณ (บาท)</label>
            <input type="number" class="form-control" name="allocated_amount" step="0.01" required>
        </div>
    </div>
</form>

<div class="modal-footer mt-3">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
    <button type="button" class="btn btn-success" id="btnSaveSubActivity">บันทึกกิจกรรมย่อย</button>
</div>