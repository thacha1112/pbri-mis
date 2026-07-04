<form id="projectBudgetForm">
    @csrf
    <div class="alert alert-success border-0 mb-3 small">
        <i class="fa-solid fa-sack-dollar me-2"></i> กรุณาระบุแหล่งเงินงบประมาณ
        
    </div>

    <div id="budget-rows">
        @foreach($project->projectBudgetSources as $budget)
        <div class="row g-2 mb-3 budget-row border-bottom pb-3" id="row_{{ $budget->id }}">
            <div class="col-md-3">
                <select class="form-select source-select" name="source_id[]" {!! $project->total_allocated_budget > 0 ? 'disabled':'' !!} required>
                    <option value="">-- แหล่งเงิน --</option>
                    @foreach($budgetSources as $s)
                        <option value="{{ $s->id }}" {{ $budget->budget_source_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select program-select" name="program_id[]" {!! $project->total_allocated_budget > 0 ? 'disabled':'' !!} {{ !$budget->program_id ? 'disabled' : '' }}>
                    <option value="">-- แผนงาน (ถ้ามี) --</option>
                    @foreach($programs->where('budget_source_id', $budget->budget_source_id) as $p)
                        <option value="{{ $p->id }}" {{ (int)$budget->program_id === (int)$p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select category-select" name="category_id[]" {!! $project->total_allocated_budget > 0 ? 'disabled':'' !!} {{ !$budget->category_id ? 'disabled' : '' }}>
                    <option value="">-- หมวดงบ (ถ้ามี) --</option>
                    @foreach($budgetCategories->where('program_id', $budget->program_id) as $c)
                        <option value="{{ $c->id }}" {{ (int)$budget->category_id === (int)$c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                
                <input type="number" class="form-control" name="amount[]" value="{{ $budget->allocated_amount }}" {!! $project->total_allocated_budget > 0 ? 'disabled':'' !!} step="0.01" required>
                
            </div>
            <div class="col-md-1">
                @if(!$project->total_allocated_budget > 0)
                    <button type="button" class="btn btn-danger btn-sm" onclick="$(this).closest('.budget-row').remove()"><i class="fa-solid fa-trash"></i></button>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @if(!$project->total_allocated_budget > 0)
        <button type="button" class="btn btn-outline-success btn-sm" onclick="addBudgetRow()">+ เพิ่มแหล่งเงิน</button>
        <div class="text-end mt-3">
            <button type="submit" class="btn btn-primary">บันทึกข้อมูลแหล่งเงิน</button>
        </div>
    @endif
    <div class="row mt-3 p-2 bg-light rounded">
        <div class="col-md-9 text-end fw-bold">ยอดเงินรวมทั้งหมด:</div>
        <div class="col-md-2">
            <input type="text" id="total-amount" class="form-control fw-bold text-primary" value="0.00" readonly>
        </div>
        <div class="col-md-1">บาท</div>
    </div>
</form>

@push('scripts')
<script>
    const masterData = {
        programs: @json($programs),
        categories: @json($budgetCategories)
    };

    // 1. ฟังก์ชันคำนวณยอดรวม
    function calculateTotal() {
        let total = 0;
        $('input[name="amount[]"]').each(function() {
            let val = parseFloat($(this).val());
            if (!isNaN(val)) total += val;
        });
        $('#total-amount').val(total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }

    calculateTotal();

    // 2. เรียกใช้เมื่อมีการพิมพ์ค่าเงิน หรือ ลบแถว
    $(document).on('input', 'input[name="amount[]"]', calculateTotal);
    $(document).on('click', '.btn-danger', function() {
        $(this).closest('.budget-row').remove();
        calculateTotal();
    });

    function addBudgetRow() {
        let rowId = 'row_' + Date.now();
        let html = `
        <div class="row g-2 mb-3 budget-row align-items-end border-bottom pb-3" id="${rowId}">
            <div class="col-md-3">
                <select class="form-select source-select" name="source_id[]" required>
                    <option value="">-- เลือกแหล่งเงิน --</option>
                    @foreach($budgetSources as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select program-select" name="program_id[]" disabled>
                    <option value="">-- แผนงาน --</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select category-select" name="category_id[]" disabled>
                    <option value="">-- หมวดงบ --</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control" name="amount[]" step="0.01" required>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm" onclick="$('#${rowId}').remove()"><i class="fa-solid fa-trash"></i></button>
            </div>
        </div>`;
        $('#budget-rows').append(html);
    }

    // กรองแผนงาน
    $(document).on('change', '.source-select', function() {
        let val = $(this).val();
        let pSelect = $(this).closest('.budget-row').find('.program-select');
        pSelect.prop('disabled', !val).empty().append('<option value="">-- แผนงาน --</option>');
        masterData.programs.filter(p => p.budget_source_id == val).forEach(p => {
            pSelect.append(`<option value="${p.id}">${p.name}</option>`);
        });
    });

    // กรองหมวดงบ
    $(document).on('change', '.program-select', function() {
        let val = $(this).val();
        let cSelect = $(this).closest('.budget-row').find('.category-select');
        cSelect.prop('disabled', !val).empty().append('<option value="">-- หมวดงบ --</option>');
        masterData.categories.filter(c => c.program_id == val).forEach(c => {
            cSelect.append(`<option value="${c.id}">${c.name}</option>`);
        });
    });

    // 🔥 ส่วนที่สำคัญ: การสั่ง Submit ฟอร์มด้วย AJAX
    $('#projectBudgetForm').on('submit', function(e) {
        e.preventDefault();

            // ปลดล็อกตัวที่ disabled เพื่อให้ส่งค่า null ไปหา Controller
        $(this).find('select').prop('disabled', false);
        
        // ตรวจสอบข้อมูลก่อนส่ง (ถ้าเลือกแหล่งเงินแต่ไม่มีแผนงานย่อย ก็บันทึกได้)
        let formData = $(this).serialize();

        $.ajax({
            url: `{{ url('plan/projects') }}/${currentProjectId}/update-budget`,
            type: 'POST',
            data: formData,
            success: function(res) {
                Swal.fire({ 
                    icon: 'success', 
                    title: res.message, 
                    timer: 1500,
                    showConfirmButton: false 
                });
                // ปลดล็อกแท็บกิจกรรม
                $('#activities-tab').removeClass('disabled text-black-50 bg-light');
            },
            error: function(err) {
                console.error(err);
                Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาดในการบันทึก' });
            }
        });
    });
</script>
@endpush