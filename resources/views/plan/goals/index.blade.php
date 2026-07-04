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
        <h5 class="m-0 fw-bold text-dark"><i class="fa-solid fa-crosshairs text-success me-2"></i>ชั้นที่ 3: จัดการเป้าประสงค์ (Goals)</h5>
        <button class="btn btn-primary btn-sm fw-bold" onclick="openAddModal()"><i class="fa-solid fa-plus me-1"></i> เพิ่มเป้าประสงค์</button>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle" id="goalTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 10%;">ลำดับ</th>
                        <th style="width: 15%;">รหัสเป้าประสงค์</th>
                        <th style="width: 55%;">ข้อความเป้าประสงค์องค์กร</th>
                        <th style="width: 20%; text-align: center;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($goals as $index => $item)
                    <!-- เก็บ data-year-id เพื่อใช้กรองหน้าตารางหลัก -->
                    <tr id="row-{{ $item->id }}" class="goal-row" data-year-id="{{ $item->strategicIssue->mission->fiscal_year_id }}">
                        <td class="row-index">{{ $index + 1 }}</td>
                        <td><span class="badge bg-success px-2 py-2">{{ $item->code ?? 'G' }}</span></td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->name }}</div>
                            <small class="text-secondary">ผูกกับยุทธศาสตร์: {{ Str::limit($item->strategicIssue->name, 70) }} (ปี {{ $item->strategicIssue->mission->fiscalYear->year }})</small>
                        </td>
                        <td class="text-center">
                            <!-- ส่งข้อมูลปีงบประมาณของยุทธศาสตร์นั้นเข้าไปในโหมดแก้ไขด้วย -->
                            <button class="btn btn-warning btn-sm me-1" onclick="openEditModal({{ json_encode($item) }}, {{ $item->strategicIssue->mission->fiscal_year_id }})"><i class="fa-solid fa-pen-to-square"></i></button>
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
                <h5 class="modal-title fw-bold" id="modalTitle">ฟอร์มเป้าประสงค์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="mainForm">
                @csrf
                <input type="hidden" id="item_id" name="id">
                <div class="modal-body p-4">

                    <!-- ส่วนเลือกระดับปีงบประมาณในฟอร์ม -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">ปีงบประมาณ <span class="text-danger">*</span></label>
                        <select class="form-select select2-in-modal" id="modal_fiscal_year_id" required>
                            <option value="">-- กรุณาเลือกปีงบประมาณก่อน --</option>
                            @foreach($fiscalYears ?? App\Models\Common\FiscalYear::where('status', 'active')->orderBy('year', 'desc')->get() as $y)
                            <option value="{{ $y->id }}">ปี พ.ศ. {{ $y->year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">เลือกประเด็นยุทธศาสตร์แม่ <span class="text-danger">*</span></label>
                        <select class="form-select select2-in-modal" id="strategic_issue_id" name="strategic_issue_id" required>
                            <option value="">-- กรุณาเลือกปีงบประมาณด้านบน --</option>
                            @foreach($issues as $i)
                            <!-- ซ่อนค่าผูกมิติ data-year-id เพื่อตรวจสอบเงื่อนไขความสัมพันธ์ชั้นสอง -->
                            <option value="{{ $i->id }}" data-year-id="{{ $i->mission->fiscal_year_id }}">[ปี {{ $i->mission->fiscalYear->year }}] {{ $i->code }}: {{ Str::limit($i->name, 80) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">รหัสเป้าประสงค์</label>
                        <input type="text" class="form-control" id="code" name="code" placeholder="เช่น G1, G2">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">ข้อความรายละเอียดเป้าประสงค์ <span class="text-danger">*</span></label>
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
        // เปิดใช้งานตัวกรองนอกตารางหลัก
        $('.select2-filter').select2({
            theme: 'bootstrap-5'
        });

        // เปิดใช้งาน Select2 ภายใน Modal ตั้งต้น
        $('.select2-in-modal').select2({
            dropdownParent: $('#mainModal'),
            theme: 'bootstrap-5'
        });

        // ================= ฟังก์ชันกรองข้อมูลหน้าตารางหลัก =================
        $('#filter_fiscal_year').on('change', function() {
            let selectedYearId = $(this).val();
            let visibleIndex = 1;

            if (selectedYearId === 'all') {
                $('.goal-row').show();
                $('.goal-row').each(function() {
                    $(this).find('.row-index').text(visibleIndex++);
                });
            } else {
                $('.goal-row').hide();
                $(`.goal-row[data-year-id="${selectedYearId}"]`).show().each(function() {
                    $(this).find('.row-index').text(visibleIndex++);
                });
            }
        });

        // ================= ฟังก์ชันทำ Cascading Dropdown ใน Modal + บล็อกป้อนค่าว่าง =================
        $('#modal_fiscal_year_id').on('change', function() {
            let selectedYearId = $(this).val();
            let issueSelect = $('#strategic_issue_id');

            // ล้างค่าเดิมในระบบความจำออก
            issueSelect.val('');

            if (selectedYearId === '') {
                issueSelect.find('option').show();
                issueSelect.find('option[value=""]').text('-- กรุณาเลือกปีงบประมาณด้านบน --');
                issueSelect.prop('disabled', true); // ล็อกกล่องไว้ถ้ายั้งไม่เลือกปีงบประมาณหลัก
            } else {
                // ซ่อนตัวเลือกยุทธศาสตร์ทั้งหมดก่อน
                issueSelect.find('option').hide();
                // เปิดให้แสดงตัวเลือกค่าว่างลำดับแรกสุดไว้เสมอ
                issueSelect.find('option[value=""]').show();

                // ค้นหาประเด็นยุทธศาสตร์ที่มีสัญชาติปีตรงกันกับที่ผู้ใช้เลือกด้านบน
                let matchingOptions = issueSelect.find(`option[data-year-id="${selectedYearId}"]`);

                if (matchingOptions.length > 0) {
                    matchingOptions.show();
                    issueSelect.find('option[value=""]').text('-- กรุณาเลือกประเด็นยุทธศาสตร์แม่ --');
                    issueSelect.prop('disabled', false); // ปลดล็อกให้เจ้าหน้าที่กรอกงานได้ทันที 🔓
                } else {
                    // หากยังไม่ได้วางโครงสร้างประเด็นยุทธศาสตร์สำหรับปีนี้ไว้เลย
                    issueSelect.find('option[value=""]').text('-- ไม่พบข้อมูลประเด็นยุทธศาสตร์ของปีงบประมาณนี้ --');
                    issueSelect.prop('disabled', true); // 🔒 บล็อกล็อกปุ่มทันทีไม่ยอมให้กดคลิกขยายเปิดดูตัวเลือก
                }
            }

            // สั่งทำลายสกินหน้ากากเก่าและสร้าง Select2 ใหม่เพื่อให้สอดรับสถานะ disabled และรายการใหม่
            if (issueSelect.data('select2')) {
                issueSelect.select2('destroy');
            }

            issueSelect.select2({
                dropdownParent: $('#mainModal'),
                theme: 'bootstrap-5',
                width: '100%'
            });

            issueSelect.trigger('change');
        });
    });

    function openAddModal() {
        isEditMode = false;
        $('#modalTitle').text('เพิ่มเป้าประสงค์ใหม่');
        $('#mainForm')[0].reset();
        $('#item_id').val('');

        // ล้างกล่องสไลด์คู่ลำดับเงื่อนไข
        $('#modal_fiscal_year_id').val('').trigger('change');
        $('#strategic_issue_id').val('').trigger('change');

        // บังคับล็อกสถานะกล่องยุทธศาสตร์ลูกให้เป็นสีเทาตั้งแต่แรกเริ่มกดปุ่มเปิดฟอร์ม
        $('#strategic_issue_id').prop('disabled', true).select2({
            dropdownParent: $('#mainModal'),
            theme: 'bootstrap-5',
            width: '100%'
        });

        $('#mainModal').modal('show');
    }

    function openEditModal(data, fiscalYearId) {
        isEditMode = true;
        $('#modalTitle').text('แก้ไขข้อมูลเป้าประสงค์');
        $('#item_id').val(data.id);

        // ดึงสถานะปีไปผูกสตรีมคัดกรองชั้นแรก
        $('#modal_fiscal_year_id').val(fiscalYearId).trigger('change');

        // หน่วงจังหวะเสี้ยววินาทีเพื่อให้กลไก Cascading คำนวณกรองและปลดล็อกฟิลด์เสร็จสมบูรณ์ก่อนจะผูกค่า ID ยุทธศาสตร์แม่เดิม
        setTimeout(function() {
            $('#strategic_issue_id').val(data.strategic_issue_id).trigger('change');
        }, 150);

        $('#code').val(data.code);
        $('#name').val(data.name);
        $('#mainModal').modal('show');
    }

    $('#mainForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#item_id').val();
        let url = isEditMode ? `{{ url('plan/goals') }}/${id}` : `{{ url('plan/goals') }}`;
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
                }).then(() => location.reload());
            }
        });
    });

    function deleteItem(id) {
        Swal.fire({
            title: 'ยืนยันลบข้อมูล?',
            text: "กลยุทธ์ทั้งหมดที่พึ่งพาเป้าประสงค์นี้จะหลุดออกจากฐานข้อมูลทันที",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'ใช่, ฉันต้องการลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('plan/goals') }}/${id}`,
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
                            // สั่งให้คำนวณและเกลี่ยเลขลำดับหน้าตารางใหม่ตามเงื่อนไขฟิลเตอร์ภายนอก
                            $('#filter_fiscal_year').trigger('change');
                        });
                    }
                });
            }
        });
    }
</script>
@endpush