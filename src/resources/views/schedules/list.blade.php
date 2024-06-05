@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- ユーザー選択ドロップダウン -->
        <div class="col-md-3">
            <h2>ユーザーを選択</h2>
            <div class="form-group">
                <label for="user_id">ユーザーを選択:</label>
                <select name="user_id" id="user_id" class="form-control" onchange="location.href='?user_id=' + this.value;">
                    @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{ $user->id == $userId ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <!-- メインコンテンツ -->
        <div class="col-md-9">
            <h2>スケジュール一覧</h2>
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
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <!-- ページネーションのリンク -->
            <div class="d-flex justify-content-center">
                {{ $schedules->appends(request()->query())->links() }}
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