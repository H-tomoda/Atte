<!DOCTYPE html>
<html>

<head>
    <title>アップロードされたファイル</title>
</head>

<body>
    <h1>アップロードされたファイルの一覧</h1>
    <ul>
        @foreach ($files as $file)
        <li>
            {{ $file->name }} - <a href="{{ asset('storage/' . str_replace('public/', '', $file->path)) }}">ダウンロード</a>
            <br>日付: {{ $file->transaction_date }}
            <br>取引先: {{ $file->client }}
            <br>取引金額: {{ number_format($file->transaction_amount) }}円
            <br><a href="{{ route('files.edit', $file->id) }}">編集</a>
        </li>
        @endforeach
    </ul>
</body>

</html>