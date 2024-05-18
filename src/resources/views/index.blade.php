@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="attendance__alert">
    @if(count($errors) > 0)
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    @endif
</div>

<div class="attendance__content">
    <div class="date" id="date"></div>
    <div class="clock" id="clock"></div>
    <!-- 現在のステータス表示 -->
    <div class="current-status">
        <h4>現在のステータス:
            @if($status === 0)
            出勤中
            @elseif($status === 1)
            休憩中
            @elseif($status === 2)
            退勤済み
            @else
            ステータス不明
            @endif
        </h4>
    </div>
    <div class="attendance__panel">
        <form class="attendance__button" action="/attendance/clock-in" method="post">
            @csrf
            <button type="submit" class="btn btn-primary" {{ $status == 1 ? 'disabled' : '' }}>勤務開始</button>
        </form>
        <form class="attendance__button" action="/attendance/clock-out" method="post">
            @csrf
            <button type="submit" class="btn btn-secondary" id="clockOutButton" {{ $status == 1 ? 'disabled' : '' }}>勤務終了</button>
        </form>
        <form class="attendance__button" action="{{ route('break.start') }}" method="post">
            @csrf
            <button type="submit" class="btn btn-success" id="startBreakButton" {{ $status == 1 ? 'disabled' : '' }}>休憩開始</button>
        </form>
        <form class="attendance__button" action="{{ route('break.end') }}" method="post">
            @csrf
            <button type="submit" class="btn btn-danger" id="endBreakButton" {{ $status != 1 ? 'disabled' : '' }}>休憩終了</button>
        </form>
    </div>

    <script>
        function updateClock() {
            var now = new Date();
            var hours = now.getHours();
            var minutes = now.getMinutes();
            document.getElementById('clock').textContent = hours.toString().padStart(2, '0') + ':' + minutes.toString().padStart(2, '0');
            document.getElementById('date').textContent = now.toISOString().split('T')[0]; // YYYY-MM-DD 形式
        }
        updateClock();
        setInterval(updateClock, 1000);
    </script>
    @endsection