<?php

session_start();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $_SESSION['game_name'] . '.csv"');

// functions.phpを読み込む
require_once(__DIR__ . '/functions.php');

// データベースに接続
$dbh = connectDB();

if (!$dbh) {
    echo ('データベースに接続できていません．');
    return;
}

// メモリにCSVデータを書き込む
$output = fopen("php://output", "w");

// CSVのヘッダ行を書き込む（idとgame_nameを除外）
fputcsv($output, array('pointed_flag', 'shoot_team_name', 'number', 'tag', 'catch_time', 'release_time', 'goal_time', 'video_time'));

// データベースからデータを取得してCSVに書き込む
$sql = "
SELECT st.pointed_flag, t.team_name AS shoot_team_name, st.number, st.tag, st.catch_time, st.release_time, st.goal_time, st.video_time 
FROM shoot_time_tb AS st 
JOIN game_tb AS g ON st.game_id = g.id 
LEFT JOIN team_tb AS t ON st.shoot_team_id = t.id
WHERE st.game_id=" . $_SESSION['game_id'] . " ORDER BY st.video_time";

$sth = $dbh->query($sql);

while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}

fclose($output);

exit();
