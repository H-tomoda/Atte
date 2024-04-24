<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\BreakAttendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BreakController extends Controller
{
    public function startBreak(Request $request)
    {
        //ユーザーがログインしている事を確認
        if (!Auth::check()) {
            return redirect()->withErrors(['message' => 'ログインしていません']);
        }

        //ユーザー特定
        $user = Auth::user();

        //出勤レコードを取得
        $attendance = Attendance::where('user_id', $user->id)->orderBy('created_at', 'desc')->first();
        //出勤レコードが存在しない場合のエラー
        if (!$attendance) {
            return back()->withErrors(['message' => '出勤レコードがありません']);
        }
        //休憩開始時刻の記録
        $break = new BreakAttendance();
        $break->user_id = $user->id;
        $break->start_time = now();
        $break->attendance_id = $attendance->id;
        $break->save();

        //出勤中のステータスを休憩中に更新
        if ($attendance->status === '出勤中') {
            $attendance->status = '休憩中';
            $attendance->save();
        }
        return redirect()->back()->with('success', '休憩／外出を開始しました');
    }
    public function endBreak(Request $request)
    {
        //ユーザーがログインしていることを確認
        if (!Auth::check()) {
            return redirect()->withErrors(['message' => 'ログインしていません']);
        }
        //ユーザー特定
        $user = Auth::user();
        //休憩レコードを取得
        $break = BreakAttendance::where('user_id', $user->id)->latest()->first();
        //休憩レコードが存在しない場合はエラー
        if (!$break) {
            return back()->withErrors(['message' => '休憩レコードがありません']);
        }
        //最後の休憩レコードを取得して終了時刻を記録
        $break->end_time = now();
        $break->save();

        //休憩終了時に出勤ステータスに変更
        $attendance = Attendance::find($break->attendance_id);
        if ($attendance && $attendance->status === '休憩中') {
            $attendance->status = '出勤中';
            $attendance->save();
        }

        return redirect()->back()->with('success', '休憩／外出を終了しました');
    }
}
