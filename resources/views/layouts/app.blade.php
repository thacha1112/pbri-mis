<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ระบบบริหารยุทธศาสตร์และงบประมาณ - PBRI MIS')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Saraban:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.standalone.min.css">


    <style>
        body {
            font-family: 'Saraban', sans-serif;
            background-color: #f4f6f9;
            overflow-x: hidden;
        }

        #wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }

        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background: #1e293b;
            color: #fff;
            min-height: 100vh;
            transition: all 0.3s;
        }

        #sidebar .sidebar-header {
            padding: 20px;
            background: #0f172a;
        }

        #sidebar ul li a {
            padding: 14px 20px;
            font-size: 1rem;
            display: block;
            color: #cbd5e1;
            text-decoration: none;
            transition: all 0.2s;
        }

        #sidebar ul li a:hover {
            color: #fff;
            background: #334155;
        }

        #sidebar ul li.active>a {
            color: #fff;
            background: #2563eb;
            font-weight: 600;
        }

        /* สไตล์สำหรับ Sub-menu (Level 2) */
        .submenu-list li a {
            padding-left: 40px !important;
            font-size: 0.95rem !important;
            background: #111827;
        }

        #content {
            width: 100%;
            padding: 24px;
            min-height: 100vh;
        }

        .fs-7 {
            font-size: 0.90rem !important;
        }

        #baseConfigMenu li a {
            border-left: 3px solid #475569;
        }

        #baseConfigMenu li.active a {
            border-left: 3px solid #3b82f6;
            background: #1e293b !important;
        }
        
        /* 🔥 ชุดแก้ไขด่วน: บังคับล็อกสเกลปฏิทินไม่ให้แตกกระจายตัวกว้างผิดมิติ */
    .datepicker-dropdown {
        padding: 8px !important;
        border-radius: 8px !important;
        border: 1px solid #dee2e6 !important;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        background-color: #ffffff !important;
        z-index: 9999 !important;
    }
    .datepicker table {
        width: 100% !important;
        margin: 0 !important;
    }
    .datepicker table tr td, .datepicker table tr th {
        width: 32px !important;
        height: 32px !important;
        text-align: center !important;
        border-radius: 6px !important;
        font-size: 14px !important;
    }
    /* สไตล์สีไฮไลต์วันที่เลือก */
    .datepicker table tr td.active, 
    .datepicker table tr td.active:hover {
        background-color: #198754 !important; /* เปลี่ยนเป็นสีเขียวธีมระบบแผน */
        color: #ffffff !important;
        background-image: none !important;
    }
    .datepicker table tr td.today, 
    .datepicker table tr td.today:hover {
        background-color: #ffc107 !important;
        color: #000000 !important;
    }

    </style>
    @stack('styles')
</head>


@php
    $isLoginPage = request()->routeIs('login') || request()->is('login');
@endphp


<body>

    <div id="wrapper">
        @unless($isLoginPage)
        <nav id="sidebar">
    <div class="sidebar-header">
        <h5 class="mb-0 fw-bold"><i class="fa-solid fa-layer-group me-2 text-info"></i>PBRI MIS</h5>
    </div>
    <ul class="list-unstyled components mt-3">
        {{-- เมนูจัดการ UMS (ให้เฉพาะ Super Admin หรือ Admin เห็น) --}}
        
        @if(auth()->user() && auth()->user()->hasRole(1))
            <li class="nav-header ps-3 text-uppercase text-white-50 fs-7 fw-bold mb-1">ระบบจัดการสิทธิ์</li>
            <li>
                <a href="#umsMenu" data-bs-toggle="collapse" aria-expanded="{{ Request::is('ums*') ? 'true' : 'false' }}" class="dropdown-toggle {{ Request::is('ums*') ? '' : 'collapsed' }}">
                    <i class="fa-solid fa-shield-halved me-2 text-info"></i> ระบบจัดการสิทธิ์ (UMS)
                </a>
                <ul class="collapse list-unstyled submenu-list {{ Request::is('ums*') ? 'show' : '' }}" id="umsMenu">
                    <li class="{{ Request::is('ums/roles*') ? 'active' : '' }}">
                        <a href="{{ url('ums/roles') }}"><i class="fa-solid fa-user-gear me-2"></i> จัดการบทบาท (Roles)</a>
                    </li>
                    <li class="{{ Request::is('ums/user-roles*') ? 'active' : '' }}">
                        <a href="{{ url('ums/user-roles') }}"><i class="fa-solid fa-users-gear me-2"></i> กำหนดสิทธิ์ผู้ใช้</a>
                    </li>
                </ul>
            </li>
        @endif
        
        <!-- HR Menu -->
        @if(auth()->user() && auth()->user()->hasAnyRoleIds([1,6]))
            <li class="nav-header ps-3 text-uppercase text-white-50 fs-7 fw-bold mb-1">งานบุคลากร (HR)</li>
            <li>
                <a href="#hrMenu" data-bs-toggle="collapse" aria-expanded="{{ Request::is('hr*') ? 'true' : 'false' }}" class="dropdown-toggle {{ Request::is('hr*') ? '' : 'collapsed' }}">
                    <i class="fa-solid fa-users me-2 text-warning"></i> งานบุคลากร (HR)
                </a>
                <ul class="collapse list-unstyled submenu-list {{ Request::is('hr*') ? 'show' : '' }}" id="hrMenu">
                    @if(auth()->user()->hasRole(1))
                        <li class="{{ Request::is('hr/departments*') ? 'active' : '' }}">
                            <a href="{{ url('hr/departments') }}"><i class="fa-solid fa-sitemap me-2"></i> โครงสร้างหน่วยงาน</a>
                        </li>
                    @endif
                    <li class="{{ Request::is('hr/personnels*') ? 'active' : '' }}">
                        <a href="{{ url('hr/personnels') }}"><i class="fa-solid fa-user-tie me-2"></i> ข้อมูลบุคลากร</a>
                    </li>
                </ul>
            </li>
        @endif

        <!-- Plan Menu -->
        @if(auth()->user() && auth()->user()->hasAnyRoleIds([2,4]))
            <li class="nav-header ps-3 text-uppercase text-white-50 fs-7 fw-bold mb-1">ติดตามความก้าวหน้าโครงการ</li>
            <li>
                <a href="#projectPlanMenu" data-bs-toggle="collapse" aria-expanded="{{ Request::is('config*', 'plan*') ? 'true' : 'false' }}" class="dropdown-toggle {{ Request::is('config*', 'plan*') ? '' : 'collapsed' }}">
                    <i class="fa-solid fa-folder-open me-2"></i> แผนโครงการ
                </a>
                <ul class="collapse list-unstyled submenu-list {{ Request::is('config*', 'plan/*') ? 'show' : '' }}" id="projectPlanMenu">
                    @if(auth()->user()->hasAnyRoleIds([1,2]))
                        <li>
                            @php
                                $isBaseConfigActive = Request::is('config/fiscal-years*', 'plan/missions*', 'plan/strategic-issues*', 'plan/goals*', 'plan/strategies*', 'plan/budget-sources*', 'plan/programs*', 'plan/budget-categories*');
                            @endphp
                            <a href="#baseConfigMenu" data-bs-toggle="collapse" aria-expanded="{{ $isBaseConfigActive ? 'true' : 'false' }}" class="dropdown-toggle ps-4 bg-dark text-warning {{ $isBaseConfigActive ? '' : 'collapsed' }}">
                                <i class="fa-solid fa-gears me-2"></i> ข้อมูลพื้นฐานยุทธศาสตร์
                            </a>
                            <ul class="collapse list-unstyled {{ $isBaseConfigActive ? 'show' : '' }}" id="baseConfigMenu" style="background-color: #0b0f17;">
                                <li class="{{ Request::is('config/fiscal-years*') ? 'active' : '' }}"><a href="{{ url('config/fiscal-years') }}" class="ps-5 fs-7 text-white-50"><i class="fa-solid fa-calendar-check me-2 text-warning"></i> จัดการปีงบประมาณ</a></li>
                                <li class="{{ Request::is('plan/missions*') ? 'active' : '' }}"><a href="{{ url('plan/missions') }}" class="ps-5 fs-7 text-white-50"><i class="fa-solid fa-compass me-2"></i> ชั้นที่ 1: พันธกิจ</a></li>
                                <li class="{{ Request::is('plan/strategic-issues*') ? 'active' : '' }}"><a href="{{ url('plan/strategic-issues') }}" class="ps-5 fs-7 text-white-50"><i class="fa-solid fa-bullseye me-2"></i> ชั้นที่ 2: ประเด็นยุทธศาสตร์</a></li>
                                <li class="{{ Request::is('plan/goals*') ? 'active' : '' }}"><a href="{{ url('plan/goals') }}" class="ps-5 fs-7 text-white-50"><i class="fa-solid fa-crosshairs me-2"></i> ชั้นที่ 3: เป้าประสงค์</a></li>
                                <li class="{{ Request::is('plan/strategies*') ? 'active' : '' }}"><a href="{{ url('plan/strategies') }}" class="ps-5 fs-7 text-white-50"><i class="fa-solid fa-chess-knight me-2"></i> ชั้นที่ 4: กลยุทธ์องค์กร</a></li>
                                <li class="{{ Request::is('plan/budget-sources*') ? 'active' : '' }}"><a href="{{ url('plan/budget-sources') }}" class="ps-5 fs-7 text-white-50"><i class="fa-solid fa-wallet me-2 text-success"></i> แหล่งเงินงบประมาณ</a></li>
                                <li class="{{ Request::is('plan/programs*') ? 'active' : '' }}"><a href="{{ url('plan/programs') }}" class="ps-5 fs-7 text-white-50"><i class="fa-solid fa-layer-group me-2 text-info"></i> แผนงาน (แหล่งเงิน L1)</a></li>
                                <li class="{{ Request::is('plan/budget-categories*') ? 'active' : '' }}"><a href="{{ url('plan/budget-categories') }}" class="ps-5 fs-7 text-white-50"><i class="fa-solid fa-tags me-2 text-danger"></i> หมวดงบรายจ่าย (L2)</a></li>
                            </ul>
                        </li>
                    @endif
                    <li class="{{ Request::is('plan/projects*') ? 'active' : '' }}">
                        <a href="{{ url('plan/projects') }}" class="ps-4"><i class="fa-solid fa-file-invoice-dollar me-2 text-success"></i> ข้อมูลโครงการ & แผนเงิน</a>
                    </li>
                </ul>
            </li>
        @endif
        
        <!-- Logout Menu -->
        <li class="mt-4">
            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="button" onclick="confirmLogout()" class="btn btn-outline-danger w-75 mx-auto d-block btn-sm">
                    <i class="fa-solid fa-right-from-bracket me-2"></i> ออกจากระบบ
                </button>
            </form>
        </li>
    </ul>
</nav>
 @endunless
        <div id="content" @if($isLoginPage) style="padding:0;" @endif>
             @unless($isLoginPage)
            <nav class="navbar navbar-expand-lg navbar-light bg-white rounded shadow-sm mb-4 py-3 px-4">
                <div class="container-fluid p-0">
                    <span class="navbar-text fw-bold text-dark fs-5">
                        <i class="fa-solid fa-bars me-2 text-secondary"></i> ระบบบริหารงานแผนและยุทธศาสตร์
                    </span>
                </div>
            </nav>
        @endunless
            <div class="container-fluid p-0">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2-enable').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            $('.datepicker-thai').datepicker({
                language: 'th-th',
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true
            });
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });

        function alertSuccess(message = 'บันทึกข้อมูลสำเร็จ') {
            Swal.fire({
                icon: 'success',
                title: message,
                showConfirmButton: false,
                timer: 1500
            });
        }
        function confirmLogout() {
            Swal.fire({
                title: 'ออกจากระบบ?',
                text: "คุณต้องการออกจากระบบใช่หรือไม่?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            })
        }
    </script>
    @stack('scripts')
</body>

</html>