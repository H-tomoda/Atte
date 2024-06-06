@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- ユーザー選択ドロップダウン -->
        <div class="col-md-3">
            <h2>検索条件</h2>
            <form method="GET" action="{{ route('schedules.list') }}" id="searchForm">
                <div class="form-group">
                    <label for="user_id">担当者を選択:</label>
                    <select name="user_id" id="user_id" class="form-control">
                        <option value="">全ての担当者</option>
                        @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ $user->id == $userId ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="start_date">開始日付</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="form-group">
                    <label for="end_date">終了日付</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <button type="submit" class="btn btn-primary">検索</button>
            </form>
        </div>
        <!-- メインコンテンツ -->
        <div class="col-md-9">
            <h2>スケジュール一覧</h2>
            <div class="row">
                <div class="col-12 mb-4">
                    <h4>ログインユーザー: {{ $loggedInUser->name }}</h4>
                </div>
                @foreach ($schedules as $schedule)
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            {{ $schedule->title }}
                        </div>
                        <div class="card-body">
                            <p><strong>担当者: </strong>{{ $schedule->user->name }}</p>
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
                {{ $schedules->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
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