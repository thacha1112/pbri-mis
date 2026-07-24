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

<div class="card border-0 shadow-sm bg-white mb-4 rounded-4">
    <div class="card-body p-4">
        <h4 class="fw-bold text-primary mb-3">ภาพรวม งปม.ที่หน่วยงานได้รับจัดสรร และจัดสรรลงโครงการ/กิจกรรมแล้ว</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle mb-0">
                <thead class="table-light text-center fw-bold">
                    <tr>
                        <th class="py-3">ปีแหล่งเงิน</th>
                        <th class="py-3 text-start">แหล่งเงิน / แผนงาน / หมวดงบ</th>
                        <th class="py-3 text-end" width="160">งปม.ที่ได้รับจัดสรร (บาท)</th>
                        <th class="py-3 text-end" width="160">จัดสรรลงโครงการแล้ว (บาท)</th>
                        <th class="py-3 text-end" width="160">งปม.ที่ได้รับจัดสรรคงเหลือ (บาท)</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $total_amount = 0;
                        $sumTotalAllocated = 0;
                        $balance = 0;
                    @endphp
                    @forelse($allocations as $item)
                        <tr class="budget-row">
                            <td class="text-center fw-bold text-secondary">
                                {{ ($item->budgetSource->fiscalYear->year ?? 0) > 2500 ? $item->budgetSource->fiscalYear->year : (($item->budgetSource->fiscalYear->year ?? 0) + 543) }}
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->budgetSource->name }}</div>
                                <small class="text-muted">
                                    <i class="fa-solid fa-folder-open me-1 text-black-50"></i>{{ $item->program->name ?? '-' }} / 
                                    <i class="fa-solid fa-layer-group me-1 text-black-50"></i>{{ $item->category->name ?? '-' }}
                                </small>
                            </td>
                            <td class="text-end fw-semibold text-secondary">
                                {{ number_format($item->total_amount, 2) }}
                                @php $total_amount += $item->total_amount; @endphp
                            </td>
                            <td class="text-end fw-semibold text-dark">
                                @php
                                    $result = DB::select("
                                        SELECT SUM(pbs.allocated_amount) as total_allocated 
                                        FROM plan_project_budget_sources pbs
                                        WHERE pbs.department_allocation_id = " . $item->id 
                                    );

                                    $totalAllocated = $result[0]->total_allocated ?? 0;
                                    $currentAllocated = $totalAllocated - $item->used_amount;
                                    $sumTotalAllocated += $currentAllocated;
                                @endphp
                                {{ number_format($currentAllocated, 2) }}
                            </td>
                            <td class="text-end fw-bold text-primary">
                                @php
                                    $itemBalance = $item->total_amount - $totalAllocated;
                                    $balance += $itemBalance;
                                @endphp
                                {{ number_format($itemBalance, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4 fst-italic">
                                <i class="fa-solid fa-inbox fs-4 mb-2 d-block text-black-50"></i>ไม่พบข้อมูลงบประมาณที่ได้รับจัดสรรในรอบปีงบประมาณนี้
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <!-- แถวรวมสรุปยอดเงินไว้ตรงท้ายตาราง -->
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="2" class="text-end py-3 text-dark">รวมทั้งสิ้น:</td>
                        <td class="text-end py-3 text-secondary">{{ number_format($total_amount, 2) }}</td>
                        <td class="text-end py-3 text-dark">{{ number_format($sumTotalAllocated, 2) }}</td>
                        <td class="text-end py-3 text-primary fs-6">{{ number_format($balance, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm bg-white rounded-4 overflow-hidden">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
        <h5 class="m-0 fw-bold text-dark"><i class="fa-solid fa-file-invoice-dollar text-success me-2"></i>ระบบทะเบียนโครงการ & แผนงบประมาณ</h5>
        <a href="{{ url('plan/projects/create') }}" class="btn btn-primary btn-sm fw-bold px-3 py-2"><i class="fa-solid fa-plus me-1"></i> จัดทำโครงการใหม่</a>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-light  text-center fw-bold">
                    <tr>
                        <th width="6%" class="py-3">ลำดับ</th>
                        <th width="15%" class="py-3">รหัสโครงการ</th>
                        <th width="49%" class="py-3 text-start">ชื่อโครงการ / หน่วยงานต้นสังกัด</th>
                        <th width="18%" class="py-3">ผู้รับผิดชอบโครงการ</th>
                        <th width="12%" class="py-3">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $index => $item)
                    <!-- บรรทัดที่ 1: ลำดับ, รหัสโครงการ, ชื่อโครงการ / หน่วยงานต้นสังกัด, จัดการ -->
                    <tr id="row-{{ $item->id }}" class="project-row border-top" data-year-id="{{ $item->fiscal_year_id }}">
                        <td class="text-center fw-bold text-muted" rowspan="2">{{ $index + 1 }}</td>
                        <td class="text-center">
                            <span class="badge bg-dark px-2 py-2 font-mono fs-7">{{ $item->project_code }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark fs-6">{{ $item->name }}</div>
                            <small class="text-secondary">
                                <i class="fa-solid fa-building me-1"></i>สังกัด: {{ $item->department?->name }} | ปี พ.ศ. {{ $item->fiscalYear?->year }}
                            </small>
                        </td>
                        <td class="text-center">
                            <div class="fw-semibold text-muted fs-7"><i class="fa-solid fa-user me-1"></i>{{ $item->personnel?->firstname." ".$item->personnel?->lastname }}</div>
                        </td>
                        <td class="text-center" rowspan="2">
                            <div class="btn-group shadow-sm">
                                <a href="{{ url('plan/projects/'.$item->id.'/edit') }}" class="btn btn-warning btn-sm text-dark" title="แก้ไข">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <button class="btn btn-danger btn-sm" onclick="deleteItem({{ $item->id }})" title="ลบ">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- บรรทัดที่ 2: แสดงแหล่งเงินที่ได้จัดสรรในโครงการนั้น -->
                    <tr class="table-light bg-opacity-50">
                        <td colspan="3" class="py-2 px-3">
                            <div class="small fw-semibold text-primary mb-1"><i class="fa-solid fa-wallet me-1"></i> รายการแหล่งงบประมาณที่จัดสรรในโครงการ:</div>
                            @if($item->projectBudgetSources->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered bg-white mb-0 align-middle fs-7">
                                        <thead class="table-secondary text-center">
                                            <tr>
                                                <th width="12%">ปีแหล่งเงิน</th>
                                                <th>แหล่งเงิน / แผนงาน / หมวดงบ</th>
                                                <th width="20%">จัดสรรลงโครงการ (บาท)</th>
                                                <th width="20%">จัดสรรลงกิจกรรมแล้ว (บาท)</th>
                                                <th width="20%">คงเหลือ (บาท)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($item->projectBudgetSources as $pbs)
                                                @php
                                                    $consumedActivity = 0;
                                                    foreach($item->activities as $act) {
                                                        $actB = $act->budgets->where('project_budget_source_id', $pbs->id)->first();
                                                        if($actB) {
                                                            $consumedActivity += (float)$actB->amount;
                                                        }
                                                    }
                                                    $sourceBalance = (float)$pbs->allocated_amount - $consumedActivity;
                                                @endphp
                                                <tr>
                                                    <td class="text-center">
                                                        <span class="badge bg-secondary">
                                                            {{ ($pbs->budgetSource->fiscalYear->year ?? 0) > 2500 ? $pbs->budgetSource->fiscalYear->year : ($pbs->budgetSource->fiscalYear->year + 543) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="fw-bold text-dark">{{ $pbs->budgetSource->name ?? '-' }}</span><br>
                                                        <span class="text-muted" style="font-size: 0.75rem;">{{ $pbs->program->name ?? '-' }} / {{ $pbs->category->name ?? '-' }}</span>
                                                    </td>
                                                    <td class="text-end fw-semibold text-dark">
                                                        {{ number_format($pbs->allocated_amount, 2) }}
                                                    </td>
                                                    <td class="text-end fw-semibold text-secondary">
                                                        {{ number_format($consumedActivity, 2) }}
                                                    </td>
                                                    <td class="text-end fw-bold {{ $sourceBalance < 0 ? 'text-danger' : ($sourceBalance == 0 ? 'text-muted' : 'text-success') }}">
                                                        {{ number_format($sourceBalance, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <span class="text-muted fst-italic small"><i class="fa-solid fa-circle-exclamation me-1"></i> ยังไม่ได้กำหนดแหล่งงบประมาณสำหรับโครงการนี้</span>
                            @endif
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

<div class="d-flex justify-content-center mt-4">
    {{ $projects->links() }}
</div>
@endsection

@push('scripts')
<script>
    function applyFilters() {
        let yearId = $('#filter_fiscal_year').val();
        let deptId = $('#filter_department').val();
        
        let url = "{{ url('plan/projects') }}?";
        if (yearId !== 'all') url += `fiscal_year_id=${yearId}&`;
        if (deptId !== 'all') url += `department_id=${deptId}&`;
        
        window.location.href = url;
    }

    $(document).ready(function() {
        $('.select2-filter').select2({ 
            theme: 'bootstrap-5' 
        });

        $('#filter_fiscal_year, #filter_department').on('change', function() {
            applyFilters();
        });
    });

    function deleteItem(id) {
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
                        }).then(() => { location.reload(); });
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON ? xhr.responseJSON.message : 'เกิดข้อผิดพลาด';
                        Swal.fire({
                            icon: 'error',
                            title: 'ไม่สามารถลบได้',
                            text: msg,
                            confirmButtonColor: '#6c757d'
                        });
                    }
                });
            }
        });
    }
</script>
@endpush