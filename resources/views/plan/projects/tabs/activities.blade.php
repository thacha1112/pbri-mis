<button type="button" class="btn btn-primary mb-3 shadow-sm" id="btn-add-activity" onclick="openActivityModal()">
    <i class="fa-solid fa-plus me-1"></i> เพิ่มกิจกรรม
</button>

<!-- ส่วนสรุปงบประมาณรายแหล่งเงินในโครงการ (ปรับดีไซน์ใหม่ให้สวยงาม) -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-header bg-light py-3 px-4 border-bottom-0">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-wallet text-primary me-2 fs-5"></i> 
            <strong class="fs-6 text-dark">สรุปงบประมาณรายแหล่งเงินในโครงการ</strong>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="budget-summary-table">
                <thead class="table-light text-center text-uppercase fs-7 text-secondary fw-bold">
                    <tr>
                        <th class="py-3" width="10%">ปีแหล่งเงิน</th>
                        <th class="py-3 text-start">แหล่งเงิน / แผนงาน / หมวดงบ</th>
                        <th class="py-3 text-end" width="15%">จัดสรรลงโครงการ (บาท)</th>
                        <th class="py-3 text-end" width="15%">จัดสรรลงกิจกรรมแล้ว (บาท)</th>
                        <th class="py-3 text-end" width="15%">คงเหลือ (บาท)</th>
                    </tr>
                </thead>
                <tbody id="budget-summary-bar">
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fa-solid fa-spinner fa-spin me-2 text-primary"></i>กำลังโหลดข้อมูลสรุปงบประมาณ...
                        </td>
                    </tr>
                </tbody>
                <!-- ส่วนแสดงยอดรวมท้ายตาราง -->
                <tfoot class="table-light fw-bold text-end" id="budget-summary-footer" style="display: none;">
                    <tr>
                        <td colspan="2" class="text-end text-dark py-3">รวมทั้งสิ้น:</td>
                        <td id="total-initial-budget" class="text-dark py-3">0.00</td>
                        <td id="total-consumed-budget" class="text-secondary py-3">0.00</td>
                        <td id="total-balance-budget" class="py-3">0.00</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div class="table-responsive shadow-sm rounded-3">
    <table class="table table-hover table-bordered align-middle bg-white mb-0" id="activity-table">
        <thead class="table-light text-center">
            <tr>
                <th width="5%">ลำดับ</th>
                <th>ชื่อกิจกรรม</th>
                <th width="20%">ระยะเวลาดำเนินการ</th>
                <th width="20%">งปม.ที่จัดสรรลงกิจกรรม</th>
                <th width="15%">จัดการ</th>
            </tr>
        </thead>
        <tbody id="activity-body">
            @forelse($project->activities as $index => $activity)
                @php
                    // ยอดเงินที่กิจกรรมนี้ได้รับจัดสรร
                    $activityTotal = $activity->budgets->sum('amount');
                    $hasBudget = $activityTotal > 0;
                @endphp

                <tr data-id="{{ $activity->id }}">
                    <td class="text-center text-muted">{{ $index + 1 }}</td>
                    <td class="fw-semibold">{{ $activity->name }}</td>
                    <td class="text-center small text-muted">
                        {{ \Carbon\Carbon::parse($activity->start_date)->addYears(543)->format('d/m/Y') }} - 
                        {{ \Carbon\Carbon::parse($activity->end_date)->addYears(543)->format('d/m/Y') }}
                    </td>
                    <td class="fw-bold text-end {{ $hasBudget ? 'text-primary' : 'text-muted' }}">
                        @if($hasBudget)
                            {{ number_format($activityTotal, 2) }}
                        @else
                            <span class="badge bg-light text-muted border fw-normal">ยังไม่ได้จัดสรร</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group shadow-sm">
                            <button type="button" class="btn btn-sm btn-warning text-dark" onclick="openActivityModal({{ $activity->id }})" title="แก้ไข">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteActivity({{ $activity->id }})" title="ลบ">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4 fst-italic">
                        <i class="fa-solid fa-inbox fs-4 mb-2 d-block text-black-50"></i>ยังไม่มีกิจกรรมในโครงการนี้
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal สำหรับเพิ่ม/แก้ไข กิจกรรม -->
<div class="modal fade" id="activityModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="activityForm">
            @csrf
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-layer-group me-2"></i> จัดการข้อมูลกิจกรรม</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="modal-content">
                    <!-- Form เนื้อหาจะถูกโหลดมาใส่ที่นี่ -->
                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> บันทึกข้อมูล</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    //var currentProjectId = {{ $project->id }};

    function openActivityModal(id = null) {
        let url = id ? `{{ url('plan/projects/activity-form') }}/${id}` : `{{ url('plan/projects/activity-form') }}`;
        $.get(url, { project_id: currentProjectId }, function(html) {
            $('#modal-content').html(html);
            var modalEl = document.getElementById('activityModal');
            var myModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl, { backdrop: 'static' });
            myModal.show();
        });
    }

    $('#activityForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: `{{ url('plan/projects') }}/${currentProjectId}/update-activities`,
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                const modalEl = document.getElementById('activityModal');
                const modalInstance = bootstrap.Modal.getInstance(modalEl); 
                if (modalInstance) modalInstance.hide();
                Swal.fire({ icon: 'success', title: 'บันทึกสำเร็จ', timer: 1000, showConfirmButton: false }).then(() => { window.location.reload(); });
            },
            error: function(xhr) { Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error'); }
        });
    });

    function updateBudgetSummary() {
        if (!currentProjectId) return;
        $('#budget-summary-bar').html('<tr><td colspan="5" class="text-center text-muted py-4"><i class="fa-solid fa-spinner fa-spin me-2 text-primary"></i>กำลังคำนวณยอดงบประมาณ...</td></tr>');
        $('#budget-summary-footer').hide();
        
        $.get(`/plan/projects/${currentProjectId}/budget-summary`, function(data) {
            let summaryHtml = '';
            
            if (!data || data.length === 0) {
                $('#budget-summary-bar').html('<tr><td colspan="5" class="text-center text-muted py-4 small">ยังไม่มีการกำหนดแหล่งงบประมาณในขั้นตอนก่อนหน้า</td></tr>');
                $('#btn-add-activity').prop('disabled', true).attr('title', 'กรุณากำหนดงบประมาณโครงการก่อน');
                return;
            }

            let sumInitial = 0;
            let sumConsumed = 0;
            let sumBalance = 0;

            data.forEach(b => {
                sumInitial += parseFloat(b.initial_budget || 0);
                sumConsumed += parseFloat(b.consumed || 0);
                sumBalance += parseFloat(b.balance || 0);

                summaryHtml += `
                    <tr>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 fw-semibold">
                                ${b.fiscal_year}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">${b.source_name}</div>
                            <small class="text-muted">
                                <i class="fa-solid fa-folder-open me-1 text-black-50"></i>${b.program_name} / 
                                <i class="fa-solid fa-layer-group me-1 text-black-50"></i>${b.category_name}
                            </small>
                        </td>
                        <td class="text-end fw-semibold text-dark">
                            ${b.initial_budget.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                        </td>
                        <td class="text-end fw-semibold text-secondary">
                            ${b.consumed.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                        </td>
                        <td class="text-end fw-bold ${b.text_class}">
                            ${b.balance.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                        </td>
                    </tr>
                `;
            });
            
            $('#budget-summary-bar').html(summaryHtml);

            // แสดงผลรวมลงใน Footer
            $('#total-initial-budget').text(sumInitial.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#total-consumed-budget').text(sumConsumed.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            
            let $balanceCell = $('#total-balance-budget');
            $balanceCell.text(sumBalance.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $balanceCell.removeClass('text-success text-danger text-primary');
            
            if (sumBalance < 0) {
                $balanceCell.addClass('text-danger');
            } else {
                $balanceCell.addClass('text-success');
            }

            $('#budget-summary-footer').show();

            // เงื่อนไขควบคุมปุ่มเพิ่มกิจกรรม: ถ้างบประมาณคงเหลือรวม <= 0 ให้ปิดปุ่ม
            let $btnAdd = $('#btn-add-activity');
            if (sumBalance <= 0) {
                $btnAdd.prop('disabled', true);
                $btnAdd.addClass('disabled');
                $btnAdd.attr('title', 'งบประมาณถูกจัดสรรครบแล้ว ไม่สามารถเพิ่มกิจกรรมได้อีก');
            } else {
                $btnAdd.prop('disabled', false);
                $btnAdd.removeClass('disabled');
                $btnAdd.removeAttr('title');
            }

        }).fail(function() {
            $('#budget-summary-bar').html('<tr><td colspan="5" class="text-center text-danger py-4 small">เกิดข้อผิดพลาดในการโหลดข้อมูลสรุปงบประมาณ</td></tr>');
        });
    }

    function deleteActivity(id) {
        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: "ข้อมูลกิจกรรมและการจัดสรรงบจะถูกลบทั้งหมด!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ลบเลย',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('plan/projects/activities') }}/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire({icon: 'success', title: 'ลบสำเร็จ', timer: 1000, showConfirmButton: false});
                        $(`tr[data-id="${id}"]`).fadeOut(300, function() { $(this).remove(); });
                        updateBudgetSummary();
                    },
                    error: function(xhr) { Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถลบข้อมูลได้', 'error'); }
                });
            }
        });
    }

    function formatBudgetInput(input) {
        let rawValue = input.value.replace(/,/g, '');
        if (isNaN(rawValue) || rawValue === "") { $(input).parent().find('.budget-hidden').val(0); return; }
        $(input).parent().find('.budget-hidden').val(rawValue);
        let parts = rawValue.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        input.value = parts.join('.');
    }

    $(document).ready(function() {
        updateBudgetSummary();
    });
</script>
@endpush