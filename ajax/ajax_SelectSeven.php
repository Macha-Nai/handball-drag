<?php

header("Content-type: application/json; charset=UTF-8");

//接続用関数の呼び出し
require_once(__DIR__ . './../functions.php');

//DBへの接続
$dbh = connectDB();

$position = "seven";

//セッションで送られるので、今後削除予定。
$game_id = $_POST['game_id'];

$_SESSION['game_id'] = $game_id;

$first_time = $_POST['first_time'];

$latter_time = $_POST['latter_time'];

$team_name = $_POST['team_name'];

$select = $_POST['select'];

$team_id = $_POST['team_id'];

$swift_flag = $_POST['swift_flag'];

$parse = array();

$shoot_course = array();

$shoot_course_s = array();

$shoot_table = array();

$result = array();

if ($dbh) {
  if ($select == 1) {
    $shoot_course = get_shoot_course($position, $team_id, $swift_flag);
    $shoot_course_s = get_shoot_course_s($position, $team_id, $swift_flag);
    $shoot_table = get_shoot_table_select($position, $first_time, $latter_time, $team_id, $team_name, $swift_flag);
  } else if ($select == 2) {
    $shoot_course = get_shoot_course_first($position, $team_id, $latter_time, $swift_flag);
    $shoot_course_s = get_shoot_course_first_s($position, $team_id, $latter_time, $swift_flag);
    $shoot_table = get_shoot_table_select_first($position, $first_time, $latter_time, $team_id, $team_name, $swift_flag);
  } else if ($select == 3) {
    $shoot_course = get_shoot_course_latter($position, $team_id, $latter_time, $swift_flag);
    $shoot_course_s = get_shoot_course_latter_s($position, $team_id, $latter_time, $swift_flag);
    $shoot_table = get_shoot_table_select_latter($position, $first_time, $latter_time, $team_id, $team_name, $swift_flag);
  }
}

$result = [
  'shoot_course' => $shoot_course,
  'shoot_course_s' => $shoot_course_s,
  'shoot_table' => $shoot_table
];

echo json_encode($result);
