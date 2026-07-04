<button type="button" class="btn btn-primary mb-3" onclick="openActivityModal()">+ เพิ่มกิจกรรม</button>

<div class="alert alert-info border-0 shadow-sm rounded-4 mb-4">
    <div class="w-100">
        <div class="d-flex align-items-center mb-3">
            <i class="fa-solid fa-wallet text-primary me-2 fs-5"></i> 
            <strong class="fs-6">สรุปผลการจัดสรรและสถานะงบประมาณคงเหลือ</strong>
        </div>
        {{-- จุดที่ JavaScript จะเข้ามาพ่นการ์ดสรุปข้อมูล --}}
        <div id="budget-summary-bar" class="row g-3"></div>
    </div>
</div>

<table class="table table-hover table-bordered align-middle" id="activity-table">
    <thead class="table-light">
        <tr>
            <th>ลำดับ</th>
            <th>ชื่อกิจกรรม</th>
            @foreach($project->projectBudgetSources as $b)
                <th>{{ $b->budgetSource->name }}</th>
            @endforeach
            <th>รวมเงิน</th>
            <th class="text-center">จัดการ</th>
        </tr>
    </thead>
    <tbody id="activity-body">
        @foreach($project->activities as $index => $activity)
            @php
                // ตรวจสอบว่ากิจกรรมหลักนี้มีข้อมูลการจัดสรรเงินรายแหล่งเงิน และมียอดรวมมากกว่า 0 หรือไม่
                $hasBudget = $activity->budgets->count() > 0 && $activity->budgets->sum('amount') > 0;
            @endphp

            <tr data-id="{{ $activity->id }}">
                <td>{{ $index + 1 }}</td>
                <td>
                    <div class="d-flex align-items-center justify-content-between">
                        <span>{{ $activity->name }}</span>
                        
                        {{-- แสดงปุ่มจัดการกิจกรรมย่อย เฉพาะกิจกรรมที่มีการผูกแหล่งเงินแล้วเท่านั้น --}}
                        @if($hasBudget)
                            <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-1 small" 
                                    onclick="toggleSubActivities({{ $activity->id }}, this)">
                                <i class="fa-solid fa-list-check me-1 small"></i> จัดการกิจกรรมย่อย
                            </button>
                        @else
                            <span class="badge bg-light text-muted border small fw-normal">ยังไม่ได้จัดสรรงบประมาณ</span>
                        @endif
                    </div>
                </td>
                @foreach($project->projectBudgetSources as $b)
                    <td>{{ number_format($activity->budgets->where('project_budget_source_id', $b->id)->first()?->amount ?? 0, 2) }}</td>
                @endforeach
                <td class="fw-bold">{{ number_format($activity->budgets->sum('amount'), 2) }}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-warning" onclick="openActivityModal({{ $activity->id }})">แก้ไข</button>
                    {{-- ปุ่มลบ --}}
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteActivity({{ $activity->id }})">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            </tr>

            {{-- แถวสำหรับแสดงกิจกรรมย่อย (สไลด์เปิด-ปิดได้) --}}
            @if($hasBudget)
                <tr id="sub-activity-row-{{ $activity->id }}" style="display:none;">
                    <td colspan="{{ $project->projectBudgetSources->count() + 4 }}" class="bg-light p-3">
                        <div class="card border-0 shadow-sm p-3 rounded-3 mb-2">
                            
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                <span class="fw-bold text-secondary">
                                    <i class="fa-solid fa-folder-tree me-1"></i> รายการกิจกรรมย่อยภายใต้: {{ $activity->name }}
                                </span>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-primary" onclick="openSubActivityModal({{ $activity->id }})">
                                        <i class="fa-solid fa-plus me-1"></i> เพิ่มกิจกรรมย่อย
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="closeSubActivityRow({{ $activity->id }})">
                                        <i class="fa-solid fa-xmark me-1"></i> ปิดช่องนี้
                                    </button>
                                </div>
                            </div>
                            
                            <div id="sub-activity-container-{{ $activity->id }}">
                                @if($activity->subActivities->count() === 0)
                                    <div class="text-center py-4 text-muted bg-light rounded-3 border border-dashed">
                                        <i class="fa-solid fa-circle-info text-secondary mb-2 fs-5 d-block"></i>
                                        <span>ยังไม่มีรายการกิจกรรมย่อยในระบบ คุณสามารถเริ่มสร้างได้โดยคลิกปุ่ม "เพิ่มกิจกรรมย่อย" ด้านบน</span>
                                    </div>
                                @else
                                    <div class="text-center py-3 text-muted small">
                                        <i class="fa-solid fa-spinner fa-spin me-1"></i> กำลังโหลดข้อมูลกิจกรรมย่อย...
                                    </div>
                                @endif
                            </div>

                        </div>
                    </td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>

<div class="modal fade" id="activityModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <form id="activityForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5>จัดการกิจกรรมหลัก</h5></div>
                <div class="modal-body" id="modal-content"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success">บันทึกกิจกรรมหลัก</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="subActivityModal" tabindex="-1">
    <div class="modal-dialog xl">
        <form id="subActivityForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5>จัดการกิจกรรมย่อย</h5></div>
                    <div class="modal-body" id="sub-modal-content"></div>
                 </div>
            </div>
        </form>
</div>

<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="paymentModalLabel">
                    <i class="fa-solid fa-money-bill-transfer text-primary me-2"></i> บันทึกการเบิกจ่าย
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body" id="payment-modal-content">
                <div class="text-center py-4">
                    <i class="fa-solid fa-spinner fa-spin text-primary fs-3"></i>
                    <p class="mt-2">กำลังโหลดข้อมูล...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // ดึงข้อมูลแหล่งเงินตั้งต้นจากฝั่ง Server
    //const currentProjectId = {!! json_encode($project->id) !!};
    const projectBudgets = @json($project->projectBudgetSources);
    

    // 1. เปิด Modal จัดการกิจกรรมหลัก
            function openActivityModal(id = null) {
                let url = id ? `{{ url('plan/projects/activity-form') }}/${id}` : `{{ url('plan/projects/activity-form') }}`;
                
                $.get(url, { project_id: currentProjectId }, function(html) {
                    $('#modal-content').html(html);
                    
                    var modalEl = document.getElementById('activityModal');
                    
                    // --- แก้ไขตรงนี้ ---
                    // ใช้ .getInstance() เพื่อเช็คว่ามีอยู่แล้วไหม ถ้ามีให้ใช้ตัวเดิม
                    var myModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl, {
                        backdrop: 'static',
                        keyboard: false
                    });
                    
                    myModal.show();
                });
            }

   


    // 2. ปรับปรุง Event Listener ของฟอร์มกิจกรรมหลัก
    // --- บันทึกกิจกรรมหลัก ---
        $('#activityForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: `{{ url('plan/projects') }}/${currentProjectId}/update-activities`,
                type: 'POST',
                data: $(this).serialize(),
                
                // --- วางโค้ดใหม่ตรงนี้ครับ ---
                success: function(res) {
                    // 1. ดึง Instance ของ Modal ที่กำลังเปิดอยู่เพื่อสั่งปิด
                    const modalEl = document.getElementById('activityModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl); 
                    
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                    
                    // 2. แสดงแจ้งเตือนและรีเฟรชหน้า
                    Swal.fire({ 
                        icon: 'success', 
                        title: 'บันทึกสำเร็จ', 
                        timer: 1000, 
                        showConfirmButton: false 
                    }).then(() => { 
                        window.location.reload(); 
                    });
                },
                // --- จบโค้ดส่วน success ---
                
                error: function(xhr) {
                    Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                }
            });
        });

    // 3. ฟังก์ชันสลับเปิด-ปิด แถวกิจกรรมย่อยแบบสไลด์เดอร์
    function toggleSubActivities(activityId, button) {
        let row = $(`#sub-activity-row-${activityId}`);
        if (row.is(':visible')) {
            row.slideUp(200);
            $(button).removeClass('btn-secondary text-white').addClass('btn-outline-secondary');
        } else {
            row.slideDown(200);
            $(button).removeClass('btn-outline-secondary').addClass('btn-secondary text-white');
        }
    }

    function closeSubActivityRow(activityId) {
        let row = $(`#sub-activity-row-${activityId}`);
        row.slideUp(200);
        
        let btnManage = $(`tr[data-id="${activityId}"]`).find('button[onclick^="toggleSubActivities"]');
        if (btnManage.length) {
            btnManage.removeClass('btn-secondary text-white').addClass('btn-outline-secondary');
        }
    }

    // 5. ตัวจัดการพ่นการ์ดแสดงผลสรุปงบประมาณคงเหลือด้านบน
    function updateBudgetSummary() {
        let summaryHtml = '';
        
        if (!projectBudgets || projectBudgets.length === 0) {
            $('#budget-summary-bar').html('<div class="col-12 text-muted small">ยังไม่มีการกำหนดแหล่งงบประมาณใน Tab 4</div>');
            return;
        }

        projectBudgets.forEach((b, index) => {
            let totalAllocated = 0;
            
            // ปรับ Selector เพื่อให้ข้ามแถวที่เป็นข้อมูลย่อย ป้องกันงบสรุปด้านบนคำนวณคลาดเคลื่อน
            $('#activity-table tbody tr').each(function() {
                if ($(this).find('td').length > 1 && !$(this).attr('id')?.startsWith('sub-activity-row-')) {
                    let cellValue = $(this).find('td').eq(index + 2).text().replace(/,/g, '');
                    totalAllocated += parseFloat(cellValue || 0);
                }
            });

            let sourceName = 'ไม่ระบุแหล่งเงิน';
            if (b?.budget_source?.name) {
                sourceName = b.budget_source.name;
            } else if (b?.budgetSource?.name) {
                sourceName = b.budgetSource.name;
            } else if (b?.budget_source_id) {
                sourceName = 'แหล่งเงิน ID: ' + b.budget_source_id;
            }

            let allocatedAmount = parseFloat(b.allocated_amount || 0);
            let balance = allocatedAmount - totalAllocated;
            
            let borderClass = 'border-primary';
            let textClass = 'text-primary';
            
            if (balance < 0) {
                borderClass = 'border-danger bg-danger-subtle';
                textClass = 'text-danger';
            } else if (balance === 0) {
                borderClass = 'border-success bg-success-subtle';
                textClass = 'text-success';
            }

            summaryHtml += `
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-white border-start border-4 ${borderClass} shadow-sm rounded-3">
                        <div class="card-body p-3">
                            <div class="fw-bold text-dark text-truncate mb-2" title="${sourceName}">
                                <i class="fa-solid fa-coins text-muted me-1 small"></i> ${sourceName}
                            </div>
                            <div class="d-flex flex-column gap-1 small text-muted border-bottom pb-2 mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>งบตั้งต้น:</span>
                                    <span class="fw-semibold text-dark">${allocatedAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>จัดสรรแล้ว:</span>
                                    <span class="fw-semibold text-secondary">${totalAllocated.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small fw-bold">คงเหลือ:</span>
                                <span class="fs-5 fw-bold ${textClass}">${balance.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        $('#budget-summary-bar').html(summaryHtml);
    }

    function loadSubActivities(activityId) {
    if (!activityId) return;

    $.get(`/plan/sub-activities/list/${activityId}`, function(data) {
        console.log("ข้อมูลกิจกรรมย่อยที่ได้รับ:", data);

        let container = $(`#sub-activity-container-${activityId}`);
        
        if (data.length === 0) {
            container.html('<div class="text-center py-3 text-muted">ยังไม่มีกิจกรรมย่อย</div>');
            return;
        }

        let html = `<table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>ชื่อกิจกรรมย่อย</th>
                                <th>งบที่ได้รับ</th>
                                <th>เบิกจ่ายแล้ว</th>
                                <th>คงเหลือ</th>
                                <th>ประวัติการเบิกจ่าย</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>`;
                        
        data.forEach(sub => {
            let amount = parseFloat(sub.total_allocated || 0);
            let totalPaid = parseFloat(sub.total_paid || 0);
            let balance = amount - totalPaid;
            
            // --- แก้ไขจุดที่ 1: สร้างตัวแปร paymentList ให้ถูกต้อง ---
            let paymentList = '';
            if (sub.budgets && sub.budgets.length > 0) {
                sub.budgets.forEach(b => {
                    if (b.payments && b.payments.length > 0) {
                        b.payments.forEach(p => {
                            paymentList += `<div class="small border-bottom py-1">
                                ${p.payment_date}: ${parseFloat(p.amount).toLocaleString(undefined, {minimumFractionDigits: 2})}
                            </div>`;
                        });
                    }
                });
            }

            // --- แก้ไขจุดที่ 2: ใช้ class ในการดักจับ (Event Delegation) แทน onclick ---
            let actionButtons = '';
            if (totalPaid > 0) {
                actionButtons = `
                    <button class="btn btn-xs btn-outline-danger" 
                            onclick="cancelPayment(${sub.id}, ${sub.activity_id})"> <i class="fa-solid fa-xmark"></i> ยกเลิก
                    </button>
                `;
            } else {
                actionButtons = `<button class="btn btn-xs btn-info btn-payment" data-id="${sub.budgets[0]?.id || 0}">
                                    <i class="fa-solid fa-money-bill"></i> เบิกจ่าย
                                </button>`;
            }

                actionButtons += ` 
                    <button class="btn btn-xs btn-warning btn-edit-sub" 
                            data-id="${sub.id}" 
                            data-activity-id="${sub.activity_id}">
                        <i class="fa-solid fa-pencil"></i>
                    </button>
                    <button class="btn btn-xs btn-danger btn-delete-sub" 
                            data-id="${sub.id}" 
                            data-activity-id="${sub.activity_id}">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                `;

            html += `<tr>
                        <td>${sub.name}</td>
                        <td>${amount.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                        <td class="text-danger fw-bold">${totalPaid.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                        <td class="fw-bold ${balance < 0 ? 'text-danger' : 'text-success'}">${balance.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                        <td>${paymentList || '<span class="text-muted small">ยังไม่มีรายการ</span>'}</td>
                        <td><div class="btn-group">${actionButtons}</div></td>
                    </tr>`;
        });
        
        html += `</tbody></table>`;
        container.html(html);
        $(`#sub-activity-row-${activityId}`).slideDown(200);
    }).fail(function(xhr) {
        console.error("โหลดรายการกิจกรรมย่อยล้มเหลว:", xhr.responseText);
    });
}
    function openSubActivityModal(activityId) {
        // 1. เรียก AJAX ไปที่ Controller เพื่อดึงข้อมูลประกอบ (เช่น ยอดงบที่เหลือ)
        $.get(`/plan/sub-activities/form/${activityId}`, function(html) {
           $('#sub-modal-content').html(html);
            $('#subActivityModal').modal('show');
        });
    }

   $(document).off('click', '#btnSaveSubActivity').on('click', '#btnSaveSubActivity', function() {
        let $form = $('#subActivityForm');
        
        // แปลง form data เป็น Object JSON
        let formData = {};
        $form.serializeArray().forEach(item => {
            formData[item.name] = item.value;
        });
        let activityId = $form.find('input[name="activity_id"]').val();
        $.ajax({
            url:  "/api/sub-activities/store/"+activityId, // เรียกไปที่ URL ใหม่ที่สร้างไว้
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData), // ส่งเป็น JSON
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                Swal.fire({ icon: 'success', title: 'บันทึกสำเร็จ' });
                $('#subActivityModal').modal('hide');
                loadSubActivities(formData.activity_id);
                window.location.reload();
            },
            error: function(xhr) {
                console.log("Error status:", xhr.status); // ดูสถานะ
                console.log("Error response:", xhr.responseText); // ดูสาเหตุจริง
                Swal.fire({ icon: 'error', title: 'Error: ' + xhr.status });
            }
        });
    });

    function openPaymentModal(subActivityBudgetId) {
        $.get(`/plan/payments/form/${subActivityBudgetId}`, function(data) {
            // ใน view นี้ ให้ส่งยอดคงเหลือ (Available Balance) มาด้วย
            $('#payment-modal-content').html(data);
            $('#paymentModal').modal('show');
        });
    }

    $(document).off('click', '#btnSavePayment').on('click', '#btnSavePayment', function() {
        let $form = $('#paymentForm');
        
        // ตรวจสอบความถูกต้องของข้อมูลเบื้องต้น
        if (!$form.get(0).checkValidity()) {
            $form.get(0).reportValidity();
            return;
        }

        // แปลง form data เป็น Object JSON
        let formData = {};
        $form.serializeArray().forEach(item => {
            formData[item.name] = item.value;
        });

        // ดึง ID ของงบประมาณกิจกรรมย่อยจาก input hidden
        let budgetId = $form.find('input[name="sub_activity_budget_id"]').val();

        $.ajax({
            url: "/plan/sub-activities/payments/" + budgetId,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                Swal.fire({ 
                    icon: 'success', 
                    title: 'บันทึกรายการเบิกจ่ายแล้ว', 
                    timer: 1200 
                });
                
                // ปิด Modal
                $('#paymentModal').modal('hide');
                
                // อัปเดตข้อมูลในหน้าจอ (คุณอาจต้องเรียกฟังก์ชันโหลดรายการกิจกรรมย่อยใหม่)
                // ตัวอย่างเช่น: loadSubActivities(activityId);
                window.location.reload();
                // ถ้าไม่อยาก reload หน้าจอ ให้ใช้วิธีเรียกอัปเดตสรุปงบประมาณแทน
                if (typeof updateBudgetSummary === 'function') {
                    updateBudgetSummary();
                }
            },
            error: function(xhr) {
                let errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'เกิดข้อผิดพลาดในการบันทึก';
                console.error("Error status:", xhr.status);
                console.error("Error response:", xhr.responseText);
                Swal.fire({ 
                    icon: 'error', 
                    title: 'ไม่สามารถเบิกจ่ายได้', 
                    text: errorMsg 
                });
            }
        });
    });
    
    // เรียกทำงานทันทีเมื่อ DOM พร้อมใช้งาน
    $(document).ready(function() {
        updateBudgetSummary();
        $('button[onclick^="toggleSubActivities"]').each(function() {
            // ดึงค่า activityId ออกมาจากฟังก์ชันที่เขียนไว้ใน onclick
            let onClickAttr = $(this).attr('onclick');
            let activityId = onClickAttr.match(/\d+/)[0]; // ดึงเลข ID ออกมา
            
            if (activityId) {
                // เรียกฟังก์ชันโหลดข้อมูลทันที
                loadSubActivities(activityId);
            }
        });
    });

    function deleteActivity(id) {
    Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: "คุณจะไม่สามารถกู้คืนข้อมูลกิจกรรมนี้ได้!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ใช่, ลบเลย!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `{{ url('plan/projects/activities') }}/${id}`, // ปรับ URL ให้ตรงกับ Route ลบของคุณ
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    Swal.fire('ลบสำเร็จ!', res.message, 'success');
                    // โหลดหน้าใหม่ หรือลบแถวออกจากตารางโดยไม่ต้องโหลดหน้า
                    $(`tr[data-id="${id}"]`).remove();
                    $(`#sub-activity-row-${id}`).remove();
                    updateBudgetSummary(); // อัปเดตตัวเลขสรุปงบประมาณใหม่
                },
                error: function(err) {
                    Swal.fire('เกิดข้อผิดพลาด!', 'ไม่สามารถลบข้อมูลได้', 'error');
                }
            });
        }
    });
}
function cancelPayment(subActivityId, activityId) {
    // เช็คว่า activityId มาจริงไหม ถ้าไม่มีให้ลองหาจาก DOM หรือตัวแปร global
    console.log("Cancelling Payment for SubID:", subActivityId, "ActivityID:", activityId);

    Swal.fire({
        title: 'ยืนยันการยกเลิกการเบิกจ่าย?',
        text: "รายการเบิกจ่ายทั้งหมดของกิจกรรมย่อยนี้จะถูกลบ!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonText: 'ไม่',
        confirmButtonText: 'ใช่, ยกเลิกเลย'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(`/plan/sub-activities/cancel-payment/${subActivityId}`, {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                Swal.fire({
                    title: 'ยกเลิกสำเร็จ!',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
                
                // แก้ไขจุดนี้: ถ้า activityId ที่ส่งมาว่าง ให้พยายามใช้ตัวแปรจากหน้าเว็บ
                // หรือระบุตัวเลข ID กิจกรรมหลักที่คุณกำลังจัดการอยู่
                if (typeof activityId === 'undefined' || !activityId) {
                    console.warn("Activity ID ไม่ถูกส่งมา, ลองโหลดหน้าใหม่เพื่อความชัวร์");
                    window.location.reload(); 
                } else {
                    loadSubActivities(activityId);
                }
                
            }).fail(function(xhr) {
                console.error("Error Detail:", xhr.responseText);
                Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถยกเลิกการเบิกจ่ายได้', 'error');
            });
        }
    });
            // ดักจับปุ่มแก้ไข
        $(document).on('click', '.btn-edit-sub', function() {
            let subId = $(this).data('id');
            editSubActivity(subId);
        });

        // ดักจับปุ่มลบ
        $(document).on('click', '.btn-delete-sub', function() {
            let subId = $(this).data('id');
            let actId = $(this).data('activity-id');
            deleteSubActivity(subId, actId);
        });
        
}
</script>
<script>
    // ดักจับการคลิกทุกอย่างที่เป็น class เหล่านี้ (ใช้ได้แม้ปุ่มถูกโหลดมาใหม่)
    $(document).on('click', '.btn-payment', function() { openPaymentModal($(this).data('id')); });
    $(document).on('click', '.btn-cancel-payment', function() { cancelPayment($(this).data('id')); });
    $(document).on('click', '.btn-edit-sub', function() { editSubActivity($(this).data('id')); });
    $(document).on('click', '.btn-delete-sub', function() { deleteSubActivity($(this).data('id')); });
</script>
@endpush