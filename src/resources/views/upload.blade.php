<!DOCTYPE html>
<html>

<head>
    <title>ファイルアップロード</title>
</head>

<body>
    <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="file">ファイルを選択:</label>
        <input type="file" name="file" id="file" required><br>

        <label for="document_type">証票種別:</label>
        <select name="document_type" id="document_type" required>
            @foreach($documentTypes as $documentType)
            <option value="{{ $documentType->name }}">{{ $documentType->name }}</option>
            @endforeach
        </select><br>

        <label for="transaction_date">取引日付:</label>
        <input type="date" name="transaction_date" id="transaction_date" required><br>

        <label for="client">取引先:</label>
        <select name="client" id="client" required>
            @foreach($clients as $client)
            <option value="{{ $client->name }}">{{ $client->name }}</option>
            @endforeach
        </select><br>

        <label for="transaction_amount">取引金額 (円):</label>
        <input type="number" name="transaction_amount" id="transaction_amount" required><br>

        <label for="remarks">補足事項:</label>
        <textarea name="remarks" id="remarks" required></textarea><br>

        <button type="submit">アップロード</button>
    </form>
</body>

</html>