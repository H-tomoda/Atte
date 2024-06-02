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
</body>

</html>