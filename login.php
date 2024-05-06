<?php
//セッションの生成
session_start();

//ログインの確認
if (
    isset($_SESSION['login']) &&
    ($_SESSION['login'] == 'OK')
) {
    //ログインフォームへ
    header('Location: user_menu.php');
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <title>ログイン画面</title>
    <link href="./css/login.css" rel="stylesheet" />
</head>

<body>
    <form action="check_login.php" method="POST">
        <h1>ログイン<span>してください</span></h1>
        <input placeholder="ユーザ名" type="text" name="user" />
        <input placeholder="パスワード" type="password" name="password" />
        <button class="btn" id="login_btn">ログイン</button>
    </form>
    <!-- PDFダウンロードボタンを追加 -->
    <div id="download_container">
        <a href="trackshot_manual.pdf" download="trackshot_manual.pdf" class="dld_btn" id="download_pdf">使用マニュアルをダウンロード</a>
    </div>
</body>

</html>