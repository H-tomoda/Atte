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
        if (BreakAttendance::isOnBreak($user->id)) {
            return back()->withErrors(['message' => '既に休憩／外出中です']);
        }
        //休憩開始時刻の記録
        $break = new BreakAttendance();
        $break->user_id = $user->id;
        $break->start_time = now();
        $break->save();
        //出勤中のステータスを休憩中に更新
        $attendance = Attendance::where('user_id', $user->id)->latest()->first();
        if ($attendance && !$attendance->clock_out && $attendance->status === '出勤中') {
            $attendance->status = '休憩中';
            $attendance->save();
        }
        return redirect()->back()->with('success', '休憩／外出を開始しました');
    }
    public function endBreak(Request $request)
    {
        //ユーザー特定
        $user = Auth::user();
        //休憩外出中であることを確認
        if (!BreakAttendance::isOnBreak($user->id)) {
            return back()->withErrors(['message' => '休憩／外出中ではありません']);
        }
        //最後の休憩レコードを取得して終了時刻を記録
        $break = BreakAttendance::where('user_id', $user->id)->latest()->first();
        $break->end_time = now();
        $break->save();

        //休憩終了時に出勤ステータスに変更
        $attendance = Attendance::where('user_id', $user->id)->latest()->first();
        if ($attendance && !$attendance->clock_out && $attendance->status === '休憩中') {
            $attendance->status = '出勤中';
            $attendance->save();
        }

        return redirect()->back()->with('success', '休憩／外出を終了しました');
    }
}
