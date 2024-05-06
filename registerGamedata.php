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

//登録状態か、POSTデータの確認
if (!(isset($_POST['registration_flag']) &&
    $_POST['registration_flag'] == 1 &&
    isset($_POST['game_name']) &&
    isset($_POST['game_date']) &&
    isset($_POST['team_name1']) &&
    isset($_POST['team_name2']) &&
    isset($_POST['abbreviation1']) &&
    isset($_POST['abbreviation2']) &&
    isset($_POST['url'])
)) {
    //登録状態でない場合、差し戻し
    header('Location: insert_gamedata.php');
}

//接続用関数の呼び出し
require_once(__DIR__ . '/functions.php');

$_SESSION['team_name1'] = $_POST['team_name1'];
$_SESSION['team_name2'] = $_POST['team_name2'];
$_SESSION['abbreviation1'] = $_POST['abbreviation1'];
$_SESSION['abbreviation2'] = $_POST['abbreviation2'];

//チームの登録
insertTeam($_SESSION['team_name1'], $_SESSION['abbreviation1'], $_SESSION['user_id']);
insertTeam($_SESSION['team_name2'], $_SESSION['abbreviation2'], $_SESSION['user_id']);

//チームIDの取得
$_SESSION['team_id1'] = getTeamIdFromName($_SESSION['team_name1'], $_SESSION['user_id']);
$_SESSION['team_id2']  = getTeamIdFromName($_SESSION['team_name2'], $_SESSION['user_id']);
//echo 'team ID 1: ' . $team_id1 . '<br>';
//echo 'team ID 2: ' . $team_id2 . '<br>';

if ($_POST['url'] == "") {
    $_POST['url'] = "none";
}

$_SESSION['game_name'] = $_POST['game_name'];
$_SESSION['game_date'] = $_POST['game_date'];
$_SESSION['url'] = $_POST['url'];

//試合の登録
$game_id = registerGame($_SESSION['user_id'], $_SESSION['game_name'], $_SESSION['game_date'], $_SESSION['team_id1'], $_SESSION['team_id2'], $_SESSION['url']);

//試合登録失敗
if ($game_id == -1) {
    header('Location: inputGamedata.php.php');
}

$_SESSION['game_id'] = $game_id;

if ($_SESSION['url'] == "none") {
    $null = "NULL";
    $time_kind1 = 0;
    $video_time1 = 0;
    $time_kind2 = 1;
    $video_time2 = 1800;
    $sql = "INSERT INTO `video_time_tb` (`game_id`, `time_kind`, `team_id`, `video_time`) VALUES ('" . $_SESSION['game_id'] . "', '" . $time_kind1 . "', " . $null . ", '" . $video_time1 . "')";
    $sth = $dbh->query($sql);
    $sql = "INSERT INTO `video_time_tb` (`game_id`, `time_kind`, `team_id`, `video_time`) VALUES ('" . $_SESSION['game_id'] . "', '" . $time_kind2 . "', " . $null . ", '" . $video_time2 . "')";
    $sth = $dbh->query($sql);
}

if ($_SESSION['shoot_time_flag']) {
    header('Location: input_shoot_time.php');
} else {
    header('Location: inputShoot.php');
}
