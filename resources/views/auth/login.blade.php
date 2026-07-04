@extends('layouts.app')

@section('content')
<style>
    /* ปรับให้ Container เต็มความสูงหน้าจอ เพื่อให้กล่องอยู่กึ่งกลางพอดี */
    .login-wrapper {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-card {
        background: #ffffff;
        border-radius: 24px;
        /* ปรับความกว้างสูงสุดเพิ่มขึ้นเล็กน้อย */
        width: 100%;
        max-width: 500px; 
        transition: all 0.3s ease;
    }

    /* ปรับปุ่มให้ดูใหญ่และกดง่ายขึ้น (Touch-friendly) */
    .btn-microsoft {
        background-color: #00a1f1;
        color: white;
        border-radius: 12px;
        padding: 18px; /* เพิ่มความสูงของปุ่ม */
        font-size: 1.1rem; /* เพิ่มขนาดตัวอักษร */
        transition: 0.3s;
        border: none;
    }

    .btn-microsoft:hover {
        background-color: #0081c2;
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 161, 241, 0.2);
    }

    .btn-microsoft img {
        width: 24px; /* ปรับขนาดโลโก้ MS */
    }

    .brand-text {
        color: #2c3e50;
        letter-spacing: -0.5px;
    }
</style>

<div class="login-wrapper">
    <div class="card login-card shadow-lg border-0">
        <div class="card-body p-5"> <!-- p-5 ช่วยให้ระยะห่างข้างในดูไม่อึดอัด -->
            
            <div class="text-center mb-5">
                <h2 class="brand-text fw-bold mb-3">เข้าสู่ระบบ</h2>
                <p class="text-muted">
                    <h3 class="text-primary fw-600">ระบบบริหารงานแผนและยุทธศาสตร์</h3>
                </p>
            </div>
            
            <div class="d-grid gap-4">
                {{-- ปุ่มเข้าสู่ระบบแบบเน้นๆ --}}
                <a href="{{ route('login.microsoft', ['type' => 'user']) }}" class="btn btn-microsoft d-flex align-items-center justify-content-center shadow-sm">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/4/44/Microsoft_logo.svg" class="me-3">
                    <span class="fw-bold">ลงชื่อเข้าใช้งานด้วย Microsoft 365</span>
                </a>
            </div>

            <div class="mt-5 text-center">
                <p class="small text-muted">
                    ใช้บัญชีอีเมลสถาบัน <b>(@pi.ac.th)</b> ในการเข้าใช้งาน <br>
                </p>
            </div>

            @if(session('error'))
                <div class="alert alert-danger mt-4 text-center small border-0 shadow-sm" style="border-radius: 12px; background-color: #fff5f5; color: #e53e3e;">
                    <i class="bi bi-exclamation-circle me-2"></i> {{ session('error') }}
                </div>
            @endif
            
        </div>
    </div>
</div>
@endsection