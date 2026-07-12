@extends('layouts.app')

@section('content')
<h4>เพิ่มบทบาทใหม่</h4>
<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>ชื่อบทบาท (ภาษาอังกฤษ)</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>ชื่อที่แสดง</label>
                <input type="text" name="display_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>คำอธิบาย</label>
                <textarea name="description" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-success">บันทึก</button>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">ย้อนกลับ</a>
        </form>
    </div>
</div>
@endsection