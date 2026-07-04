@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm bg-white">
    <div class="card-header bg-white py-3 border-bottom">
        <h5 class="m-0 fw-bold text-warning"><i class="fa-solid fa-user-pen me-2"></i>แก้ไขข้อมูลบุคลากร</h5>
    </div>
    <div class="card-body p-4">

        <form action="{{ url('hr/personnels/'.$personnel->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold">รหัสบุคลากร / พนักงาน</label>
                    <input type="text" class="form-control" name="emp_code" value="{{ $personnel->emp_code }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">ชื่อจริง <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="firstname" value="{{ $personnel->firstname }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">นามสกุล <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="lastname" value="{{ $personnel->lastname }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">อีเมลติดต่อ (Email)</label>
                    <input type="email" class="form-control" name="email" value="{{ $personnel->email }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">เลือกหน่วยงานต้นสังกัด <span class="text-danger">*</span></label>
                    <select class="form-select select2-enable" name="department_id" required>
                        @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ $personnel->department_id == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">สถานะเจ้าหน้าที่</label>
                    <select class="form-select" name="status">
                        <option value="active" {{ $personnel->status == 'active' ? 'selected' : '' }}>ปฏิบัติงาน (Active)</option>
                        <option value="inactive" {{ $personnel->status == 'inactive' ? 'selected' : '' }}>พ้นสภาพ (Inactive)</option>
                    </select>
                </div>
            </div>

            <div class="mt-5 border-top pt-3 d-flex justify-content-end">
                <a href="{{ url('hr/personnels') }}" class="btn btn-light me-2 fw-bold px-4">
                    <i class="fa-solid fa-arrow-left me-1"></i> ยกเลิก
                </a>
                <button type="submit" class="btn btn-warning fw-bold px-4">
                    <i class="fa-solid fa-check me-1"></i> บันทึกการแก้ไข
                </button>
            </div>
        </form>
    </div>
</div>
@endsection