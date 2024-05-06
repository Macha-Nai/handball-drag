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
//データベースの接続確認
if (!$dbh) {  //接続できていない場合
  echo 'DBに接続できていません。';
  return;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
  <title>パスワード変更画面</title>
  <!-- Bootstrap 5.0.2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <!-- 自作CSS -->
  <link href="./css/common.css" rel="stylesheet">
  <link href="./css/change_pass.css" rel="stylesheet">
</head>

<body>
  <header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top navi">
      <button type="button" class="logo" onclick="openMenu()"><?php echo $_SESSION['user_name'][0]; ?></button>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link text-white user_menu" href="user_menu.php">ユーザメニュー</a>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <!-- ポップアップメニュー -->
  <div id="popupMenu" class="menu-popup" onclick="closeMenu()">
    <div class="menu-content" onclick="event.stopPropagation()">
      <!-- 閉じるボタン -->
      <div class="col-10 user_name"><?php echo $_SESSION['user_name']; ?></div>
      <div class="col-2">
        <span class="closebtn" onclick="closeMenu()">&times;</span>
      </div>
      <br>
      <?php
      if (
        isset($_SESSION['admin_flag']) &&
        $_SESSION['admin_flag'] == true
      ) {
        echo '<a href="newAccount.php">アカウント追加</a>';
      }
      ?>
      <a href="logout.php">ログアウト</a>
    </div>
  </div>
  <div class="container-fluid">
    <form action="update_pass.php" method="POST" id="password-change-form">
      <h2>パスワード変更</h2>
      <div>
        <label for="current-password">現在のパスワード:</label>
        <input type="password" id="current-password" name="current-password" required>
      </div>
      <div>
        <label for="new-password">新しいパスワード:</label>
        <input type="password" id="new-password" name="new-password" required>
      </div>
      <div>
        <label for="confirm-new-password">新しいパスワード（確認）:</label>
        <input type="password" id="confirm-new-password" name="confirm-new-password" required>
      </div>
      <div>
        <button type="submit" id="submit-button">パスワード変更</button>
      </div>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./js/change_pass.js"></script>
</body>

</html>