@extends('layouts.app')

@push('styles')
<style>
    /* ตั้งค่าคอนเทนเนอร์ให้ตารางเลื่อน Scroll แนวนอนได้สมบูรณ์ */
    .report-table-container {
        font-size: 0.85rem;
        background: #fff;
        border-radius: 0.5rem;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .report-table {
        width: 100%;
        white-space: nowrap; /* ล็อกให้ข้อความทุกช่องไม่ตัดบรรทัด เพื่อรักษาโครงสร้างตาราง Excel */
        margin-bottom: 0 !important;
    }

    .report-table th, 
    .report-table td {
        vertical-align: middle;
        padding: 0.75rem 0.85rem;
        border-color: #dee2e6 !important;
    }
    
    .report-table th {
        text-align: center;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    /* โทนสีหัวข้อกลุ่ม */
    .bg-header-main { background-color: #59359a !important; color: white; } 
    .bg-header-budget { background-color: #e65c00 !important; color: white; } 
    .bg-header-income { background-color: #d63384 !important; color: white; } 
    .bg-header-total { background-color: #198754 !important; color: white; } 
    .bg-header-status { background-color: #0dcaf0 !important; color: #212529; } 
    
    /* สไตล์แถวรวมทั้งสิ้น (Footer) ให้ลอยเด่นและไม่จม */
    .report-table tfoot tr {
        background-color: #f1f3f5 !important;
        font-weight: 700;
        border-top: 2px solid #adb5bd !important;
        position: sticky;
        bottom: 0;
        z-index: 10;
    }

    .parent-department {
        font-weight: 600;
        color: #2c3e50;
    }
    
    .child-department {
        padding-left: 2rem !important;
        color: #495057;
        font-weight: normal;
    }

    .filter-card {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <!-- Header Title -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark m-0"><i class="fa-solid fa-chart-line text-primary me-2"></i>รายงานสรุปผลการดำเนินงานโครงการ</h3>
            <p class="text-muted small m-0 mt-1">ตารางสรุปผลการดำเนินงานโครงการตามแผนปฏิบัติการ จำแนกตามโครงสร้างหน่วยงาน</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm bg-white mb-4 rounded-4">
        <div class="card-body p-4">
            <!-- Filter Section -->
            <form action="{{ route('plan.reports.project_summary') }}" method="GET" class="filter-card p-3 rounded-4 mb-4">
                <div class="row align-items-end g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-secondary small">ปีงบประมาณ</label>
                        <select name="fiscal_year_id" class="form-select select2-filter">
                            <option value="all">-- ทุกปีงบประมาณ --</option>
                            @foreach($fiscalYears as $year)
                                <option value="{{ $year->id }}" {{ request('fiscal_year_id') == $year->id ? 'selected' : '' }}>
                                    ปี พ.ศ. {{ $year->year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary small">หน่วยงานสูงสุด (รวมหน่วยงานย่อย)</label>
                        <select name="parent_department_id" class="form-select select2-filter">
                            <option value="all">-- ทุกหน่วยงานสูงสุด --</option>
                            @foreach($parentDepartments as $pDept)
                                <option value="{{ $pDept->id }}" {{ request('parent_department_id') == $pDept->id ? 'selected' : '' }}>
                                    {{ $pDept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <!-- 🟢 แก้ไขปุ่ม Export Excel เป็นแบบนี้ -->
                        <a href="{{ route('plan.reports.project_summary.export.excel', request()->all()) }}" class="btn btn-success fw-bold ms-auto shadow-sm">
                            <i class="fa-solid fa-file-excel me-1"></i> Excel
                        </a>
                    </div>

                    <div class="col-md-5 d-flex gap-2">
                        <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> ค้นหา
                        </button>
                        <a href="{{ route('plan.reports.project_summary') }}" class="btn btn-outline-secondary px-3">
                            <i class="fa-solid fa-rotate-left me-1"></i> ล้างค่า
                        </a>
                    </div>
                </div>
            </form>

            <!-- Responsive Table Section -->
            <div class="table-responsive report-table-container pb-2">
                <table class="table table-bordered table-hover report-table">
                    <thead>
                        <tr>
                            <th rowspan="2" class="bg-header-main">ลำดับ</th>
                            <th rowspan="2" class="bg-header-main text-start ps-3">ชื่อหน่วยงาน</th>
                            <th rowspan="2" class="bg-header-main">วงเงินที่ได้รับจัดสรร</th>
                            
                            <th colspan="3" class="bg-header-budget">เงินงบประมาณ</th>
                            <th colspan="3" class="bg-header-income">เงินรายได้</th>
                            <th colspan="4" class="bg-header-total">ผลการใช้จ่ายรวม</th>
                            <th colspan="5" class="bg-header-status">สถานะโครงการ</th>
                        </tr>
                        <tr>
                            <!-- เงินงบประมาณ -->
                            <th class="bg-header-budget">วงเงินจัดสรร</th>
                            <th class="bg-header-budget">ผลเบิกจ่าย</th>
                            <th class="bg-header-budget">คงเหลือ</th>
                            
                            <!-- เงินรายได้ -->
                            <th class="bg-header-income">วงเงินจัดสรร</th>
                            <th class="bg-header-income">ผลเบิกจ่าย</th>
                            <th class="bg-header-income">คงเหลือ</th>
                            
                            <!-- ผลการใช้จ่ายรวม -->
                            <th class="bg-header-total">วงเงินทั้งหมด</th>
                            <th class="bg-header-total">เบิกจ่ายรวม</th>
                            <th class="bg-header-total">คงเหลือรวม</th>
                            <th class="bg-header-total">ร้อยละเบิกจ่าย</th>
                            
                            <!-- สถานะโครงการ -->
                            <th class="bg-header-status">จำนวน (รวม)</th>
                            <th class="bg-header-status">แล้วเสร็จ</th>
                            <th class="bg-header-status">ดำเนินการ</th>
                            <th class="bg-header-status">ยังไม่ทำ</th>
                            <th class="bg-header-status">ยกเลิก</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $index = 1; @endphp
                        @forelse($reportData as $row)
                            <tr class="{{ $row->is_parent ? 'table-light parent-department' : '' }}">
                                <td class="text-center text-muted">{{ $row->is_parent ? $index++ : '' }}</td>
                                <td class="{{ $row->is_parent ? 'ps-3' : 'child-department' }}">
                                    @if(!$row->is_parent)
                                        <i class="fa-solid fa-angle-right text-muted me-1 small"></i>
                                    @endif
                                    {{ $row->department_name }}
                                </td>
                                <td class="text-end fw-semibold text-dark">{{ number_format($row->total_allocated, 2) }}</td>
                                
                                <!-- เงินงบประมาณ -->
                                <td class="text-end text-secondary">{{ number_format($row->budget_allocated, 2) }}</td>
                                <td class="text-end text-secondary">{{ number_format($row->budget_paid, 2) }}</td>
                                <td class="text-end {{ $row->budget_remaining < 0 ? 'text-danger fw-bold' : 'text-secondary' }}">
                                    {{ number_format($row->budget_remaining, 2) }}
                                </td>
                                
                                <!-- เงินรายได้ -->
                                <td class="text-end text-secondary">{{ number_format($row->income_allocated, 2) }}</td>
                                <td class="text-end text-secondary">{{ number_format($row->income_paid, 2) }}</td>
                                <td class="text-end {{ $row->income_remaining < 0 ? 'text-danger fw-bold' : 'text-secondary' }}">
                                    {{ number_format($row->income_remaining, 2) }}
                                </td>
                                
                                <!-- ผลการใช้จ่ายรวม -->
                                <td class="text-end fw-bold text-dark">{{ number_format($row->total_allocated, 2) }}</td>
                                <td class="text-end fw-bold text-success">{{ number_format($row->total_paid, 2) }}</td>
                                <td class="text-end fw-bold {{ $row->total_remaining < 0 ? 'text-danger' : 'text-primary' }}">
                                    {{ number_format($row->total_remaining, 2) }}
                                </td>
                                <td class="text-end fw-bold">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        {{ number_format($row->percent_paid, 2) }}%
                                    </span>
                                </td>
                                
                                <!-- สถานะโครงการ -->
                                <td class="text-center fw-bold">{{ $row->total_projects }}</td>
                                <td class="text-center text-success fw-semibold">{{ $row->completed }}</td>
                                <td class="text-center text-warning fw-semibold text-dark">{{ $row->processing }}</td>
                                <td class="text-center text-muted">{{ $row->waiting }}</td>
                                <td class="text-center text-danger">{{ $row->cancelled }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="18" class="text-center text-muted py-5 fst-italic">
                                    <i class="fa-solid fa-inbox fs-3 mb-2 d-block text-black-50"></i> ไม่พบข้อมูลรายงานการดำเนินงานตามเงื่อนไขที่เลือก
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <!-- Footer สรุปผลรวมทั้งหมด -->
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-end py-3 pe-3 text-dark">รวมทั้งสิ้น:</td>
                            <td class="text-end text-dark">{{ number_format($totals->total_allocated_all, 2) }}</td>
                            
                            <td class="text-end">{{ number_format($totals->budget_allocated, 2) }}</td>
                            <td class="text-end">{{ number_format($totals->budget_paid, 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($totals->budget_remaining, 2) }}</td>
                            
                            <td class="text-end">{{ number_format($totals->income_allocated, 2) }}</td>
                            <td class="text-end">{{ number_format($totals->income_paid, 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($totals->income_remaining, 2) }}</td>
                            
                            <td class="text-end text-dark">{{ number_format($totals->total_allocated, 2) }}</td>
                            <td class="text-end text-success">{{ number_format($totals->total_paid, 2) }}</td>
                            <td class="text-end text-primary">{{ number_format($totals->total_remaining, 2) }}</td>
                            <td class="text-end">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                    {{ number_format($totals->percent_paid, 2) }}%
                                </span>
                            </td>
                            
                            <td class="text-center fw-bold">{{ $totals->total_projects }}</td>
                            <td class="text-center text-success fw-bold">{{ $totals->completed }}</td>
                            <td class="text-center text-warning text-dark fw-bold">{{ $totals->processing }}</td>
                            <td class="text-center text-muted fw-bold">{{ $totals->waiting }}</td>
                            <td class="text-center text-danger fw-bold">{{ $totals->cancelled }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($.fn.select2) {
            $('.select2-filter').select2({ theme: 'bootstrap-5', width: '100%' });
        }
    });
</script>
@endpush