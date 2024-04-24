<?php

use Carbon\Carbon; ?>
html
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')

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
        @foreach ($groupedAttendances as $date => $attendances)
        <tr>
            <th colspan="6" class="date">{{ $date }}</th>
        </tr>
        @foreach ($attendances['attendances'] as $attendance)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $attendance->user->name }}</td>
            <td>{{ Carbon::parse($attendance->clock_in)->format('m-d H:i:s') }}</td>
            <td>{{ $attendance->clock_out ? Carbon::parse($attendance->clock_out)->format('m-d H:i:s') : '未退勤' }}</td>
            <td>
                @php
                // 休憩時間を初期化
                $totalBreakTime = 0;
                // 当該日の出勤データに関連付けられた休憩時間を取得し、休憩時間を合計
                foreach ($attendance->breaks as $break) {
                $totalBreakTime += $break->calculateBreakTime($attendance->clock_in, $attendance->clock_out);
                }
                // 合計した休憩時間をhh:mm形式に変換して表示
                echo sprintf('%02d:%02d', floor($totalBreakTime / 60), $totalBreakTime % 60);
                @endphp
            </td>
            <td>{{ $attendance->work_time ? sprintf('%02d:%02d', floor($attendance->work_time / 60), $attendance->work_time % 60) : '0:00' }}</td>
            <td>
                @php
                dd($attendance->breaks);
                // 休憩時間の計算ロジック
                @endphp
            </td>
            <td>
                @php
                dd($attendance->breaks, $attendance->clock_in, $attendance->clock_out);
                // 休憩時間の計算ロジック
                @endphp
            </td>
        </tr>
        @endforeach
        @endforeach
    </tbody>
</table>
@endsection