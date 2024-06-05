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

        .form-group {
            margin-bottom: 15px;
        }

        .form-buttons {
            margin-top: 15px;
        }

        .actions {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="{{ route('clients.create') }}">取引先マスター登録</a></li>
                <li><a href="{{ route('document_types.create') }}">証票種別マスター登録</a></li>
                <li><a href="{{ route('upload.form') }}">登録画面に戻る</a></li>
            </ul>
        </nav>
    </header>

    <h1>アップロードされたファイルの一覧</h1>

    <form action="{{ route('files.index') }}" method="GET" id="searchForm">
        <div class="form-group">
            <label for="transaction_date_start">開始日:</label>
            <input type="date" name="transaction_date_start" id="transaction_date_start" value="{{ request('transaction_date_start') }}">
        </div>
        <div class="form-group">
            <label for="transaction_date_end">終了日:</label>
            <input type="date" name="transaction_date_end" id="transaction_date_end" value="{{ request('transaction_date_end') }}">
        </div>
        <div class="form-group">
            <label for="client">取引先:</label>
            <input type="text" name="client" id="client" value="{{ request('client') }}">
        </div>
        <div class="form-group">
            <label for="transaction_amount_min">取引金額 (円) 以上:</label>
            <input type="number" name="transaction_amount_min" id="transaction_amount_min" value="{{ request('transaction_amount_min') }}">
        </div>
        <div class="form-group">
            <label for="transaction_amount_max">取引金額 (円) 以下:</label>
            <input type="number" name="transaction_amount_max" id="transaction_amount_max" value="{{ request('transaction_amount_max') }}">
        </div>
        <div class="form-group">
            <label for="search_type">検索タイプ:</label>
            <select name="search_type" id="search_type">
                <option value="AND" {{ request('search_type') == 'AND' ? 'selected' : '' }}>AND検索</option>
                <option value="OR" {{ request('search_type') == 'OR' ? 'selected' : '' }}>OR検索</option>
            </select>
        </div>
        <div class="form-buttons">
            <button type="submit">検索</button>
            <button type="button" id="clearButton">クリア</button>
        </div>
    </form>

    <table>
        <thead>
            <tr>
                <th>プレビュー</th>
                <th>ファイル名</th>
                <th>取引日付</th>
                <th>取引先</th>
                <th>取引金額</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($files as $file)
            <tr>
                <td><a href="{{ asset('storage/' . str_replace('public/', '', $file->path)) }}" target="_blank">プレビュー確認</a></td>
                <td>{{ $file->name }}</td>
                <td>{{ $file->transaction_date }}</td>
                <td>{{ $file->client }}</td>
                <td>{{ number_format($file->transaction_amount) }}円</td>
                <td class="actions"><a href="{{ route('files.edit', $file->id) }}">編集</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $files->links() }}
    <a href="{{ route('upload.form') }}">登録画面に戻る</a>

    <script>
        document.getElementById('clearButton').addEventListener('click', function() {
            document.getElementById('transaction_date_start').value = '';
            document.getElementById('transaction_date_end').value = '';
            document.getElementById('client').value = '';
            document.getElementById('transaction_amount_min').value = '';
            document.getElementById('transaction_amount_max').value = '';
            document.getElementById('search_type').selectedIndex = 0;
            document.getElementById('searchForm').submit();
        });
    </script>
</body>


</html>