<!DOCTYPE html>
<html>

<head>
    <title>ファイルアップロード</title>
</head>

<body>
    <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="pdf">PDFファイルを選択:</label>
        <input type="file" name="pdf" id="pdf" required><br>

        <label for="document_type">証票種別:</label>
        <select name="document_type" id="document_type" required>
            <option value="type1">Type 1</option>
            <option value="type2">Type 2</option>
        </select><br>

        <label for="transaction_date">取引日付:</label>
        <input type="date" name="transaction_date" id="transaction_date" required><br>

        <label for="client">取引先:</label>
        <select name="client" id="client" required>
            <option value="client1">Client 1</option>
            <option value="client2">Client 2</option>
        </select><br>

        <label for="transaction_amount">取引金額 (円):</label>
        <input type="number" name="transaction_amount" id="transaction_amount" required><br>

        <label for="remarks">補足事項:</label>
        <textarea name="remarks" id="remarks" required></textarea><br>

        <button type="submit">アップロード</button>
    </form>
</body>

</html>