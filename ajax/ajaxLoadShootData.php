<?php

header("Content-type: application/json; charset=UTF-8");

//接続用関数の呼び出し
require_once(__DIR__ . './../functions.php');

//DBへの接続
$dbh = connectDB();

//セッションで送られるので、今後削除予定。
$game_id = $_POST['game_id'];

$shoot_id = $_POST['shoot_id'];

$result = array();

if ($dbh) {
    //ゴール位置の検索
    $sql = 'SELECT * FROM `shoot_tb` WHERE `id`=' . $shoot_id;
    $sth = $dbh->query($sql);  //SQLの実行
    //データの取得
    $shoot = $sth->fetch(PDO::FETCH_ASSOC);
    $position_xy = $shoot['position_xy'];
    $json2 = str_replace('[', '', $position_xy);
    $json3 = str_replace(']', '', $json2);
    $xy_explode = explode(",", $json3);
    // 配列内の要素をfloat型に変換する
    foreach ($xy_explode as $value) {
        $value = floatval($value);
    }
    $result = [
        'position_xy' => $xy_explode,
        'pointed_flag' => $shoot['pointed_flag'],
        'shoot_team_id' => $shoot['shoot_team_id'],
        'rebound' => $shoot['rebound'],
        'swift_attack' => $shoot['swift_attack'],
        'empty_shoot' => $shoot['empty_shoot'],
        'GK_block' => $shoot['GK_block'],
        'DF_block' => $shoot['DF_block'],
        'seven_shoot' => $shoot['7m_shoot'],
        'shooter_kind' => $shoot['shooter_kind'],
        'goal_position' => $shoot['goal_position'],
        'tag' => $shoot['tag'],
        'memo' => $shoot['memo'],
        'video_time' => $shoot['video_time']
    ];
}

echo json_encode($result);
