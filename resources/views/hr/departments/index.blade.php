@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm bg-white">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
        <h5 class="m-0 fw-bold text-dark"><i class="fa-solid fa-sitemap text-warning me-2"></i>จัดการโครงสร้างหน่วยงาน</h5>
        <button class="btn btn-primary btn-sm fw-bold" onclick="openAddModal()"><i class="fa-solid fa-plus me-1"></i> เพิ่มหน่วยงาน</button>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 10%;">ลำดับ</th>
                        <th style="width: 35%;">ชื่อหน่วยงาน</th>
                        <th style="width: 25%;">หน่วยงานหลัก (สังกัต)</th>
                        <th style="width: 15%;">สถานะ</th>
                        <th style="width: 15%; text-align: center;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($departments as $index => $item)
                    <tr id="row-{{ $item->id }}">
                        <td>{{ $index + 1 }}</td>
                        <td class="fw-bold">{{ $item->name }}</td>
                        <td><span class="text-muted">{{ $item->parent ? $item->parent->name : 'หน่วยงานหลักสูงสุด' }}</span></td>
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

<div class="modal fade" id="deptModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="modalTitle">เพิ่มหน่วยงาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deptForm">
                @csrf
                <input type="hidden" id="dept_id" name="id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">ชื่อหน่วยงาน <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="เช่น กองเทคโนโลยีดิจิทัล" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">สังกัดหน่วยงานหลัก (ถ้ามี)</label>
                        <select class="form-select select2-in-modal" id="parent_id" name="parent_id">
                            <option value="">-- เป็นหน่วยงานระดับบนสุด --</option>
                            @foreach($parentDepartments as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">สถานะ</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active">เปิดใช้งาน</option>
                            <option value="inactive">ปิดใช้งาน</option>
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
        // บังคับให้ Select2 ทำงานใน Modal ได้ดีขึ้น
        $('.select2-in-modal').select2({
            dropdownParent: $('#deptModal'),
            theme: 'bootstrap-5'
        });
    });

    function openAddModal() {
        isEditMode = false;
        $('#modalTitle').text('เพิ่มหน่วยงานใหม่');
        $('#deptForm')[0].reset();
        $('#dept_id').val('');
        $('#parent_id').val('').trigger('change');
        $('#deptModal').modal('show');
    }

    function openEditModal(data) {
        isEditMode = true;
        $('#modalTitle').text('แก้ไขข้อมูลหน่วยงาน');
        $('#dept_id').val(data.id);
        $('#name').val(data.name);
        $('#parent_id').val(data.parent_id).trigger('change');
        $('#status').val(data.status);
        $('#deptModal').modal('show');
    }

    $('#deptForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#dept_id').val();
        let url = isEditMode ? `{{ url('hr/departments') }}/${id}` : `{{ url('hr/departments') }}`;
        let formData = $(this).serialize();
        if (isEditMode) formData += '&_method=PUT';

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(res) {
                $('#deptModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: res.message,
                    showConfirmButton: false,
                    timer: 1300
                }).then(() => location.reload());
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด!',
                    text: xhr.responseJSON.message
                });
            }
        });
    });

    function deleteItem(id) {
        Swal.fire({
            title: 'ยืนยันการลบข้อมูล?',
            text: "ข้อมูลหน่วยงานนี้จะถูกลบออกถาวร",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('hr/departments') }}/${id}`,
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
                        $(`#row-${id}`).remove();
                    }
                });
            }
        });
    }
</script>
@endpush