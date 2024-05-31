<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::where('user_id', Auth::id())->whereBetween('start_time', [now()->startOfWeek(), now()->endOfWeek()])->get();
        return view('sfa', compact('schedules'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date' => 'required|date',
                'start_time' => 'required',
                'end_time' => 'required',
                'activity' => 'required',
                'location' => 'nullable|string'
            ]);

            $schedule = new Schedule($validated);
            $schedule->user_id = Auth::id();
            //var_dump($schedule);
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
}
