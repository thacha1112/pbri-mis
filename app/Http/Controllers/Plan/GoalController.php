<?php

namespace App\Http\Controllers\Plan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan\Goal;
use App\Models\Plan\StrategicIssue;

class GoalController extends Controller
{
    public function index()
    {
        $goals = Goal::with('strategicIssue.mission.fiscalYear')->orderBy('id', 'desc')->get();
        $issues = StrategicIssue::with('mission.fiscalYear')->where('status', 'active')->get();
        return view('plan.goals.index', compact('goals', 'issues'));
    }

    public function store(Request $request)
    {
        $request->validate(['strategic_issue_id' => 'required', 'name' => 'required|string']);
        Goal::create($request->all());
        return response()->json(['success' => true, 'message' => 'บันทึกเป้าประสงค์สำเร็จ']);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['strategic_issue_id' => 'required', 'name' => 'required|string']);
        Goal::findOrFail($id)->update($request->all());
        return response()->json(['success' => true, 'message' => 'อัปเดตเป้าประสงค์สำเร็จ']);
    }

    public function destroy($id)
    {
        Goal::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'ลบเป้าประสงค์เรียบร้อยแล้ว']);
    }
}
