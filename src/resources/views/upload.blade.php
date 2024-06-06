<!DOCTYPE html>
<html>

<head>
    <title>ファイルアップロード</title>
    <link rel="stylesheet" href="{{ asset('css/upload.css') }}">
</head>

<body>
    <header>
        <nav class="header-buttons">
            <button onclick="location.href='{{ route('clients.create') }}'">取引先マスター登録</button>
            <button onclick="location.href='{{ route('document_types.create') }}'">証票種別マスター登録</button>
            <button onclick="location.href='{{ route('files.index') }}'">伝票一覧画面</button>
        </nav>
    </header>

    <div class="container">
        <div class="sidebar">
            <h1>ファイルアップロード</h1>

            @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = "{{ route('upload.form') }}";
                }, 3000); // 3秒後にリダイレクト
            </script>
            @endif

            <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="file">ファイルを選択:</label>
                    <input type="file" name="file" id="file" required>
                </div>

                <div class="form-group">
                    <label for="document_type">証票種別:</label>
                    <select name="document_type" id="document_type" required>
                        @foreach($documentTypes as $documentType)
                        <option value="{{ $documentType->name }}">{{ $documentType->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="transaction_date">取引日付:</label>
                    <input type="date" name="transaction_date" id="transaction_date" required>
                </div>

                <div class="form-group">
                    <label for="client">取引先:</label>
                    <select name="client" id="client" required>
                        @foreach($clients as $client)
                        <option value="{{ $client->name }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" id="openModal">検索</button>
                </div>

                <div class="form-group">
                    <label for="transaction_amount">取引金額 (円):</label>
                    <input type="number" name="transaction_amount" id="transaction_amount" required>
                </div>

                <div class="form-group">
                    <label for="remarks">補足事項:</label>
                    <textarea name="remarks" id="remarks" required></textarea>
                </div>

                <button type="submit">アップロード</button>
            </form>
        </div>

        <div class="main-content">
            <h2>簡易ビュー</h2>
            <table>
                <thead>
                    <tr>
                        <th>プレビュー</th>
                        <th>ファイル名</th>
                        <th>取引先</th>
                        <th>取引金額</th>
                        <th>証票種別</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($files as $file)
                    <tr>
                        <td><a href="{{ asset('storage/' . str_replace('public/', '', $file->path)) }}" target="_blank">プレビュー確認</a></td>
                        <td>{{ $file->name }}</td>
                        <td>{{ $file->client }}</td>
                        <td>{{ number_format($file->transaction_amount) }}円</td>
                        <td>{{ $file->document_type }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $files->links('vendor.pagination.bootstrap-4') }}
        </div>
    </div>

    <!-- モーダル -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>取引先を選択</h2>
            <input type="text" id="client_search" placeholder="取引先を検索">
            <select id="client_list" size="10" style="width: 100%; margin-top: 10px;">
                @foreach($clients as $client)
                <option value="{{ $client->name }}">{{ $client->name }}</option>
                @endforeach
            </select>
            <button type="button" id="selectClient">選択</button>
        </div>
    </div>

    <script>
        // モーダルを開く
        document.getElementById('openModal').onclick = function() {
            document.getElementById('myModal').style.display = "block";
        };

        // モーダルを閉じる
        document.getElementsByClassName('close')[0].onclick = function() {
            document.getElementById('myModal').style.display = "none";
        };

        // モーダル外をクリックすると閉じる
        window.onclick = function(event) {
            if (event.target == document.getElementById('myModal')) {
                document.getElementById('myModal').style.display = "none";
            }
        };

        // 取引先を選択してフォームにセット
        document.getElementById('selectClient').onclick = function() {
            var selectedClient = document.getElementById('client_list').value;
            document.getElementById('client').value = selectedClient;
            document.getElementById('myModal').style.display = "none";
        };

        // 取引先の検索
        document.getElementById('client_search').addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            const options = document.getElementById('client_list').options;
            for (let i = 0; i < options.length; i++) {
                const option = options[i];
                if (option.text.toLowerCase().includes(searchValue)) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            }
        });
    </script>
</body>

</html>