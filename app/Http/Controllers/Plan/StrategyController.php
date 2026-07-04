<?php

namespace App\Http\Controllers\Plan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan\Strategy;
use App\Models\Plan\Goal;

class StrategyController extends Controller
{
    public function index()
    {
        $strategies = Strategy::with('goal.strategicIssue.mission.fiscalYear')->orderBy('id', 'desc')->get();
        $goals = Goal::with('strategicIssue')->where('status', 'active')->get();
        return view('plan.strategies.index', compact('strategies', 'goals'));
    }

    public function store(Request $request)
    {
        $request->validate(['goal_id' => 'required', 'name' => 'required|string']);
        Strategy::create($request->all());
        return response()->json(['success' => true, 'message' => 'บันทึกกลยุทธ์สำเร็จ']);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['goal_id' => 'required', 'name' => 'required|string']);
        Strategy::findOrFail($id)->update($request->all());
        return response()->json(['success' => true, 'message' => 'อัปเดตกลยุทธ์สำเร็จ']);
    }

    public function destroy($id)
    {
        Strategy::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'ลบกลยุทธ์เรียบร้อยแล้ว']);
    }
}
