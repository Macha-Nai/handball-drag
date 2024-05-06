<?php
//セッションの生成
session_start();
//ログインの確認
if (!(isset($_SESSION['login']) && ($_SESSION['login'] == 'OK'))) {
  //ログインフォームへ
  header('Location: login.php');
}

//接続用関数の呼び出し
require_once(__DIR__ . '/functions.php');

//DBへの接続
$dbh = connectDB();

if ($dbh) {
  $sql = "ALTER TABLE shoot_tb CHANGE own_goal empty_shoot INT COMMENT 'エンプティシュート'";
  $sth = $dbh->query($sql);

  echo 'ok';
}
