<?php

//セッションの生成
session_start();

//接続用関数の呼び出し
require_once(__DIR__ . '/functions.php');

$game_name_flag = false;
$game_date_flag = false;
$team_name1 = "";
$team_name2 = "";
$team1_input_flag = false; //チーム1の入力確認
$team2_input_flag = false; //チーム2の入力確認
$same_team_flag = false; //チームの重複
$url = "";
$url_flag = false; //URLの入力確認
$abbreviation1 = "";
$abbreviation2 = "";
$abbreviation1_input_flag = false; //チーム1の略称の入力確認
$abbreviation2_input_flag = false; // チーム2の略称の入力確認
$same_abbreviation_flag = false; // 略称の重複
$selected_team1_flag = false; // チーム1が登録済みチームか
$selected_team2_flag = false; //チーム2が登録済みチームか

//DBへの接続
$dbh = connectDB();

if (isset($_POST['game_name'])) {
  $game_name = htmlspecialchars($_POST['game_name'], ENT_QUOTES);
  $game_name_flag = true;
}
if (isset($_POST['date'])) {
  $game_date = $_POST['date'];
  $game_date_flag = true;
}

//チーム1
//チーム1の新規入力が選択されており、
//チーム名が記載されている場合
if (
  isset($_POST['team1-radio']) &&
  $_POST['team1-radio'] == "radio_new_team1" &&
  isset($_POST['new_team_name1']) &&
  $_POST['new_team_name1'] != ""
) {
  $team_name1 = htmlspecialchars($_POST['new_team_name1'], ENT_QUOTES);
  $team_exists = confirmTeamFromName($team_name1, $_SESSION['user_id']);
  if ($team_exists != true) { //存在してない
    $team1_input_flag = true;
  }
  //$team1_input_flag = insertTeam($team_name1);
}

//チーム1の新規入力が選択されており、
//略称が記載されている場合
if (
  isset($_POST['team1-radio']) &&
  $_POST['team1-radio'] == "radio_new_team1" &&
  isset($_POST['abbreviation1']) &&
  $_POST['abbreviation1'] != ""
) {
  $abbreviation1 = htmlspecialchars($_POST['abbreviation1'], ENT_QUOTES);
  $abbreviation1_input_flag = true;
}

//チーム1の登録済みチームが選択されており、
//チームが選択されている場合
if (
  isset($_POST['team1-radio']) &&
  $_POST['team1-radio'] == "radio_selected_team1" &&
  isset($_POST['selected_team1']) &&
  $_POST['selected_team1'] != ""
) {
  $team_no1 = $_POST['selected_team1'];
  $team_name1 = searchTeamNameFromID($team_no1);
  if ($team_name1 != "") {
    $team1_input_flag = true; //チーム1入力成功
    $abbreviation1_input_flag = true;
    $selected_team1_flag = true;
  }
}

//チーム2
//チーム2の新規入力が選択されており、
//チーム名が記載されている場合
if (
  isset($_POST['team2-radio']) &&
  $_POST['team2-radio'] == "radio_new_team2" &&
  isset($_POST['new_team_name2']) &&
  $_POST['new_team_name2'] != ""
) {
  $team_name2 = htmlspecialchars($_POST['new_team_name2'], ENT_QUOTES);
  $team_exists = confirmTeamFromName($team_name2, $_SESSION['user_id']);
  if ($team_exists != true) { //存在していない
    $team2_input_flag = true;
  }
  //$team2_input_flag = insertTeam($team_name2);
}

//チーム2の新規入力が選択されており、
//略称が記載されている場合
if (
  isset($_POST['team2-radio']) &&
  $_POST['team2-radio'] == "radio_new_team2" &&
  isset($_POST['abbreviation2']) &&
  $_POST['abbreviation2'] != ""
) {
  $abbreviation2 = htmlspecialchars($_POST['abbreviation2'], ENT_QUOTES);
  $abbreviation2_input_flag = true;
}

//チーム2の登録済みチームが選択されており、
//チームが選択されている場合
if (
  isset($_POST['team2-radio']) &&
  $_POST['team2-radio'] == "radio_selected_team2" &&
  isset($_POST['selected_team2']) &&
  $_POST['selected_team2'] != ""
) {
  $team_no2 = $_POST['selected_team2'];
  $team_name2 = searchTeamNameFromID($team_no2);
  if ($team_name2 != "") {
    $team2_input_flag = true; //チーム2入力成功
    $abbreviation2_input_flag = true;
    $selected_team2_flag = true;
  }
}

if (
  $team_name1 == $team_name2
) {
  $same_team_flag = true;
}

if ($abbreviation1 == $abbreviation2 && $abbreviation1 != "" && $abbreviation2 != "") {
  $same_abbreviation_flag = true;
}

if (isset($_POST['url'])) {
  $url = htmlspecialchars($_POST['url'], ENT_QUOTES);
  $url_flag = true;
}

if ($selected_team1_flag) {
  $team_id1 = getTeamIdFromName($team_name1, $_SESSION['user_id']);
  $sql = 'SELECT abbreviation FROM `team_tb` WHERE `id`=' . $team_id1;
  $sth = $dbh->query($sql);
  $abbreviation1 = $sth->fetch(PDO::FETCH_COLUMN);
}

if ($selected_team2_flag) {
  $team_id2 = getTeamIdFromName($team_name2, $_SESSION['user_id']);
  $sql = 'SELECT abbreviation FROM `team_tb` WHERE `id`=' . $team_id2;
  $sth = $dbh->query($sql);
  $abbreviation2 = $sth->fetch(PDO::FETCH_COLUMN);
}

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>試合情報送信</title>
  <link rel="stylesheet" href="./common.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <!--
    <link href="main.css" rel="stylesheet">
-->
  <link href="./css/common.css" rel="stylesheet">
</head>

<body>
  <div class="container">
    <?php
    //入力が失敗の場合
    if (
      $game_name_flag == false ||
      $game_date_flag == false ||
      $team1_input_flag == false ||
      $team2_input_flag == false ||
      $abbreviation1_input_flag == false ||
      $abbreviation2_input_flag == false ||
      $same_team_flag == true ||
      $same_abbreviation_flag == true ||
      $url_flag == false
    ) {
      echo "<p>入力に不備があります。</p>";
      echo '<ul>';
      if ($game_name_flag == false) {
        echo '<li>試合情報がありません。</li>';
      }
      if ($game_date_flag == false) {
        echo '<li>日付がありません。</li>';
      }
      if ($team1_input_flag == false) {
        echo '<li>チーム1の情報に不備があります。</li>';
      }
      if ($team2_input_flag == false) {
        echo '<li>チーム2の情報に不備があります。</li>';
      }
      if ($abbreviation1_input_flag == false) {
        echo '<li>チーム1の略称が入力されていません。</li>';
      }
      if ($abbreviation2_input_flag == false) {
        echo '<li>チーム2の略称が入力されていません。</li>';
      }
      if ($same_team_flag == true) {
        echo '<li>チーム1とチーム2が同一です。</li>';
      }
      if ($same_abbreviation_flag == true) {
        echo '<li>チーム1とチーム2の略称が同一です。</li>';
      }
      if ($url_flag == false) {
        echo '<li>URLに不備があります。</li>';
      }
      echo '</ul>';
      echo "<br><br>
		<h5><a href='./inputGamedata.php'>試合情報入力へ戻る</a></h5>
		<br><br><br>
		</body>
		</html>";
      die;
    }
    echo '<h3>以下の内容で試合情報を入力しますか</h3>';
    echo '試合名: ' . $game_name . '<br>';
    echo '試合日: ' . $game_date . '<br>';
    echo 'チーム 1: ' . $team_name1 . '<br>';
    echo 'チーム1の略称: ' . $abbreviation1 . '<br>';
    echo 'チーム 2: ' . $team_name2 . '<br>';
    echo 'チーム2の略称: ' . $abbreviation2 . '<br>';
    echo 'URL: ' . $url;
    ?>
    <form action="./registerGamedata.php" method="POST">
      <input type="hidden" name="registration_flag" value=1>
      <input type="hidden" name="game_name" value="<?php echo $game_name; ?>">
      <input type="hidden" name="game_date" value="<?php echo $game_date ?>">
      <input type="hidden" name="team_name1" value="<?php echo $team_name1; ?>">
      <input type="hidden" name="team_name2" value="<?php echo $team_name2; ?>">
      <input type="hidden" name="abbreviation1" value="<?php echo $abbreviation1; ?>">
      <input type="hidden" name="abbreviation2" value="<?php echo $abbreviation2; ?>">
      <input type="hidden" name="url" value="<?php echo $url; ?>">
      <input type="submit" value="入力開始">
    </form>

    <br><br>
    <h5><a href='inputGamedata.php'>試合情報入力に戻る</a></h5>
    <h5><a href='user_menu.php'>ユーザメニューに戻る</a></h5>
  </div>
</body>

</html>