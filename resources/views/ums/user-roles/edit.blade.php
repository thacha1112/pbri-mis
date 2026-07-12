@extends('layouts.app')

@section('content')
<h4>กำหนดสิทธิ์ให้: {{ $user->name }}</h4>
<div class="card shadow-sm mt-3">
    <div class="card-body">
        <form action="{{ route('user-roles.update', $user->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-bold">เลือกบทบาท (Roles):</label>
                @foreach($roles as $role)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" 
                            {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ $role->display_name }}</label>
                    </div>
                @endforeach
            </div>
            <button type="submit" class="btn btn-success">บันทึกสิทธิ์</button>
            <a href="{{ route('user-roles.index') }}" class="btn btn-secondary">ย้อนกลับ</a>
        </form>
    </div>
</div>
@endsection