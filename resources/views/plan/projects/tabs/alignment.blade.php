<form id="alignmentForm" action="{{ route('plan.projects.update-alignment', $project->id) }}" method="POST">
    @csrf
    {{-- เปลี่ยนตารางให้เป็น Checklist --}}
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th style="width: 50px;" class="text-center">เลือก</th>
                <th>โครงสร้างความสอดคล้อง (พันธกิจ > ยุทธศาสตร์ > เป้าประสงค์ > กลยุทธ์)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($missions as $m)
                <tr class="table-active">
                    <td colspan="2" class="fw-bold text-primary">พันธกิจ: {{ $m->name }}</td>
                </tr>
                @foreach($m->strategicIssues as $si)
                    @foreach($si->goals as $g)
                        @foreach($g->strategies as $str)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="strategies[]" value="{{ $str->id }}" 
                                           class="form-check-input" 
                                           {{ $project->selectedStrategies->contains('strategy_id', $str->id) ? 'checked' : '' }}>
                                </td>
                                <td>
                                    <div class="ms-2 small text-primary"><i class="fa-solid fa-bullseye me-1"></i> ยุทธศาสตร์: {{ $si->code }} {{ $si->name }}</div>
                                    <div class="ms-3 small text-success"><i class="fa-solid fa-flag-checkered me-1"></i> เป้าประสงค์: {{ $g->code }} {{ $g->name }}</div>
                                    <div class="ms-4 fw-bold"><i class="fa-solid fa-chevron-right me-1"></i> กลยุทธ์: {{ $str->code }} {{ $str->name }}</div>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            @endforeach
        </tbody>
    </table>
    
    <div class="text-end mt-4">
        <button type="submit" class="btn btn-primary px-5">
            <i class="fa-solid fa-save me-1"></i> บันทึกความสอดคล้อง
        </button>
    </div>
</form>