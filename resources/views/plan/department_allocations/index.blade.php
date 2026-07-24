@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span>การจัดสรรงบประมาณรายหน่วยงาน</span>
        <button class="btn btn-light btn-sm fw-bold" onclick="openAllocationModal()">
            <i class="fa-solid fa-plus me-1"></i> จัดสรรงบประมาณ
        </button>
    </div>
    <div class="card-body">
        {{-- Filter Row --}}
        <form action="" method="GET" class="row g-3 mb-4 bg-light p-2 rounded">
            <div class="col-md-3">
                <label class="form-label fw-bold">ปีงบที่ใช้ทำโครงการ *</label>
                <select name="fiscal_year_id" onchange="this.form.submit()" class="form-select">
                    <option value="">-- ทุกปีงบประมาณ --</option>
                    @foreach($fiscalYears as $fy)
                        <option value="{{ $fy->id }}" {{ $selectedFiscalYearId == $fy->id ? 'selected' : '' }}>
                            {{ $fy->year }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">หน่วยงาน</label>
                <select name="department_id" onchange="this.form.submit()" class="form-select">
                    <option value="">-- ทุกหน่วยงาน --</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>หน่วยงาน</th>
                    <th>ปีแหล่งเงิน</th> {{-- เพิ่มคอลัมน์ใหม่ --}}
                    <th>แหล่งเงิน</th>
                    <th>แผนงาน</th>
                    <th>หมวดงบ</th>
                    <th class="text-center">ยอดจัดสรร (บาท)</th>
                    <th class="text-center" style="width: 120px;">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp
                @foreach($allocations as $a)
                 @php $grandTotal += $a->total_amount; @endphp
                    <tr>
                        <td>{{ $a->department->name }}</td>
                        {{-- แสดงปีของแหล่งเงินต้นทาง --}}
                        <td class="text-center">{{ ($a->budgetSource->fiscalYear->year ?? 0) }}</td>
                        <td>{{ $a->budgetSource->name }}</td>
                        <td>{{ $a->program->name ?? '-' }}</td>
                        <td>{{ $a->category->name ?? '-' }}</td>
                        <td class="text-end fw-bold text-primary">{{ number_format($a->total_amount, 2) }}</td>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm" onclick="editAllocation({{ $a->id }})"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn btn-danger btn-sm" onclick="deleteAllocation({{ $a->id }})"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
                @if($allocations->isEmpty())
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">ไม่พบข้อมูลการจัดสรรงบประมาณ</td>
                </tr>
                @endif
            </tbody>
            {{-- เพิ่มส่วนสรุปยอดรวมที่นี่ --}}
            <tfoot class="table-group-divider">
                <tr class="table-secondary">
                    <td colspan="5" class="text-end fw-bold">รวมยอดจัดสรรทั้งหมด</td>
                    <td class="text-end fw-bold text-success fs-5">{{ number_format($grandTotal, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- โหลด Modal มาใช้งาน --}}
@include('plan.department_allocations._modal')

@endsection

@push('scripts')
<script>
    // เปิด Modal โหมดเพิ่มข้อมูล (เคลียร์ค่าเก่า)
    function openAllocationModal() {
        $('#allocationForm')[0].reset();
        $('#allocation_id').val('');
        $('#total_amount_display').val(''); 
        $('#total_amount').val('');
        $('#budget_source_id').html('<option value="">-- เลือกแหล่งเงิน --</option>');
        $('#program_id').html('<option value="">-- เลือกแผนงาน --</option>');
        $('#category_id').html('<option value="">-- เลือกหมวดงบ --</option>');
        $('#modalTitle').text('จัดสรรงบประมาณรายหน่วยงาน');
        $('#allocationModal').modal('show');
    }

    // เปิด Modal โหมดแก้ไขข้อมูล
    function editAllocation(id) {
        $.get('/plan/department-allocations/' + id + '/edit', function(res) {
            $('#allocation_id').val(res.allocation.id);
            $('#fiscal_year_id').val(res.allocation.fiscal_year_id);
            $('#source_fiscal_year_id').val(res.source_fiscal_year_id);
            $('#department_id').val(res.allocation.department_id);
            
            let amt = res.allocation.total_amount;
            $('#total_amount').val(amt);
            $('#total_amount_display').val(formatNumberWithComma(parseFloat(amt).toFixed(2)));

            let sourceHtml = '<option value="">-- เลือกแหล่งเงิน --</option>';
            res.sources.forEach(s => sourceHtml += `<option value="${s.id}" ${s.id == res.allocation.budget_source_id ? 'selected' : ''}>${s.name}</option>`);
            $('#budget_source_id').html(sourceHtml);

            let programHtml = '<option value="">-- เลือกแผนงาน --</option>';
            res.programs.forEach(p => programHtml += `<option value="${p.id}" ${p.id == res.allocation.program_id ? 'selected' : ''}>${p.name}</option>`);
            $('#program_id').html(programHtml);

            let catHtml = '<option value="">-- เลือกหมวดงบ --</option>';
            res.categories.forEach(c => catHtml += `<option value="${c.id}" ${c.id == res.allocation.category_id ? 'selected' : ''}>${c.name}</option>`);
            $('#category_id').html(catHtml);

            $('#modalTitle').text('แก้ไขการจัดสรรงบประมาณ');
            $('#allocationModal').modal('show');
        });
    }

    // ลบข้อมูล
    function deleteAllocation(id) {
        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: "คุณต้องการลบข้อมูลการจัดสรรนี้ใช่หรือไม่!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/plan/department-allocations/' + id,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire('ลบแล้ว!', res.message, 'success').then(() => { location.reload(); });
                    }
                });
            }
        });
    }

    // ฟังก์ชันสำหรับใส่ลูกน้ำ
    function formatNumberWithComma(num) {
        if (!num) return '';
        let parts = num.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ","); 
        return parts.join(".");
    }

    $(document).ready(function() {
        // Cascade: เปลี่ยนปีงบ -> ดึงแหล่งเงิน
        

        // Cascade: เปลี่ยนแหล่งเงิน -> ดึงแผนงาน
        $('#budget_source_id').on('change', function() {
            let sourceId = $(this).val();
            $('#program_id').html('<option value="">-- เลือกแผนงาน --</option>');
            $('#category_id').html('<option value="">-- เลือกหมวดงบ --</option>');
            
            if(sourceId) {
                $.get('/plan/get-programs-by-source/' + sourceId, function(data) {
                    let options = '<option value="">-- เลือกแผนงาน --</option>';
                    data.forEach(item => options += `<option value="${item.id}">${item.name}</option>`);
                    $('#program_id').html(options);
                });
            }
        });

        // Cascade: เปลี่ยนแผนงาน -> ดึงหมวดงบ
        $('#program_id').on('change', function() {
            let programId = $(this).val();
            $('#category_id').html('<option value="">-- เลือกหมวดงบ --</option>');
            
            if(programId) {
                $.get('/plan/get-categories-by-program/' + programId, function(data) {
                    let options = '<option value="">-- เลือกหมวดงบ --</option>';
                    data.forEach(item => options += `<option value="${item.id}">${item.name}</option>`);
                    $('#category_id').html(options);
                });
            }
        });

        // ดักจับเมื่อผู้ใช้พิมพ์ตัวเลข
        $('#total_amount_display').on('input', function() {
            let rawValue = $(this).val().replace(/[^0-9.]/g, '');
            let parts = rawValue.split('.');
            if (parts.length > 2) {
                rawValue = parts[0] + '.' + parts.slice(1).join('');
            }
            $('#total_amount').val(rawValue);
            $(this).val(formatNumberWithComma(rawValue));
        });

        // เมื่อหลุดโฟกัสให้เติม .00 อัตโนมัติ
        $('#total_amount_display').on('blur', function() {
            let val = $('#total_amount').val();
            if (val && !isNaN(val)) {
                let floatVal = parseFloat(val).toFixed(2);
                $('#total_amount').val(floatVal);
                $(this).val(formatNumberWithComma(floatVal));
            }
        });

        $('#source_fiscal_year_id').on('change', function() {
            let fyId = $(this).val();
            
            // ล้างค่าที่เกี่ยวข้องทั้งหมด
            $('#budget_source_id').html('<option value="">-- เลือกแหล่งเงิน --</option>');
            $('#program_id').html('<option value="">-- เลือกแผนงาน --</option>');
            $('#category_id').html('<option value="">-- เลือกหมวดงบ --</option>');
            
            if(fyId) {
                $.get('/plan/get-sources-by-year/' + fyId, function(data) {
                    let options = '<option value="">-- เลือกแหล่งเงิน --</option>';
                    data.forEach(item => options += `<option value="${item.id}">${item.name}</option>`);
                    $('#budget_source_id').html(options);
                });
            }
        });
    });
</script>
{{-- ================= ส่วนที่แสดงการแจ้งเตือนจาก PHP ================= --}}
<script>
    // ตรวจสอบสถานะการบันทึกสำเร็จ
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'สำเร็จ',
            text: "{{ session('success') }}",
            timer: 2000,
            showConfirmButton: false
        });
    @endif

    // ตรวจสอบกรณีเกิด Error จาก Validation
    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'พบข้อผิดพลาด!',
            html: '<ul class="text-start">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>'
        });
    @endif

    // ... (ส่วนฟังก์ชัน openAllocationModal, editAllocation, deleteAllocation ของเดิมของคุณ) ...
</script>
@endpush