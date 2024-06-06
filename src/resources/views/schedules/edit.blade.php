@extends('layouts.app')

@section('content')
<div class="container">
    <h2>スケジュールを編集</h2>
    <form method="post" action="{{ route('schedules.update', $schedule->id) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="activity">活動</label>
            <select name="activity" id="activity" class="form-control" required>
                <option value="" disabled>活動を選択してください</option>
                <option value="社内活動" {{ $schedule->activity == '社内活動' ? 'selected' : '' }}>社内活動</option>
                <option value="社外活動" {{ $schedule->activity == '社外活動' ? 'selected' : '' }}>社外活動</option>
                <option value="その他" {{ $schedule->activity == 'その他' ? 'selected' : '' }}>その他</option>
            </select>
        </div>
        <div class="form-group">
            <label for="title">活動タイトル</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ $schedule->title }}" required>
        </div>
        <div class="form-group">
            <label for="date">日付</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ $schedule->date }}" required>
        </div>
        <div class="form-group">
            <label for="start_time">開始時間</label>
            <input type="time" name="start_time" id="start_time" class="form-control" value="{{ Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}" required>
        </div>
        <div class="form-group">
            <label for="end_time">終了時間</label>
            <input type="time" name="end_time" id="end_time" class="form-control" value="{{ Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}" required>
        </div>
        <div class="form-group">
            <label for="description">説明</label>
            <textarea name="description" id="description" class="form-control" placeholder="説明">{{ $schedule->description }}</textarea>
        </div>
        <div class="form-group">
            <label for="location">場所</label>
            <input type="text" name="location" id="location" class="form-control" placeholder="場所" value="{{ $schedule->location }}">
        </div>
        <button type="submit" class="btn btn-primary">更新</button>
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