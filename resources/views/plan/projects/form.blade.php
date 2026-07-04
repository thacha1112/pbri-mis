@extends('layouts.app')

@section('content')
<div class="d-flex align-items-center mb-3">
    <a href="{{ url('plan/projects') }}" class="btn btn-outline-secondary btn-sm me-3"><i class="fa-solid fa-arrow-left"></i> กลับตารางโครงการ</a>
    <h4 class="m-0 fw-bold text-dark"><i class="fa-solid fa-file-signature text-primary me-2"></i>บันทึกแผนโครงการ</h4>
</div>

<ul class="nav nav-tabs border-bottom-0 custom-project-tabs" id="projectTab" role="tablist">
    <li class="nav-item">
        <button class="nav-link active fw-bold" id="general-tab" data-bs-toggle="tab" data-bs-target="#general-pane" type="button" role="tab"><i class="fa-solid fa-circle-info me-1"></i> 1. ข้อมูลทั่วไป</button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-bold {{ !$isEdit ? 'disabled text-black-50 bg-light' : '' }}" id="alignment-tab" data-bs-toggle="tab" data-bs-target="#alignment-pane" type="button" role="tab"><i class="fa-solid fa-diagram-project me-1"></i> 2. ความสอดคล้องพันธกิจ</button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-bold {{ !$isEdit ? 'disabled text-black-50 bg-light' : '' }}" id="details-tab" data-bs-toggle="tab" data-bs-target="#details-pane" type="button" role="tab"><i class="fa-solid fa-list-check me-1"></i> 3. รายละเอียดโครงการ</button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-bold {{ !$isEdit ? 'disabled text-black-50 bg-light' : '' }}" id="budget-tab" data-bs-toggle="tab" data-bs-target="#budget-pane" type="button" role="tab"><i class="fa-solid fa-sack-dollar me-1"></i> 4. แหล่งเงินโครงการ</button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-bold {{ !$isEdit ? 'disabled text-black-50 bg-light' : '' }}" id="activities-tab" data-bs-toggle="tab" data-bs-target="#activities-pane" type="button" role="tab"><i class="fa-solid fa-chart-line me-1"></i> 5. แผนกิจกรรม & เบิกจ่าย</button>
    </li>
</ul>

<div class="tab-content card border-0 shadow-sm p-4 bg-white rounded-bottom" id="projectTabContent" style="border-top-left-radius: 0px !important;">

    <div class="tab-pane fade show active" id="general-pane" role="tabpanel" aria-labelledby="general-tab" tabindex="0">
        <form id="generalForm">
            @csrf
            @if($isEdit)
            @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">รหัสโครงการ <span class="text-danger">*</span></label>
                    <input type="text" class="form-control font-mono fw-bold" id="project_code" name="project_code" value="{{ $project->project_code }}" placeholder="เช่น PRJ69001" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label fw-bold">ชื่อโครงการยุทธศาสตร์ / แผนงานอนุมัติ <span class="text-danger">*</span></label>
                    <input type="text" class="form-control fw-bold text-dark" id="name" name="name" value="{{ $project->name }}" placeholder="ระบุชื่อโครงการเต็มใบด้านบน" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">ปีงบประมาณ <span class="text-danger">*</span></label>
                    <select class="form-select select2-setup" id="fiscal_year_id" name="fiscal_year_id" required>
                        <option value="">-- เลือกปีงบประมาณ --</option>
                        @foreach($fiscalYears as $y)
                        <option value="{{ $y->id }}" {{ $project->fiscal_year_id == $y->id ? 'selected' : '' }}>ปี พ.ศ. {{ $y->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">หน่วยงานผู้รับผิดชอบโครงการ <span class="text-danger">*</span></label>
                    <select class="form-select select2-setup" id="department_id" name="department_id" required>
                        <option value="">-- เลือกกอง/คณะ/สังกัด --</option>
                        @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ $project->department_id == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">หัวหน้าโครงการ / ผู้รับผิดชอบตรง <span class="text-danger">*</span></label>
                    <select class="form-select select2-setup" id="personnel_id" name="personnel_id" required>
                        <option value="">-- เลือกเจ้าหน้าที่ผู้รับผิดชอบ --</option>
                        @foreach($personnels as $p)
                        <option value="{{ $p->id }}" {{ $project->personnel_id == $p->id ? 'selected' : '' }}>
                            {{ $p->name ?? ($p->firstname . " " . $p->lastname) }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">วิธีดำเนินการโครงการ <span class="text-danger">*</span></label>
                    <select class="form-select select2-setup" id="project_method_id" name="project_method_id" required>
                        <option value="">-- เลือกประเภทวิธีดำเนินการ --</option>
                        @foreach($projectMethods as $pm)
                        <option value="{{ $pm->id }}" {{ $project->project_method_id == $pm->id ? 'selected' : '' }}>{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">มิติครุภัณฑ์สิ่งก่อสร้าง <span class="text-danger">*</span></label>
                    <select class="form-select select2-setup" id="construction_type_id" name="construction_type_id" required>
                        @foreach($constructionTypes as $ct)
                        <option value="{{ $ct->id }}" {{ $project->construction_type_id == $ct->id ? 'selected' : '' }}>{{ $ct->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">มิติการเดินทางไปต่างประเทศ <span class="text-danger">*</span></label>
                    <select class="form-select select2-setup" id="overseas_type_id" name="overseas_type_id" required>
                        @foreach($overseasTypes as $ot)
                        <option value="{{ $ot->id }}" {{ $project->overseas_type_id == $ot->id ? 'selected' : '' }}>{{ $ot->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row" data-start-raw="{{ $project->start_date ? $project->start_date->format('Y-m-d') : '' }}" data-end-raw="{{ $project->end_date ? $project->end_date->format('Y-m-d') : '' }}" id="project-date-binding">
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-bold">ระยะเวลาเริ่มต้นโครงการ <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary"><i class="fa-solid fa-calendar-days"></i></span>
                        <input type="text" class="form-control fw-semibold th-datepicker" id="start_date" name="start_date" placeholder="วว/ดด/ปปปป" autocomplete="off" required>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-bold">ระยะเวลาสิ้นสุดโครงการ <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary"><i class="fa-solid fa-calendar-days"></i></span>
                        <input type="text" class="form-control fw-semibold th-datepicker" id="end_date" name="end_date" placeholder="วว/ดด/ปปปป" autocomplete="off" required>
                    </div>
                </div>
            </div>

            <div class="text-end border-t pt-3">
                <button type="submit" class="btn btn-success px-5 fw-bold"><i class="fa-solid fa-floppy-disk me-1"></i> บันทึกข้อมูลทั่วไป</button>
            </div>
        </form>
    </div>

    <div class="tab-pane fade" id="alignment-pane" role="tabpanel" aria-labelledby="alignment-tab" tabindex="0">
        @if($isEdit)
            @include('plan.projects.tabs.alignment')
        @else
            <div class="text-center py-5 text-muted">
                <i class="fa-solid fa-lock fa-2x mb-2 text-warning"></i><br>
                ระบบจะเปิดให้กรอกเมื่อผ่านการบันทึกข้อมูลทั่วไปเรียบร้อยแล้ว
            </div>
        @endif
    </div>
    <div class="tab-pane fade" id="details-pane" role="tabpanel" aria-labelledby="details-tab" tabindex="0">
        @if($isEdit)
            @include('plan.projects.tabs.details')
        @else
            <div class="text-center py-5 text-muted">
                <i class="fa-solid fa-lock fa-2x mb-2 text-warning"></i><br>
                ระบบจะเปิดให้กรอกเมื่อผ่านการบันทึกข้อมูลทั่วไปเรียบร้อยแล้ว
            </div>
        @endif
    </div>
   <div class="tab-pane fade" id="budget-pane" role="tabpanel" aria-labelledby="budget-tab" tabindex="0">
        @if($isEdit)
            {{-- ดึงไฟล์ย่อยของแท็บแหล่งเงินมาแสดง --}}
            @include('plan.projects.tabs.budget')
        @else
            <div class="text-center py-5 text-muted">
                <i class="fa-solid fa-lock fa-2x mb-2 text-warning"></i><br>
                ระบบจะเปิดให้กรอกเมื่อผ่านการบันทึกข้อมูลทั่วไปเรียบร้อยแล้ว
            </div>
        @endif
    </div>
    <div class="tab-pane fade" id="activities-pane" role="tabpanel" aria-labelledby="activities-tab" tabindex="0">
          @if($isEdit)
            {{-- ดึงไฟล์ย่อยของแท็บแหล่งเงินมาแสดง --}}
            @include('plan.projects.tabs.activities')
        @else
            <div class="text-center py-5 text-muted">
                <i class="fa-solid fa-lock fa-2x mb-2 text-warning"></i><br>
                ระบบจะเปิดให้กรอกเมื่อผ่านการบันทึกข้อมูลทั่วไปเรียบร้อยแล้ว
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js"></script>

<script>
    const isEditModeGlobal = @json($isEdit);
    let currentProjectId = @json($project->id ?? null);

    $(document).ready(function() {
        $('.select2-setup').select2({ theme: 'bootstrap-5', width: '100%' });

        // 1. ดักรับค่าดิบ ค.ศ. ผ่านแผงคุมภายนอก
        let rawStartDate = $('#project-date-binding').attr('data-start-raw');
        let rawEndDate = $('#project-date-binding').attr('data-end-raw');

        // 2. 🔥 ประกาศคำสั่งเปิดปฏิทินไทย พ.ศ. ลงบนอินพุตที่มีคลาส .th-datepicker โดยตรง
        $('.th-datepicker').datepicker({
            format: 'dd/mm/yyyy',
            language: 'th',
            thaiyear: true, // คำนวณ ปี ค.ศ. เป็น พ.ศ. ในหน้ากากสกินให้อัตโนมัติ
            autoclose: true,
            todayHighlight: true
        });

        // 3. ปรับค่าอัปเดตวันที่เดิมในฐานข้อมูล (ถ้ามี)
        if (rawStartDate) {
            let dateObj = new Date(rawStartDate);
            $('#start_date').datepicker('update', dateObj);
        }
        if (rawEndDate) {
            let dateObj = new Date(rawEndDate);
            $('#end_date').datepicker('update', dateObj);
        }

        // ================= สคริปต์ประมวลผลเซฟข้อมูล Tabที่ 1 ผ่าน AJAX =================
        $('#generalForm').on('submit', function(e) {
            e.preventDefault();
            let url = isEditModeGlobal ? `{{ url('plan/projects') }}/${currentProjectId}` : `{{ url('plan/projects') }}`;
            
            // แปลงค่า พ.ศ. กลับไปเป็น ค.ศ. สำหรับจัดเก็บลง MySQL
            let sDate = $('#start_date').datepicker('getDate');
            let eDate = $('#end_date').datepicker('getDate');
            let dataArray = $(this).serializeArray();
            
            if (sDate) {
                let formattedSDate = sDate.getFullYear() + '-' + String(sDate.getMonth() + 1).padStart(2, '0') + '-' + String(sDate.getDate()).padStart(2, '0');
                $.each(dataArray, function() { if (this.name === 'start_date') this.value = formattedSDate; });
            }
            if (eDate) {
                let formattedEDate = eDate.getFullYear() + '-' + String(eDate.getMonth() + 1).padStart(2, '0') + '-' + String(eDate.getDate()).padStart(2, '0');
                $.each(dataArray, function() { if (this.name === 'end_date') this.value = formattedEDate; });
            }

            $.ajax({
                url: url, type: 'POST', data: $.param(dataArray),
                success: function(res) {
                    if (!isEditModeGlobal && res.project_id) {
                        Swal.fire({ icon: 'success', title: res.message, showConfirmButton: true, confirmButtonText: 'ดำเนินการต่อ' })
                        .then(() => { window.location.href = `{{ url('plan/projects') }}/${res.project_id}/edit`; });
                    } else {
                        Swal.fire({ icon: 'success', title: res.message, showConfirmButton: false, timer: 1300 });
                    }
                },
                error: function(err) {
                    let errors = err.responseJSON?.errors;
                    let message = 'ตรวจสอบความถูกต้องของฟิลด์ข้อมูลใหม่อีกครั้ง';
                    if (errors && errors.project_code) { message = 'รหัสโครงการนี้ถูกใช้ในระบบงบประมาณไปเรียบร้อยแล้ว'; }
                    Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด!', text: message });
                }
            });
        });
    });
</script>
@endpush