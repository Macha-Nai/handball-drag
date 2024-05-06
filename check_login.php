<?php

//接続用関数の呼び出し
require_once(__DIR__ . '/functions.php');

//セッションの生成
session_start();
if (!(isset($_POST['user']) && isset($_POST['password']))) {
    header('Location: login.php');
}


//ユーザ名/パスワード
$user = htmlspecialchars($_POST['user'], ENT_QUOTES);
$password = htmlspecialchars($_POST['password'], ENT_QUOTES);

$login = 'NG';
//DBへの接続
$dbh = connectDB();
if ($dbh) {
    //データベースへの問い合わせSQL文(文字列)
    $sql = 'SELECT * FROM user_tb WHERE user_name = "' . $user . '"';
    //echo $sql;
    $sth = $dbh->query($sql);  //SQLの実行
    //データの取得
    $result = $sth->fetch(PDO::FETCH_ASSOC);

    //ユーザidの変更
    // $sql = "UPDATE game_tb SET user_id = 11 WHERE id = 4";
    // $sth = $dbh->query($sql);  //SQLの実行

    //認証
    if (password_verify($password, $result['user_password'])) {  //配列数が唯一の場合
        //ログイン成功
        $login = 'OK';

        //表示用ユーザ名をセッション変数に保存
        $_SESSION['user_name'] = $result['user_name'];
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['login'] = $login;
        if ($result['admin_flag'] == true) {
            $_SESSION['admin_flag'] = true;
        }
        if ($result['shoot_time_flag'] == true) {
            $_SESSION['shoot_time_flag'] = true;
        } else {
            $_SESSION['shoot_time_flag'] = false;
        }
    } else {
        //ログイン失敗
        $login = 'Error';
    }

    $shh = null;  //データの消去
    $dbh = null;  //DBを閉じる
}
//ログイン成功ならばページ移動
if ($login == 'OK') {
    //echo 'OK';
    header('Location: user_menu.php');
} else {
    //echo 'NG';
    //ログイン失敗：ログインフォーム画面へ
    header('Location: login_fail.html');
}
