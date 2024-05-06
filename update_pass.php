<?php
//セッションの生成
session_start();
//ログインの確認
if (!(isset($_SESSION['login']) &&
  ($_SESSION['login'] == 'OK'))) {
  //ログインフォームへ
  header('Location: login.php');
}

//接続用関数の呼び出し
require_once(__DIR__ . '/functions.php');

//DBへの接続
$dbh = connectDB();

if (!$dbh) {  //接続できていない場合
  echo 'DBに接続できていません。';
  return;
}

//登録状態か、POSTデータの確認
if (
  !(isset($_POST['current-password'])) ||
  !(isset($_POST['new-password'])) ||
  !(isset($_POST['confirm-new-password']))
) {
  //登録状態でない場合、差し戻し
  header('Location: change_pass.php');
}

$password = htmlspecialchars($_POST['current-password'], ENT_QUOTES);
$new_password = htmlspecialchars($_POST['new-password'], ENT_QUOTES);
$confirm_new_password = htmlspecialchars($_POST['confirm-new-password'], ENT_QUOTES);

if ($new_password !== $confirm_new_password) {
  //新しいパスワードが一致しない場合、差し戻し
  header('Location: change_pass.php');
}

$sql = 'SELECT user_password FROM user_tb WHERE id=' . $_SESSION['user_id'];
$sth = $dbh->query($sql);
$password_data = $sth->fetch(PDO::FETCH_COLUMN);

$password_check = false;

if (password_verify($password, $password_data)) {
  $password_check = true;
} else {
  $password_check = false;
}

$change_success = false;

if ($password_check) {
  $sql = 'UPDATE user_tb SET user_password="' . password_hash($new_password, PASSWORD_DEFAULT) . '" WHERE id=' . $_SESSION['user_id'];
  $sth = $dbh->query($sql);
  if ($sth != FALSE) {
    $change_success = true;
  } else {
    $change_success = false;
  }
}

?>


<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>パスワード更新画面</title>
  <link href="./css/update_pass.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/common.css">
</head>

<body>
  <?php
  if ($password_check) {
    if ($change_success) {
      echo '<h2>パスワードの変更が完了しました。</h2><footer><h4><a href="user_menu.php">◀︎ ユーザメニューに戻る</a></h4><br><br><br></footer>';
    } else {
      echo '<h2>新しいパスワードと確認用の新しいパスワードが一致しませんでした。もう一度やり直してください。</h2><footer><h4><a href="change_pass.php">◀︎ パスワード変更画面に戻る</a></h4><br><br><br></footer>';
    }
  } else {
    echo '<h2>パスワードが一致しません。もう一度やり直してください。</h2><footer><h4><a href="change_pass.php">◀︎ パスワード変更画面に戻る</a></h4><br><br><br></footer>';
  }
  ?>
</body>

</html>