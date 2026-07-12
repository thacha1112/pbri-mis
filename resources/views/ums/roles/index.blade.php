@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4><i class="fa-solid fa-user-gear"></i> จัดการบทบาท (Roles)</h4>
    <a href="{{ route('roles.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> เพิ่มบทบาท</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ชื่อบทบาท (Key)</th>
                    <th>ชื่อที่แสดง</th>
                    <th>คำอธิบาย</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->display_name }}</td>
                    <td>{{ $role->description }}</td>
                    <td>
                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-warning">แก้ไข</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection