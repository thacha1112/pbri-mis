@extends('layouts.app')

@section('content')
<!-- ปุ่มย้อนกลับ -->
<div class="mb-3">
    <a href="{{ route('plan.disbursements.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> ย้อนกลับหน้ารวม
    </a>
</div>

<!-- Header ส่วนข้อมูลโครงการ -->
<div class="card border-0 shadow-sm bg-white mb-4 rounded-4 overflow-hidden">
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-8">
                <span class="badge bg-dark px-2 py-1 font-mono fs-7 mb-2">{{ $project->project_code }}</span>
                <h4 class="fw-bold text-primary mb-1">{{ $project->name }}</h4>
                <p class="text-muted mb-0">
                    <i class="fa-solid fa-building me-1"></i> {{ $project->department->name ?? '-' }} | 
                    <i class="fa-solid fa-user me-1 ms-2"></i> ผู้รับผิดชอบ: {{ $project->personnel->firstname ?? '-' }} {{ $project->personnel->lastname ?? '-' }}
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="d-inline-block text-start bg-light rounded-3 p-3 border w-100 shadow-sm">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">งบจัดสรรลงกิจกรรมรวม:</span>
                        <span class="fw-bold text-dark">{{ number_format($totalAllocatedToActivities, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">เบิกจ่ายแล้วสะสม:</span>
                        <span class="fw-bold text-warning">{{ number_format($totalPaid, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-1 mt-1">
                        <span class="text-muted small fw-bold">คงเหลือ:</span>
                        <span class="fw-bold fs-5 {{ $balance < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($balance, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ส่วนรายการกิจกรรมและเบิกจ่าย -->
<h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-list-check text-primary me-2"></i>รายการกิจกรรมและการเบิกจ่าย</h5>

@forelse($project->activities as $index => $activity)
<div class="card border border-primary-subtle shadow-sm mb-4 rounded-4 overflow-hidden">
    <div class="card-header bg-primary bg-opacity-10 py-3 border-bottom-0 d-flex justify-content-between align-items-center">
        <div>
            <span class="badge bg-primary me-2">กิจกรรมที่ {{ $index + 1 }}</span>
            <strong class="text-dark fs-6">{{ $activity->name }}</strong>
        </div>
        <div class="small text-muted">
            <i class="fa-regular fa-calendar me-1"></i> 
            {{ \Carbon\Carbon::parse($activity->start_date)->addYears(543)->format('d/m/Y') }} - 
            {{ \Carbon\Carbon::parse($activity->end_date)->addYears(543)->format('d/m/Y') }}
        </div>
    </div>
    
    <div class="card-body p-0">
        @if($activity->budgets->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary fs-7 text-center">
                        <tr>
                            <th width="35%" class="text-start ps-4 py-3">แหล่งงบประมาณที่ใช้</th>
                            <th width="15%" class="text-end py-3">จัดสรร (บาท)</th>
                            <th width="15%" class="text-end py-3">เบิกจ่ายแล้ว (บาท)</th>
                            <th width="15%" class="text-end py-3">คงเหลือ (บาท)</th>
                            <th width="20%" class="text-center py-3">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activity->budgets as $budget)
                            @php
                                $bSource = $budget->projectBudgetSource;
                                $paid = $budget->payments->sum('amount');
                                $remain = $budget->amount - $paid;
                                // เช็คประเภทการเบิกจ่ายที่มีอยู่เดิม (ถ้ามี)
                                $existingType = $budget->payments->count() > 0 ? $budget->payments->first()->payment_type : '';
                            @endphp
                            <!-- แถวข้อมูลแหล่งเงิน -->
                            <tr class="bg-white">
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $bSource->budgetSource->name ?? '-' }}</div>
                                    <div class="small text-muted">
                                        {{ $bSource->program->name ?? '-' }} / {{ $bSource->category->name ?? '-' }}
                                    </div>
                                </td>
                                <td class="text-end fw-semibold text-dark">{{ number_format($budget->amount, 2) }}</td>
                                <td class="text-end fw-semibold text-warning">{{ number_format($paid, 2) }}</td>
                                <td class="text-end fw-bold {{ $remain < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($remain, 2) }}
                                </td>
                                <td class="text-center">
                                    <!-- 🟢 เงื่อนไข: ถ้างบเหลือ <= 0 หรือยอดสะสมเต็ม ปิดปุ่มเบิกเงิน -->
                                    <button class="btn btn-sm btn-primary shadow-sm rounded-3 px-3" 
                                        onclick="openPaymentModal({{ $budget->id }}, {{ $remain }}, '{{ $existingType }}')"
                                        {{ $remain <= 0 ? 'disabled title=งบประมาณหมดแล้ว' : '' }}>
                                        <i class="fa-solid fa-plus me-1"></i> เบิกเงิน
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- แถวประวัติการเบิกจ่าย -->
                            @if($budget->payments->count() > 0)
                            <tr>
                                <td colspan="5" class="p-0 border-0">
                                    <div class="bg-light p-3 border-bottom shadow-inner">
                                        <div class="small fw-bold text-secondary mb-2"><i class="fa-solid fa-clock-rotate-left me-1"></i> ประวัติการทำรายการเบิกจ่ายงบนี้:</div>
                                        <table class="table table-sm table-bordered bg-white mb-0 fs-7">
                                            <thead class="table-secondary text-center">
                                                <tr>
                                                    <th width="20%">วันที่รายการ</th>
                                                    <th width="20%">ประเภท</th>
                                                    <th>รายละเอียด/หมายเหตุ</th>
                                                    <th width="20%">ยอดเงิน (บาท)</th>
                                                    <th width="10%">ลบ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($budget->payments as $payment)
                                                <tr>
                                                    <td class="text-center">{{ \Carbon\Carbon::parse($payment->payment_date)->addYears(543)->format('d/m/Y') }}</td>
                                                    <td class="text-center">
                                                        @if($payment->payment_type == 'payment') <span class="badge bg-success-subtle text-success border">เบิกจ่ายจริง</span>
                                                        @elseif($payment->payment_type == 'transfer') <span class="badge bg-info-subtle text-info border">โอนเงิน</span>
                                                        @else <span class="badge bg-warning-subtle text-warning border">ยืมเงิน</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $payment->description ?? '-' }}</td>
                                                    <td class="text-end fw-bold text-dark">{{ number_format($payment->amount, 2) }}</td>
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-outline-danger p-1" onclick="deletePayment({{ $payment->id }})" title="ลบรายการ">
                                                            <i class="fa-solid fa-trash-can"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center text-muted py-4 small fst-italic">
                <i class="fa-solid fa-circle-exclamation me-1"></i> กิจกรรมนี้ยังไม่มีการจัดสรรงบประมาณ
            </div>
        @endif
    </div>
</div>
@empty
<div class="alert alert-light border shadow-sm text-center py-5 rounded-4 text-muted">
    <i class="fa-solid fa-inbox fs-1 mb-3 d-block text-black-50"></i> โครงการนี้ยังไม่มีข้อมูลกิจกรรม
</div>
@endforelse

<!-- Modal บันทึกการเบิกจ่าย -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="paymentForm">
            @csrf
            <input type="hidden" name="activity_budget_id" id="activity_budget_id">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header bg-light border-bottom-0 py-3 px-4">
                    <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-hand-holding-dollar me-2"></i> บันทึกรายการเบิกจ่าย</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning py-2 px-3 small border-0 shadow-sm d-flex justify-content-between mb-3">
                        <span>ยอดคงเหลือที่เบิกได้สูงสุด:</span>
                        <strong id="max_payment_display" class="text-danger fs-6">0.00</strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">วันที่ทำรายการ <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="payment_date" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">ประเภทรายการ <span class="text-danger">*</span></label>
                        <select class="form-select" name="payment_type" id="payment_type_select" required>
                            <option value="payment">เบิกจ่ายจริง (Payment)</option>
                            <option value="borrow">ยืมเงิน (Borrow)</option>
                            <option value="transfer">โอนเงิน (Transfer)</option>
                        </select>
                        <div id="type_lock_notice" class="form-text text-danger small" style="display: none;">
                            <i class="fa-solid fa-lock me-1"></i> ล็อคประเภทตามประวัติการเบิกจ่ายครั้งแรก
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">ยอดเงิน (บาท) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control fw-bold text-primary text-end" id="amount_input" oninput="formatPaymentInput(this)" placeholder="0.00" required>
                        <input type="hidden" name="amount" id="amount_hidden">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">รายละเอียด/หมายเหตุ</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="ระบุรายละเอียด..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light py-3 px-4">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-save me-1"></i> ยืนยันการบันทึก</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentMaxRemain = 0;

    // 🟢 ปรับฟังก์ชันรับค่า existingType มาล็อค Dropdown
    function openPaymentModal(budgetId, remain, existingType) {
        $('#activity_budget_id').val(budgetId);
        currentMaxRemain = parseFloat(remain);
        
        $('#max_payment_display').text(currentMaxRemain.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' บาท');
        $('#paymentForm')[0].reset();
        $('#amount_hidden').val('');

        let $typeSelect = $('#payment_type_select');
        let $notice = $('#type_lock_notice');

        if (existingType && existingType !== '') {
            // ถ้ามีประวัติแล้ว ให้เลือกประเภทเดิมและ Disable ไว้
            $typeSelect.val(existingType).prop('disabled', true);
            // สร้าง hidden input เพื่อส่งค่าประเภทไปด้วย เพราะช่อง disabled จะไม่ส่งค่าผ่านฟอร์ม
            if($('#hidden_payment_type').length === 0) {
                $('#paymentForm').append(`<input type="hidden" name="payment_type" id="hidden_payment_type" value="${existingType}">`);
            } else {
                $('#hidden_payment_type').val(existingType);
            }
            $notice.show();
        } else {
            // ถ้ายังไม่มีประวัติ ปลดล็อคให้เลือกได้อิสระ
            $typeSelect.prop('disabled', false);
            $('#hidden_payment_type').remove();
            $notice.hide();
        }
        
        var myModal = new bootstrap.Modal(document.getElementById('paymentModal'), { backdrop: 'static' });
        myModal.show();
    }

    function formatPaymentInput(input) {
        let rawValue = input.value.replace(/,/g, '');
        if (isNaN(rawValue) || rawValue === "") { 
            $('#amount_hidden').val(0); 
            return; 
        }

        let floatVal = parseFloat(rawValue);
        if(floatVal > currentMaxRemain) {
            Swal.fire({icon: 'warning', title: 'ยอดเกินงบประมาณ', text: 'เบิกได้สูงสุด ' + currentMaxRemain.toLocaleString() + ' บาท', timer: 2000});
            floatVal = currentMaxRemain;
            rawValue = currentMaxRemain.toString();
        }

        $('#amount_hidden').val(floatVal);
        let parts = rawValue.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        input.value = parts.join('.');
    }

    // Ajax Save: บันทึกการเบิกจ่าย
    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        
        // ปลดล็อคชั่วคราวเพื่อให้ serialize ดึงค่าประเภทไปได้ (กรณีถูก disable)
        $('#payment_type_select').prop('disabled', false);
        let formData = $(this).serialize();
        if($('#hidden_payment_type').length > 0) {
            $('#payment_type_select').prop('disabled', true);
        }

        let submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> กำลังบันทึก...');

        $.ajax({
            url: `{{ route('plan.disbursements.payments.store') }}`,
            type: 'POST',
            data: formData,
            success: function(res) {
                if(res.success) {
                    $('#paymentModal').modal('hide');
                    Swal.fire({
                        icon: 'success', 
                        title: 'บันทึกสำเร็จ', 
                        text: res.message,
                        timer: 1500, 
                        showConfirmButton: false
                    }).then(() => { 
                        window.location.reload(); 
                    });
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html('<i class="fa-solid fa-save me-1"></i> ยืนยันการบันทึก');
                let msg = xhr.responseJSON ? xhr.responseJSON.message : 'ไม่สามารถบันทึกข้อมูลได้';
                Swal.fire('เกิดข้อผิดพลาด', msg, 'error');
            }
        });
    });

    // Ajax Delete: ลบรายการเบิกจ่าย
    function deletePayment(paymentId) {
        Swal.fire({
            title: 'ยืนยันลบรายการเบิกจ่าย?',
            text: "หากลบแล้ว ยอดเงินจะถูกคืนกลับไปยังงบประมาณคงเหลือ",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ยืนยันการลบ'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('plan/disbursements/payments') }}/${paymentId}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if(res.success) {
                            Swal.fire({
                                icon: 'success', 
                                title: 'ลบสำเร็จ', 
                                timer: 1000, 
                                showConfirmButton: false
                            }).then(() => { 
                                window.location.reload(); 
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถลบข้อมูลได้', 'error');
                    }
                });
            }
        });
    }
</script>
@endpush