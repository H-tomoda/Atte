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
            <td>{{$loop -> iteration}}</td>
            <td scope="col">{{ $attendance -> user->name}}</td>
            <td scope="col">{{ $attendance -> clock_in}}</td>
            <td scope="col">{{ $attendance -> clock_out ??'未退勤'}}</td>
            <td scope="col">休憩時間</td>
            <td scope="col">勤務時間</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endforeach
{{$paginatedAttendances->links()}}
@endsection