<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $attendances = $user->attendances()->orderBy('created_at', 'desc')->get();
        return view('attendance.index', compact('attendances'));
    }
    public function clockIn(Request $request)
    {
        $user = Auth::user();
        try {
            DB::beginTransaction();

            // 過去の勤怠データを確認
            $existingAttendance = Attendance::where('user_id', $user->id)
                ->whereDate('clock_in', today()) // 今日の日付の勤怠データを確認
                ->whereNull('clock_out') //退勤していないかの確認
                ->first();

            // すでに出勤済みの場合はエラーを返す
            if ($existingAttendance) {
                return back()->withErrors(['message' => '既に出勤しています']);
            }
            $attendance = new Attendance();
            $attendance->user_id = $user->id;
            $attendance->clock_in = now();
            $attendance->save();
            DB::commit();
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }
    public function clockOut(Request $request)
    {
        $user = Auth::user();
        // 過去の勤怠データを確認
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('clock_in', today()) // 今日の日付の勤怠データを確認
            ->whereNull('clock_out') // 退勤していない勤怠データを確認
            ->first();

        // 退勤済みの場合はエラーを返す
        if (!$attendance) {
            return back()->withErrors(['message' => '出勤していないか、既に退勤済みです']);
        }
        $attendance->clock_out = now();
        $attendance->save();
        return redirect()->back();
    }
    public function atte()
    {
        $user = Auth::user();
        $attendances = $user->attendances()->orderBy('clock_in', 'desc')->get();

        $attendances->transform(function ($attendance) {
            $attendance->clock_in = \Carbon\Carbon::parse($attendance->clock_in);
            return $attendance;
        });
        // 日付ごとにグループ化
        $groupedAttendances = $attendances->groupBy(function ($attendance) {
            return $attendance->clock_in->toDatestring();
        });
        // compact() 関数で変数をビューに渡す
        return view('atte', compact('groupedAttendances'));
    }
}
