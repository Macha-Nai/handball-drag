<?php

//接続用関数の呼び出し
require_once(__DIR__ . '/functions.php');


//入力に不備あり
if (
    !isset($_POST['user']) ||
    !isset($_POST['mail']) ||
    !isset($_POST['password'])
) {
    echo '<html><body>';
    echo "<br><br>";
    echo "<h1><b>Sorry</b></h1> <br>";
    echo "<h1>入力に失敗しています. </h1><br>";
    echo "<h5><a href='newAccount.php'>◀︎ 新規アカウント作成に戻る</a></h5>";
    echo '</body></html>';
    die;
}

//ユーザ名・メールアドレス・パスワード
$user_name = htmlspecialchars($_POST['user'], ENT_QUOTES);
$user_mail = htmlspecialchars($_POST['mail'], ENT_QUOTES);
$user_password = password_hash(htmlspecialchars($_POST['password'], ENT_QUOTES), PASSWORD_DEFAULT);

//DBへの接続
$dbh = connectDB();
if ($dbh) {
    //重複確認 (ユーザ名とメールアドレスの確認)
    $sql = 'SELECT count(*) as `num` FROM `user_tb` WHERE `user_name`="' . $user_name . '" OR `user_mail`="' . $user_mail . '"';
    $sth = $dbh->query($sql);  //SQLの実行
    //データの取得
    $result = $sth->fetch(PDO::FETCH_ASSOC);
    if ($result['num'] > 0) { //重複あり
        echo '<html><body>';
        echo "<br><br>";
        echo "<h1><b>Sorry</b></h1> <br>";
        echo "<h1>すでに登録されたユーザ/メールアドレスです。</h1><br>";
        echo "<h5><a href='newAccount.php'>◀︎ 新規アカウント作成に戻る</a></h5>";
        echo '</body></html>';
        die;
    }
    if (isset($_POST['admin_flag']) && $_POST['shoot_time_flag']) {
        //管理者ユーザ登録 && シュート時間測定権限あり
        registerUser($user_name, $user_mail, $user_password, $_POST['admin_flag'], $_POST['shoot_time_flag']);
    } else if (isset($_POST['admin_flag'])) {
        //管理者ユーザ登録
        registerUser($user_name, $user_mail, $user_password, $_POST['admin_flag'], 0);
    } else if (isset($_POST['shoot_time_flag'])) {
        //シュート時間測定権限あり
        registerUser($user_name, $user_mail, $user_password, 0, $_POST['shoot_time_flag']);
    } else {
        //通常ユーザ登録
        registerUser($user_name, $user_mail, $user_password, 0, 0);
    }
}

?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>新規アカウント作成</title>
    <link href="main.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/common.css">
</head>

<body>


    <?php

    if ($sth == FALSE) {
        echo "<br><br>";
        echo "<h5><a href='newAccount.php'>◀︎ 新規アカウント作成に戻る</a></h5>";
        echo "<br><br>";
        echo "<h1><b>Sorry</b></h1> <br>";
        echo "<h1>入力に失敗しています. </h1><br>";
    } else {
        echo "<h1><b>Completed</b></h1><br><br>";
        echo "<h1>登録が完了しました.</h1>";
    }

    ?>
    <footer>
        <h5><a href="user_menu.php">◀︎ ユーザメニューに戻る</a></h5>
        <br><br><br>
    </footer>
</body>

</html>