@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- サイドバー -->
        <div class="col-md-3">
            <h2>スケジュールを追加</h2>
            <form method="post" action="{{ route('schedules.store') }}" id="scheduleForm">
                @csrf
                <div class="form-group">
                    <label for="activity">活動</label>
                    <select name="activity" id="activity" class="form-control" required>
                        <option value="" disabled selected>活動を選択してください</option>
                        <option value="社内活動">社内活動</option>
                        <option value="社外活動">社外活動</option>
                        <option value="その他">その他</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="title">活動タイトル</label>
                    <input type="text" name="title" id="title" class="form-control" placeholder="活動タイトル" required>
                </div>
                <div class="form-group">
                    <label for="date">日付</label>
                    <input type="date" name="date" id="date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="start_time">開始時間</label>
                    <input type="time" name="start_time" id="start_time" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="end_time">終了時間</label>
                    <input type="time" name="end_time" id="end_time" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="description">説明</label>
                    <textarea name="description" id="description" class="form-control" placeholder="説明"></textarea>
                </div>
                <div class="form-group">
                    <label for="location">場所</label>
                    <input type="text" name="location" id="location" class="form-control" placeholder="場所">
                </div>
                <button type="submit" class="btn btn-primary">活動を追加</button>
            </form>
        </div>
        <!-- メインコンテンツ -->
        <div class="col-md-9">
            <h2>あなたのスケジュール（{{ $startOfWeek->format('Y-m-d') }} から {{ $endOfWeek->format('Y-m-d') }}）</h2>
            <div class="row">
                @foreach ($schedules as $schedule)
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            {{ $schedule->title }}
                        </div>
                        <div class="card-body">
                            <p><strong>活動: </strong>{{ $schedule->activity }}</p>
                            <p><strong>日付: </strong>{{ $schedule->date }}</p>
                            <p><strong>時間: </strong>{{ $schedule->start_time }} - {{ $schedule->end_time }}</p>
                            <p><strong>場所: </strong>{{ $schedule->location }}</p>
                            <p><strong>説明: </strong>{{ $schedule->description }}</p>
                            <a href="{{ route('schedules.edit', $schedule->id) }}" class="btn btn-warning">編集</a>
                            <form action="{{ route('schedules.destroy', $schedule->id) }}" method="post" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">削除</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <!-- ページネーションのリンク -->
            <div class="d-flex justify-content-center">
                {{ $schedules->links() }}
            </div>
        </div>
    </div>
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