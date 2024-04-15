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
            //ユーザーの最新出勤レコードを確認
            $attendance = Attendance::where('user_id', $user->id)->latest()->first();
            //出勤していない場合、エラーを返す
            if (!$attendance) {
                return back()->withErrors(['message' => '出勤データがありません']);
            }
            //既に退勤済みの場合、エラーを返す
            if ($attendance->clock_out) {
                return back()->withErrors(['message' => '既に退勤済みです']);
            }
            //ステータスを退勤済みに更新
            $attendance->status = '退勤済み';
            // 退勤時刻を記録
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
