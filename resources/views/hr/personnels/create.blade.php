@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm bg-white">
    <div class="card-header bg-white py-3 border-bottom">
        <h5 class="m-0 fw-bold text-success"><i class="fa-solid fa-user-plus me-2"></i>เพิ่มข้อมูลบุคลากรใหม่</h5>
    </div>
    <div class="card-body p-4">

        @if ($errors->any())
        <div class="alert alert-danger border-0 mb-4 shadow-sm">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ url('hr/personnels') }}" method="POST">
            @csrf

            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold">รหัสบุคลากร / พนักงาน</label>
                    <input type="text" class="form-control" name="emp_code" value="{{ old('emp_code') }}" placeholder="เช่น EMP69001">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">ชื่อจริง <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="firstname" value="{{ old('firstname') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">นามสกุล <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="lastname" value="{{ old('lastname') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">อีเมลติดต่อ (Email)</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="username@pbri.ac.th">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">เลือกหน่วยงานต้นสังกัด <span class="text-danger">*</span></label>
                    <select class="form-select select2-enable" name="department_id" required>
                        <option value="">-- เลือกหน่วยงาน --</option>
                        @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">สถานะเจ้าหน้าที่</label>
                    <select class="form-select" name="status">
                        <option value="active">ปฏิบัติงาน (Active)</option>
                        <option value="inactive">พ้นสภาพ (Inactive)</option>
                    </select>
                </div>
            </div>

            <div class="mt-5 border-top pt-3 d-flex justify-content-end">
                <a href="{{ url('hr/personnels') }}" class="btn btn-light me-2 fw-bold px-4">
                    <i class="fa-solid fa-arrow-left me-1"></i> ย้อนกลับ
                </a>
                <button type="submit" class="btn btn-primary fw-bold px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกข้อมูล
                </button>
            </div>
        </form>
    </div>
</div>
@endsection