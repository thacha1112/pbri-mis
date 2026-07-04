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
        <h5 class="m-0 fw-bold text-dark"><i class="fa-solid fa-layer-group text-info me-2"></i>ชั้นที่ 1: จัดการข้อมูลแผนงาน (Programs)</h5>
        <button class="btn btn-primary btn-sm fw-bold" onclick="openAddModal()"><i class="fa-solid fa-plus me-1"></i> เพิ่มข้อมูลแผนงาน</button>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle" id="programTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 10%;">ลำดับ</th>
                        <th style="width: 55%;">ชื่อโครงการ/รายละเอียดโครงสร้างแผนงาน</th>
                        <th style="width: 20%;">แหล่งเงินต้นสังกัด</th>
                        <th style="width: 15%; text-align: center;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($programs as $index => $item)
                    <tr id="row-{{ $item->id }}" class="program-row" data-year-id="{{ $item->budgetSource?->fiscal_year_id }}">
                        <td class="row-index">{{ $index + 1 }}</td>
                        <td class="fw-bold text-dark">{{ $item->name }}</td>
                        <td>
                            <span class="badge bg-success-subtle text-success px-3 py-2 fs-7">
                                {{ $item->budgetSource ? $item->budgetSource->name : 'ไม่ระบุแหล่งเงิน' }}
                                @if($item->budgetSource?->fiscalYear)
                                (ปี {{ $item->budgetSource->fiscalYear->year }})
                                @endif
                            </span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm me-1" onclick="openEditModal({{ json_encode($item) }}, {{ $item->budgetSource?->fiscal_year_id ?? 'null' }})">
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
                <h5 class="modal-title fw-bold" id="modalTitle">ฟอร์มข้อมูลแผนงาน</h5>
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
                        <label class="form-label fw-bold">เลือกแหล่งเงินงบประมาณหลัก <span class="text-danger">*</span></label>
                        <select class="form-select select2-in-modal" id="budget_source_id" name="budget_source_id" required>
                            <option value="">-- กรุณาเลือกปีงบประมาณด้านบน --</option>
                            @foreach($sources as $s)
                            <option value="{{ $s->id }}" data-year-id="{{ $s->fiscal_year_id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">ชื่อแผนงานระบบยุทธศาสตร์ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="เช่น แปรรูปแผนงานพัฒนาการศึกษาขั้นสูง, แผนงานพื้นฐาน" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">สถานะการใช้งาน</label>
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
        // เปิดใช้งานตัวกรองหลักที่อยู่นอกตาราง
        $('.select2-filter').select2({
            theme: 'bootstrap-5'
        });

        // เปิดใช้งาน Select2 ภายใน Modal ฟอร์ม
        $('.select2-in-modal').select2({
            dropdownParent: $('#mainModal'),
            theme: 'bootstrap-5',
            width: '100%'
        });

        // 🔥 โคลนเก็บออปชันดั้งเดิมทั้งหมดของกล่องเลือกแหล่งเงินเก็บไว้ในหน่วยความจำเพื่อความแม่นยำในการคัดกรองสด
        const allSourceOptions = $('#budget_source_id').find('option').clone();

        // ================= ฟังก์ชันกรองข้อมูลหน้าตารางหลัก =================
        $('#filter_fiscal_year').on('change', function() {
            let selectedYearId = $(this).val();
            let visibleIndex = 1;

            if (selectedYearId === 'all') {
                $('.program-row').show();
                $('.program-row').each(function() {
                    $(this).find('.row-index').text(visibleIndex++);
                });
            } else {
                $('.program-row').hide();
                $(`.program-row[data-year-id="${selectedYearId}"]`).show().each(function() {
                    $(this).find('.row-index').text(visibleIndex++);
                });
            }
        });

        // ================= ฟังก์ชันทำ Cascading Dropdown ใน Modal + สั่งล็อกกล่องว่าง =================
        $('#modal_fiscal_year_id').on('change', function() {
            let selectedYearId = $(this).val();
            let sourceSelect = $('#budget_source_id');

            // ล้างค่าปัจจุบันที่ถูกเลือกค้างไว้
            sourceSelect.empty();

            if (selectedYearId === '') {
                sourceSelect.append('<option value="">-- กรุณาเลือกปีงบประมาณด้านบน --</option>');
                sourceSelect.prop('disabled', true); // ล็อกกล่องไว้ถ้ายั้งไม่ได้ระบุปีหลัก
            } else {
                // คัดเลือกคัดแยกออปชันที่มี data-year-id ตรงตามปีงบประมาณที่คลิกเลือกมา
                let matchingOptions = allSourceOptions.filter(function() {
                    let yearId = $(this).data('year-id');
                    return $(this).val() === "" || (yearId != undefined && yearId.toString() === selectedYearId.toString());
                });

                if (matchingOptions.length > 1) { // มีมากกว่า 1 แสดงว่าตรวจพบข้อมูลจริงของปีนั้น
                    sourceSelect.append(matchingOptions.clone());
                    sourceSelect.find('option[value=""]').text('-- กรุณาเลือกแหล่งเงินงบประมาณหลัก --');
                    sourceSelect.prop('disabled', false); // ปลดล็อก 🎉
                } else {
                    sourceSelect.append('<option value="">-- ไม่พบข้อมูลแหล่งเงินของปีงบประมาณนี้ --</option>');
                    sourceSelect.prop('disabled', true); // ล็อกทันที 🔒
                }
            }

            // สั่งทำลายและวาดโครงสร้างสกินอินเตอร์เฟสอันใหม่ให้กับ Select2
            if (sourceSelect.data('select2')) {
                sourceSelect.select2('destroy');
            }
            sourceSelect.select2({
                dropdownParent: $('#mainModal'),
                theme: 'bootstrap-5',
                width: '100%'
            });
            sourceSelect.trigger('change');
        });
    });

    function openAddModal() {
        isEditMode = false;
        $('#modalTitle').text('เพิ่มข้อมูลแผนงานใหม่ (L1)');
        $('#mainForm')[0].reset();
        $('#item_id').val('');

        // ล้างระบบจับคู่เงื่อนไขปีงบและแหล่งเงินใน Modal
        $('#modal_fiscal_year_id').val('').trigger('change');
        $('#status').val('active');

        $('#mainModal').modal('show');
    }

    function openEditModal(data, fiscalYearId) {
        isEditMode = true;
        $('#modalTitle').text('แก้ไขข้อมูลแผนงาน');
        $('#item_id').val(data.id);
        $('#name').val(data.name);
        $('#status').val(data.status);

        // จุดชนวนสลับฟิลเตอร์ชั้นที่ 1 (ปีงบประมาณ)
        if (fiscalYearId && fiscalYearId !== null) {
            $('#modal_fiscal_year_id').val(fiscalYearId).trigger('change');

            // หน่วงจังหวะเสี้ยววินาทีเพื่อให้ก้อนตัวเลือกแหล่งเงินประมวลผลล้างและปลดล็อกตู้สำเร็จก่อนดึงไอดีเก่ามาผูกค่า
            setTimeout(function() {
                $('#budget_source_id').val(data.budget_source_id).trigger('change');
            }, 150);
        } else {
            $('#modal_fiscal_year_id').val('').trigger('change');
        }

        $('#mainModal').modal('show');
    }

    $('#mainForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#item_id').val();
        let url = isEditMode ? `{{ url('plan/programs') }}/${id}` : `{{ url('plan/programs') }}`;
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
            title: 'ยืนยันลบแผนงานงานนี้?',
            text: "หมวดงบประมาณรายจ่ายย่อย (L2) ทั้งหมดในกลุ่มนี้จะถูกลบออกถาวรไปด้วย",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('plan/programs') }}/${id}`,
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
                            // สั่งเรียกใช้ฟังก์ชันเกลี่ยรีรันลำดับแถวใหม่ให้ตรงกับฟิลเตอร์ปีหน้าจอหลักปัจจุบัน
                            $('#filter_fiscal_year').trigger('change');
                        });
                    }
                });
            }
        });
    }
</script>
@endpush