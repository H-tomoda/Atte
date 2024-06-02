<!DOCTYPE html>
<html>

<head>
    <title>取引先の追加</title>
</head>

<body>
    <h1>取引先の追加</h1>
    @if (session('success'))
    <p>{{ session('success') }}</p>
    @endif
    <form action="{{ route('clients.store') }}" method="POST">
        @csrf
        <label for="name">取引先名:</label>
        <input type="text" name="name" id="name" required><br>
        <button type="submit">追加</button>
    </form>
</body>

</html>