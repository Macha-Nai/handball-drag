<?php

//セッションの生成
session_start();

//ログインの確認
//if(!(isset($_SESSION['login']) && ($_SESSION['login'] == 'OK'))) {

//ログインフォームへ
//header('Location: login.html');

//}

//接続用関数の呼び出し
require_once(__DIR__ . '/functions.php');

?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>新規アカウント作成</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="./css/main.css" rel="stylesheet">
</head>

<body>

    <form action="insert_account.php" method="POST">

        <h1>新規アカウント作成</h1>

        <input placeholder="ユーザ名" type="text" name="user" required />
        <input placeholder="メールアドレス" type="email" name="mail" />
        <input placeholder="パスワード" type="password" name="password" required />
        <div class="form-check">
            <input type="checkbox" name="admin_flag" id="admin_flag" class="form-check-input" value=1><label for="admin_flag" class="form-check-label">管理者</label>
        </div>
        <div class="form-check">
            <input type="checkbox" name="shoot_time_flag" id="shoot_time_flag" class="form-check-input" value=1><label for="shoot_time_flag" class="form-check-label">シュート時間測定</label>
        </div>


        <input class="btn" type="submit" value="登録する">

    </form>
    <footer>
        <h5><a href="./user_menu.php">◀︎ ユーザメニューに戻る</a></h5>
        <br><br><br>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
</body>

</html>