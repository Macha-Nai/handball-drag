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

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
  <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
  <title>分析結果画面</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <!-- <link rel="stylesheet" href="game_result.css"> -->
  <link rel="stylesheet" href="./css/common.css">
</head>

<body>
  <div class="container">
    <div class="row" id="game_name">
      <?php

      $sql = 'SELECT * FROM game_tb';
      $sth = $dbh->query($sql);  //SQLの実行
      $game_name = $sth->fetchAll(PDO::FETCH_ASSOC);

      // テーブルを生成
      echo '<table class="table">';
      if (!empty($game_name)) {
        // ヘッダ行を生成
        echo "<tr>";
        foreach ($game_name[0] as $key => $value) {
          echo "<th>{$key}</th>";
        }
        echo "</tr>";

        // データ行を生成
        foreach ($game_name as $row) {
          echo "<tr>";
          foreach ($row as $value) {
            echo "<td>{$value}</td>";
          }
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='" . count($game_name[0]) . "'>No data found</td></tr>";
      }
      echo "</table>";
      ?>
    </div>
  </div>
</body>

</html>