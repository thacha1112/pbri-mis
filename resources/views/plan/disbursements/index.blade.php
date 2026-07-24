@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm bg-white mb-4 rounded-4">
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
            @if(auth()->user()->hasAnyRoleIds([1,2]))
                <div class="col-md-4">
                    <label class="form-label fw-bold text-secondary">ค้นหาตามหน่วยงาน</label>
                    <select class="form-select select2-filter" id="filter_department" onchange="applyFilters()">
                        <option value="all">-- ทุกหน่วยงาน --</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm bg-white rounded-4 overflow-hidden">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
        <h5 class="m-0 fw-bold text-primary"><i class="fa-solid fa-money-bill-transfer me-2"></i>ระบบเบิกจ่ายงบประมาณ (Disbursements)</h5>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-center fw-bold text-uppercase fs-7 text-secondary">
                    <tr>
                        <th width="5%" class="py-3">ลำดับ</th>
                        <th width="15%" class="py-3">รหัสโครงการ</th>
                        <th width="30%" class="py-3 text-start">ชื่อโครงการ / หน่วยงานต้นสังกัด</th>
                        <th width="12%" class="py-3 text-end">งบประมาณโครงการ</th>
                        <th width="13%" class="py-3 text-end">เบิกจ่ายแล้ว</th>
                        <th width="13%" class="py-3 text-end">คงเหลือ</th>
                        <th width="12%" class="py-3 text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $index => $item)
                        @php
                            // คำนวณงบประมาณรวมของโครงการ
                            $totalProjectBudget = $item->projectBudgetSources->sum('allocated_amount');
                            
                            // คำนวณยอดเบิกจ่ายสะสมทั้งหมด (1 LV)
                            $totalPaid = 0;
                            foreach($item->activities as $act) {
                                foreach($act->budgets as $budget) {
                                    $totalPaid += $budget->payments->sum('amount');
                                }
                            }
                            
                            $balance = $totalProjectBudget - $totalPaid;
                        @endphp
                    <tr class="border-bottom">
                        <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                        <td class="text-center">
                            <span class="badge bg-dark px-2 py-2 font-mono fs-7">{{ $item->project_code }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark fs-6">{{ $item->name }}</div>
                            <small class="text-secondary">
                                <i class="fa-solid fa-building me-1"></i>สังกัด: {{ $item->department?->name }} | ปี พ.ศ. {{ $item->fiscalYear?->year }}
                            </small>
                        </td>
                        <td class="text-end fw-semibold text-dark">
                            {{ number_format($totalProjectBudget, 2) }}
                        </td>
                        <td class="text-end fw-semibold text-warning">
                            {{ number_format($totalPaid, 2) }}
                        </td>
                        <td class="text-end fw-bold {{ $balance < 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($balance, 2) }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('plan.disbursements.show', $item->id) }}" class="btn btn-outline-primary btn-sm fw-bold shadow-sm">
                                <i class="fa-solid fa-hand-holding-dollar me-1"></i> จัดการเบิกจ่าย
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5 fst-italic">
                            <i class="fa-solid fa-folder-open fs-3 mb-2 d-block text-black-50"></i>ยังไม่มีโครงการที่พร้อมสำหรับการเบิกจ่าย
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $projects->links() }}
</div>
@endsection

@push('scripts')
<script>
    function applyFilters() {
        let yearId = $('#filter_fiscal_year').val();
        let deptId = $('#filter_department').val();
        
        let url = "{{ route('plan.disbursements.index') }}?";
        if (yearId !== 'all') url += `fiscal_year_id=${yearId}&`;
        if (deptId !== 'all') url += `department_id=${deptId}&`;
        
        window.location.href = url;
    }

    $(document).ready(function() {
        $('.select2-filter').select2({ theme: 'bootstrap-5' });
    });
</script>
@endpush