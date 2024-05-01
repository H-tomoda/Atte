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
        @foreach ($attendances as $attendance)
        <tr>
            <td>{{$loop -> iteration}}</td>
            <td scope="col">{{ $attendance -> user->name}}</td>
            <td scope="col">{{ $attendance -> clock_in}}</td>
            <td scope="col">{{ $attendance -> clock_out ??'未退勤'}}</td>
            <td scope="col">
                @if(isset($attendance['total_break_time']))
                {{$attendance['break_time']}}時間
                @else
                0 時間
                @endif
            </td>
            <td scope="col">
                @if(isset($attendance['total_work_time']))
                {{$attendance['total_work_time']}}時間
                @else
                0 時間
                @endif
            </td>
        </tr>
        @endforeach
        @endforeach
    </tbody>
</table>
@endsection