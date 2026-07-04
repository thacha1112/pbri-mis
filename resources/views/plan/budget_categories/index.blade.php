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
        <h5 class="m-0 fw-bold text-dark"><i class="fa-solid fa-tags text-danger me-2"></i>ชั้นที่ 2: จัดการหมวดงบรายจ่าย (Budget Categories)</h5>
        <button class="btn btn-primary btn-sm fw-bold" onclick="openAddModal()"><i class="fa-solid fa-plus me-1"></i> เพิ่มหมวดงบรายจ่าย</button>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle" id="categoryTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 10%;">ลำดับ</th>
                        <th style="width: 50%;">ชื่อหมวดงบประมาณรายจ่ายรายย่อย</th>
                        <th style="width: 25%;">แผนงาน & แหล่งเงินต้นขั้ว</th>
                        <th style="width: 15%; text-align: center;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $index => $item)
                    <tr id="row-{{ $item->id }}" class="category-row" data-year-id="{{ $item->program?->budgetSource?->fiscal_year_id }}">
                        <td class="row-index">{{ $index + 1 }}</td>
                        <td class="fw-bold text-dark">{{ $item->name }}</td>
                        <td>
                            <div class="text-primary fw-bold fs-7">{{ $item->program?->name ?? 'ไม่ระบุแผนงาน' }}</div>
                            <small class="text-muted">
                                แหล่งเงิน: {{ $item->program?->budgetSource?->name ?? '-' }}
                                @if($item->program?->budgetSource?->fiscalYear)
                                (ปี {{ $item->program->budgetSource->fiscalYear->year }})
                                @endif
                            </small>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm me-1" onclick="openEditModal({{ json_encode($item) }}, {{ $item->program?->budgetSource?->fiscal_year_id ?? 'null' }}, {{ $item->program_id }})">
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
                <h5 class="modal-title fw-bold" id="modalTitle">ฟอร์มข้อมูลหมวดงบรายจ่าย</h5>
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
                        <label class="form-label fw-bold">เลือกแผนงานต้นขั้ว (Level 1) <span class="text-danger">*</span></label>
                        <select class="form-select select2-in-modal" id="program_id" name="program_id" required>
                            <option value="">-- กรุณาเลือกปีงบประมาณด้านบน --</option>
                            @foreach($programs as $p)
                            <option value="{{ $p->id }}" data-year-id="{{ $p->budgetSource?->fiscal_year_id }}">
                                [แหล่งเงิน: {{ $p->budgetSource?->name ?? 'ไม่ระบุ' }}] -> {{ $p->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">ชื่อหมวดงบรายจ่าย / รายละเอียดค่าใช้จ่าย <span class="text-danger">*</span></label>
                        <input type="text" class="form-control fw-bold" id="name" name="name" placeholder="เช่น งบบุคลากร, งบดำเนินงาน, งบเงินอุดหนุนยุทธศาสตร์" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">สถานะระบบ</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active">เปิดใช้งาน (Active)</option>
                            <option value="inactive">ปิดใช้งาน (Inactive)</option>
                        </select>
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
        // ตัวกรองนอกตารางหลัก
        $('.select2-filter').select2({
            theme: 'bootstrap-5'
        });

        // ปลุกหัวเชื้อสกิน Select2 ในฟอร์ม Modal
        $('.select2-in-modal').select2({
            dropdownParent: $('#mainModal'),
            theme: 'bootstrap-5',
            width: '100%'
        });

        // 🔥 โคลนเก็บตัวเลือกแผนงานดั้งเดิมทั้งหมดของ HTML ไว้ในหน่วยความจำตั้งแต่เริ่มโหลดหน้า
        const allProgramOptions = $('#program_id').find('option').clone();

        // ================= ฟังก์ชันกรองข้อมูลหน้าตารางหลัก =================
        $('#filter_fiscal_year').on('change', function() {
            let selectedYearId = $(this).val();
            let visibleIndex = 1;

            if (selectedYearId === 'all') {
                $('.category-row').show();
                $('.category-row').each(function() {
                    $(this).find('.row-index').text(visibleIndex++);
                });
            } else {
                $('.category-row').hide();
                $(`.category-row[data-year-id="${selectedYearId}"]`).show().each(function() {
                    $(this).find('.row-index').text(visibleIndex++);
                });
            }
        });

        // ================= ฟังก์ชันทำ Cascading Dropdown ใน Modal + บล็อกล็อกปุ่มว่าง =================
        $('#modal_fiscal_year_id').on('change', function() {
            let selectedYearId = $(this).val();
            let programSelect = $('#program_id');

            // ล้างก้อนขยะข้อมูลเก่าค้างคาในตู้ HTML ออกให้หมด
            programSelect.empty();

            if (selectedYearId === '') {
                programSelect.append('<option value="">-- กรุณาเลือกปีงบประมาณด้านบน --</option>');
                programSelect.prop('disabled', true); // ล็อกกล่องเป็นสีเทา 🔒
            } else {
                // คัดกรองออปชันแผนงานที่มีไอดีปีงบตรงตามเงื่อนไขชั้นที่ 1
                let matchingOptions = allProgramOptions.filter(function() {
                    let yearId = $(this).data('year-id');
                    return $(this).val() === "" || (yearId != undefined && yearId.toString() === selectedYearId.toString());
                });

                if (matchingOptions.length > 1) { // มีมากกว่าออปชันค่าว่างเริ่มต้น แปลว่าพบแผนงานในปีนั้น
                    programSelect.append(matchingOptions.clone());
                    programSelect.find('option[value=""]').text('-- กรุณาเลือกแผนงานต้นขั้ว --');
                    programSelect.prop('disabled', false); // ปลดล็อกให้เลือกได้ 🔓
                } else {
                    programSelect.append('<option value="">-- ไม่พบข้อมูลแผนงานระบบของปีงบประมาณนี้ --</option>');
                    programSelect.prop('disabled', true); // ล็อกกล่องทันที 🔒
                }
            }

            // สั่งทำลายและชุบชีวิตอินเตอร์เฟส Select2 อันใหม่ขึ้นมาแทนคลังแคชเดิม
            if (programSelect.data('select2')) {
                programSelect.select2('destroy');
            }
            programSelect.select2({
                dropdownParent: $('#mainModal'),
                theme: 'bootstrap-5',
                width: '100%'
            });
            programSelect.trigger('change');
        });
    });

    function openAddModal() {
        isEditMode = false;
        $('#modalTitle').text('เพิ่มหมวดงบรายจ่ายองค์กรใหม่ (L2)');
        $('#mainForm')[0].reset();
        $('#item_id').val('');

        // สั่งล้างเคลียร์คัดกรองคู่เลเวลความปลอดภัยใน Modal
        $('#modal_fiscal_year_id').val('').trigger('change');
        $('#status').val('active');

        $('#mainModal').modal('show');
    }

    function openEditModal(data, fiscalYearId, programId) {
        isEditMode = true;
        $('#modalTitle').text('แก้ไขข้อมูลหมวดงบรายจ่าย');
        $('#item_id').val(data.id);
        $('#name').val(data.name);
        $('#status').val(data.status);

        // จุดชนวนประมวลผล Cascade ย้อนประวัติข้อมูลเดิม
        if (fiscalYearId && fiscalYearId !== null) {
            $('#modal_fiscal_year_id').val(fiscalYearId).trigger('change');

            // หน่วงเวลาเซกชันเสี้ยววินาทีเพื่อให้ระบบล้างและรีเซ็ตกล่องลูกเสร็จก่อนจะทำการยัดไอดีเดิมเข้าไปล็อกสตรีม
            setTimeout(function() {
                $('#program_id').val(programId).trigger('change');
            }, 150);
        } else {
            $('#modal_fiscal_year_id').val('').trigger('change');
        }

        $('#mainModal').modal('show');
    }

    $('#mainForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#item_id').val();
        let url = isEditMode ? `{{ url('plan/budget-categories') }}/${id}` : `{{ url('plan/budget-categories') }}`;
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
            title: 'ยืนยันลบหมวดงานนี้จริงหรือไม่?',
            text: "ข้อมูลนี้จะถูกถอนการเปิดให้ผูกในระบบเงินของโครงการถาวรทันที",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'ลบออกถาวร',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('plan/budget-categories') }}/${id}`,
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
                            // สั่งรีนับเลขลำดับตารางใหม่ตามปีงบประมาณที่ฟิลเตอร์ค้างไว้ด้านนอก
                            $('#filter_fiscal_year').trigger('change');
                        });
                    }
                });
            }
        });
    }
</script>
@endpush