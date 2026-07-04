<form id="projectDetailsForm">
    @csrf
    <div class="mb-3">
        <label class="form-label fw-bold">1. ความสำคัญ หลักการและเหตุผล <span class="text-danger">*</span></label>
        <textarea class="form-control" name="background_rationale" rows="3" required>{{ $project->background_rationale }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">2. วัตถุประสงค์ของโครงการ <span class="text-danger">*</span></label>
        <textarea class="form-control" name="objectives" rows="3" required>{{ $project->objectives }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">3. กลุ่มเป้าหมาย <span class="text-danger">*</span></label>
        <textarea class="form-control" name="target_group" rows="2" required>{{ $project->target_group }}</textarea>
    </div>

    <div class="row">
        <div class="col-md-12 mb-3">
            <label class="form-label fw-bold">4.ตัวชี้วัดโครงการ</label>
            <textarea class="form-control" name="indicators" rows="4" placeholder="ระบุตัวชี้วัดโครงการ...">{{ $project->indicators }}</textarea>
        </div>
        <div class="col-md-12 mb-3">
            <label class="form-label fw-bold">5.ผลผลิตโครงการ (Outputs)</label>
            <textarea class="form-control" name="outputs" rows="4" placeholder="ระบุผลผลิตโครงการ...">{{ $project->outputs }}</textarea>
        </div>
    </div>

    <div class="text-end pt-3">
        <button type="submit" class="btn btn-success px-5 fw-bold">บันทึกรายละเอียด</button>
    </div>
</form>
@push('scripts')
<script>
    $('#projectDetailsForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: `{{ url('plan/projects') }}/${currentProjectId}/update-details`,
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                Swal.fire({ icon: 'success', title: res.message, timer: 1300 });
                // ปลดล็อกแท็บถัดไป
                $('#budget-tab').removeClass('disabled text-black-50 bg-light');
            }
        });
    });
</script>
@endpush