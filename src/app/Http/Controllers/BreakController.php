<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\BreakAttendance;
use Illuminate\Support\Facades\Auth;

class BreakController extends Controller
{
    public function startBreak(Request $request)
    {
        //ユーザー特定
        $user = Auth::user();
        //該当が休憩中ではない事を確認
        if (Attendance::where('user_id', $user->id)->where('status', '1')->exists()) {
            return back()->withErrors(['message' => '既に休憩／外出中です']);
        }
        //出勤中の最新のレコードを取得
        $attendance = Attendance::where('user_id', $user->id)
            ->whereNull('clock_out') // 未退勤のレコードを取得
            ->latest() // 最新のレコードを取得
            ->first();

        if (!$attendance) {
            return back()->withErrors(['message' => '出勤中のレコードが見つかりません']);
        }

        //休憩開始時刻の記録
        $break = new BreakAttendance();
        $break->attendance_id = $attendance->id; // attendance_idを設定
        $break->start_time = now();
        $break->save();

        //出勤中のステータスを休憩中に更新
        $attendance->status = '1';
        $attendance->save();

        return redirect()->back()->with('success', '休憩／外出を開始しました');
    }

    public function endBreak(Request $request)
    {
        //ユーザー特定
        $user = Auth::user();
        //休憩外出中であることを確認
        if (!Attendance::where('user_id', $user->id)->where('status', '1')->exists()) {
            return back()->withErrors(['message' => '休憩／外出中ではありません']);
        }
        //最後の休憩レコードを取得して終了時刻を記録
        $attendance = Attendance::where('user_id', $user->id)
            ->whereNull('clock_out') // 未退勤のレコードを取得
            ->latest() // 最新のレコードを取得
            ->first();
        if (!$attendance) {
            return back()->withErrors(['message' => '出勤中のレコードが見つかりません']);
        }

        $break = BreakAttendance::where('attendance_id', $attendance->id)->latest()->first();
        $break->end_time = now();
        $break->save();

        //休憩終了時に出勤ステータスに変更
        $attendance->status = '0';
        $attendance->save();

        return redirect()->back()->with('success', '休憩／外出を終了しました');
    }
}
