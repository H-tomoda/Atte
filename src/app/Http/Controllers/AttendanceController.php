<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

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
                ->whereDate('clock_in', Carbon::today()) // 今日の日付の勤怠データを確認
                ->whereNull('clock_out') //退勤していないかの確認
                ->first(); // 一つのデータのみを取得するため first() を使用する

            // 同一の日に出勤している場合はエラーとする
            if ($existingAttendance !== null) {
                return back()->withErrors(['message' => '既に出勤しています']);
            }
            // 出勤データが存在しない場合に新しい出勤データを生成
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
        try {
            DB::beginTransaction();
            // 過去の勤怠データを確認
            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('clock_in', Carbon::today()) // 今日の日付の勤怠データを確認
                ->whereNull('clock_out') // 退勤していない勤怠データを確認
                ->first();
            // 出勤していない場合はエラーを返す
            if (!$attendance) {
                return back()->withErrors(['message' => '出勤データがありません']);
            }
            // 退勤していない場合はエラーを返す
            if (!$attendance->clock_out) {
                return back()->withErrors(['message' => '出勤していないか、既に退勤済みです']);
            }
            $attendance->clock_out = now();
            $attendance->save();
            DB::commit();
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }
    public function atte()
    {
        $user = Auth::user();
        $attendances = $user->attendances()->orderBy('clock_in', 'desc')->get();

        $attendances->transform(function ($attendance) {
            $attendance->clock_in = Carbon::parse($attendance->clock_in);
            return $attendance;
        });
        // 日付ごとにグループ化
        $groupedAttendances = $attendances->groupBy(function ($attendance) {
            return $attendance->clock_in->toDatestring();
        });
        // compact() 関数で変数をビューに渡す
        return view('atte', compact('groupedAttendances'));
    }
    public function attendances()
    {
        // すべての勤怠データを取得し、日付ごとにグループ化する
        $attendances = Attendance::orderBy('clock_in', 'desc')->get()->groupBy(function ($attendance) {
            // Carbonインスタンスに変換
            $carbonDate = Carbon::parse($attendance->clock_in);
            // toDatestring() を呼び出す
            return $carbonDate->toDateString();
        });
        // 各日付ごとのグループをページネーションする
        $paginatedAttendances = new \Illuminate\Pagination\LengthAwarePaginator(
            $attendances->forPage(\Illuminate\Pagination\Paginator::resolveCurrentPage(), 10),
            $attendances->count(),
            10,
            null,
            ['path' => \illuminate\Pagination\Paginator::resolveCurrentPage()]
        );
        // ビューにグループ化された勤怠データを渡す
        return view('attendances', compact('paginatedAttendances'));
    }
}
