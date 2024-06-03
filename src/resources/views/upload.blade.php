<!DOCTYPE html>
<html>

<head>
    <title>ファイルアップロード</title>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .form-group label {
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="{{ route('clients.create') }}">取引先マスター登録</a></li>
                <li><a href="{{ route('document_types.create') }}">証票種別マスター登録</a></li>
                <li><a href="{{ route('files.index') }}">伝票一覧画面</a></li>
            </ul>
        </nav>
    </header>

    <h1>ファイルアップロード</h1>

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
        }

        // モーダルを閉じる
        document.getElementsByClassName('close')[0].onclick = function() {
            document.getElementById('myModal').style.display = "none";
        }

        // モーダル外をクリックすると閉じる
        window.onclick = function(event) {
            if (event.target == document.getElementById('myModal')) {
                document.getElementById('myModal').style.display = "none";
            }
        }

        // 取引先を選択してフォームにセット
        document.getElementById('selectClient').onclick = function() {
            var selectedClient = document.getElementById('client_list').value;
            document.getElementById('client').value = selectedClient;
            document.getElementById('myModal').style.display = "none";
        }

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