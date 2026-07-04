@extends('layouts.app')

@section('content')
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
        <h5 class="m-0 fw-bold text-dark"><i class="fa-solid fa-bullseye text-danger me-2"></i>ชั้นที่ 2: จัดการประเด็นยุทธศาสตร์</h5>
        <button class="btn btn-primary btn-sm fw-bold" onclick="openAddModal()"><i class="fa-solid fa-plus me-1"></i> เพิ่มยุทธศาสตร์</button>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle" id="issueTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 10%;">ลำดับ</th>
                        <th style="width: 15%;">รหัส/ชื่อยุทธศาสตร์</th>
                        <th style="width: 55%;">รายละเอียดประเด็นยุทธศาสตร์</th>
                        <th style="width: 20%; text-align: center;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($issues as $index => $item)
                    <tr id="row-{{ $item->id }}" class="issue-row" data-year-id="{{ $item->mission->fiscal_year_id }}">
                        <td class="row-index">{{ $index + 1 }}</td>
                        <td><span class="badge bg-secondary px-2 py-2">{{ $item->code ?? 'ยุทธศาสตร์' }}</span></td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->name }}</div>
                            <small class="text-muted">ภายใต้พันธกิจ: {{ Str::limit($item->mission->name, 70) }} (ปี {{ $item->mission->fiscalYear->year }})</small>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm me-1" onclick="openEditModal({{ json_encode($item) }}, {{ $item->mission->fiscal_year_id }})"><i class="fa-solid fa-pen-to-square"></i></button>
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
                <h5 class="modal-title fw-bold" id="modalTitle">ฟอร์มประเด็นยุทธศาสตร์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="mainForm">
                @csrf
                <input type="hidden" id="item_id" name="id">
                <div class="modal-body p-4">

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
                        <label class="form-label fw-bold">เลือกพันธกิจแม่ <span class="text-danger">*</span></label>
                        <select class="form-select select2-in-modal" id="mission_id" name="mission_id" required>
                            <option value="">-- กรุณาเลือกปีงบประมาณด้านบน --</option>
                            @foreach($missions as $m)
                            <option value="{{ $m->id }}" data-year-id="{{ $m->fiscal_year_id }}">[ปี {{ $m->fiscalYear->year }}] {{ Str::limit($m->name, 90) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">รหัสกำกับยุทธศาสตร์ (ถ้ามี)</label>
                        <input type="text" class="form-control" id="code" name="code" placeholder="เช่น ยุทธศาสตร์ที่ 1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">รายละเอียดประเด็นยุทธศาสตร์ <span class="text-danger">*</span></label>
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
        // เปิดใช้งาน Select2 ตัวกรองนอกตาราง
        $('.select2-filter').select2({
            theme: 'bootstrap-5'
        });

        // เปิดใช้งาน Select2 ภายใน Modal
        $('.select2-in-modal').select2({
            dropdownParent: $('#mainModal'),
            theme: 'bootstrap-5'
        });

        // ================= ฟังก์ชันกรองข้อมูลหน้าตารางหลัก =================
        $('#filter_fiscal_year').on('change', function() {
            let selectedYearId = $(this).val();
            let visibleIndex = 1;

            if (selectedYearId === 'all') {
                $('.issue-row').show();
                $('.issue-row').each(function() {
                    $(this).find('.row-index').text(visibleIndex++);
                });
            } else {
                $('.issue-row').hide();
                $(`.issue-row[data-year-id="${selectedYearId}"]`).show().each(function() {
                    $(this).find('.row-index').text(visibleIndex++);
                });
            }
        });

        // ================= ฟังก์ชันทำ Cascading Dropdown ใน Modal =================
        // ================= ฟังก์ชันทำ Cascading Dropdown ใน Modal =================
        // ================= ฟังก์ชันทำ Cascading Dropdown ใน Modal (เวอร์ชันบล็อกป้อนค่าว่าง) =================
        $('#modal_fiscal_year_id').on('change', function() {
            let selectedYearId = $(this).val();
            let missionSelect = $('#mission_id');

            // 1. ล้างค่าที่เลือกค้างไว้
            missionSelect.val('');

            if (selectedYearId === '') {
                missionSelect.find('option').show();
                missionSelect.find('option[value=""]').text('-- กรุณาเลือกปีงบประมาณด้านบน --');
                missionSelect.prop('disabled', true); // ล็อกกล่องไว้ถ้ายังไม่เลือกปี
            } else {
                // 2. ซ่อนตัวเลือกทั้งหมดก่อน
                missionSelect.find('option').hide();

                // 3. เปิดแสดงเฉพาะตัวเลือกเริ่มต้น และตัวเลือกที่ data-year-id ตรงกัน
                missionSelect.find('option[value=""]').show();

                let matchingOptions = missionSelect.find(`option[data-year-id="${selectedYearId}"]`);

                if (matchingOptions.length > 0) {
                    matchingOptions.show();
                    missionSelect.find('option[value=""]').text('-- กรุณาเลือกพันธกิจแม่ --');
                    missionSelect.prop('disabled', false); // ปลดล็อกให้เจ้าหน้าที่กดเลือกได้ 🎉
                } else {
                    // กรณีปีงบประมาณนั้นยังไม่มีข้อมูลพันธกิจ
                    missionSelect.find('option[value=""]').text('-- ไม่พบข้อมูลพันธกิจของปีงบประมาณนี้ --');
                    missionSelect.prop('disabled', true); // 🔥 ล็อกกล่องทันที ไม่ให้กดเลือกได้
                }
            }

            // 4. สั่งทำลายโครงสร้างอินเตอร์เฟสเดิมของ Select2
            if (missionSelect.data('select2')) {
                missionSelect.select2('destroy');
            }

            // 5. รัน Select2 ขึ้นมาใหม่เพื่ออัปเดตหน้าตา (UI) รวมถึงสถานะdisabled ด้วย
            missionSelect.select2({
                dropdownParent: $('#mainModal'),
                theme: 'bootstrap-5',
                width: '100%'
            });

            missionSelect.trigger('change');
        });
    });

    function openAddModal() {
        isEditMode = false;
        $('#modalTitle').text('เพิ่มข้อมูลประเด็นยุทธศาสตร์ใหม่');
        $('#mainForm')[0].reset();
        $('#item_id').val('');

        // ล้างสถานะกล่องเลือกคู่ลำดับ
        $('#modal_fiscal_year_id').val('').trigger('change');
        $('#mission_id').val('').trigger('change');

        // คืนค่าให้สามารถกรองแบบปกติได้ก่อนกรอก
        $('#mission_id').find('option').show();

        $('#mainModal').modal('show');
    }

    function openEditModal(data, fiscalYearId) {
        isEditMode = true;
        $('#modalTitle').text('แก้ไขข้อมูลประเด็นยุทธศาสตร์');
        $('#item_id').val(data.id);

        // ชี้เป้าตัวแปรให้ตรงเลเวลชั้นข้อมูล
        $('#modal_fiscal_year_id').val(fiscalYearId).trigger('change');

        // ล็อกเวลาเล็กน้อยผ่านการประมวลผลเพื่อให้ Cascading ทำงานกรองเงื่อนไขเสร็จก่อนผูกค่า
        setTimeout(function() {
            $('#mission_id').val(data.mission_id).trigger('change');
        }, 150);

        $('#code').val(data.code);
        $('#name').val(data.name);
        $('#mainModal').modal('show');
    }

    $('#mainForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#item_id').val();
        let url = isEditMode ? `{{ url('plan/strategic-issues') }}/${id}` : `{{ url('plan/strategic-issues') }}`;
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
            text: "เป้าประสงค์และกลยุทธ์ย่อยภายในจุดนี้จะถูกถอนออกถาวรทั้งหมด",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'ใช่, ฉันต้องการลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('plan/strategic-issues') }}/${id}`,
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
                            // รีเฟรชดักลำดับหลังจากลบข้อมูลออกตามฟิลเตอร์นอกตาราง
                            $('#filter_fiscal_year').trigger('change');
                        });
                    }
                });
            }
        });
    }
</script>
@endpush