@extends('layouts.app')

@section('content')
<div class="container">
    <h2>あなたの週間スケジュール</h2>
    <div id="calendar"></div> <!-- カレンダーUIコンポーネントのプレースホルダー -->

    <!-- 新しいスケジュールを提出するためのフォーム -->
    <form method="post" action="{{ route('schedules.store') }}">
        @csrf
        <input type="text" name="title" placeholder="活動タイトル" required>
        <input type="date" name="date" required> <!-- 日付の入力 -->
        <input type="time" name="start_time" required> <!-- 開始時間 -->
        <input type="time" name="end_time" required> <!-- 終了時間 -->
        <textarea name="description" placeholder="説明"></textarea>
        <input type="text" name="location" placeholder="場所">
        <input type="text" name="activity" placeholder="活動">
        <button type="submit">活動を追加</button>
    </form>
</div>
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
@endsection

<link href='{{ asset('fullcalendar/main.css') }}' rel='stylesheet' />
<script src='{{ asset('fullcalendar/main.js') }}'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        form.addEventListener('submit', function() {
            const startTimeInput = document.querySelector('input[name="start_time"]');
            const endTimeInput = document.querySelector('input[name="end_time"]');
            // 秒を追加する
            startTimeInput.value += ':00';
            endTimeInput.value += ':00';
        });
    });
</script>