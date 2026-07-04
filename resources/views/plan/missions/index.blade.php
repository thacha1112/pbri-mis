@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm bg-white mb-4">
    <div class="card-body p-3">
        <div class="row align-items-center">
            <div class="col-md-4">
                <label class="form-label fw-bold text-secondary"><i class="fa-solid fa-filter me-1"></i> กรองข้อมูลตามปีงบประมาณ</label>
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
        <h5 class="m-0 fw-bold text-dark"><i class="fa-solid fa-compass text-primary me-2"></i>ชั้นที่ 1: จัดการข้อมูลพันธกิจ</h5>
        <button class="btn btn-primary btn-sm fw-bold" onclick="openAddModal()"><i class="fa-solid fa-plus me-1"></i> เพิ่มพันธกิจ</button>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle" id="missionTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 10%;">ลำดับ</th>
                        <th style="width: 20%;">ปีงบประมาณ</th>
                        <th style="width: 50%;">รายละเอียดพันธกิจ</th>
                        <th style="width: 20%; text-align: center;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($missions as $index => $item)
                    <tr id="row-{{ $item->id }}" class="mission-row" data-year-id="{{ $item->fiscal_year_id }}">
                        <td class="row-index">{{ $index + 1 }}</td>
                        <td><span class="badge bg-primary px-3 py-2 fs-6">พ.ศ. {{ $item->fiscalYear->year }}</span></td>
                        <td class="fw-bold text-wrap">{{ $item->name }}</td>
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

<div class="modal fade" id="mainModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="modalTitle">ฟอร์มพันธกิจ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="mainForm">
                @csrf
                <input type="hidden" id="item_id" name="id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">ผูกปีงบประมาณ <span class="text-danger">*</span></label>
                        <select class="form-select select2-in-modal" id="fiscal_year_id" name="fiscal_year_id" required>
                            <option value="">-- เลือกปีงบประมาณ --</option>
                            @foreach($fiscalYears as $y)
                            <option value="{{ $y->id }}">ปี พ.ศ. {{ $y->year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">ข้อความพันธกิจองค์กร <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="name" name="name" rows="4" placeholder="ระบุเนื้อหาพันธกิจ..." required></textarea>
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
        // เปิดใช้งาน Select2 สำหรับตัวกรองหน้าตารางหลัก
        $('.select2-filter').select2({
            theme: 'bootstrap-5'
        });

        // เปิดใช้งาน Select2 สำหรับใน Modal
        $('.select2-in-modal').select2({
            dropdownParent: $('#mainModal'),
            theme: 'bootstrap-5'
        });

        // ฟังก์ชันทำงานเมื่อผู้ใช้งานเปลี่ยนการกรองปีงบประมาณ
        $('#filter_fiscal_year').on('change', function() {
            let selectedYearId = $(this).val();
            let visibleIndex = 1;

            if (selectedYearId === 'all') {
                $('.mission-row').show();
                // รีรันเลขลำดับหน้าตารางใหม่ทั้งหมด
                $('.mission-row').each(function() {
                    $(this).find('.row-index').text(visibleIndex++);
                });
            } else {
                $('.mission-row').hide();
                // แสดงเฉพาะแถวที่มี data-year-id ตรงกับที่เลือก และรันเลขลำดับเฉพาะแถวที่แสดง
                $(`.mission-row[data-year-id="${selectedYearId}"]`).show().each(function() {
                    $(this).find('.row-index').text(visibleIndex++);
                });
            }
        });
    });

    function openAddModal() {
        isEditMode = false;
        $('#modalTitle').text('เพิ่มข้อมูลพันธกิจใหม่');
        $('#mainForm')[0].reset();
        $('#item_id').val('');
        $('#fiscal_year_id').val('').trigger('change');
        $('#mainModal').modal('show');
    }

    function openEditModal(data) {
        isEditMode = true;
        $('#modalTitle').text('แก้ไขข้อมูลพันธกิจ');
        $('#item_id').val(data.id);
        $('#fiscal_year_id').val(data.fiscal_year_id).trigger('change');
        $('#name').val(data.name);
        $('#mainModal').modal('show');
    }

    $('#mainForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#item_id').val();
        let url = isEditMode ? `{{ url('plan/missions') }}/${id}` : `{{ url('plan/missions') }}`;
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
            title: 'ลบข้อมูลพันธกิจหรือไม่?',
            text: "ประเด็นยุทธศาสตร์และกลยุทธ์ที่ผูกอยู่ใต้พันธกิจนี้จะถูกลบไปด้วยทั้งหมด",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'ใช่, ฉันต้องการลบ'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('plan/missions') }}/${id}`,
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
                            // สั่งให้รีเฟรชเลขลำดับหน้าตารางใหม่หลังลบแถวเสร็จสอดคล้องตามฟิลเตอร์ปัจจุบัน
                            $('#filter_fiscal_year').trigger('change');
                        });
                    }
                });
            }
        });
    }
</script>
@endpush