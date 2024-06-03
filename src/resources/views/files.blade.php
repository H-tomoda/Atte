<!DOCTYPE html>
<html>

<head>
    <title>アップロードされたファイル</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>

<body>
    <h1>アップロードされたファイルの一覧</h1>

    <form action="{{ route('files.index') }}" method="GET">
        <div>
            <label for="transaction_date">日付:</label>
            <input type="date" name="transaction_date" id="transaction_date" value="{{ request('transaction_date') }}">
        </div>
        <div>
            <label for="client">取引先:</label>
            <input type="text" name="client" id="client" value="{{ request('client') }}">
        </div>
        <div>
            <label for="transaction_amount_min">取引金額 (円) 以上:</label>
            <input type="number" name="transaction_amount_min" id="transaction_amount_min" value="{{ request('transaction_amount_min') }}">
        </div>
        <div>
            <label for="transaction_amount_max">取引金額 (円) 以下:</label>
            <input type="number" name="transaction_amount_max" id="transaction_amount_max" value="{{ request('transaction_amount_max') }}">
        </div>
        <div>
            <label for="search_type">検索タイプ:</label>
            <select name="search_type" id="search_type">
                <option value="AND" {{ request('search_type') == 'AND' ? 'selected' : '' }}>AND検索</option>
                <option value="OR" {{ request('search_type') == 'OR' ? 'selected' : '' }}>OR検索</option>
            </select>
        </div>
        <button type="submit">検索</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ファイル名</th>
                <th>日付</th>
                <th>取引先</th>
                <th>取引金額</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($files as $file)
            <tr>
                <td>{{ $file->name }} - <a href="{{ asset('storage/' . str_replace('public/', '', $file->path)) }}">ダウンロード</a></td>
                <td>{{ $file->transaction_date }}</td>
                <td>{{ $file->client }}</td>
                <td>{{ number_format($file->transaction_amount) }}円</td>
                <td><a href="{{ route('files.edit', $file->id) }}">編集</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('upload.form') }}">登録画面に戻る</a>
</body>

</html>