<!DOCTYPE html>
<html>

<head>
    <title>証票種別の編集</title>
</head>

<body>
    <h1>証票種別の編集</h1>
    @if (session('success'))
    <p>{{ session('success') }}</p>
    @endif
    <form action="{{ route('document_types.update', $documentType->id) }}" method="POST">
        @csrf
        <label for="name">証票種別名:</label>
        <input type="text" name="name" id="name" value="{{ $documentType->name }}" required><br>
        <button type="submit">更新</button>
    </form>

    <form action="{{ route('document_types.destroy', $documentType->id) }}" method="POST" style="margin-top: 20px;">
        @csrf
        @method('DELETE')
        <button type="submit" onclick="return confirm('本当に削除しますか？')">削除</button>
    </form>
    <a href="{{ route('upload.form') }}">登録画面に戻る</a>
</body>

</html>