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

    <h2>取引先リスト</h2>
    <ul>
        @foreach ($clients as $client)
        <li>ID: {{ $client->id }}, Name: {{ $client->name }} <a href="{{ route('clients.edit', $client->id) }}">編集</a></li>
        @endforeach
    </ul>
</body>

</html>