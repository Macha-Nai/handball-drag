<?php

//接続用関数の呼び出し
require_once(__DIR__ . './../functions.php');

//DBへの接続
$dbh = connectDB();

//セッションで送られるので、今後削除予定。
$game_id = $_POST['game_id'];

//ここからは送られるデータ
$timeRadio = -1;
if (isset($_POST['timeRadio'])) {
    $timeRadio = $_POST['timeRadio'];
}
$team_id = "NULL";
$time_flag = false;

if (isset($_POST['team_id'])) {
    $team_id = $_POST['team_id'];
    if ($team_id == -1) {
        $team_id = "NULL";
    }
}

if ($timeRadio == -1) {
    echo "none";
    return;
}

if ($dbh) {
    if ($timeRadio == 4 || $timeRadio == 5) {
        $sql = "DELETE FROM `video_time_tb` WHERE `game_id`=" . $game_id . " AND `time_kind`=" . $timeRadio . " AND `team_id`=" . $team_id;
    } else {
        $sql = "DELETE FROM `video_time_tb` WHERE `game_id`=" . $game_id . " AND `time_kind`=" . $timeRadio;
    }
    $dbh->query($sql); //SQLの実行
    if ($timeRadio == 0) {
        echo '前半開始';
    } else if ($timeRadio == 1) {
        echo '後半開始';
    } else if ($timeRadio == 2) {
        echo '延長前半開始';
    } else if ($timeRadio == 3) {
        echo '延長後半開始';
    } else if ($timeRadio == 4 || $timeRadio == 5) {
        echo 'のタイムアウト';
    }
}
