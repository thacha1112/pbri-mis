# PROJECT MEMORY: PBRI MIS (ระบบจัดการยุทธศาสตร์และโครงสร้างงบประมาณ)

เวอร์ชัน: 1.0 (อัปเดตล่าสุด: มิถุนายน 2569)

## 1. TECHNICAL STACK (เทคโนโลยีที่ใช้)

- Framework: Laravel 13.x / PHP 8.3+
- Database: MySQL 8.x / MariaDB
- Frontend: Blade Template, Bootstrap 5, jQuery (AJAX), Select2 (Bootstrap-5 Theme), SweetAlert2

## 2. DATABASE ARCHITECTURE & RELATIONSHIPS (โครงสร้างฐานข้อมูล)

ความสัมพันธ์ของฐานข้อมูลถูกออกแบบให้ร้อยเรียงกันเป็นแบบลำดับขั้น (Cascading Structure) โดยมีปีงบประมาณ (fiscal_year_id) เป็นตัวควบคุมแกนกลางสูงสุด

### A. ข้อมูลพื้นฐานระบบ (Prefix: config)

- FiscalYear (`fiscal_years`): [id, year, status] (ตารางปีงบประมาณหลัก)

### B. ชั้นข้อมูลฝั่งยุทธศาสตร์ (Prefix: plan, Namespace: App\Models\Plan)

- Mission (`plan_missions`): [id, fiscal_year_id, name, status] (ชั้นที่ 1: พันธกิจ)
- StrategicIssue (`plan_strategic_issues`): [id, mission_id, code, name, status] (ชั้นที่ 2: ประเด็นยุทธศาสตร์)
- Goal (`plan_goals`): [id, strategic_issue_id, code, name, status] (ชั้นที่ 3: เป้าประสงค์องค์กร)
- Strategy (`plan_strategies`): [id, goal_id, code, name, status] (ชั้นที่ 4: กลยุทธ์องค์กร)

### C. ชั้นข้อมูลฝั่งจัดสรรคลังเงิน (Prefix: plan, Namespace: App\Models\Plan)

- BudgetSource (`plan_budget_sources`): [id, fiscal_year_id, name, status] (แหล่งเงินกลาง ผูกเข้ากับปีงบประมาณหลัก)
- Program (`plan_programs`): [id, budget_source_id, name, status] (งบประมาณ Level 1: แผนงานองค์กร)
- BudgetCategory (`plan_budget_categories`): [id, program_id, name, status] (งบประมาณ Level 2: หมวดรายจ่าย/วัสดุ)
  _หมายเหตุ: โครงสร้างคลังเงินมีความยืดหยุ่นสูงมาก (ยึดตามโจทย์ที่ว่า แหล่งเงินบางประเภท เช่น ทุนวิจัยภายนอก/เงินบริจาค ไม่จำเป็นต้องผูกแผนงานหรือหมวดงบประมาณรายจ่ายก็ได้)_

### D. โมดูลงานบุคคล (Prefix: hr, Namespace: App\Models\HR)

- Department (`departments`) และ Personnel (`personnels`) -> มีความพร้อมสำหรับดักดึงข้อมูลไปผูกเป็น "หน่วยงานเจ้าของโครงการ" และ "ผู้รับผิดชอบโครงการ" ในระดับถัดไป

## 3. CORE DEVELOPED COMPONENTS (โมดูลที่พัฒนาเสร็จแล้ว)

- เส้นทางระบบ (Web Routes): ทำระบบ Routing แบบกลุ่มรัดกุมภายใต้ Prefix `plan` โดยใช้ Resource Mapping คัดกรองเฉพาะฟังก์ชันที่ใช้จริง (`except(['create', 'edit', 'show'])`)
- ระบบหลังบ้าน (Controllers): เขียนลอจิกสไตล์ AJAX Async Handler โดยส่งข้อมูลกลับไปแสดงผลฝั่งผู้ใช้ในรูปแบบ JSON `response()->json(['success' => true, 'message' => '...'])`
- รูปแบบหน้าจอ (UI UX Blueprint): ใช้มาตรฐาน Single Page Management (แสดงผลตารางข้อมูลหลัก และเปิดฟอร์มป้อนข้อมูลผ่านกล่องป๊อปอัพ Modal บนหน้าจอเดียวโดยไม่ต้องรีโหลดหน้าเว็บใหม่ทั้งหมด) พร้อมระบบกรอง Client-side Filter นอกตาราง

## 4. RESOLVED PATTERNS & QUICK FIXES (จุดสำคัญที่แก้ไขสำเร็จแล้ว)

- 🔥 บั๊กกล่อง Select2 ในฟอร์ม Modal ไม่กรองข้อมูล (Critical): เนื่องจากปลั๊กอิน Select2 ดักจำสถานะรายการเอาไว้ในหน่วยความจำของมัน การสั่งซ่อนแถวข้อมูลด้วย `.hide()` หรือ `.show()` ของ jQuery ธรรมดา หน้าจอ UI จะไม่เกิดผลลัพธ์การคัดกรองใดๆ วิธีแก้ไขที่สำเร็จและใช้เป็นมาตรฐานในระบบคือ:
  1. ทำการสแกนโคลน (Clone) ตัวเลือกทั้งหมดเก็บไว้ในตัวแปรหน่วยความจำ (Constant) ทันทีที่โหลดหน้าจอเสร็จ
  2. เมื่อผู้ใช้เปลี่ยนค่าระดับบน ให้ล้างข้อมูลแท็กออปชันทิ้งให้หมดเกลี้ยง (`.empty()`)
  3. คัดเลือกเฉพาะรายการที่ตรงตามเงื่อนไขผูกพัน (`data-year-id` หรือ `data-issue-id`) จากหน่วยความจำมาเขียนลงตู้ HTML ใหม่สดๆ
  4. บังคับเรียกใช้สคริปต์ทำลายหน้ากากเดิม `.select2('destroy')` แล้วสั่งรันเปิดระบบ `.select2({...})` ซ้ำอีกรอบเพื่อให้วาดการกรองขึ้นหน้าจอ UI ได้อย่างถูกต้อง 100%
- ฟังก์ชันบล็อกตัวเลือกว่าง (Lockout Selector): พัฒนาระบบตรวจสอบแบบ Dynamic Property หากตรวจไม่พบข้อมูลระดับแม่ (เช่น ปีนั้นไม่มีพันธกิจ/ยุทธศาสตร์) ตัวระบบจะสั่งล็อกแถวเป็นสีเทาทันที (`.prop('disabled', true)`) ป้องกันไม่ให้ผู้ใช้ส่งค่าว่างไปบันทึกจนฐานข้อมูลพัง
- สคริปต์ Null-Safe ป้องกันหน้าจอดับ: เปลี่ยนมาใช้คำสั่งเช็คโครงสร้าง Null-safe operator สไตล์ PHP 8+ และเงื่อนไขตรวจสอบค่าว่าง (`$item->fiscalYear?->year ?? 'ไม่ระบุปี'`) ในไฟล์วิวทุกลำดับชั้น เพื่อป้องกันระบบ Crash ดับหน้าจอตอนที่เรียกประวัติข้อมูลย้อนหลังที่ยังไม่เคยผูกมิติไอดีปีงบประมาณครับ

## 5. NEXT DEVELOPMENT PHASE (แผนงานในก้าวถัดไป)

- ขึ้นโครงสร้างฐานข้อมูลระบบทะเบียนโครงการอนุมัติ (ตาราง `projects`)
- พัฒนาระบบฟอร์มลงทะเบียนโครงการ โดยผูกความสัมพันธ์เข้ากับ กลยุทธ์องค์กรชั้นที่ 4 (`plan_strategies`) และหมวดงบประมาณรายจ่าย Level 2 (`plan_budget_categories`)
- พัฒนาระบบคำนวณวงเงินงบประมาณ และ Ledger Tracking ตรวจสอบยอดเงินคงเหลือของโครงการแบบ Real-time
