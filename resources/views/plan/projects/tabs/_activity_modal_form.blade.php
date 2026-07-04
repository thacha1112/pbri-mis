
    <div class="row g-2">
        <input type="hidden" name="activities[{{ $index }}][id]" value="{{ $activity->id ?? '' }}">
        
        <div class="col-md-12">
            <label class="form-label small fw-bold">ชื่อกิจกรรม</label>
            <input type="text" class="form-control" name="activities[{{ $index }}][name]" value="{{ $activity->name ?? '' }}" required>
        </div>
        <div class="col-md-12">
            <label class="form-label small fw-bold">วัตถุประสงค์</label>
            <textarea class="form-control" name="activities[{{ $index }}][objectives]" rows="2">{{ $activity->objectives ?? '' }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-bold">ตัวชี้วัด</label>
            <textarea class="form-control" name="activities[{{ $index }}][indicators]" rows="2">{{ $activity->indicators ?? '' }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-bold">กลุ่มเป้าหมาย</label>
            <textarea class="form-control" name="activities[{{ $index }}][target_group]" rows="2">{{ $activity->target_group ?? '' }}</textarea>
        </div>
        <div class="col-md-12">
            <label class="form-label small fw-bold">ผลผลิต (Outputs)</label>
            <textarea class="form-control" name="activities[{{ $index }}][outputs]" rows="2">{{ $activity->outputs ?? '' }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-bold">วันที่เริ่มต้น</label>
            <input type="date" class="form-control" name="activities[{{ $index }}][start_date]" value="{{ $activity?->start_date?->format('Y-m-d') ?? '' }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-bold">วันที่สิ้นสุด</label>
            <input type="date" class="form-control" name="activities[{{ $index }}][end_date]" value="{{ $activity?->end_date?->format('Y-m-d') ?? '' }}" required>
        </div>
        <div class="col-md-12 mt-2">
            <label class="small fw-bold border-bottom pb-1 w-100">จัดสรรงบประมาณ:</label>
            @foreach($project->projectBudgetSources as $b)
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text" style="width:140px">{{ $b->budgetSource->name }}</span>
                    <input type="number" class="form-control" name="activities[{{ $index }}][budget][{{ $b->id }}]" 
                        value="{{ $activity?->budgets->where('project_budget_source_id', $b->id)->first()?->amount ?? '' }}" 
                        placeholder="0.00" step="0.01">
                </div>
            @endforeach
        </div>
    </div>
