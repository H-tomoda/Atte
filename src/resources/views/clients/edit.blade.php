<!DOCTYPE html>
<html>

<head>
    <title>取引先の編集</title>
</head>

<body>
    <h1>取引先の編集</h1>
    @if (session('success'))
    <p>{{ session('success') }}</p>
    @endif
    <form action="{{ route('clients.update', $client->id) }}" method="POST">
        @csrf
        <label for="name">取引先名:</label>
        <input type="text" name="name" id="name" value="{{ $client->name }}" required><br>
        <button type="submit">更新</button>
    </form>
</body>

</html>