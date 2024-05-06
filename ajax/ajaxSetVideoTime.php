<?php

//接続用関数の呼び出し
require_once(__DIR__ . './../functions.php');

//DBへの接続
$dbh = connectDB();

//セッションで送られるので、今後削除予定。
$game_id = $_POST['game_id'];

//ここからは送られるデータ
$time_kind = $_POST['timeRadio'];
$team_id = "NULL";
$video_time = intval($_POST['videoTime']);
$time_flag = false;

if (isset($_POST['team_id'])) {
    $team_id = $_POST['team_id'];
    if ($team_id == -1) {
        $team_id = "NULL";
    }
}

if ($dbh) {
    $sql = "SELECT count(*) as ct FROM `video_time_tb` WHERE `game_id`=" . $game_id . ' AND `time_kind`=' . $time_kind;
    if ($team_id != "NULL") {
        $sql .= " AND `team_id`=" . $team_id;
    }
    $sth = $dbh->query($sql);  //SQLの実行
    //データの取得
    $result = $sth->fetch(PDO::FETCH_ASSOC);
    if ($result['ct'] == 0 || $team_id != "NULL") {
        if ($result['ct'] > 2) {
            $sql = "SELECT * FROM `video_time_tb` WHERE `game_id`=" . $game_id . " AND `team_id`=" . $team_id;
            $sth = $dbh->query($sql);
            echo 'のタイムアウト(';
            // . round($video_time, 2) . ')';
            $flag = false;
            while ($row = $sth->fetch()) {
                if ($flag == true) {
                    echo ',';
                }
                echo s2h($row['video_time']);
                $flag = true;
            }
            echo ')';
            return;
        } else {
            $sql = "INSERT INTO `video_time_tb` (`game_id`, `time_kind`, `team_id`, `video_time`) VALUES ('" . $game_id . "', '" . $time_kind . "', " . $team_id . ", '" . $video_time . "')";
        }
    } else {
        if ($result['ct'] == 0) {
            $sql = "INSERT INTO `video_time_tb` (`game_id`, `time_kind`, `team_id`, `video_time`) VALUES ('" . $game_id . "', '" . $time_kind . "', " . $team_id . ", '" . $video_time . "')";
        } else {
            $sql = "UPDATE `video_time_tb` SET `video_time` = '" . $video_time . "' WHERE `game_id` = " . $game_id . ' AND `time_kind`=' . $time_kind;
        }
    }
    $sth = $dbh->query($sql);
    $video_time_change = s2h($video_time);
    if ($time_kind == 0) {
        echo '前半開始(' . $video_time_change . ')';
    } else if ($time_kind == 1) {
        echo '後半開始(' . $video_time_change . ')';
    } else if ($time_kind == 2) {
        echo '延長前半開始(' . $video_time_change . ')';
    } else if ($time_kind == 3) {
        echo '延長後半開始(' . $video_time_change . ')';
    } else if ($time_kind == 4 || $time_kind == 5) {
        $sql = "SELECT * FROM `video_time_tb` WHERE `game_id`=" . $game_id . " AND `team_id`=" . $team_id;
        $sth = $dbh->query($sql);
        echo 'のタイムアウト(';
        // . round($video_time, 2) . ')';
        $flag = false;
        while ($row = $sth->fetch()) {
            if ($flag == true) {
                echo ',';
            }
            echo s2h($row['video_time']);
            $flag = true;
        }
        echo ')';
    }
}
