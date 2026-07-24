<form id="projectDetailsForm" action="{{ route('plan.projects.update-details', $project->id) }}" method="POST">
    @csrf
    
    <div class="mb-3">
        <label class="form-label fw-bold">1. ความสำคัญ หลักการและเหตุผล <span class="text-danger">*</span></label>
        <textarea class="form-control" name="background_rationale" rows="3" required>{{ old('background_rationale', $project->background_rationale) }}</textarea>
    </div>
    
    <div class="mb-3">
        <label class="form-label fw-bold">2. วัตถุประสงค์ของโครงการ <span class="text-danger">*</span></label>
        <textarea class="form-control" name="objectives" rows="3" required>{{ old('objectives', $project->objectives) }}</textarea>
    </div>
    
    <div class="mb-3">
        <label class="form-label fw-bold">3. กลุ่มเป้าหมาย <span class="text-danger">*</span></label>
        <textarea class="form-control" name="target_group" rows="2" required>{{ old('target_group', $project->target_group) }}</textarea>
    </div>

    <div class="row">
        <div class="col-md-12 mb-3">
            <label class="form-label fw-bold">4. ตัวชี้วัดโครงการ</label>
            <textarea class="form-control" name="indicators" rows="4">{{ old('indicators', $project->indicators) }}</textarea>
        </div>
        <div class="col-md-12 mb-3">
            <label class="form-label fw-bold">5. ผลผลิตโครงการ (Outputs)</label>
            <textarea class="form-control" name="outputs" rows="4">{{ old('outputs', $project->outputs) }}</textarea>
        </div>
    </div>

    <div class="text-end pt-3">
        <button type="submit" class="btn btn-success px-5 fw-bold">
            <i class="fa-solid fa-save me-1"></i> บันทึกรายละเอียด
        </button>
    </div>
</form>