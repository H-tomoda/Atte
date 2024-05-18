@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Attendance Records</h1>
    <div class="row justify-content-center mb-3">
        <div class="col-auto">
            <a href="{{ route('attendances', ['date' => \Carbon\Carbon::parse($date)->subDay()->toDateString()]) }}" class="btn btn-primary">&lt; 1日前</a>
        </div>
        <div class="col-auto">
            <span class="h4">{{ $date }}</span>
        </div>
        <div class="col-auto">
            <a href="{{ route('attendances', ['date' => \Carbon\Carbon::parse($date)->addDay()->toDateString()]) }}" class="btn btn-primary">１日後 &gt;</a>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>社員名</th>
                <th>勤務開始時間</th>
                <th>勤務終了時間</th>
                <th>休憩時間</th>
                <th>労働時間</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $index => $attendance)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $attendance->user->name }}</td>
                <td>{{ $attendance->clock_in ? $attendance->clock_in->format('H:i') : 'No Data' }}</td>
                <td>{{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '勤務中' }}</td>
                <td>{{ floor($attendance->break_time / 60) }}時間 {{ $attendance->break_time % 60 }}分</td>
                <td>{{ floor($attendance->total_work_time / 60) }}時間 {{ $attendance->total_work_time % 60 }}分</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $attendances->links() }}
</div>
@if($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
@endsection