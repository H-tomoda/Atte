<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $currentPage = $request->input('page', 1);
        $startOfWeek = Carbon::now()->startOfWeek()->addWeeks($currentPage - 1);
        $endOfWeek = Carbon::now()->endOfWeek()->addWeeks($currentPage - 1);

        $userId = $request->input('user_id', Auth::id());

        // ページネーションを最初に適用
        $schedules = Schedule::where('user_id', $userId)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->paginate(9);

        // ページネーションが適用された後の各スケジュールに対して重複チェック
        $schedules->getCollection()->each(function ($schedule) use ($schedules) {
            $schedule->is_conflict = $schedules->getCollection()->filter(function ($other) use ($schedule) {
                return $other->id !== $schedule->id && $other->date === $schedule->date && (
                    ($other->start_time <= $schedule->start_time && $other->end_time >= $schedule->start_time) ||
                    ($other->start_time <= $schedule->end_time && $other->end_time >= $schedule->end_time) ||
                    ($other->start_time >= $schedule->start_time && $other->end_time <= $schedule->end_time)
                );
            })->isNotEmpty();
        });

        return view('schedules.index', compact('schedules', 'startOfWeek', 'endOfWeek', 'userId'));
    }
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'activity' => 'required',
                'location' => 'nullable|string'
            ]);

            // 重複チェック
            $existingSchedule = Schedule::where('user_id', Auth::id())
                ->where('date', $request->date)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                        ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                        ->orWhere(function ($query) use ($request) {
                            $query->where('start_time', '<', $request->start_time)
                                ->where('end_time', '>', $request->end_time);
                        });
                })
                ->first();

            if ($existingSchedule && !$request->input('confirm', false)) {
                // フロントエンドに重複メッセージを返す
                return back()->withErrors(['confirm' => '時間が重複していますが問題ないでしょうか'])
                    ->withInput()
                    ->with('confirm', true);
            }

            $schedule = new Schedule($validated);
            $schedule->user_id = Auth::id();
            $schedule->save();

            Log::info('Schedule created successfully.', ['id' => $schedule->id]);

            return back()->with('success', 'スケジュールが正常に保存されました。');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error: ' . $e->getMessage(), ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating schedule: ' . $e->getMessage());
            return back()->withErrors('スケジュール保存中にエラーが発生しました。')->withInput();
        }
    }

    public function edit(Schedule $schedule)
    {
        // 時間の形式を設定
        $schedule->start_time = Carbon::parse($schedule->start_time)->format('H:i');
        $schedule->end_time = Carbon::parse($schedule->end_time)->format('H:i');

        return view('schedules.edit', compact('schedule'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'activity' => 'required',
                'location' => 'nullable|string'
            ]);

            $schedule->update($validated);

            Log::info('Schedule updated successfully.', ['id' => $schedule->id]);

            return redirect()->route('schedules.index')->with('success', 'スケジュールが正常に更新されました。');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error: ' . $e->getMessage(), ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating schedule: ' . $e->getMessage());
            return back()->withErrors('スケジュール更新中にエラーが発生しました。')->withInput();
        }
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        Log::info('Schedule deleted successfully.', ['id' => $schedule->id]);

        return back()->with('success', 'スケジュールが正常に削除されました。');
    }

    public function list(Request $request)
    {
        $userId = $request->input('user_id', null);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Schedule::with('user'); // ユーザー情報をロード

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        $schedules = $query->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->paginate(9);

        $users = User::all();
        $loggedInUser = Auth::user();

        return view('schedules.list', compact('schedules', 'users', 'userId', 'startDate', 'endDate', 'loggedInUser'));
    }
}
