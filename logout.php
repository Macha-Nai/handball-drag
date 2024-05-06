<?php
//セッションの開始
session_start();
session_unset();  //セッションの初期化
session_destroy();  //セッションを破棄
?>

<html>

<head>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>ログアウト</title>
    <link href="./css/main.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/common.css">
</head>

<body>

    <br><br>

    <h1><b>Logout completed</b></h1><br><br>
    <h5>ログアウトしました.</h5><br><br><br>
    <h5><a href="login.php">ログイン画面へ戻る▶︎</a>
        <h5>
</body>

</html>