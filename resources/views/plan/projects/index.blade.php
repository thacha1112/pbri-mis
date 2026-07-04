@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm bg-white mb-4">
    <div class="card-body p-3">
        <div class="row align-items-center g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold text-secondary">ค้นหาตามปีงบประมาณ</label>
                <select class="form-select select2-filter" id="filter_fiscal_year" onchange="applyFilters()">
                    <option value="all">-- ทุกปีงบประมาณ --</option>
                    @foreach($fiscalYears as $y)
                        <option value="{{ $y->id }}" {{ request('fiscal_year_id') == $y->id ? 'selected' : '' }}>ปี พ.ศ. {{ $y->year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold text-secondary">ค้นหาตามหน่วยงาน</label>
                <select class="form-select select2-filter" id="filter_department" onchange="applyFilters()">
                    <option value="all">-- ทุกหน่วยงาน --</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm bg-white">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
        <h5 class="m-0 fw-bold text-dark"><i class="fa-solid fa-file-invoice-dollar text-success me-2"></i>ระบบทะเบียนโครงการ & แผนงบประมาณ</h5>
        <a href="{{ url('plan/projects/create') }}" class="btn btn-primary btn-sm fw-bold"><i class="fa-solid fa-plus me-1"></i> จัดทำโครงการใหม่</a>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 8%;">ลำดับ</th>
                        <th style="width: 15%;">รหัสโครงการ</th>
                        <th style="width: 42%;">ชื่อโครงการ / หน่วยงานต้นสังกัด</th>
                        <th style="width: 20%;">ผู้รับผิดชอบโครงการ</th>
                        <th style="width: 15%; text-align: center;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $index => $item)
                    <tr id="row-{{ $item->id }}" class="project-row" data-year-id="{{ $item->fiscal_year_id }}">
                        <td class="row-index">{{ $index + 1 }}</td>
                        <td><span class="badge bg-dark px-2 py-2 font-mono fs-7">{{ $item->project_code }}</span></td>
                        <td>
                            <div class="fw-bold text-dark fs-6">{{ $item->name }}</div>
                            <small class="text-secondary"><i class="fa-solid fa-building me-1"></i>สังกัด: {{ $item->department?->name }} (ปี พ.ศ. {{ $item->fiscalYear?->year }})</small>
                        </td>
                        <td>
                            <div class="fw-semibold text-muted fs-7"><i class="fa-solid fa-user me-1"></i>{{ $item->personnel?->firstname." ".$item->personnel?->lastname }}</div>
                        </td>
                        <td class="text-center">
                            <a href="{{ url('plan/projects/'.$item->id.'/edit') }}" class="btn btn-warning btn-sm me-1"><i class="fa-solid fa-pen-to-square"></i></a>
                            <button class="btn btn-danger btn-sm" onclick="deleteItem({{ $item->id }})"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">-- ยังไม่มีการจัดทำแผนโครงการในระบบคลังปัจจุบัน --</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
{{-- ใส่ Pagination ไว้ท้ายตาราง --}}
<div class="d-flex justify-content-center mt-4">
    {{ $projects->links() }}
</div>
@endsection

@push('scripts')
<script>
    // 1. ประกาศฟังก์ชันไว้นอก document.ready เพื่อให้เรียกใช้งานได้จากทุกที่
    function applyFilters() {
        let yearId = $('#filter_fiscal_year').val();
        let deptId = $('#filter_department').val();
        
        let url = "{{ url('plan/projects') }}?";
        if (yearId !== 'all') url += `fiscal_year_id=${yearId}&`;
        if (deptId !== 'all') url += `department_id=${deptId}&`;
        
        window.location.href = url;
    }

    $(document).ready(function() {
        // Init Select2
        $('.select2-filter').select2({ 
            theme: 'bootstrap-5' 
        });

        // หากต้องการให้ Select2 เรียกฟังก์ชันนี้เมื่อมีการเปลี่ยนค่า
        // แนะนำให้ผูกผ่าน jQuery แทนการใช้ onchange ใน HTML จะดูสะอาดกว่า
        $('#filter_fiscal_year, #filter_department').on('change', function() {
            applyFilters();
        });
    });

    function deleteItem(id) {
        // ... (โค้ด deleteItem คงเดิม) ...
        Swal.fire({
            title: 'ยืนยันลบโครงการ?',
            text: "คำเตือน: ข้อมูลทั้งหมดจะถูกลบทิ้งถาวร",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'ยืนยันลบ'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('plan/projects') }}/${id}`,
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
                            timer: 1200
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