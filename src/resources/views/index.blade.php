@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="attendance__alert">
    // メッセージ機能
</div>

<div class="attendance__content">
    <div class="attendance__panel">
        <form class="attendance__button" action="/attendance/clock-in" method="post">
            @csrf
            <button class="attendance__button-submit" type="submit">勤務開始</button>
        </form>
        <form class="attendance__button" action="/attendance/clock-out" method="post">
            @csrf
            <button class="attendance__button-submit" type="submit">勤務終了</button>
        </form>
    </div>
</div>
@endsection