<?php

//接続用関数の呼び出し
require_once(__DIR__ . './../functions.php');

//DBへの接続
$dbh = connectDB();

$shoot_id = $_POST['shoot_id'];
$game_id = $_POST['game_id'];

if ($dbh) {
    $sql = 'DELETE FROM shoot_tb WHERE id = ' . $shoot_id;
    //echo $sql;
    $sth = $dbh->query($sql);  //SQLの実行
    $sql = 'SELECT * FROM `shoot_tb` WHERE game_id=' . $game_id . ' ORDER BY video_time';
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
        $result .= '<tr onclick="Click_Sub(this);" id="';
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
