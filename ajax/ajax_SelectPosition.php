<?php

header("Content-type: application/json; charset=UTF-8");

require_once(__DIR__ . '/..' . '/functions.php'); // パスの記述を明確に

// $dbh = connectDB();

$position = $_POST['position'] ?? null; // PHP 7+ の null合体演算子を使用
$game_id = $_POST['game_id'] ?? null;
$_SESSION['game_id'] = $game_id;
$first_time = $_POST['first_time'] ?? null;
$latter_time = $_POST['latter_time'] ?? null;
$team_name = $_POST['team_name'] ?? null;
$select = $_POST['select'] ?? null;
$team_id = $_POST['team_id'] ?? null;
$swift_flag = $_POST['swift_flag'] ?? null;

$parse = [];
$shoot_course = [];
$shoot_course_s = [];
$shoot_table = [];
$result = [];

// if ($dbh) {
//   if ($select == 1) {
//     $parse = get_parse_all($game_id, $position, $team_id, $swift_flag);
//     $shoot_course = get_shoot_course($position, $team_id, $swift_flag);
//     $shoot_course_s = get_shoot_course_s($position, $team_id, $swift_flag);
//     $shoot_table = get_shoot_table_select($position, $first_time, $latter_time, $team_id, $team_name, $swift_flag);
//   } else if ($select == 2) {
//     $parse = get_parse_first($game_id, $position, $team_id, $latter_time, $swift_flag);
//     $shoot_course = get_shoot_course_first($position, $team_id, $latter_time, $swift_flag);
//     $shoot_course_s = get_shoot_course_first_s($position, $team_id, $latter_time, $swift_flag);
//     $shoot_table = get_shoot_table_select_first($position, $first_time, $latter_time, $team_id, $team_name, $swift_flag);
//   } else if ($select == 3) {
//     $parse = get_parse_latter($game_id, $position, $team_id, $latter_time, $swift_flag);
//     $shoot_course = get_shoot_course_latter($position, $team_id, $latter_time, $swift_flag);
//     $shoot_course_s = get_shoot_course_latter_s($position, $team_id, $latter_time, $swift_flag);
//     $shoot_table = get_shoot_table_select_latter($position, $first_time, $latter_time, $team_id, $team_name, $swift_flag);
//   }
// }

if ($select == 1) {
  $parse = get_parse_all($game_id, $position, $team_id, $swift_flag);
  $shoot_course = get_shoot_course($position, $team_id, $swift_flag);
  $shoot_course_s = get_shoot_course_s($position, $team_id, $swift_flag);
  $shoot_table = get_shoot_table_select($position, $first_time, $latter_time, $team_id, $team_name, $swift_flag);
} else if ($select == 2) {
  $parse = get_parse_first($game_id, $position, $team_id, $latter_time, $swift_flag);
  $shoot_course = get_shoot_course_first($position, $team_id, $latter_time, $swift_flag);
  $shoot_course_s = get_shoot_course_first_s($position, $team_id, $latter_time, $swift_flag);
  $shoot_table = get_shoot_table_select_first($position, $first_time, $latter_time, $team_id, $team_name, $swift_flag);
} else if ($select == 3) {
  $parse = get_parse_latter($game_id, $position, $team_id, $latter_time, $swift_flag);
  $shoot_course = get_shoot_course_latter($position, $team_id, $latter_time, $swift_flag);
  $shoot_course_s = get_shoot_course_latter_s($position, $team_id, $latter_time, $swift_flag);
  $shoot_table = get_shoot_table_select_latter($position, $first_time, $latter_time, $team_id, $team_name, $swift_flag);
}

$result = [
  'parse' => $parse,
  'shoot_course' => $shoot_course,
  'shoot_course_s' => $shoot_course_s,
  'shoot_table' => $shoot_table
];

echo json_encode($result);
