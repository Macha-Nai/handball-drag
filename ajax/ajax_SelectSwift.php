<?php

header("Content-type: application/json; charset=UTF-8");

// 接続用関数の呼び出し
require_once(__DIR__ . '/../functions.php'); // パスの記述を修正

// DBへの接続
$dbh = connectDB();

// $_POST 変数からの入力を検証して取得
$game_id = $_POST['game_id'] ?? null;
$first_time = $_POST['first_time'] ?? null;
$latter_time = $_POST['latter_time'] ?? null;
$team_name1 = $_POST['team_name1'] ?? null;
$team_name2 = $_POST['team_name2'] ?? null;
$select = $_POST['select'] ?? null;
$team_id1 = $_POST['team_id1'] ?? null;
$team_id2 = $_POST['team_id2'] ?? null;
$swift_flag = $_POST['swift_flag'] ?? null;

// セッションに game_id を設定
$_SESSION['game_id'] = $game_id;

// 初期化
$parse1 = [];
$parse2 = [];
$table = [];
$result = [];

if ($dbh) {
  switch ($select) {
    case 1:
      $parse1 = get_All($team_id1, $swift_flag);
      $parse2 = get_All($team_id2, $swift_flag);
      $table = get_table($swift_flag, $first_time, $latter_time, $team_id1, $team_id2, $team_name1, $team_name2);
      break;
    case 2:
      $parse1 = get_First($team_id1, $swift_flag, $latter_time);
      $parse2 = get_First($team_id2, $swift_flag, $latter_time);
      $table = get_table_first($swift_flag, $first_time, $latter_time, $team_id1, $team_id2, $team_name1, $team_name2);
      break;
    case 3:
      $parse1 = get_Latter($team_id1, $swift_flag, $latter_time);
      $parse2 = get_Latter($team_id2, $swift_flag, $latter_time);
      $table = get_table_latter($swift_flag, $first_time, $latter_time, $team_id1, $team_id2, $team_name1, $team_name2);
      break;
  }
}

$result = [
  'parse1' => $parse1,
  'parse2' => $parse2,
  'table' => $table,
];

echo json_encode($result);
