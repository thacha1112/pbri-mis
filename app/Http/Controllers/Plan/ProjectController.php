<?php

namespace App\Http\Controllers\Plan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan\Project;
use App\Models\Plan\ProjectActivity;
use App\Models\Plan\ProjectMethod;
use App\Models\Plan\ConstructionType;
use App\Models\Plan\OverseasType;
use App\Models\Common\FiscalYear;
use App\Models\Common\Personnel;
use App\Models\Common\Department;
use App\Models\Plan\Program;
use App\Models\Plan\BudgetSource;
use App\Models\Plan\BudgetCategory;

class ProjectController extends Controller
{
    /**
     * หน้าแรกแสดงรายการตารางโครงการทั้งหมด
     */
    public function index(Request $request)
    {
        $query = Project::query();
        $user = auth()->user();

        // 1. ตรวจสอบสิทธิ์: ถ้าไม่ใช่ Super Admin หรือ Admin (role 1, 2)
        // ให้บังคับ where เฉพาะหน่วยงานของ User คนนั้น
        if (!$user->hasAnyRoleIds([1, 2])) {
            // ดึง department_id จากตาราง personnel ของ user
            if ($user->personnal) {
                $query->where('department_id', $user->personnal->department_id);
            } else {
                // กรณีไม่มีข้อมูล personnel ให้กันไว้เผื่อ user ที่ไม่มีสังกัด
                $query->whereRaw('1=0'); 
            }
        } else {
            // 2. ถ้าเป็น Admin (มีสิทธิ์เห็นทุกหน่วย) ถึงจะยอมให้กรองด้วย Filter ได้
            if ($request->has('department_id') && $request->department_id != 'all') {
                $query->where('department_id', $request->department_id);
            }
        }

        // กรองด้วยปีงบประมาณ (ให้ใช้ได้ทุกคน)
        if ($request->has('fiscal_year_id') && $request->fiscal_year_id != 'all') {
            $query->where('fiscal_year_id', $request->fiscal_year_id);
        }

        $projects = $query->paginate(10)->withQueryString();
        
        // สำหรับ Admin ให้เห็นรายชื่อหน่วยงานทั้งหมด เพื่อเลือก Filter
        // สำหรับ User ปกติ ไม่จำเป็นต้องส่ง $departments ไปให้เห็นก็ได้ หรือจะส่งไปให้เห็นหน่วยงานตัวเองอย่างเดียวก็ได้
        $fiscalYears = FiscalYear::all();
        $departments = $user->hasAnyRoleIds([1, 2]) ? Department::all() : collect();

        return view('plan.projects.index', compact('projects', 'fiscalYears', 'departments'));
    }

    /**
     * หน้าฟอร์มจัดทำโครงการใหม่ (โหมดเพิ่มข้อมูลเริ่มต้น)
     */
    public function create(Request $request)
    {
        $project = new Project(); // ส่งก้อนโมเดลว่างเพื่อแชร์ฟอร์มร่วมกันกับโหมดแก้ไข
        $isEdit = false;
        $activeTab = $request->get('tab', 'general'); // รับค่าจาก URL

        // ดึงข้อมูล Master Lookups ไปป้อนลงช่อง Select ฟิลด์ข้อมูลทั่วไป
        $fiscalYears       = FiscalYear::where('status', 'active')->orderBy('year', 'desc')->get();
        $departments       = Department::where('status', 'active')->get();
        $personnels        = Personnel::where('status', 'active')->get();
        $projectMethods    = ProjectMethod::where('status', 'active')->get();
        $constructionTypes = ConstructionType::where('status', 'active')->get();
        $overseasTypes     = OverseasType::where('status', 'active')->get();

        return view('plan.projects.form', compact(
            'project',
            'isEdit',
            'fiscalYears',
            'departments',
            'personnels',
            'projectMethods',
            'constructionTypes',
            'overseasTypes',
            'activeTab'
        ));
    }

    /**
     * ลอจิก AJAX ประมวลผลเซฟข้อมูลทั่วไปโครงการครั้งแรก (เปิดตั๋วโปรเจกต์)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_code'         => 'required|string|max:100|unique:plan_projects,project_code',
            'name'                 => 'required|string|max:255',
            'fiscal_year_id'       => 'required|integer',
            'personnel_id'         => 'required|integer',
            'department_id'        => 'required|integer',
            'project_method_id'    => 'required|integer',
            'construction_type_id' => 'required|integer',
            'overseas_type_id'     => 'required|integer',
            'start_date'           => 'required|date',
            'end_date'             => 'required|date|after_or_equal:start_date',
        ]);

        // กำหนดสิทธิ์ตั้งต้นความสอดคล้องชั้นที่ 2-4 ให้เป็น 0 ชั่วคราว (เนื่องจากกำหนดในฐานข้อมูลเป็น NOT NULL) 
        // เพื่อให้เซฟผ่านใน Tabที่ 1 ก่อน แล้วค่อยไปอัปเดตสลับค่าจริงใน Tabที่ 2
        /*$validated['mission_id']         = 0;
        $validated['strategic_issue_id'] = 0;
        $validated['goal_id']            = 0;
        $validated['strategy_id']        = 0;*/

        $project = Project::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'บันทึกข้อมูลทั่วไปสำเร็จ! ระบบปลดล็อกแท็บรายการถัดไปเรียบร้อย',
            'project_id' => $project->id
        ]);
    }

    /**
     * หน้าฟอร์มสำหรับดึงข้อมูลโครงการมาแสดงผลแบบแยก Tab ในกรณีเข้าสู่โหมดแก้ไขข้อมูล
     */
    public function edit(Request $request,$id)
    {
        $project = Project::with('projectBudgetSources.budgetSource', 'activities.budgets','activities.subActivities')->findOrFail($id);
        $isEdit = true;
        $activeTab = $request->get('tab', 'general'); // รับค่าจาก URL

        $fiscalYears       = FiscalYear::where('status', 'active')->orderBy('year', 'desc')->get();
        $departments       = Department::where('status', 'active')->get();
        $personnels        = Personnel::where('status', 'active')->get();
        $projectMethods    = ProjectMethod::where('status', 'active')->get();
        $constructionTypes = ConstructionType::where('status', 'active')->get();
        $overseasTypes     = OverseasType::where('status', 'active')->get();

        $budgetSources = BudgetSource::where('fiscal_year_id', $project->fiscal_year_id)->get();
        $programs = Program::all();
        $budgetCategories = BudgetCategory::all();

        return view('plan.projects.form', compact(
            'project',
            'isEdit',
            'fiscalYears',
            'departments',
            'personnels',
            'projectMethods',
            'constructionTypes',
            'overseasTypes',
            'budgetSources', 'programs', 'budgetCategories'
        ));
    }

    /**
     * ลอจิก AJAX ประมวลผลบันทึกอัปเดตความเปลี่ยนแปลงฝั่งข้อมูลทั่วไป (Tabที่ 1)
     */
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'project_code'         => 'required|string|max:100|unique:plan_projects,project_code,' . $id,
            'name'                 => 'required|string|max:255',
            'fiscal_year_id'       => 'required|integer',
            'personnel_id'         => 'required|integer',
            'department_id'        => 'required|integer',
            'project_method_id'    => 'required|integer',
            'construction_type_id' => 'required|integer',
            'overseas_type_id'     => 'required|integer',
            'start_date'           => 'required|date',
            'end_date'             => 'required|date|after_or_equal:start_date',
        ]);

        $project->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'ปรับปรุงข้อมูลทั่วไปโครงการสำเร็จ'
        ]);
    }

    /**
     * ลบข้อมูลโครงการ
     */
    public function destroy($id)
    {
        $project = Project::findOrFail($id);

        // ตรวจสอบว่าโครงการนี้มีงบประมาณผูกพันอยู่หรือไม่
        // สมมติว่าความสัมพันธ์คือ $project->activityBudgets()->exists()
        // หรือเช็คจากตารางที่คุณเก็บข้อมูลแหล่งเงิน
        if ($project->activityBudgets()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่สามารถลบโครงการได้ เนื่องจากมีข้อมูลการจัดสรรแหล่งเงินแล้ว'
            ], 422);
        }

        $project->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'ลบโครงการเรียบร้อยแล้ว'
        ]);
    }
    /**
     * อัปเดตข้อมูลมิติยุทธศาสตร์ความสอดคล้องพันธกิจ (Tabที่ 2)
     */
    public function updateAlignment(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'mission_id'         => 'required|integer',
            'strategic_issue_id' => 'required|integer',
            'goal_id'            => 'required|integer',
            'strategy_id'        => 'required|integer',
        ]);

        // บันทึกทับรหัสจำลองดัมมี่เลข 1 ที่เซ็ตไว้ตอนแรกด้วยรหัสยุทธศาสตร์สถาบันจริง
        $project->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'บันทึกความสอดคล้องแผนยุทธศาสตร์โครงการเรียบร้อยแล้ว!'
        ]);
    }

    public function updateDetails(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        // รับค่าและบันทึกข้อมูลทุกฟิลด์ที่ส่งมาจาก Form
        $project->update($request->only([
            'background_rationale', 
            'objectives', 
            'target_group', 
            'indicators', 
            'outputs'
        ]));

        return response()->json(['success' => true, 'message' => 'บันทึกรายละเอียดเรียบร้อย']);
    }

    public function updateBudget(Request $request, $id)
    {
        try {
            $project = Project::findOrFail($id);

            if ($request->source_id) {
                foreach ($request->source_id as $index => $sourceId) {
                    // เช็คว่ามี ID ส่งมาหรือไม่ (ถ้ามีคือ Update, ถ้าไม่มีคือ Create ใหม่)
                    

                    \App\Models\Plan\ProjectBudgetSource::updateOrCreate(
                        [
                            // เงื่อนไขในการหาว่า "แถวนี้คือแถวเดียวกันหรือไม่"
                            'project_id'       => $project->id,
                            'budget_source_id' => $sourceId,
                        ],
                        [
                            // ข้อมูลที่จะให้ Update (หรือ Create ถ้ายังไม่มี)
                            'program_id'       => $request->program_id[$index] ?? null,
                            'category_id'      => $request->category_id[$index] ?? null,
                            'allocated_amount' => $request->amount[$index] ?? 0,
                        ]
                    );
                }
            }

            return response()->json(['success' => true, 'message' => 'บันทึกเรียบร้อย']);
        } catch (\Exception $e) {
            // หากเกิด Error ให้ส่งเป็น JSON แทนการปล่อยให้ Laravel พ่นหน้า HTML ออกมา
            return response()->json([
                'success' => false, 
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateActivities(Request $request, $id)
    {
        // รับค่าจาก array index 0 (ตามที่คุณออกแบบไว้ว่าส่งมาทีละแถว)
        $data = $request->activities[0]; 

        // รายการ field ทั้งหมดที่ต้องบันทึก
        $activityData = [
            'name'         => $data['name'],
            'objectives'   => $data['objectives'] ?? null,
            'indicators'   => $data['indicators'] ?? null,
            'target_group' => $data['target_group'] ?? null,
            'outputs'      => $data['outputs'] ?? null,
            'start_date'   => $data['start_date'] ?? null,
            'end_date'     => $data['end_date'] ?? null,
        ];

        if (!empty($data['id'])) {
            // กรณีแก้ไข (Update)
            $activity = \App\Models\Plan\ProjectActivity::findOrFail($data['id']);
            $activity->update($activityData);
            // ล้างงบเก่าออกก่อนบันทึกใหม่
            $activity->budgets()->delete();
        } else {
            // กรณีเพิ่มใหม่ (Create)
            $project = Project::findOrFail($id);
            $activity = $project->activities()->create($activityData);
        }

        // บันทึกงบประมาณ
        if (!empty($data['budget'])) {
            foreach ($data['budget'] as $sourceId => $amount) {
                // กรองเฉพาะค่าที่มีจำนวนเงินมากกว่า 0
                if (!is_null($amount) && $amount != 0) {
                    $activity->budgets()->create([
                        'project_budget_source_id' => $sourceId, 
                        'amount' => $amount
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'บันทึกข้อมูลกิจกรรมและงบประมาณเรียบร้อยแล้ว']);
    }
    
    public function getActivityForm($id = 0) 
    {
        // ถ้า id เป็น 0 คือการเพิ่มใหม่, ถ้ามี id คือการแก้ไข
        $activity = $id > 0 ? ProjectActivity::findOrFail($id) : null;
        $project = Project::findOrFail(request('project_id'));
        
      return view('plan.projects.tabs._activity_modal_form', [
            'activity' => $activity,
            'project' => $project,
            'index' => 0 // ระบุเป็น 0 เสมอสำหรับ Modal
        ]);
    }
   

    public function show($id)
    {
        // ดึงข้อมูลโครงการพร้อมกับ Activities และข้อมูลอื่นๆ ที่จำเป็น
        $project = \App\Models\Plan\Project::with(['activities.budgets', 'projectBudgetSources.budgetSource'])->findOrFail($id);
        
        // ส่งไปที่ View แสดงรายละเอียด
        return view('plan.projects.show', compact('project'));
    }
    public function destroyActivity($id)
    {
        try {
            $activity = \App\Models\Plan\ProjectActivity::findOrFail($id);
            
            // ลบงบประมาณที่ผูกกับกิจกรรมนี้ก่อน (เพื่อป้องกัน Error จาก Foreign Key)
            $activity->budgets()->delete();
            
            // ลบกิจกรรม
            $activity->delete();

            return response()->json([
                'success' => true, 
                'message' => 'ลบกิจกรรมเรียบร้อยแล้ว'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'ไม่สามารถลบกิจกรรมได้: ' . $e->getMessage()
            ], 500);
        }
    }

}
