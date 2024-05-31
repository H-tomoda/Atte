<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BreakController;
use App\Models\Attendance;
use PhpParser\Node\Stmt\Break_;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\PdfFileController;

Route::middleware('auth')->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('home');
    Route::middleware('password.auth')->group(function () {
        // Route::get('/attendances', [AttendanceController::class, 'index']);
        Route::get('/password/form', [AttendanceController::class, 'showPasswordForm'])->name('password.form');
        Route::post('/authenticate/with/password', [AttendanceController::class, 'authenticateWithPassword'])->name('authenticate.with.password');
    });
});
Route::get('/attendance', [AttendanceController::class, 'index']);
Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn']);
Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut']);
Route::get('/atte', [AttendanceController::class, 'atte']);
Route::post('/break/start', [BreakController::class, 'startBreak'])->name('break.start');
Route::post('/break/end', [BreakController::class, 'endBreak'])->name('break.end');
Route::get('/attendances', [AttendanceController::class, 'attendances'])->name('attendances');
Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');





Route::get('/upload', [PdfFileController::class, 'create'])->name('upload.form');
Route::post('/upload', [PdfFileController::class, 'store'])->name('upload.store');
Route::get('/files', [PdfFileController::class, 'index'])->name('files.index');
