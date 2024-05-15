@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="attendance__alert">
    @if(count($errors)>0)
    <ul>
        @foreach ($errors ->all() as $error)
        <li>{{$error}}</li>
        @endforeach
    </ul>
    @endif
</div>

<div class="attendance__content">
    <div class="date" id="date"></div>
    <div class="clock" id="clock"></div>
    <div class="attendance__panel">
        <form class="attendance__button" action="/attendance/clock-in" method="post">
            @csrf
            <button type="submit" class="btn btn-primary"> 勤務開始</button>
        </form>
        <form class="attendance__button" action="/attendance/clock-out" method="post">
            @csrf
            <button type="submit" class="btn btn-secondary" id="clockOutButton">勤務終了</button>
        </form>
        <form class="attendance__button" action="{{route('break.start')}}" method="post">
            @csrf
            <button type="submit" class="btn btn-success" id="startBreakButton">休憩開始</button>
        </form>
        <form class="attendance__button" action="{{route('break.end')}}" method="post">
            @csrf
            <button type="submit" class="btn btn-danger" id="endBreakButton">休憩終了</button>
        </form>
    </div>

    <script>
        function updateClock() {
            var now = new Date();
            var hours = now.getHours();
            var minutes = now.getMinutes();
            var seconds = now.getSeconds();
            var timeString = hours.toString().padStart(2, '0') + ':' + minutes.toString().padStart(2, '0');
            document.getElementById('clock').textContent = timeString;

            var year = now.getFullYear();
            var month = now.getMonth() + 1;
            var day = now.getDate();
            var dateString = year + '/' + month.toString().padStart(2, '0') + '/' + day.toString().padStart(2, '0');
            document.getElementById('date').textContent = dateString;
        }

        // 初期表示
        updateClock();

        // 1秒ごとに更新
        setInterval(updateClock, 1000);

        //退勤ボタンがクリックされた時の処理
        document.getElementById('clockOutButton').addEventListener('click', function() {
            //ボタンの取得
            var startBreakButton = document.getElementById('startBreakButton');
            var endBreakButton = document.getElementById('endBreakButton');
            //ボタンの無効化
            startBreakButton.disabled = true;
            endBreakButton.disabled = true;
        });
    </script>
    @endsection