@extends('layouts.app')

@section('content')
<!-- ส่วนค้นหาและกรองข้อมูลหน้าจอหลัก (Main Filter Section) -->
<div class="card border-0 shadow-sm bg-white mb-4">
    <div class="card-body p-3">
        <div class="row align-items-center">
            <div class="col-md-4">
                <label class="form-label fw-bold text-secondary"><i class="fa-solid fa-filter me-1"></i> กรองตามปีงบประมาณ</label>
                <select class="form-select select2-filter" id="filter_fiscal_year">
                    <option value="all">-- แสดงทุกปีงบประมาณ --</option>
                    @foreach($fiscalYears ?? App\Models\Common\FiscalYear::where('status', 'active')->orderBy('year', 'desc')->get() as $y)
                    <option value="{{ $y->id }}">ปี พ.ศ. {{ $y->year }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm bg-white">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
        <h5 class="m-0 fw-bold text-dark"><i class="fa-solid fa-chess-knight text-warning me-2"></i>ชั้นที่ 4: จัดการข้อมูลกลยุทธ์ (Strategies)</h5>
        <button class="btn btn-primary btn-sm fw-bold" onclick="openAddModal()"><i class="fa-solid fa-plus me-1"></i> เพิ่มข้อมูลกลยุทธ์</button>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle" id="strategyTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 10%;">ลำดับ</th>
                        <th style="width: 15%;">รหัสกลยุทธ์</th>
                        <th style="width: 55%;">ข้อความรายละเอียดกลยุทธ์</th>
                        <th style="width: 20%; text-align: center;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($strategies as $index => $item)
                    <!-- ดึงข้อมูลเชื่อมโยงหากันเพื่อหาไอดีปีงบประมาณเก็บไว้ที่ tr สำหรับทำฟิลเตอร์หน้าแรก -->
                    <tr id="row-{{ $item->id }}" class="strategy-row" data-year-id="{{ $item->goal->strategicIssue->mission->fiscal_year_id }}">
                        <td class="row-index">{{ $index + 1 }}</td>
                        <td><span class="badge bg-warning text-dark px-2 py-2">{{ $item->code ?? 'S' }}</span></td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->name }}</div>
                            <small class="text-secondary">
                                ภายใต้เป้าประสงค์: {{ Str::limit($item->goal->name, 60) }}
                                <br>(ประเด็นยุทธศาสตร์: {{ $item->goal->strategicIssue->code }} | ปีงบประมาณ พ.ศ. {{ $item->goal->strategicIssue->mission->fiscalYear->year }})
                            </small>
                        </td>
                        <td class="text-center">
                            <!-- บีบสับส่งข้อมูลไอดีความสัมพันธ์ข้ามตารางเพื่อป้อนเข้าสู่ระบบแก้ไขฟอร์ม -->
                            <button class="btn btn-warning btn-sm me-1" onclick="openEditModal({{ json_encode($item) }}, {{ $item->goal->strategicIssue->mission->fiscal_year_id }}, {{ $item->goal->strategic_issue_id }})">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteItem({{ $item->id }})"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="mainModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="modalTitle">ฟอร์มข้อมูลกลยุทธ์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="mainForm">
                @csrf
                <input type="hidden" id="item_id" name="id">
                <div class="modal-body p-4">

                    <!-- ชั้นที่ 1: เลือกปีงบประมาณ -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">ปีงบประมาณ <span class="text-danger">*</span></label>
                        <select class="form-select select2-in-modal" id="modal_fiscal_year_id" required>
                            <option value="">-- กรุณาเลือกปีงบประมาณก่อน --</option>
                            @foreach($fiscalYears ?? App\Models\Common\FiscalYear::where('status', 'active')->orderBy('year', 'desc')->get() as $y)
                            <option value="{{ $y->id }}">ปี พ.ศ. {{ $y->year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- ชั้นที่ 2: เลือกประเด็นยุทธศาสตร์ (กรองตามปีงบประมาณ) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">เลือกประเด็นยุทธศาสตร์ <span class="text-danger">*</span></label>
                        <select class="form-select select2-in-modal" id="modal_strategic_issue_id" required>
                            <option value="">-- กรุณาเลือกปีงบประมาณด้านบน --</option>
                            @foreach(App\Models\Plan\StrategicIssue::with('mission')->where('status', 'active')->get() as $i)
                            <option value="{{ $i->id }}" data-year-id="{{ $i->mission->fiscal_year_id }}">{{ $i->code }}: {{ Str::limit($i->name, 80) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- ชั้นที่ 3: เลือกเป้าประสงค์แม่ (กรองตามประเด็นยุทธศาสตร์) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">เลือกเป้าประสงค์แม่ <span class="text-danger">*</span></label>
                        <select class="form-select select2-in-modal" id="goal_id" name="goal_id" required>
                            <option value="">-- กรุณาเลือกประเด็นยุทธศาสตร์ด้านบน --</option>
                            @foreach($goals as $g)
                            <option value="{{ $g->id }}" data-issue-id="{{ $g->strategic_issue_id }}">
                                {{ $g->code ? $g->code . ': ' : '' }}{{ $g->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">รหัสกำกับกลยุทธ์</label>
                        <input type="text" class="form-control" id="code" name="code" placeholder="เช่น S1, S2">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">รายละเอียดกลยุทธ์องค์กร <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="name" name="name" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let isEditMode = false;

    $(document).ready(function() {
        // ตัวกรองหลักนอกตาราง
        $('.select2-filter').select2({
            theme: 'bootstrap-5'
        });

        // เปิดใช้งาน Select2 สำหรับกล่องใน Modal ตอนเริ่มต้น
        $('.select2-in-modal').select2({
            dropdownParent: $('#mainModal'),
            theme: 'bootstrap-5',
            width: '100%'
        });

        // 🔥 [เคล็ดลับสำคัญ] ก๊อปปี้เก็บตัวเลือกเป้าประสงค์ดั้งเดิมทั้งหมดจาก HTML เอาไว้ในหน่วยความจำตั้งแต่โหลดหน้าจอเสร็จ
        const allGoalOptions = $('#goal_id').find('option').clone();

        // ================= ฟังก์ชันกรองข้อมูลหน้าตารางหลัก =================
        $('#filter_fiscal_year').on('change', function() {
            let selectedYearId = $(this).val();
            let visibleIndex = 1;
            if (selectedYearId === 'all') {
                $('.strategy-row').show();
                $('.strategy-row').each(function() {
                    $(this).find('.row-index').text(visibleIndex++);
                });
            } else {
                $('.strategy-row').hide();
                $(`.strategy-row[data-year-id="${selectedYearId}"]`).show().each(function() {
                    $(this).find('.row-index').text(visibleIndex++);
                });
            }
        });

        // ================= Cascading ชั้นที่ 1: เลือกปีงบ -> กรองประเด็นยุทธศาสตร์ =================
        $('#modal_fiscal_year_id').on('change', function() {
            let selectedYearId = $(this).val();
            let issueSelect = $('#modal_strategic_issue_id');
            let goalSelect = $('#goal_id');

            issueSelect.val('');
            goalSelect.val('');
            goalSelect.prop('disabled', true);

            if (selectedYearId === '') {
                issueSelect.find('option').show();
                issueSelect.find('option[value=""]').text('-- กรุณาเลือกปีงบประมาณด้านบน --');
                issueSelect.prop('disabled', true);
            } else {
                issueSelect.find('option').hide();
                issueSelect.find('option[value=""]').show();

                let matchingIssues = issueSelect.find(`option[data-year-id="${selectedYearId}"]`);
                if (matchingIssues.length > 0) {
                    matchingIssues.show();
                    issueSelect.find('option[value=""]').text('-- กรุณาเลือกประเด็นยุทธศาสตร์ --');
                    issueSelect.prop('disabled', false);
                } else {
                    issueSelect.find('option[value=""]').text('-- ไม่พบข้อมูลประเด็นยุทธศาสตร์ในปีนี้ --');
                    issueSelect.prop('disabled', true);
                }
            }
            refreshSelect2(issueSelect);
            refreshSelect2(goalSelect);

            // สั่งล้างเป้าประสงค์ตามลงไปด้วย
            issueSelect.trigger('change');
        });

        // ================= 🔥 Cascading ชั้นที่ 2: เปลี่ยนวิธีลบ-เคลียร์ก้อน HTML ใหม่สด ๆ =================
        $('#modal_strategic_issue_id').on('change', function() {
            let selectedIssueId = $(this).val();
            let goalSelect = $('#goal_id');

            // 1. ล้างลบเนื้อหาแท็ก option ในกล่องออกให้หมดเกลี้ยงก่อนเลย
            goalSelect.empty();

            if (selectedIssueId === '' || selectedIssueId === null) {
                let defaultText = $('#modal_fiscal_year_id').val() === '' ? '-- กรุณาเลือกปีงบประมาณด้านบน --' : '-- กรุณาเลือกประเด็นยุทธศาสตร์ด้านบน --';
                goalSelect.append(`<option value="">${defaultText}</option>`);
                goalSelect.prop('disabled', true);
            } else {
                // 2. ดึงโครงสร้างดั้งเดิมที่เราโคลนเก็บไว้ในหน่วยความจำออกมาสแกนหาตัวที่ match
                let matchingOptions = allGoalOptions.filter(function() {
                    let issueId = $(this).data('issue-id');
                    return $(this).val() === "" || (issueId != undefined && issueId.toString() === selectedIssueId.toString());
                });

                if (matchingOptions.length > 1) { // มีมากกว่า 1 แปลว่ามีข้อมูลนอกจากค่าเริ่มต้น
                    // 3. ยัดก้อน option ที่ผ่านการกรองลงไปในตู้แช่ HTML จริง
                    goalSelect.append(matchingOptions.clone());
                    goalSelect.find('option[value=""]').text('-- กรุณาเลือกเป้าประสงค์แม่ --');
                    goalSelect.prop('disabled', false);
                } else {
                    goalSelect.append('<option value="">-- ไม่พบข้อมูลเป้าประสงค์ภายใต้ยุทธศาสตร์นี้ --</option>');
                    goalSelect.prop('disabled', true);
                }
            }

            // 4. บังคับให้ Select2 เกิดใหม่เพื่ออัปเดตหน้าจอ UI ให้ตรงกับ HTML จริงที่เราเพิ่งเขียนเข้าไป
            refreshSelect2(goalSelect);
        });
    });

    function refreshSelect2(element) {
        if (element.data('select2')) {
            element.select2('destroy');
        }
        element.select2({
            dropdownParent: $('#mainModal'),
            theme: 'bootstrap-5',
            width: '100%'
        });
    }

    function initModalSelect2() {
        $('.select2-in-modal').select2({
            dropdownParent: $('#mainModal'),
            theme: 'bootstrap-5',
            width: '100%'
        });
    }

    function openAddModal() {
        isEditMode = false;
        $('#modalTitle').text('เพิ่มกลยุทธ์องค์กรใหม่');
        $('#mainForm')[0].reset();
        $('#item_id').val('');

        // เซ็ตตัวแปรล็อกความปลอดภัยป้องกันส่งค่าว่างตั้งแต่เริ่มเปิดหน้าต่าง
        $('#modal_fiscal_year_id').val('').trigger('change');
        $('#mainModal').modal('show');
    }

    function openEditModal(data, fiscalYearId, strategicIssueId) {
        isEditMode = true;
        $('#modalTitle').text('แก้ไขข้อมูลกลยุทธ์');
        $('#item_id').val(data.id);

        // จุดระเบิดคำสั่งเปลี่ยนค่าเรียงลำดับชั้นทีละเสี้ยววินาทีเพื่อให้การตั้งค่าดักกรองปลดล็อกทำงานเรียงกันสมบูรณ์
        $('#modal_fiscal_year_id').val(fiscalYearId).trigger('change');

        setTimeout(function() {
            $('#modal_strategic_issue_id').val(strategicIssueId).trigger('change');
            setTimeout(function() {
                $('#goal_id').val(data.goal_id).trigger('change');
            }, 150);
        }, 150);

        $('#code').val(data.code);
        $('#name').val(data.name);
        $('#mainModal').modal('show');
    }

    $('#mainForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#item_id').val();
        let url = isEditMode ? `{{ url('plan/strategies') }}/${id}` : `{{ url('plan/strategies') }}`;
        let formData = $(this).serialize();
        if (isEditMode) formData += '&_method=PUT';

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(res) {
                $('#mainModal').modal('hide');
                Swal.fire({
                        icon: 'success',
                        title: res.message,
                        showConfirmButton: false,
                        timer: 1300
                    })
                    .then(() => location.reload());
            }
        });
    });

    function deleteItem(id) {
        Swal.fire({
            title: 'ยืนยันการลบข้อมูล?',
            text: "การลบข้อมูลกลยุทธ์อาจส่งผลกระทบต่อแผนผูกโครงการต้นสังกัดได้",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'ใช่, ฉันต้องการลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('plan/strategies') }}/${id}`,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: res.message,
                            showConfirmButton: false,
                            timer: 1300
                        });
                        $(`#row-${id}`).fadeOut(400, function() {
                            $(this).remove();
                            $('#filter_fiscal_year').trigger('change');
                        });
                    }
                });
            }
        });
    }
</script>
@endpush