@extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm p-4 bg-white">
            <h4 class="fw-bold text-primary mb-1">ยินดีต้อนรับสู่ระบบ PM & Budget</h4>
            <p class="text-secondary mb-0">ระบบนี้พัฒนาด้วย Laravel 13 แบบ Blade Template + CDN พร้อมใช้งานเต็มรูปแบบ</p>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-white">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="m-0 fw-bold text-dark"><i class="fa-solid fa-vial me-2 text-warning"></i>ทดสอบการทำงานของ UI Components</h6>
            </div>
            <div class="card-body p-4">
                <form id="testForm">
                    <div class="mb-3">
                        <label class="form-label fw-bold">เลือกยุทธศาสตร์ (Select2 Autocomplete)</label>
                        <select class="form-select select2-enable" name="test_strategy" required>
                            <option value="">-- พิมพ์ค้นหาหรือเลือกยุทธศาสตร์ --</option>
                            <option value="1">ยุทธศาสตร์ที่ 1: พัฒนาเทคโนโลยีดิจิทัลองค์กร</option>
                            <option value="2">ยุทธศาสตร์ที่ 2: ปรับปรุงโครงสร้างพื้นฐานระบบเซิร์ฟเวอร์</option>
                            <option value="3">ยุทธศาสตร์ที่ 3: เสริมสร้างทักษะการวิเคราะห์ข้อมูล (Power BI)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">วันที่เริ่มโครงการ (DatePicker ภาษาไทย)</label>
                        <div class="input-group">
                            <input type="text" class="form-control datepicker-thai" placeholder="วว/ดด/ปปปป" readonly required>
                            <span class="input-group-text bg-light"><i class="fa-solid fa-calendar-days"></i></span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i class="fa-solid fa-paper-plane me-2"></i>ทดสอบส่งฟอร์ม (SweetAlert2)
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // ทดสอบการกดยืนยันด้วย SweetAlert2 เมื่อส่งฟอร์ม
        $('#testForm').on('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'ยืนยันการบันทึกงาน?',
                text: "นี่คือการทดสอบการเรียกใช้งาน SweetAlert2",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'ตกลง',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    alertSuccess('ระบบ CDN ทุกตัวทำงานได้อย่างสมบูรณ์ครับ!');
                    $('#testForm')[0].reset();
                    $('.select2-enable').val(null).trigger('change');
                }
            });
        });
    });
</script>
@endpush