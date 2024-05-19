@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<table class="table">
    <thead>
        <tr>
            <th>Date</th>
            <th>#</th>
            <th>氏名</th>
            <th>勤務開始</th>
            <th>勤務終了</th>
            <th>休憩時間</th>
            <th>勤務時間</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($groupedAttendances as $date => $attendances)
        @foreach ($attendances as $index => $attendance)
        <tr>
            <!-- Display the date only on the first record of each group -->
            <td>{{ $loop->first ? $date : '' }}</td>
            <td>{{ $index + 1 }}</td>
            <td>{{ $attendance->user->name }}</td>
            <td>{{ $attendance->clock_in->format('H:i') }}</td>
            <td>{{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '勤務中' }}</td>
            <td>{{ floor($attendance->break_time / 60) }}時間{{ $attendance->break_time % 60 }}分</td>
            <td>{{ floor($attendance->total_work_time / 60) }}時間{{ $attendance->total_work_time % 60 }}分</td>
        </tr>
        @endforeach
        @endforeach
    </tbody>
</table>
@endsection