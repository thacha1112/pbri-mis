@extends('layouts.app')

@section('content')
<div class="d-flex align-items-center mb-3">
    <a href="{{ url('plan/projects') }}" class="btn btn-outline-secondary btn-sm me-3"><i class="fa-solid fa-arrow-left"></i> กลับตารางโครงการ</a>
    <h4 class="m-0 fw-bold text-dark"><i class="fa-solid fa-file-signature text-primary me-2"></i>บันทึกแผนโครงการ</h4>
</div>
{{-- 1. สร้างตัวแปรระบุ Tab ปัจจุบัน (จาก URL query string หรือ default คือ general) --}}
@php $activeTab = request()->get('tab', 'general'); @endphp
<ul class="nav nav-tabs border-bottom-0 custom-project-tabs" id="projectTab" role="tablist">
    @foreach(['general' => 'บันทึกโครงการ', 'alignment' => 'พันธกิจ', 'details' => 'รายละเอียดโครงการ', 'budget' => 'งปม.ที่ได้รับจัดสรรลงโครงการ', 'activities' => 'บันทึกกิจกรรม'] as $key => $label)
        @php
            // เช็คว่ามี ID หรือไม่
            $hasId = ($project && $project->id);
            $isDisabled = !$hasId
        @endphp
        
        <li class="nav-item">
            <a class="nav-link fw-bold {{ $activeTab == $key ? 'active' : '' }} {{ $isDisabled ? 'disabled text-muted' : '' }}" 
            @if($isDisabled)
                style="cursor: not-allowed; opacity: 0.6; pointer-events: none;"
            @else
                
                onclick="window.location.href='{{ url('plan/projects', [$project->id]) }}/edit?tab={{ $key }}'"
                style="cursor: pointer;"
            @endif
            role="button">
                {{ $label }}
            </a>
        </li>
    @endforeach
</ul>

<div class="tab-content card border-0 shadow-sm p-4 bg-white rounded-bottom" id="projectTabContent" style="border-top-left-radius: 0px !important;">
  
    {{-- แสดงข้อความ Success --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- แสดงข้อความ Error (รวมถึง validation error ต่างๆ) --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="tab-pane fade {{ $activeTab == 'general' ? 'show active' : '' }}" id="general-pane" role="tabpanel" aria-labelledby="general-tab" tabindex="0">
        <form id="generalForm" action="{{ $isEdit ? route('plan.projects.update', $project->id) : route('plan.projects.store') }}" method="POST">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif
            <input type="hidden" name="redirect_tab" value="alignment">

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">รหัสโครงการ <span class="text-danger">*</span></label>
                    <input type="text" class="form-control font-mono fw-bold" id="project_code" name="project_code" value="{{ old('project_code', $project->project_code) }}" placeholder="เช่น PRJ69001" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label fw-bold">ชื่อโครงการยุทธศาสตร์ / แผนงานอนุมัติ <span class="text-danger">*</span></label>
                    <input type="text" class="form-control fw-bold text-dark" id="name" name="name" value="{{ old('name', $project->name) }}" placeholder="ระบุชื่อโครงการเต็มใบด้านบน" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">ปีงบประมาณ <span class="text-danger">*</span></label>
                    <select class="form-select select2-setup" id="fiscal_year_id" name="fiscal_year_id" required>
                        <option value="">-- เลือกปีงบประมาณ --</option>
                        @foreach($fiscalYears as $y)
                        <option value="{{ $y->id }}" {{ old('fiscal_year_id', $project->fiscal_year_id) == $y->id ? 'selected' : '' }}>ปี พ.ศ. {{ $y->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">หน่วยงานผู้รับผิดชอบโครงการ <span class="text-danger">*</span></label>
                    <select class="form-select select2-setup" id="department_id" name="department_id" required>
                        <option value="">-- เลือกกอง/คณะ/สังกัด --</option>
                        @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ old('department_id', $project->department_id) == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">หัวหน้าโครงการ / ผู้รับผิดชอบตรง <span class="text-danger">*</span></label>
                    <select class="form-select select2-setup" id="personnel_id" name="personnel_id" required>
                        <option value="">-- เลือกเจ้าหน้าที่ผู้รับผิดชอบ --</option>
                        @foreach($personnels as $p)
                        <option value="{{ $p->id }}" {{ old('personnel_id', $project->personnel_id) == $p->id ? 'selected' : '' }}>
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
                            <option value="{{ $pm->id }}" {{ old('project_method_id', $project->project_method_id) == $pm->id ? 'selected' : '' }}>{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">มิติครุภัณฑ์สิ่งก่อสร้าง <span class="text-danger">*</span></label>
                    <select class="form-select select2-setup" id="construction_type_id" name="construction_type_id" required>
                        <option value="">-- เลือกมิติครุภัณฑ์ --</option>
                        @foreach($constructionTypes as $ct)
                            <option value="{{ $ct->id }}" {{ old('construction_type_id', $project->construction_type_id) == $ct->id ? 'selected' : '' }}>{{ $ct->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">มิติการเดินทางไปต่างประเทศ <span class="text-danger">*</span></label>
                    <select class="form-select select2-setup" id="overseas_type_id" name="overseas_type_id" required>
                        <option value="">-- เลือกมิติการเดินทาง --</option>
                        @foreach($overseasTypes as $ot)
                            <option value="{{ $ot->id }}" {{ old('overseas_type_id', $project->overseas_type_id) == $ot->id ? 'selected' : '' }}>{{ $ot->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row" data-start-raw="{{ $project->start_date ? $project->start_date->format('Y-m-d') : '' }}" data-end-raw="{{ $project->end_date ? $project->end_date->format('Y-m-d') : '' }}" id="project-date-binding">
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-bold">ระยะเวลาเริ่มต้นโครงการ <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary"><i class="fa-solid fa-calendar-days"></i></span>
                        <input type="text" 
                            class="form-control fw-semibold th-datepicker" 
                            id="start_date" 
                            name="start_date" 
                            value="{{ \App\Helpers\DateHelper::toThaiDate(old('start_date', $project->start_date)) }}" 
                            placeholder="วว/ดด/ปปปป" 
                            autocomplete="off" 
                            required>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-bold">ระยะเวลาสิ้นสุดโครงการ <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary"><i class="fa-solid fa-calendar-days"></i></span>
                        <input type="text" 
                            class="form-control fw-semibold th-datepicker" 
                            id="end_date" 
                            name="end_date" 
                            value="{{ \App\Helpers\DateHelper::toThaiDate(old('end_date', $project->end_date)) }}" 
                            placeholder="วว/ดด/ปปปป" 
                            autocomplete="off" 
                            required>
                    </div>
                </div>
            </div>
            <!-- 🟢 เพิ่มฟิลด์สถานะโครงการในข้อมูลทั่วไป -->
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-bold">สถานะโครงการ <span class="text-danger">*</span></label>
                    <select class="form-select select2-setup" id="status" name="status" required>
                        <option value="pending" {{ old('status', $project->status) == 'pending' ? 'selected' : '' }}>รอดำเนินการ (Pending)</option>
                        <option value="inprogress" {{ old('status', $project->status) == 'inprogress' ? 'selected' : '' }}>กำลังดำเนินการ (In Progress)</option>
                        <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>เสร็จสิ้น (Completed)</option>
                        <option value="cancelled" {{ old('status', $project->status) == 'cancelled' ? 'selected' : '' }}>ยกเลิก (Cancelled)</option>
                    </select>
                </div>
            </div>

            <div class="text-end border-t pt-3">
                <button type="submit" class="btn btn-success px-5 fw-bold"><i class="fa-solid fa-floppy-disk me-1"></i> บันทึกข้อมูลทั่วไป</button>
            </div>
        </form>
    </div>

    {{-- Tab ความสอดคล้อง --}}
    <div class="tab-pane fade {{ $activeTab == 'alignment' ? 'show active' : '' }}" id="alignment-pane" role="tabpanel" aria-labelledby="alignment-tab" tabindex="0">
        @if($isEdit)
            @include('plan.projects.tabs.alignment')
        @else
            <div class="text-center py-5 text-muted">
                <i class="fa-solid fa-lock fa-2x mb-2 text-warning"></i><br>
                ระบบจะเปิดให้กรอกเมื่อผ่านการบันทึกข้อมูลทั่วไปเรียบร้อยแล้ว
            </div>
        @endif
    </div>

    {{-- Tab รายละเอียดโครงการ --}}
    <div class="tab-pane fade {{ $activeTab == 'details' ? 'show active' : '' }}" id="details-pane" role="tabpanel" aria-labelledby="details-tab" tabindex="0">
        @if($isEdit)
            @include('plan.projects.tabs.details')
        @else
            <div class="text-center py-5 text-muted">
                <i class="fa-solid fa-lock fa-2x mb-2 text-warning"></i><br>
                ระบบจะเปิดให้กรอกเมื่อผ่านการบันทึกข้อมูลทั่วไปเรียบร้อยแล้ว
            </div>
        @endif
    </div>

   {{-- Tab แหล่งเงิน --}}
    <div class="tab-pane fade {{ $activeTab == 'budget' ? 'show active' : '' }}" id="budget-pane" role="tabpanel" aria-labelledby="budget-tab" tabindex="0">
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

   {{-- Tab กิจกรรม --}}
    <div class="tab-pane fade {{ $activeTab == 'activities' ? 'show active' : '' }}" id="activities-pane" role="tabpanel" aria-labelledby="activities-tab" tabindex="0">
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

       

        // 1. ตั้งค่า Datepicker
        $('.th-datepicker').datepicker({
            format: 'dd/mm/yyyy',
            language: 'th',
            thaiyear: true,
            autoclose: true,
            todayHighlight: true
        });

        // 2. ฟังก์ชันช่วยปรับค่าจาก ค.ศ. (Y-m-d) เป็น พ.ศ. (dd/mm/yyyy)
        $('.th-datepicker').each(function() {
            let val = $(this).val();
            if (val && val.includes('-')) {
                let parts = val.split('-'); // ยึดตาม format Y-m-d
                if(parts.length === 3) {
                    let year = parseInt(parts[0]) + 543; // แปลง ค.ศ. -> พ.ศ.
                    let thaiDate = `${parts[2]}/${parts[1]}/${year}`;
                    $(this).val(thaiDate);
                    $(this).datepicker('update', thaiDate);
                }
            }
        });

        // ================= สคริปต์ประมวลผลเซฟข้อมูล Tabที่ 1 ผ่าน AJAX =================
        $('#generalForm').on('submit', function() {
            let sDate = $('#start_date').datepicker('getDate');
            let eDate = $('#end_date').datepicker('getDate');
            
            if (sDate) {
                let formatted = sDate.getFullYear() + '-' + String(sDate.getMonth() + 1).padStart(2, '0') + '-' + String(sDate.getDate()).padStart(2, '0');
                $('<input>').attr({type: 'hidden', name: 'start_date_formatted', value: formatted}).appendTo('#generalForm');
            }
            if (eDate) {
                let formatted = eDate.getFullYear() + '-' + String(eDate.getMonth() + 1).padStart(2, '0') + '-' + String(eDate.getDate()).padStart(2, '0');
                $('<input>').attr({type: 'hidden', name: 'end_date_formatted', value: formatted}).appendTo('#generalForm');
            }
            return true; // ยอมให้ Submit
        });
    });
</script>
@endpush