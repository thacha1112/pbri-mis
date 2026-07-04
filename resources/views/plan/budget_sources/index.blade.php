@extends('layouts.app')

@section('content')
<!-- ส่วนกรองข้อมูลหน้าจอหลักตามปีงบประมาณ -->
<div class="card border-0 shadow-sm bg-white mb-4">
    <div class="card-body p-3">
        <div class="row align-items-center">
            <div class="col-md-4">
                <label class="form-label fw-bold text-secondary"><i class="fa-solid fa-filter me-1"></i> กรองตามปีงบประมาณ</label>
                <select class="form-select select2-filter" id="filter_fiscal_year">
                    <option value="all">-- แสดงทุกปีงบประมาณ --</option>
                    @foreach($fiscalYears as $y)
                    <option value="{{ $y->id }}">ปี พ.ศ. {{ $y->year }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm bg-white">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
        <h5 class="m-0 fw-bold text-dark"><i class="fa-solid fa-wallet text-success me-2"></i>จัดการข้อมูลแหล่งเงินงบประมาณ</h5>
        <button class="btn btn-primary btn-sm fw-bold" onclick="openAddModal()"><i class="fa-solid fa-plus me-1"></i> เพิ่มแหล่งเงิน</button>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 10%;">ลำดับ</th>
                        <th style="width: 20%;">ปีงบประมาณ</th>
                        <th style="width: 40%;">ชื่อแหล่งเงินงบประมาณ</th>
                        <th style="width: 15%;">สถานะระบบ</th>
                        <th style="width: 15%; text-align: center;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sources as $index => $item)
                    <!-- ใส่ data-year-id สำหรับทำ Client-side Filter ด้วย jQuery -->
                    <tr id="row-{{ $item->id }}" class="source-row" data-year-id="{{ $item->fiscal_year_id }}">
                        <td class="row-index">{{ $index + 1 }}</td>
                        <td><span class="badge bg-primary px-3 py-2 fs-6">พ.ศ. {{ $item->fiscalYear?->year ?? 'ไม่ระบุปี' }}</span></td>
                        <td class="fw-bold text-dark fs-6">{{ $item->name }}</td>
                        <td>
                            <span class="badge bg-{{ $item->status == 'active' ? 'success' : 'danger' }}-subtle text-{{ $item->status == 'active' ? 'success' : 'danger' }} px-3 py-2 rounded-pill">
                                {{ $item->status == 'active' ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm me-1" onclick="openEditModal({{ json_encode($item) }})"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button class="btn btn-danger btn-sm" onclick="deleteItem({{ $item->id }})"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="mainModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="modalTitle">ฟอร์มข้อมูลแหล่งเงิน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="mainForm">
                @csrf
                <input type="hidden" id="item_id" name="id">
                <div class="modal-body p-4">
                    <!-- เพิ่มส่วนผูกปีงบประมาณในฟอร์มเพิ่มข้อมูล -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">ปีงบประมาณองค์กร <span class="text-danger">*</span></label>
                        <select class="form-select select2-in-modal" id="fiscal_year_id" name="fiscal_year_id" required>
                            <option value="">-- เลือกปีงบประมาณ --</option>
                            @foreach($fiscalYears as $y)
                            <option value="{{ $y->id }}">ปี พ.ศ. {{ $y->year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">ชื่อแหล่งเงินงบประมาณ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control fw-bold" id="name" name="name" placeholder="เช่น เงินงบประมาณแผ่นดิน, เงินรายได้สถาบัน" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">สถานะ</label>
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

        // ปลุกหัวเชื้อ Select2 ใน Modal ฟอร์ม
        $('.select2-in-modal').select2({
            dropdownParent: $('#mainModal'),
            theme: 'bootstrap-5',
            width: '100%'
        });

        // ================= ฟังก์ชันกรองข้อมูลหน้าตารางหลัก =================
        $('#filter_fiscal_year').on('change', function() {
            let selectedYearId = $(this).val();
            let visibleIndex = 1;

            if (selectedYearId === 'all') {
                $('.source-row').show();
                $('.source-row').each(function() {
                    $(this).find('.row-index').text(visibleIndex++);
                });
            } else {
                $('.source-row').hide();
                $(`.source-row[data-year-id="${selectedYearId}"]`).show().each(function() {
                    $(this).find('.row-index').text(visibleIndex++);
                });
            }
        });
    });

    function openAddModal() {
        isEditMode = false;
        $('#modalTitle').text('เพิ่มข้อมูลแหล่งเงินใหม่');
        $('#mainForm')[0].reset();
        $('#item_id').val('');
        $('#fiscal_year_id').val('').trigger('change');
        $('#status').val('active');
        $('#mainModal').modal('show');
    }

    function openEditModal(data) {
        isEditMode = true;
        $('#modalTitle').text('แก้ไขข้อมูลแหล่งเงิน');
        $('#item_id').val(data.id);
        $('#fiscal_year_id').val(data.fiscal_year_id).trigger('change');
        $('#name').val(data.name);
        $('#status').val(data.status);
        $('#mainModal').modal('show');
    }

    $('#mainForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#item_id').val();
        let url = isEditMode ? `{{ url('plan/budget-sources') }}/${id}` : `{{ url('plan/budget-sources') }}`;
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
            title: 'ยืนยันการลบข้อมูล?',
            text: "คำเตือน: โครงสร้างแผนงาน (L1) และหมวดงบรายจ่าย (L2) ทั้งหมดที่ผูกอยู่ใต้แหล่งเงินนี้จะถูกถอนรากถอนโคนออกถาวรทั้งหมดทันที",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('plan/budget-sources') }}/${id}`,
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