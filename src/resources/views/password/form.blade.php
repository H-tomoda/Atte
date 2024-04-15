<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Authentication</title>
</head>

<body>
    <h2>管理サイトに入る場合はパスワードを入力してください</h2>
    <h3>※わからない場合は管理者にお問い合わせください</h3>
    <form method="post" action="{{route('authenticate.with.password')}}">
        @csrf
        <input type="password" name="password" placeholder="Password">
        <button type="submit">管理者専用サイト</button>
    </form>
</body>

</html>