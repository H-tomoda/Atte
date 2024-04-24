<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakAttendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        // その日の出勤回数を取得
        $todayAttendances = Attendance::where('user_id', $user->id)
            ->whereDate('clock_in', Carbon::today())
            ->count();

        // 1日2回目の出勤にエラーで返す
        if ($todayAttendances >= 1) {
            return back()->withErrors(['message' => '1日複数回の出勤を制限しています。管理者にお問合せ下さい']);
        }

        // 出勤データを保存
        DB::beginTransaction();
        try {
            $attendance = new Attendance();
            $attendance->user_id = $user->id;
            $attendance->clock_in = now();
            $attendance->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('attendance.index')->withErrors(['message' => $e->getMessage()]);
        }

        return view('index');
    }
    public function clockOut(Request $request)
    {
        $user = Auth::user();
        try {
            DB::beginTransaction();
            // ユーザーの最新出勤レコードを確認
            $attendance = Attendance::where('user_id', $user->id)->latest()->first();
            // 出勤していない場合、エラーを返す
            if (!$attendance) {
                return back()->withErrors(['message' => '出勤データがありません']);
            }
            // 既に退勤済みの場合、エラーを返す
            if ($attendance->clock_out) {
                return back()->withErrors(['message' => '既に退勤済みです']);
            }
            // ステータスを退勤済みに更新
            $attendance->status = '退勤済み';
            // 退勤時刻を記録
            $attendance->clock_out = now();
            // 休憩時間を計算してhh:mm形式で保存
            var_dump('aaa');
            $breakTime = $this->calculateBreakTime($user->id, $attendance->clock_in, $attendance->clock_out); // 修正
            $attendance->break_time = $breakTime;

            $attendance->save();
            DB::commit();
            return view('index');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }
    public function calculateBreakTime($userId, $startTime, $endTime)
    {
        $breakStart = BreakAttendance::where('user_id', $userId)
            ->where('start_time', '>=', $startTime)
            ->where('start_time', '<=', $endTime)
            ->min('start_time');
        $breakEnd = BreakAttendance::where('user_id', $userId)
            ->where('end_time', '>=', $startTime)
            ->where('end_time', '<=', $endTime)
            ->max('end_time');
        if ($breakStart && $breakEnd) {
            return Carbon::parse($breakEnd)->diffInMinutes(Carbon::parse($breakStart)) / 60;
        } else {
            return 0;
        }
    }
    public function atte()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->withErrors(['message' => 'ログインしてください']);
        }

        $attendances = $user->attendances()
            ->orderBy('clock_in', 'desc')
            ->get()
            ->map(function ($attendance) use ($user) {
                $attendance->clock_in = Carbon::parse($attendance->clock_in);
                $attendance->clock_out = $attendance->clock_out ? Carbon::parse($attendance->clock_out) : null;
                $attendance->break_time = 0; // 初期化
                $attendance->work_time = $attendance->clock_out ? $attendance->clock_in->diffInMinutes($attendance->clock_out) / 60 : 0;
                $attendance->user = $user;
                return $attendance;
            });

        $groupedAttendances = $attendances->groupBy(function ($attendance) {
            return $attendance->clock_in->toDateString();
        })->map(function ($attendances, $date) use ($user) {
            $totalBreakTime = 0;
            $totalWorkTime = 0;

            foreach ($attendances as $attendance) {
                $totalBreakTime += $this->calculateTotalBreakTime($attendance->breaks, $attendance->clock_in, $attendance->clock_out);
                $totalWorkTime += $attendance->work_time;
            }

            return [
                'attendances' => $attendances,
                'total_break_time' => $totalBreakTime,
                'total_work_time' => $totalWorkTime,
                'user' => $user,
            ];
        });

        return view('atte', compact('groupedAttendances'));
    }
    public function calculateTotalBreakTime($breaks, $clockIn, $clockOut)
    {
        $totalBreakTime = 0;
        foreach ($breaks as $break) {
            $totalBreakTime += $break->calculateBreakTime($clockIn, $clockOut);
        }
        return $totalBreakTime;
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
    public function showPasswordForm()
    {
        return view('password.form');
    }
    public function authenticateWithPassword(Request $request)
    {
        $password = env('PASSWORD');
        if ($request->input('password') !== $password) {
            return redirect()->route('password.form')->withErrors(['message' => 'passwordを正しく入力してください']);
        }
        // 認証が成功した場合の処理
        Log::info('Authentication successful'); // ログ出力
        return redirect()->route('attendances'); // 適切なリダイレクト先に変更する
    }
}
