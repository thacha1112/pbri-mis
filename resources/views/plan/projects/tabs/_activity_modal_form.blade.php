<div class="row g-2">
    <input type="hidden" name="activities[{{ $index }}][id]" value="{{ $activity->id ?? '' }}">
    
    <div class="col-md-12 mb-2">
        <label class="form-label small fw-bold">ชื่อกิจกรรม</label>
        <input type="text" class="form-control" name="activities[{{ $index }}][name]" value="{{ $activity->name ?? '' }}" placeholder="ระบุชื่อกิจกรรม" required>
    </div>
    
    <div class="col-md-6 mb-2">
        <label class="form-label small fw-bold">วันที่เริ่มต้น</label>
        <input type="date" class="form-control" name="activities[{{ $index }}][start_date]" value="{{ $activity?->start_date?->format('Y-m-d') ?? '' }}" required>
    </div>
    <div class="col-md-6 mb-2">
        <label class="form-label small fw-bold">วันที่สิ้นสุด</label>
        <input type="date" class="form-control" name="activities[{{ $index }}][end_date]" value="{{ $activity?->end_date?->format('Y-m-d') ?? '' }}" required>
    </div>
    
    <div class="col-md-12 mt-3">
        <label class="small fw-bold border-bottom pb-2 w-100 mb-3"><i class="fa-solid fa-coins text-warning me-1"></i> จัดสรรงบประมาณลงกิจกรรม:</label>
        @foreach($project->projectBudgetSources as $b)
            @php
                $yearBE = ($b->budgetSource->fiscalYear->year ?? 0);
                $programName = $b->program->name ?? 'ไม่ระบุแผนงาน';
                $categoryName = $b->category->name ?? 'ไม่ระบุหมวดงบ';
                
                // 1. งบที่จัดสรรไว้ในโครงการนี้ของแหล่งเงินนี้
                $totalProjectSourceAmount = $b->allocated_amount;

                // 2. งบที่ถูกจัดสรรไปใช้ในกิจกรรมอื่นๆ แล้วทั้งหมด (ไม่รวมของกิจกรรมปัจจุบันที่กำลังแก้)
                $allocatedToOtherActivities = \App\Models\Plan\ActivityBudget::where('project_budget_source_id', $b->id)
                    ->when(isset($activity) && $activity->id, function($query) use ($activity) {
                        $query->where('activity_id', '!=', $activity->id);
                    })
                    ->sum('amount');

                // 3. งบของแหล่งเงินนี้ที่ยังคงเหลือให้จัดสรรได้
                $sourceRemaining = $totalProjectSourceAmount - $allocatedToOtherActivities;

                // 4. ค่าปัจจุบันที่กิจกรรมนี้ใช้อยู่
                $val = $activity?->budgets->where('project_budget_source_id', $b->id)->first()?->amount ?? 0;
            @endphp
            
            <div class="card mb-2 border-0 shadow-sm bg-light">
                <div class="card-body p-2 px-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <span class="badge bg-secondary small">ปี {{ $yearBE }}</span>
                            <span class="fw-bold text-dark ms-1">{{ $b->budgetSource->name }}</span>
                        </div>
                        <span class="small text-muted text-truncate" style="max-width: 250px;">{{ $programName }} / {{ $categoryName }}</span>
                    </div>

                    <!-- 🟢 แสดงข้อมูลโควตาและยอดคงเหลือของแหล่งเงิน -->
                    <div class="d-flex justify-content-between small text-muted mb-2 px-1 bg-white rounded p-1 border">
                        <span>จัดสรรลงโครงการ: <strong class="text-dark">{{ number_format($totalProjectSourceAmount, 2) }}</strong></span>
                        <span>คงเหลือ: <strong class="{{ $sourceRemaining < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($sourceRemaining, 2) }}</strong> บ.</span>
                    </div>

                    <div class="input-group input-group-sm">
                        <span class="input-group-text fw-bold bg-white {{ $sourceRemaining <= 0 && $val == 0 ? 'text-muted' : 'text-primary' }}">ระบุงบกิจกรรม</span>
                        
                        {{-- ช่องแสดงผล (ใส่ลูกน้ำ) พร้อมเงื่อนไขปิดการกรอก (disabled) ถ้างบหมด --}}
                        <input type="text" 
                            class="form-control text-end fw-bold {{ $sourceRemaining <= 0 && $val == 0 ? 'text-muted bg-secondary-subtle' : 'text-primary' }} budget-display" 
                            value="{{ number_format($val, 2) }}" 
                            oninput="formatBudgetInput(this)"
                            {{ $sourceRemaining <= 0 && $val == 0 ? 'disabled' : '' }}
                            placeholder="{{ $sourceRemaining <= 0 && $val == 0 ? 'งบประมาณหมดแล้ว' : 'ระบุจำนวนเงิน' }}">
                        
                        {{-- ช่องส่งค่าจริง (hidden) ส่งค่าเดิมไปเสมอ --}}
                        <input type="hidden" 
                            name="activities[{{ $index }}][budget][{{ $b->id }}]" 
                            class="budget-hidden" 
                            value="{{ $val }}">
                            
                        <span class="input-group-text bg-white">บาท</span>
                    </div>

                    @if($sourceRemaining <= 0 && $val == 0)
                        <div class="text-danger small mt-1" style="font-size: 0.75rem;">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i> แหล่งเงินนี้ถูกจัดสรรลงกิจกรรมอื่นจนเต็มวงเงินแล้ว
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>