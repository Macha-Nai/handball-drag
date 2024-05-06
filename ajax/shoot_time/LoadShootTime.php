<?php

header("Content-type: application/json; charset=UTF-8");

//接続用関数の呼び出し
require_once(__DIR__ . './../../functions.php');

//DBへの接続
$dbh = connectDB();

//セッションで送られるので、今後削除予定。
$game_id = $_POST['game_id'];

$shoot_id = intval($_POST['shoot_id']);

$result = array();

if ($dbh) {
    //ゴール位置の検索
    $sql = 'SELECT * FROM `shoot_time_tb` WHERE `id`=' . $shoot_id;
    $sth = $dbh->query($sql);  //SQLの実行
    //データの取得
    $shoot = $sth->fetch(PDO::FETCH_ASSOC);
    $result = [
        'pointed_flag' => $shoot['pointed_flag'],
        'shoot_team_id' => $shoot['shoot_team_id'],
        'catch_time' => $shoot['catch_time'],
        'release_time' => $shoot['release_time'],
        'shoot_goal_time' => $shoot['goal_time'],
        'number' => $shoot['number'],
        'tag' => $shoot['tag'],
        'video_time' => $shoot['video_time']
    ];
}

echo json_encode($result);
