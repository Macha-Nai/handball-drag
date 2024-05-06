<?php

//接続用関数の呼び出し
require_once(__DIR__ . './../../functions.php');

//DBへの接続
$dbh = connectDB();

$shoot_id = $_POST['shoot_id'];

//セッションで送られるので、今後削除予定。
$game_id = $_POST['game_id'];

//ここからは送られるデータ
$pointed_flag = $_POST['pointed_flag'];
$shoot_team_id = $_POST['shoot_team_id'];
$catch_time = $_POST['catch_time'];
$release_time = $_POST['release_time'];
$shoot_goal_time = $_POST['shoot_goal_time'];
$video_time = $_POST['video_time'];
$shoot_tag = 0;
if ($_POST['shoot_tag'] == null || $_POST['shoot_tag'] == "") {
    $shoot_tag = 'NULL';
} else {
    $shoot_tag = $_POST['shoot_tag'];
}
$shoot_memo = $_POST['shoot_memo'];

if ($dbh) {
    $sql = 'UPDATE `shoot_time_tb` SET `pointed_flag`=' . $pointed_flag . ', `catch_time`=' . $catch_time . ', `release_time`=' . $release_time . ', `goal_time`=' . $shoot_goal_time . ', `shoot_team_id`=' . $shoot_team_id . ',number=' . $shoot_tag . ',video_time=' . $video_time . ',tag="' . $shoot_memo . '" WHERE id=' . $shoot_id;
    $sth = $dbh->query($sql);  //SQLの実行
    $sql = 'SELECT * FROM `shoot_time_tb` WHERE game_id=' . $game_id . ' ORDER BY video_time';
    $sth = $dbh->query($sql);
    $teamname_sql = 'SELECT * FROM `team_tb`';
    $sth2 = $dbh->query($teamname_sql);
    $teamname_list = $sth2->fetchAll(PDO::FETCH_ASSOC);
    $result = "";
    while ($row = $sth->fetch()) {
        $team_id = $row['shoot_team_id'];
        $seconds = $row['video_time'];
        $time = s2h($seconds);
        $point_flag = $row['pointed_flag'];
        $gool_judge = '○';
        if ($point_flag == 0) {
            $gool_judge = '×';
        }
        $result .= '<tr id="';
        $result .=  $row['id'];
        $result .= '"><th>';
        $result .= $time;
        $result .= '</th><td>';
        $result .= $gool_judge;
        $result .= '</td><td>';
        $result .= $teamname_list[$team_id - 1]['abbreviation'];
        $result .= '</td></tr>';
    }
    echo $result;
}
