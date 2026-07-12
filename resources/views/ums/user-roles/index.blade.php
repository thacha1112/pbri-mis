@extends('layouts.app')

@section('content')
<h4><i class="fa-solid fa-users-gear"></i> กำหนดสิทธิ์ผู้ใช้</h4>

<form action="{{ route('user-roles.index') }}" method="GET" class="mb-3">
    <div class="input-group w-50">
        <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อ หรือ นามสกุล..." value="{{ $search }}">
        <button class="btn btn-primary" type="submit">ค้นหา</button>
        <a href="{{ route('user-roles.index') }}" class="btn btn-secondary">ล้าง</a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ชื่อ - นามสกุล</th>
                    <th>บทบาทปัจจุบัน</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td> <td>
                        @foreach($user->roles as $role)
                            <span class="badge bg-info text-dark">{{ $role->display_name }}</span>
                        @endforeach
                    </td>
                    <td>
                        <a href="{{ route('user-roles.edit', $user->id) }}" class="btn btn-sm btn-warning">กำหนดสิทธิ์</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="mt-3">
            {{ $users->appends(['search' => $search])->links() }}
        </div>
    </div>
</div>
@endsection