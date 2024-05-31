<!DOCTYPE html>
<html>

<head>
    <title>アップロードされたファイル</title>
</head>

<body>
    <h1>アップロードされたファイルの一覧</h1>
    <ul>
        @foreach ($files as $file)
        <li>{{ $file->name }} - <a href="{{ Storage::url($file->path) }}">ダウンロード</a></li>
        @endforeach
    </ul>
</body>

</html>