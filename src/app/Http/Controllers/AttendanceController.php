<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakAttendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

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

            //その日の出勤回数を取得
            $todayAttendances = Attendance::where('user_id', $user->id)
                ->whereDate('clock_in', Carbon::today())
                ->count();
            //1日2回目の出勤にエラーで返す
            if ($todayAttendances >= 1) {
                return back()->withErrors(['message' => '1日複数回の出勤を制限しています。管理者にお問合せ下さい']);
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
            $attendance->status = '2';
            // 退勤時刻を記録
            $attendance->clock_out = now();
            // 休憩時間を計算してhh:mm形式で保存
            $breakTime = $this->calculateBreakTime($user->id, $attendance->clock_in, $attendance->clock_out);
            $attendance->break_time = $breakTime;

            // 労働時間の計算
            $startTime = Carbon::parse($attendance->clock_in);
            $endTime = Carbon::parse($attendance->clock_out);
            $workDuration = $endTime->diffInMinutes($startTime); // 労働時間（分単位）

            // 休憩時間を差し引く
            $workDuration -= $attendance->break_time;

            // total_work_time に分単位の労働時間をセット
            $attendance->total_work_time = $workDuration;

            // データベースに保存する前にログを出力
            \Log::info('Clock out method - Before saving to database', [
                'user_id' => $user->id,
                'attendance_id' => $attendance->id,
                'break_time' => $breakTime,
                'total_work_time' => $workDuration // ログに労働時間も含める
            ]);

            // 保存処理
            $attendance->save();

            // ログに出力
            \Log::info('Saved Attendance:', $attendance->toArray());
            DB::commit();
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }
    public function calculateBreakTime($userId, $startTime, $endTime)
    {
        // メソッドが呼び出されたことをログに記録
        Log::info('Start calculateBreakTime method');

        // ユーザーの勤務記録を検索し、ログに記録
        $attendance = Attendance::where('user_id', $userId)
            ->where('clock_in', '>=', $startTime)
            ->where('clock_in', '<=', $endTime)
            ->first();

        Log::info('Attendance record found: ' . json_encode($attendance));

        if ($attendance) {
            // 休憩の開始時間を検索し、ログに記録
            $breakStart = BreakAttendance::where('attendance_id', $attendance->id)
                ->where('start_time', '>=', $startTime)
                ->where('start_time', '<=', $endTime)
                ->min('start_time');

            Log::info('Break start time: ' . $breakStart);

            // 休憩の終了時間を検索し、ログに記録
            $breakEnd = BreakAttendance::where('attendance_id', $attendance->id)
                ->where('end_time', '>=', $startTime)
                ->where('end_time', '<=', $endTime)
                ->max('end_time');

            Log::info('Break end time: ' . $breakEnd);

            if ($breakStart && $breakEnd) {
                // 休憩時間の計算とログへの記録
                $breakDuration = Carbon::parse($breakEnd)->diffInMinutes(Carbon::parse($breakStart));
                Log::info('Break duration (minutes): ' . $breakDuration);

                // 休憩時間を分単位でデータベースに格納する
                $breakDurationInMinutes = (int) $breakDuration;
                $attendance->break_time = $breakDurationInMinutes;
                $attendance->save(); // データベースに保存

                return $breakDurationInMinutes; // 分単位の休憩時間を返す
            }
        }

        // 休憩時間が計算できなかった場合にログに記録
        Log::info('No break time calculated');
        return 0; // デフォルト値として 0 分を返す
    }
    public function atte()
    {
        $user = Auth::user();
        $attendances = $user->attendances()->orderBy('clock_in', 'desc')->get();

        $attendances->transform(function ($attendance) use ($user) {
            $attendance->clock_in = Carbon::parse($attendance->clock_in);
            //$attendance->clock_outの値をCarbonインスタンスに変換して、$attendance->clock_outに再代入する
            $attendance->clock_out = $attendance->clock_out ? Carbon::parse($attendance->clock_out) : null;
            //休憩時間を計算して追加する
            $breakTime = $this->calculateBreakTime($user->id, $attendance->clock_in, $attendance->clock_out);
            $attendance->break_time = $breakTime;
            return $attendance;
        });
        // 日付ごとにグループ化
        $groupedAttendances = $attendances->groupBy(function ($attendance) {
            return $attendance->clock_in->toDatestring();
        });
        //各日付ごとの休憩時間を計算して追加
        $groupedAttendances->each(function ($attendances, $date) {
            $totalBreakTime = $attendances->sum('break_time');
            //日付ごとのそう休憩時間を$groupedAttendancesに追加
            $groupedAttendances[$date]['total_break_time'] = $totalBreakTime;
        });
        // compact() 関数で変数をビューに渡す
        return view('atte', compact('groupedAttendances'));
    }
    public function attendances()
    {
        $attendances = Attendance::with('user')
            ->orderBy('clock_in', 'desc')
            ->get()
            ->map(function ($attendance) {
                $attendance->clock_in = Carbon::parse($attendance->clock_in);
                $attendance->clock_out = $attendance->clock_out ? Carbon::parse($attendance->clock_out) : null;
                return $attendance;
            })
            ->groupBy(function ($attendance) {
                return Carbon::parse($attendance->clock_in)->format('Y-m-d'); // 日付でグループ化
            });

        // ここでページネーションのためのロジックを追加
        // 例として、最初のグループのみを取得して表示
        $dateKey = $attendances->keys()->first();
        $firstDayAttendances = $attendances[$dateKey];

        return view('attendances', compact('firstDayAttendances', 'dateKey'));
    }
}
