<!DOCTYPE html>
<html>

<head>
    <title>ファイル情報の編集</title>
</head>

<body>
    <h1>ファイル情報の編集</h1>
    @if (session('success'))
    <p>{{ session('success') }}</p>
    @endif
    <form action="{{ route('files.update', $file->id) }}" method="POST">
        @csrf
        @method('PUT')
        <label for="name">ファイル名:</label>
        <input type="text" name="name" id="name" value="{{ $file->name }}" required><br>

        <label for="document_type">証票種別:</label>
        <select name="document_type" id="document_type" required>
            @foreach($documentTypes as $documentType)
            <option value="{{ $documentType->name }}" {{ $file->document_type == $documentType->name ? 'selected' : '' }}>{{ $documentType->name }}</option>
            @endforeach
        </select><br>

        <label for="transaction_date">取引日付:</label>
        <input type="date" name="transaction_date" id="transaction_date" value="{{ $file->transaction_date }}" required><br>

        <label for="client">取引先:</label>
        <select name="client" id="client" required>
            @foreach($clients as $client)
            <option value="{{ $client->name }}" {{ $file->client == $client->name ? 'selected' : '' }}>{{ $client->name }}</option>
            @endforeach
        </select><br>

        <label for="transaction_amount">取引金額 (円):</label>
        <input type="number" name="transaction_amount" id="transaction_amount" value="{{ $file->transaction_amount }}" required><br>

        <label for="remarks">補足事項:</label>
        <textarea name="remarks" id="remarks" required>{{ $file->remarks }}</textarea><br>

        <button type="submit">更新</button>
    </form>
</body>

</html>