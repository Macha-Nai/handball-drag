<?php

//接続用関数の呼び出し
require_once(__DIR__ . './../functions.php');

//DBへの接続
$dbh = connectDB();

$shoot_id = $_POST['shoot_id'];

//セッションで送られるので、今後削除予定。
$game_id = $_POST['game_id'];

//ここからは送られるデータ
$position_xy = $_POST['position_xy'];
$pointed_flag = $_POST['pointed_flag'];
$shoot_team_id = $_POST['shoot_team_id'];
$rebound = $_POST['rebound'];
$swift_attack = $_POST['swift_attack'];
$empty_shoot = $_POST['empty_shoot'];
$gk_block_option = $_POST['gk_block_option'];
$df_block_option = $_POST['df_block_option'];
$seven_throw_option = $_POST['seven_throw_option'];
$shooter_kind = $_POST['shooter_kind'];
$goal_position_text = $_POST['goal_position'];
$video_time = intval($_POST['video_time']);
$shoot_tag = $_POST['shoot_tag'];
$shoot_memo = $_POST['shoot_memo'];

if ($seven_throw_option == 'true') {
    $position_xy = 'NULL';
}

if ($dbh) {
    //ゴール位置の検索
    $sql = "SELECT * FROM `goal_position_kind_tb` WHERE `goal_position_kind` LIKE '" . $goal_position_text . "'";
    //echo $sql;
    $sth = $dbh->query($sql);  //SQLの実行
    //データの取得
    $result = $sth->fetch(PDO::FETCH_ASSOC);
    if ($result == false) {
        $goal_position = 'NULL';
    } else {
        $goal_position = $result['id'];
    }
    $sql = 'UPDATE `shoot_tb` SET position_xy="' . $position_xy . '",pointed_flag=' . $pointed_flag . ',shoot_team_id=' . $shoot_team_id . ',rebound=' . $rebound . ',swift_attack=' . $swift_attack . ',empty_shoot=' . $empty_shoot . ',GK_block=' . $gk_block_option . ',DF_block=' . $df_block_option . ',7m_shoot=' . $seven_throw_option . ',shooter_kind=' . $shooter_kind . ',goal_position=' . $goal_position . ',tag="' . $shoot_tag . '",video_time=' . $video_time . ',memo="' . $shoot_memo . '" WHERE id=' . $shoot_id;
    // echo $sql;
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
