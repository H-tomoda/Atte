<!DOCTYPE html>
<html>

<head>
    <title>証票種別の追加</title>
</head>

<body>
    <h1>証票種別の追加</h1>
    @if (session('success'))
    <p>{{ session('success') }}</p>
    @endif
    <form action="{{ route('document_types.store') }}" method="POST">
        @csrf
        <label for="name">証票種別名:</label>
        <input type="text" name="name" id="name" required><br>
        <button type="submit">追加</button>
    </form>
</body>

</html>