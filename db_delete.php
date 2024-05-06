<?php
//接続用関数の呼び出し
require_once(__DIR__ . '/functions.php');
deleteTable(); //テーブルの削除

//テーブルの生成
function deleteTable()
{
  //DBへの接続
  $dbh = connectDB();
  //データベースの接続確認
  if (!$dbh) {  //接続できていない場合
    echo 'DBに接続できていません．';
    return;
  }

  //テーブルが存在するかを確認するSQL文
  $sql = "DROP TABLE IF EXISTS `affiliation_tb`, `game_tb`, `goal_position_kind_tb`, `shooter_kind_tb`, `shoot_tb`, `team_tb`, `user_group_tb`, `user_tb`, `video_time_tb`";
  $dbh->query($sql); //SQLの実行
}
echo '削除完了';
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>
  <h5><a href='login.php'>◀︎ ログイン画面に移動</a></h5>
</body>

</html>