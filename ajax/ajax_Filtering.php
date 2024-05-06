<?php

header("Content-type: application/json; charset=UTF-8");

// 接続用関数の呼び出し
require_once(__DIR__ . '/../functions.php'); // パスの記述を修正

// DBへの接続
$dbh = connectDB();

// $_POST 変数からの入力を検証して取得
$game_id = $_POST['game_id'] ?? null;
$extension = $_POST['extension'] ?? null;
$first_time = $_POST['first_time'] ?? null;
$latter_time = $_POST['latter_time'] ?? null;
$team_name1 = $_POST['team_name1'] ?? null;
$team_name2 = $_POST['team_name2'] ?? null;
$select = $_POST['select'] ?? null;
$position = $_POST['position'] ?? null;
$team_id1 = $_POST['team_id1'] ?? null;
$team_id2 = $_POST['team_id2'] ?? null;
$swift_flag = $_POST['swift_flag'] ?? null;
$number1 = $_POST['number1'] ?? null;
$number2 = $_POST['number2'] ?? null;
$third_time = $_POST['third_time'] ?? null;
$fourth_time = $_POST['fourth_time'] ?? null;

// 初期化
$data1 = [];
$data2 = [];
$table = [];
$result = [];

if ($dbh) {
    $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id` =' . $_SESSION['game_id'];

    if ($extension == 1) {
        //
    } else {
        // check
    }
}

$result = [
    'parse1' => $parse1,
    'parse2' => $parse2,
    'table' => $table,
];

echo json_encode($result);
