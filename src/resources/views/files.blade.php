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
</body>

</html>