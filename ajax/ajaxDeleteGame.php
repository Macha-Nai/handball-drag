<?php

//接続用関数の呼び出し
require_once(__DIR__ . './../functions.php');

//DBへの接続
$dbh = connectDB();

//セッションで送られるので、今後削除予定。
$game_id = $_POST['game_id'];

//ここからは送られるデータ
$id = $_POST['id'];

if ($dbh) {
  $sql = 'DELETE FROM `game_tb` WHERE id=' . $id;
  $sth = $dbh->query($sql);  //SQLの実行
  echo "削除完了";
}
