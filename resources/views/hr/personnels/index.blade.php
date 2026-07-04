@extends('layouts.app')

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card border-0 shadow-sm bg-white">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
        <h5 class="m-0 fw-bold text-dark"><i class="fa-solid fa-user-tie text-primary me-2"></i>จัดการข้อมูลบุคลากรเบื้องต้น</h5>
        <a href="{{ url('hr/personnels/create') }}" class="btn btn-primary btn-sm fw-bold px-3">
            <i class="fa-solid fa-plus me-1"></i> เพิ่มข้อมูลบุคลากร
        </a>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>รหัสพนักงาน</th>
                        <th>ชื่อ-นามสกุล</th>
                        <th>อีเมล</th>
                        <th>หน่วยงานที่สังกัด</th>
                        <th>ตำแหน่งงาน</th>
                        <th>สถานะ</th>
                        <th style="text-align: center; width: 12%;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($personnels as $item)
                    <tr id="row-{{ $item->id }}">
                        <td><code>{{ $item->emp_code ?? '-' }}</code></td>
                        <td class="fw-bold">{{ $item->firstname }} {{ $item->lastname }}</td>
                        <td><span class="text-secondary">{{ $item->email ?? '-' }}</span></td>
                        <td><span class="badge bg-secondary-subtle text-dark fs-6">{{ $item->department ? $item->department->name : 'ไม่ระบุสังกัด' }}</span></td>
                        <td>{{ $item->position_title ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $item->status == 'active' ? 'success' : 'danger' }}-subtle text-{{ $item->status == 'active' ? 'success' : 'danger' }} px-3 py-2 rounded-pill">
                                {{ $item->status == 'active' ? 'ปฏิบัติงาน' : 'พ้นสภาพ' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ url('hr/personnels/'.$item->id.'/edit') }}" class="btn btn-warning btn-sm me-1"><i class="fa-solid fa-pen-to-square"></i></a>
                            <button class="btn btn-danger btn-sm" onclick="deleteItem({{ $item->id }})"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">ไม่พบข้อมูลบุคลากรในระบบ</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function deleteItem(id) {
        Swal.fire({
            title: 'ยืนยันการลบข้อมูล?',
            text: "ประวัติของบุคลากรท่านนี้จะถูกลบออกจากฐานข้อมูล",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('hr/personnels') }}/${id}`,
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
                        });
                    }
                });
            }
        });
    }
</script>
@endpush