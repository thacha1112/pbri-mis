<form id="alignmentForm">
    @csrf
    <div class="alert alert-info border-0 shadow-sm mb-4 d-flex align-items-center">
        <i class="fa-solid fa-diagram-project fs-4 me-3 text-primary"></i>
        <div>
            <div class="fw-bold fs-6">เชื่อมโยงมิติความสอดคล้องแผนยุทธศาสตร์สถาบัน</div>
            <small class="text-secondary">ระบบคัดกรองเป้าหมายยุทธศาสตร์เฉพาะของ ปีงบประมาณ พ.ศ. {{ $project->fiscalYear?->year }} ตามที่ระบุไว้ในข้อมูลทั่วไป</small>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">1. พันธกิจสถาบัน <span class="text-danger">*</span></label>
        <select class="form-select select2-alignment" id="modal_mission_id" name="mission_id" required>
            <option value="">-- กรุณาเลือกพันธกิจองค์กร --</option>
            @foreach(App\Models\Plan\Mission::where('fiscal_year_id', $project->fiscal_year_id)->where('status', 'active')->get() as $m)
                <option value="{{ $m->id }}" {{ $project->mission_id == $m->id ? 'selected' : '' }}>
                    {{ $m->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">2. ประเด็นยุทธศาสตร์ <span class="text-danger">*</span></label>
        <select class="form-select select2-alignment" id="modal_strategic_issue_id" name="strategic_issue_id" required>
            <option value="">-- กรุณาเลือกพันธกิจด้านบนก่อน --</option>
            @foreach(App\Models\Plan\StrategicIssue::where('status', 'active')
                ->whereIn('mission_id', App\Models\Plan\Mission::where('fiscal_year_id', $project->fiscal_year_id)->pluck('id'))
                ->get() as $si)
                <option value="{{ $si->id }}" data-parent-id="{{ $si->mission_id }}" {{ $project->strategic_issue_id == $si->id ? 'selected' : '' }}>
                    {{ $si->code ? $si->code . ': ' : '' }}{{ $si->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">3. เป้าประสงค์องค์กร <span class="text-danger">*</span></label>
        <select class="form-select select2-alignment" id="modal_goal_id" name="goal_id" required>
            <option value="">-- กรุณาเลือกประเด็นยุทธศาสตร์ด้านบนก่อน --</option>
            @foreach(App\Models\Plan\Goal::where('status', 'active')
                ->whereIn('strategic_issue_id', App\Models\Plan\StrategicIssue::whereIn('mission_id', App\Models\Plan\Mission::where('fiscal_year_id', $project->fiscal_year_id)->pluck('id'))->pluck('id'))
                ->get() as $g)
                <option value="{{ $g->id }}" data-parent-id="{{ $g->strategic_issue_id }}" {{ $project->goal_id == $g->id ? 'selected' : '' }}>
                    {{ $g->code ? $g->code . ': ' : '' }}{{ $g->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">4. กลยุทธ์องค์กร (Level 4) <span class="text-danger">*</span></label>
        <select class="form-select select2-alignment" id="modal_strategy_id" name="strategy_id" required>
            <option value="">-- กรุณาเลือกเป้าประสงค์ด้านบนก่อน --</option>
            @foreach(App\Models\Plan\Strategy::where('status', 'active')
                ->whereIn('goal_id', App\Models\Plan\Goal::whereIn('strategic_issue_id', App\Models\Plan\StrategicIssue::whereIn('mission_id', App\Models\Plan\Mission::where('fiscal_year_id', $project->fiscal_year_id)->pluck('id'))->pluck('id'))->pluck('id'))
                ->get() as $str)
                <option value="{{ $str->id }}" data-parent-id="{{ $str->goal_id }}" {{ $project->strategy_id == $str->id ? 'selected' : '' }}>
                    {{ $str->code ? $str->code . ': ' : '' }}{{ $str->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="text-end border-t pt-3 mt-4">
        <button type="submit" class="btn btn-primary px-5 fw-bold"><i class="fa-solid fa-square-check me-1"></i> บันทึกความสอดคล้องพันธกิจ</button>
    </div>
</form>

@push('scripts')
<script>
$(document).ready(function() {
    // ปลุกสคริปต์สกินกลุ่มชั้นข้อมูล Select2 ภายในบอร์ดหลัก
    initializeSelect2Alignment();

    // 🛠️ โคลนตัวเลือกดั้งเดิมทั้งหมดแยกเก็บไว้เพื่อใช้เป็นฐานข้อมูลในการกรองซ้ำๆ
    const rawIssueOptions = $('#modal_strategic_issue_id').find('option').clone();
    const rawGoalOptions = $('#modal_goal_id').find('option').clone();
    const rawStrategyOptions = $('#modal_strategy_id').find('option').clone();

    function initializeSelect2Alignment() {
        $('.select2-alignment').select2({
            dropdownParent: $('#alignment-pane'),
            theme: 'bootstrap-5',
            width: '100%'
        });
    }

    // 🛠️ ฟังก์ชันการกรองและวาดโครงสร้างสิทธิ์โมเดลลูกใหม่แบบเสถียร (ล้างค่าและสร้างใหม่เพื่อป้องกัน Select2 เอ๋อ)
    function executeCascadeFilter(targetElement, sourceOptions, activeParentId, defaultText, errorText) {
        targetElement.empty();
        
        // แปลงค่าให้เป็น String ป้องกันตัวแปร null หรือ undefined ทำงานผิดพลาด
        let cleanedParentId = activeParentId ? activeParentId.toString().trim() : '';

        if (cleanedParentId === '') {
            targetElement.append(`<option value="">${defaultText}</option>`);
            targetElement.prop('disabled', true);
        } else {
            // สแกนหาออปชันที่มี data-parent-id ตรงกับรหัสแม่ที่เลือกมา
            let matchingPool = sourceOptions.filter(function() {
                let nodeParentId = $(this).attr('data-parent-id');
                return $(this).val() === "" || (nodeParentId !== undefined && nodeParentId.toString().trim() === cleanedParentId);
            });

            if (matchingPool.length > 1) {
                targetElement.append(matchingPool.clone());
                targetElement.find('option[value=""]').text(defaultText);
                targetElement.prop('disabled', false);
            } else {
                targetElement.append(`<option value="">${errorText}</option>`);
                targetElement.prop('disabled', true);
            }
        }

        // รีเซ็ตล้างอินเทอร์เฟซคราบเก่าของระบบออกถาวร
        if (targetElement.data('select2')) { targetElement.select2('destroy'); }
        targetElement.select2({ dropdownParent: $('#alignment-pane'), theme: 'bootstrap-5', width: '100%' });
    }

    // ================= [EVENT] ชั้นที่ 1: เปลี่ยนพันธกิจ -> กรองประเด็นยุทธศาสตร์ =================
    $('#modal_mission_id').on('change', function() {
        if (window.isBypassingCascade) return;
        
        let selectedId = $(this).val();
        executeCascadeFilter($('#modal_strategic_issue_id'), rawIssueOptions, selectedId, '-- กรุณาเลือกประเด็นยุทธศาสตร์ --', '-- ไม่พบประเด็นยุทธศาสตร์ภายใต้พันธกิจนี้ --');
        
        // เมื่อตัวแม่เปลี่ยน ต้องสั่งล้างและล็อกกล่องระดับหลานและเหลนลงไปด้วยเสมอเพื่อความสะอาดของข้อมูล
        executeCascadeFilter($('#modal_goal_id'), rawGoalOptions, '', '-- กรุณาเลือกประเด็นยุทธศาสตร์ด้านบนก่อน --', '');
        executeCascadeFilter($('#modal_strategy_id'), rawStrategyOptions, '', '-- กรุณาเลือกเป้าประสงค์ด้านบนก่อน --', '');
    });

    // ================= [EVENT] ชั้นที่ 2: เปลี่ยนประเด็นยุทธศาสตร์ -> กรองเป้าประสงค์ =================
    $('#modal_strategic_issue_id').on('change', function() {
        if (window.isBypassingCascade) return;
        
        let selectedId = $(this).val();
        executeCascadeFilter($('#modal_goal_id'), rawGoalOptions, selectedId, '-- กรุณาเลือกเป้าประสงค์องค์กร --', '-- ไม่พบเป้าประสงค์ภายใต้ยุทธศาสตร์นี้ --');
        
        // ล้างและล็อกกล่องเหลน (กลยุทธ์)
        executeCascadeFilter($('#modal_strategy_id'), rawStrategyOptions, '', '-- กรุณาเลือกเป้าประสงค์ด้านบนก่อน --', '');
    });

    // ================= [EVENT] ชั้นที่ 3: เปลี่ยนเป้าประสงค์ -> กรองกลยุทธ์องค์กร L4 =================
    $('#modal_goal_id').on('change', function() {
        if (window.isBypassingCascade) return;
        
        let selectedId = $(this).val();
        executeCascadeFilter($('#modal_strategy_id'), rawStrategyOptions, selectedId, '-- กรุณาเลือกกลยุทธ์องค์กร --', '-- ไม่พบกลยุทธ์ภายใต้เป้าประสงค์นี้ --');
    });

    // ================= [INITIALIZE] จัดการโหลดเคสข้อมูลเก่าในฐานข้อมูล (Edit Mode) =================
    function loadExistingAlignmentData() {
        window.isBypassingCascade = true; // เปิดสวิตช์ข้ามการตรวจจับ Event ลูปค้างชั่วคราว

        let savedMissionId = @json($project->mission_id);
        let savedIssueId = @json($project->strategic_issue_id);
        let savedGoalId = @json($project->goal_id);
        let savedStrategyId = @json($project->strategy_id);

        // ตรวจเช็คว่าค่าในเบสไม่ใช่รหัสโครงสร้างดัมมี่เริ่มต้น (รหัส 1 หรือค่าว่าง)
        if (savedMissionId && savedMissionId.toString() !== '1' && savedMissionId.toString() !== '') {
            
            // กรองชั้นประเด็นยุทธศาสตร์ตามพันธกิจเดิม
            executeCascadeFilter($('#modal_strategic_issue_id'), rawIssueOptions, savedMissionId, '-- กรุณาเลือกประเด็นยุทธศาสตร์ --', '');
            $('#modal_strategic_issue_id').val(savedIssueId).trigger('change.select2');

            // กรองชั้นเป้าประสงค์ตามประเด็นยุทธศาสตร์เดิม
            executeCascadeFilter($('#modal_goal_id'), rawGoalOptions, savedIssueId, '-- กรุณาเลือกเป้าประสงค์องค์กร --', '');
            $('#modal_goal_id').val(savedGoalId).trigger('change.select2');

            // กรองชั้นกลยุทธ์ตามเป้าประสงค์เดิม
            executeCascadeFilter($('#modal_strategy_id'), rawStrategyOptions, savedGoalId, '-- กรุณาเลือกกลยุทธ์องค์กร --', '');
            $('#modal_strategy_id').val(savedStrategyId).trigger('change.select2');

        } else {
            // กรณีเป็นแผนงานที่เพิ่งกดบันทึกเพิ่มขึ้นมาจากแท็บ 1 สดๆ ให้เคลียร์ค่าว่างและสั่งล็อกกล่องย่อยทั้งหมดไว้ชั่วคราว
            executeCascadeFilter($('#modal_strategic_issue_id'), rawIssueOptions, '', '-- กรุณาเลือกพันธกิจด้านบนก่อน --', '');
            executeCascadeFilter($('#modal_goal_id'), rawGoalOptions, '', '-- กรุณาเลือกประเด็นยุทธศาสตร์ด้านบนก่อน --', '');
            executeCascadeFilter($('#modal_strategy_id'), rawStrategyOptions, '', '-- กรุณาเลือกเป้าประสงค์ด้านบนก่อน --', '');
        }

        window.isBypassingCascade = false; // ปิดสวิตช์เพื่อให้เจ้าหน้าที่สลับเลือกคัดกรองได้ตามปกติ
    }

    // เรียกใช้ฟังก์ชันตรวจสอบข้อมูลเก่าทันทีเมื่อโหลดคอมโพเนนต์เสร็จ
    loadExistingAlignmentData();

    // ================= ส่งข้อมูลสับเปลี่ยนอัปเดตบันทึก AJAX ยุทธศาสตร์โครงการ =================
    $('#alignmentForm').on('submit', function(e) {
        e.preventDefault();
        let url = `{{ url('plan/projects') }}/${currentProjectId}/update-alignment`;
        
        $.ajax({
            url: url,
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                Swal.fire({ icon: 'success', title: res.message, showConfirmButton: false, timer: 1300 });
                // ปลดล็อกเปิดสิทธิ์แท็บรายละเอียดความสำคัญลำดับถัดไปให้เข้ากรอกข้อมูลต่อได้ทันที
                $('#details-tab').removeClass('disabled text-black-50 bg-light');
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด!', text: 'ไม่สามารถบันทึกความสอดคล้องแผนยุทธศาสตร์ได้' });
            }
        });
    });
});
</script>
@endpush