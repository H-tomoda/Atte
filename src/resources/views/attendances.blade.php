@extends('layouts.app')

@section('content')
<div class="day-attendance">
    <h3>日付: {{ $dateKey }}</h3>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>氏名</th>
                <th>勤務開始</th>
                <th>勤務終了</th>
                <th>休憩時間</th>
                <th>勤務時間</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($firstDayAttendances as $index => $attendance)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $attendance->user->name }}</td>
                <td>{{ $attendance->clock_in ? $attendance->clock_in->format('H:i') : 'データなし' }}</td>
                <td>{{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '未退勤' }}</td>
                <td>{{ $attendance->break_time ? floor($attendance->break_time / 60) .'時間'. ($attendance->break_time % 60) .'分' : '0時間0分' }}</td>
                <td>
                    @if($attendance->total_work_time)
                    <?php
                    $hours = floor($attendance->total_work_time / 60);
                    $minutes = $attendance->total_work_time % 60;
                    ?>
                    {{ $hours }}時間{{ $minutes }}分
                    @else
                    0時間0分
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection