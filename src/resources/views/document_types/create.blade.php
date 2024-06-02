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

    <h2>証票種別リスト</h2>
    <ul>
        @foreach ($documentTypes as $documentType)
        <li>ID: {{ $documentType->id }}, Name: {{ $documentType->name }} <a href="{{ route('document_types.edit', $documentType->id) }}">編集</a></li>
        @endforeach
    </ul>
</body>

</html>