<form id="projectBudgetForm" action="{{ route('plan.projects.update-budget', $project->id) }}" method="POST">
    @csrf
    <div class="alert alert-primary border-0 shadow-sm mb-4 rounded-4 px-4 py-3 small text-primary bg-primary-subtle bg-opacity-10 d-flex align-items-center">
        <i class="fa-solid fa-circle-info fs-5 me-3"></i>
        <div>
            <strong>คำแนะนำ:</strong> ระบบจะแสดงงบประมาณที่หน่วยงานได้รับในปีงบประมาณ 
            <span class="badge bg-primary px-2 py-1 ms-1">{{ $project->fiscalYear->year  }} (พ.ศ.)</span>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-center text-uppercase fs-7 text-secondary fw-bold">
                    <tr>
                        <th class="py-3" width="10%">ปีแหล่งเงิน</th>
                        <th class="py-3 text-start">แหล่งเงิน / แผนงาน / หมวดงบ</th>
                        <th class="py-3 text-end" width="15%">งปม.ที่ได้รับจัดสรร (บาท)</th>
                        <th class="py-3 text-end" width="15%">จัดสรรลงโครงการแล้ว (บาท)</th>
                        <th class="py-3 text-end" width="15%">งปม.คงเหลือ (บาท)</th>
                        <th class="py-3 text-end" width="18%">ระบุงบลงโครงการ (บาท)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allocations as $item)
                        @php
                            $remaining = $item->total_amount - $item->used_amount;
                        @endphp
                        <tr class="budget-row">
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 fw-semibold">
                                    {{ ($item->budgetSource->fiscalYear->year ?? 0) }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->budgetSource->name }}</div>
                                <small class="text-muted">
                                    <i class="fa-solid fa-folder-open me-1 text-black-50"></i>{{ $item->program->name ?? '-' }} / 
                                    <i class="fa-solid fa-layer-group me-1 text-black-50"></i>{{ $item->category->name ?? '-' }}
                                </small>
                                <input type="hidden" name="allocation_id[]" value="{{ $item->id }}">
                            </td>
                            <td class="text-end fw-semibold text-secondary">
                                {{ number_format($item->total_amount, 2) }}
                            </td>
                            <td class="text-end text-muted">
                                {{ number_format($item->used_amount, 2) }}
                            </td>
                            
                            {{-- งบคงเหลือ --}}
                            <td class="text-end fw-bold {{ $remaining < 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($remaining, 2) }}
                            </td>
                            
                            <td>
                                <div class="input-group input-group-sm shadow-sm">
                                    <input type="text" 
                                        class="form-control text-end fw-bold text-primary budget-input" 
                                        value="{{ number_format($item->project_amount, 2) }}" 
                                        data-max="{{ $item->remaining_amount }}" 
                                        oninput="validateAndFormat(this)">
                                    <span class="input-group-text bg-light text-muted">บ.</span>
                                </div>
                                <input type="hidden" name="amount[]" class="budget-hidden" value="{{ $item->project_amount }}">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4 fst-italic">
                                <i class="fa-solid fa-inbox fs-4 mb-2 d-block text-black-50"></i>ไม่พบข้อมูลงบประมาณที่ได้รับจัดสรรใน-ปีงบประมาณนี้
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                
                {{-- แถวสรุปยอดรวมท้ายตาราง --}}
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="5" class="text-end py-3 text-dark">รวมงบประมาณที่จัดสรรลงในโครงการนี้ทั้งสิ้น:</td>
                        @php
                            $totalProjectBudget = $project->projectBudgetSources->sum('allocated_amount');
                        @endphp
                        <td class="text-end py-3 text-primary fs-6">
                            {{ number_format($totalProjectBudget, 2) }} <span class="small fw-normal text-muted">บาท</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="text-end mt-3">
        <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm rounded-3">
            <i class="fa-solid fa-save me-2"></i> บันทึกงบประมาณโครงการ
        </button>
    </div>
</form>

@push('scripts')
<script>
    function validateAndFormat(input) {
        let max = parseFloat($(input).data('max'));
        let rawValue = input.value.replace(/,/g, '');
        
        if (parseFloat(rawValue) > max) {
            Swal.fire({
                icon: 'warning',
                title: 'เกินงบประมาณ',
                text: 'ยอดที่ระบุเกินกว่างบที่หน่วยงานได้รับ (สูงสุด: ' + max.toLocaleString() + ')',
                timer: 2000
            });
            rawValue = max; 
        }

        $(input).closest('td').find('.budget-hidden').val(rawValue);

        let parts = rawValue.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        input.value = parts.join('.');
    }

    document.addEventListener("DOMContentLoaded", function() {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        @endif

        @if($errors->any())
            let errorList = '<ul class="text-start" style="list-style-type: none; padding-left: 0;">';
            @foreach($errors->all() as $error)
                errorList += '<li class="mb-1 text-danger"><i class="fa-solid fa-circle-exclamation me-2"></i>{{ $error }}</li>';
            @endforeach
            errorList += '</ul>';

            Swal.fire({
                icon: 'error',
                title: 'พบข้อผิดพลาด!',
                html: errorList,
                confirmButtonText: 'ตกลง',
                confirmButtonColor: '#d33'
            });
        @endif
    });
</script>
@endpush