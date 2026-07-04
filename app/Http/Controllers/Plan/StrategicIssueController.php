<?php

namespace App\Http\Controllers\Plan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan\StrategicIssue;
use App\Models\Plan\Mission;

class StrategicIssueController extends Controller
{
    public function index()
    {
        $issues = StrategicIssue::with('mission.fiscalYear')->orderBy('id', 'desc')->get();
        $missions = Mission::with('fiscalYear')->where('status', 'active')->get();
        return view('plan.strategic_issues.index', compact('issues', 'missions'));
    }

    public function store(Request $request)
    {
        $request->validate(['mission_id' => 'required', 'name' => 'required|string']);
        StrategicIssue::create($request->all());
        return response()->json(['success' => true, 'message' => 'บันทึกประเด็นยุทธศาสตร์สำเร็จ']);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['mission_id' => 'required', 'name' => 'required|string']);
        StrategicIssue::findOrFail($id)->update($request->all());
        return response()->json(['success' => true, 'message' => 'อัปเดตประเด็นยุทธศาสตร์สำเร็จ']);
    }

    public function destroy($id)
    {
        StrategicIssue::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'ลบประเด็นยุทธศาสตร์เรียบร้อยแล้ว']);
    }
}
