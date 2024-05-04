@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')

@foreach ($paginatedAttendances as $date => $dateAttendances)
<table class="table">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">氏名</th>
            <th scope="col">勤務開始</th>
            <th scope="col">勤務終了</th>
            <th scope="col">休憩時間</th>
            <th scope="col">勤務時間</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th colspan="6" class="date">{{ $date }}</th>
        </tr>
        @foreach ($dateAttendances as $attendance)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $attendance->user->name }}</td>
            <td>{{ $attendance->clock_in->format('H:i') }}</td>
            <td>{{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '未退勤' }}</td>
            <td>
                @if(isset($attendance->break_time))
                {{ floor($attendance->break_time / 60) }}時間{{ $attendance->break_time % 60 }}分
                @else
                0時間0分
                @endif
            </td>
            <td>
                @if(isset($attendance->total_work_time))
                <?php
                $hours = floor($attendance->total_work_time / 60);
                $minutes = $attendance->total_work_time % 60;
                ?>
                {{ $hours }}時間{{ $minutes }}分
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endforeach
{{$paginatedAttendances->links()}}
@endsection