<form id="activityForm">
    @csrf
    <div id="activity-container">
        @foreach($project->activities as $activity)
            @include('plan.projects.tabs._activity_row', ['activity' => $activity])
        @endforeach
    </div>
    <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addNewActivity()">+ เพิ่มกิจกรรม</button>
    <div class="text-end border-t pt-3 mt-4">
        <button type="submit" class="btn btn-success px-5 fw-bold">บันทึกกิจกรรมและงบประมาณ</button>
    </div>
</form>

@push('scripts')
<script>
function toggleSubActivities(activityId, button) {
    let row = $(`#sub-activity-row-${activityId}`);
    
    // ตรวจสอบสถานะการเปิด-ปิด
    if (row.is(':visible')) {
        row.slideUp(200);
        $(button).removeClass('btn-secondary text-white').addClass('btn-outline-secondary');
    } else {
        // สามารถเรียก AJAX โหลดตารางย่อยมาอัปเดตข้างในก่อนแสดงผลได้ที่นี่
        // loadSubActivityTable(activityId);
        
        row.slideDown(200);
        $(button).removeClass('btn-outline-secondary').addClass('btn-secondary text-white');
    }
}

function addNewActivity() {
    let sources = @json($project->projectBudgetSources->load('budgetSource'));
    let actId = 'new_' + Date.now();
    
    // สร้าง input งบประมาณแบบระบุ ID กิจกรรม
    let budgetInputs = sources.map(b => `
        <div class="input-group input-group-sm mb-1">
            <span class="input-group-text" style="width:140px">${b.budget_source.name}</span>
            <input type="number" class="form-control" name="activities[${actId}][budget][${b.id}]" placeholder="0.00" step="0.01">
        </div>
    `).join('');

    let html = `
    <div class="card p-3 mb-3 border-primary activity-row" id="${actId}">
        <div class="row g-2">
            <div class="col-md-12"><label class="form-label small fw-bold">ชื่อกิจกรรม</label>
                <input type="text" class="form-control" name="activities[${actId}][name]" required></div>
            
            <div class="col-md-12"><label class="form-label small fw-bold">วัตถุประสงค์</label>
                <textarea class="form-control" name="activities[${actId}][objectives]" rows="2"></textarea></div>
            
            <div class="col-md-6"><label class="form-label small fw-bold">ตัวชี้วัดโครงการ</label>
                <textarea class="form-control" name="activities[${actId}][indicators]" rows="2"></textarea></div>
            
            <div class="col-md-6"><label class="form-label small fw-bold">กลุ่มเป้าหมาย</label>
                <textarea class="form-control" name="activities[${actId}][target_group]" rows="2"></textarea></div>
            
            <div class="col-md-12"><label class="form-label small fw-bold">ผลผลิต (Outputs)</label>
                <textarea class="form-control" name="activities[${actId}][outputs]" rows="2"></textarea></div>
            
            <div class="col-md-6"><label class="form-label small fw-bold">วันที่เริ่มต้น</label>
                <input type="date" class="form-control" name="activities[${actId}][start_date]" required></div>
            
            <div class="col-md-6"><label class="form-label small fw-bold">วันที่สิ้นสุด</label>
                <input type="date" class="form-control" name="activities[${actId}][end_date]" required></div>
            
            <div class="col-md-12 mt-2">
                <label class="small fw-bold border-bottom pb-1 w-100">จัดสรรงบประมาณ:</label>
                ${budgetInputs}
            </div>
        </div>
        <button type="button" class="btn btn-danger btn-sm mt-2" onclick="$('#${actId}').remove()">ลบกิจกรรมนี้</button>
    </div>`;
    $('#activity-container').append(html);
}
// เพิ่มส่วนนี้ลงไปต่อจากฟังก์ชัน addNewActivity()
$('#activityForm').on('submit', function(e) {
    e.preventDefault(); // หยุดการโหลดหน้าเว็บแบบปกติ

    let formData = $(this).serialize(); // เตรียมข้อมูลจากฟอร์ม

    $.ajax({
        url: `{{ url('plan/projects') }}/${currentProjectId}/update-activities`, // ตรวจสอบว่ามีตัวแปร currentProjectId อยู่ในหน้านี้
        type: 'POST',
        data: formData,
        beforeSend: function() {
            // อาจจะใส่ Loading... ไว้ที่นี่
        },
        success: function(res) {
            Swal.fire({ 
                icon: 'success', 
                title: res.message, 
                timer: 1500,
                showConfirmButton: false 
            });
            // หากต้องการรีโหลดบางส่วนหรือทำอะไรต่อ
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            Swal.fire({ 
                icon: 'error', 
                title: 'เกิดข้อผิดพลาด', 
                text: 'ไม่สามารถบันทึกข้อมูลได้ กรุณาตรวจสอบข้อมูลอีกครั้ง' 
            });
        }
    });
});
</script>
@endpush