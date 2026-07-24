<div class="modal fade" id="allocationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="allocationForm" action="{{ route('department-allocations.store') }}" method="POST">
            @csrf
            <input type="hidden" id="allocation_id" name="id">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalTitle">จัดสรรงบประมาณรายหน่วยงาน</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">1. ปีงบที่ใช้ทำโครงการ <span class="text-danger">*</span></label>
                            <select name="fiscal_year_id" id="fiscal_year_id" class="form-select" required>
                                <option value="">-- เลือกปีงบ --</option>
                                @foreach($fiscalYears as $fy)
                                    <option value="{{ $fy->id }}">{{ $fy->year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-primary">2. ปีของแหล่งเงิน <span class="text-danger">*</span></label>
                            <select id="source_fiscal_year_id" name="source_fiscal_year_id" class="form-select border-primary" required>
                                <option value="">-- เลือกปีแหล่งเงิน --</option>
                                @foreach($fiscalYears as $fy)
                                    <option value="{{ $fy->id }}">{{ $fy->year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">หน่วยงาน <span class="text-danger">*</span></label>
                            <select name="department_id" id="department_id" class="form-select" required>
                                <option value="">-- เลือกหน่วยงาน --</option>
                                @foreach($departments as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">แหล่งเงิน <span class="text-danger">*</span></label>
                            <select name="budget_source_id" id="budget_source_id" class="form-select" required>
                                <option value="">-- เลือกแหล่งเงิน --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">แผนงาน</label>
                            <select name="program_id" id="program_id" class="form-select">
                                <option value="">-- เลือกแผนงาน --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">หมวดงบ</label>
                            <select name="category_id" id="category_id" class="form-select">
                                <option value="">-- เลือกหมวดงบ --</option>
                            </select>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="form-label fw-bold text-success fs-5">ยอดจัดสรรรวม (บาท) <span class="text-danger">*</span></label>
                            <input type="text" id="total_amount_display" class="form-control form-control-lg text-end fw-bold text-primary" required>
                            <input type="hidden" name="total_amount" id="total_amount">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">บันทึกข้อมูล</button>
                </div>
            </div>
        </form>
    </div>
</div>