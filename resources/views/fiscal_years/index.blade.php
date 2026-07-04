@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm bg-white">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
        <h5 class="m-0 fw-bold text-dark">
            <i class="fa-solid fa-calendar-days text-primary me-2"></i>จัดการข้อมูลปีงบประมาณ (MIS Common Data)
        </h5>
        <button class="btn btn-primary btn-sm fw-bold px-3" onclick="openAddModal()">
            <i class="fa-solid fa-plus me-1"></i> เพิ่มปีงบประมาณใหม่
        </button>
    </div>

    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 10%;">ลำดับ</th>
                        <th style="width: 25%;">ปีงบประมาณ (พ.ศ.)</th>
                        <th style="width: 25%;">สถานะระบบ</th>
                        <th style="width: 25%;">คำอธิบาย/หมายเหตุ</th>
                        <th style="width: 15%; text-align: center;">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($years as $index => $item)
                    <tr id="row-{{ $item->id }}">
                        <td>{{ $index + 1 }}</td>
                        <td class="fw-bold text-primary fs-5">{{ $item->year }}</td>
                        <td>
                            @if($item->status == 'active')
                            <span class="badge bg-success-subtle text-success px-3 py-2 fs-6 rounded-pill">เปิดใช้งาน</span>
                            @else
                            <span class="badge bg-danger-subtle text-danger px-3 py-2 fs-6 rounded-pill">ปิดใช้งาน</span>
                            @endif
                        </td>
                        <td class="text-secondary">{{ $item->description ?? '-' }}</td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm me-1" onclick="openEditModal({{ json_encode($item) }})" title="แก้ไข">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteYear({{ $item->id }}, {{ $item->year }})" title="ลบ">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">ไม่พบข้อมูลปีงบประมาณในระบบ</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="fiscalYearModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="modalTitle">เพิ่มปีงบประมาณ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="fiscalYearForm">
                @csrf
                <input type="hidden" id="year_id" name="id">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">ปีงบประมาณ (พ.ศ.) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control fs-5 text-center fw-bold" id="year" name="year" min="2500" max="3000" placeholder="เช่น 2569" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">สถานะการเปิดให้ใช้งานในแผนงาน</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active">เปิดใช้งาน (Active)</option>
                            <option value="inactive">ปิดใช้งาน (Inactive)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">หมายเหตุ / ข้อมูลอ้างอิงของระบบ MIS</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="ระบุรายละเอียดเพิ่มเติมสำหรับใช้เชื่อมโยงฐานข้อมูล..."></textarea>
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

    // เปิด Modal โหมดเพิ่มข้อมูลใหม่
    function openAddModal() {
        isEditMode = false;
        $('#modalTitle').html('<i class="fa-solid fa-plus-circle text-success me-2"></i>เพิ่มปีงบประมาณใหม่');
        $('#fiscalYearForm')[0].reset();
        $('#year_id').val('');
        $('#year').prop('readonly', false); // โหมดเพิ่มให้กรอกตัวเลขได้
        $('#fiscalYearModal').modal('show');
    }

    // เปิด Modal โหมดแก้ไขข้อมูลเดิม
    function openEditModal(data) {
        isEditMode = true;
        $('#modalTitle').html('<i class="fa-solid fa-pen-to-square text-warning window me-2"></i>แก้ไขข้อมูลปีงบประมาณ');
        $('#year_id').val(data.id);
        $('#year').val(data.year).prop('readonly', true); // ล็อกฟิลด์ปีไว้ไม่ให้แก้ ป้องกันข้อมูล MIS คลาดเคลื่อน
        $('#status').val(data.status);
        $('#description').val(data.description);
        $('#fiscalYearModal').modal('show');
    }

    // จัดการส่งฟอร์มผ่าน AJAX (ทำงานได้ทั้งสร้างและอัปเดต)
    $('#fiscalYearForm').on('submit', function(e) {
        e.preventDefault();

        let id = $('#year_id').val();
        let url = isEditMode ? `{{ url('config/fiscal-years') }}/${id}` : `{{ url('config/fiscal-years') }}`;
        let formData = $(this).serialize();

        if (isEditMode) {
            formData += '&_method=PUT'; // หลอก Method สำหรับ Route PUT ของ Laravel
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#fiscalYearModal').modal('hide');
                    Swal.fire({
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1300
                        })
                        .then(() => {
                            location.reload(); // รีโหลดสตรีมหน้าจอเพื่ออัปเดตตาราง
                        });
                }
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                let errorHtml = '';
                $.each(errors, function(key, value) {
                    errorHtml += `• ${value[0]}<br>`;
                });
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่สามารถบันทึกข้อมูลได้',
                    html: errorHtml
                });
            }
        });
    });

    // ฟังก์ชันส่งคำสั่งลบข้อมูลผ่าน AJAX + แจ้งเตือนด้วย SweetAlert2
    function deleteYear(id, yearValue) {
        Swal.fire({
            title: `ยืนยันการลบปีงบประมาณ ${yearValue}?`,
            text: "คำเตือน: การลบข้อมูลนี้อาจส่งผลกระทบต่อโครงการกลางที่ผูกกับฐานข้อมูล MIS ชิ้นอื่น",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'ใช่, ฉันต้องการลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('config/fiscal-years') }}/${id}`,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 1300
                            });
                            $(`#row-${id}`).fadeOut(400, function() {
                                $(this).remove();
                            }); // ลบแถวออกแบบเนียนๆ
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'ไม่สามารถลบข้อมูลนี้ได้เนื่องจากระบบขัดข้อง'
                        });
                    }
                });
            }
        });
    }
</script>
@endpush