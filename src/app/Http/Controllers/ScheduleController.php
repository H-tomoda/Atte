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

        $schedules = Schedule::where('user_id', $userId)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->paginate(10);

        $users = User::all();

        return view('schedules.index', compact('schedules', 'startOfWeek', 'endOfWeek', 'users', 'userId'));
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

    public function list(Request $request)
    {
        $userId = $request->input('user_id', Auth::id());

        $schedules = Schedule::where('user_id', '!=', $userId)
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->paginate(10);

        $users = User::all();

        return view('schedules.list', compact('schedules', 'users', 'userId'));
    }
}
