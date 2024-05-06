<?php
//セッションの生成
session_start();
//ログインの確認
if (!(isset($_SESSION['login']) && ($_SESSION['login'] == 'OK'))) {
    //ログインフォームへ
    header('Location: login.php');
}

//接続用関数の呼び出し
require_once(__DIR__ . '/functions.php');

// PCかそれ以外かを判定
$ua = $_SERVER['HTTP_USER_AGENT'];
$ua_flag = '';
if ((strpos($ua, 'Android') !== false) && (strpos($ua, 'Mobile') !== false) || (strpos($ua, 'iPhone') !== false) || (strpos($ua, 'Windows Phone') !== false)) {
    //スマホの場合に読み込むソースを記述
    $ua_flag = 'iPhone';
} elseif ((strpos($ua, 'Android') !== false) || (strpos($ua, 'iPad') !== false)) {
    //タブレットの場合に読み込むソースを記述
    $ua_flag = 'iPad';
} else {
    //PCの場合に読み込むソースを記述
    $ua_flag = 'PC';
}

//DBへの接続
$dbh = connectDB();

$index = 0;

$url = 'none';
$game_id = 'none';

$youtube_id = "none";

if (isset($_POST['game_id']) && $_POST['game_id'] != "") {
    $_SESSION['url'] = $_POST['url'];
    $_SESSION['game_id'] = $_POST['game_id'];
    $url = $_SESSION['url'];
    $game_id = $_SESSION['game_id'];
    if ($_SESSION['url'] != "none") {
        $youtube_id = getYoutubeIdFromUrl($url);
    }
    $sql = 'SELECT * FROM game_tb WHERE id=' . $_SESSION['game_id'];
    $game_tb = $dbh->query($sql);
    $game = $game_tb->fetch(PDO::FETCH_ASSOC);
    $team1_id = $game['team_id1'];
    $team2_id = $game['team_id2'];
    $sql = 'SELECT * FROM team_tb WHERE id=' . $team1_id;
    $sth_team1 = $dbh->query($sql);
    $team1 = $sth_team1->fetch(PDO::FETCH_ASSOC);
    $team1_name = $team1['team_name'];
    $team1_ab = $team1['abbreviation'];
    $sql = 'SELECT * FROM team_tb WHERE id=' . $team2_id;
    $sth_team2 = $dbh->query($sql);
    $team2 = $sth_team2->fetch(PDO::FETCH_ASSOC);
    $team2_name = $team2['team_name'];
    $team2_ab = $team2['abbreviation'];
    $_SESSION['team_name1'] = $team1_name;
    $_SESSION['team_name2'] = $team2_name;
    $_SESSION['abbreviation1'] = $team1_ab;
    $_SESSION['abbreviation2'] = $team2_ab;
    $_SESSION['team_id1'] = $team1_id;
    $_SESSION['team_id2'] = $team2_id;
    $_SESSION['game_date'] = mb_substr($game['date'], 0, 10);
    $_SESSION['game_name'] = $game['name'];
} else {
    if (
        !isset($_SESSION['game_id']) || $_SESSION['game_id'] == "" ||
        !isset($_SESSION['url']) || $_SESSION['url'] == ""
    ) {
        //game_id, URLがなかったら飛ばす
        header('Location: inputGamedata.php');
    } else {
        $url = $_SESSION['url'];
        $game_id = $_SESSION['game_id'];
        if ($_SESSION['url'] != "none") {
            $youtube_id = getYoutubeIdFromUrl($url);
        } else {
            $youtube_id = "none";
        }
    }
}

if ($dbh) {
    $first_start = s2h(get_first_start());
    $latter_start = s2h(get_latter_start());
    $ex_first_start = s2h(get_ex_first_start());
    $ex_latter_start = s2h(get_ex_latter_start());
    $timeout1 = get_timeout($_SESSION['team_id1']);
    $timeout2 = get_timeout($_SESSION['team_id2']);
    if ($_SESSION['url'] == "none") {
        $sql = "INSERT INTO `video_time_tb` (`game_id`, `time_kind`, `team_id`, `video_time`) VALUES ('" . $_SESSION['game_id'] . "', '" . "0" . "', " . "NULL" . ", '" . 0 . "')";
        $dbh->query($sql);
        $sql = "INSERT INTO `video_time_tb` (`game_id`, `time_kind`, `team_id`, `video_time`) VALUES ('" . $_SESSION['game_id'] . "', '" . "1" . "', " . "NULL" . ", '" . 1800 . "')";
        $dbh->query($sql);
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" user-scalable=no>
    <title>シュート入力画面</title>
    <link rel="stylesheet" href="./css/inputShoot.css">
    <link rel="stylesheet" href="./css/common.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <div id="head_gamename"><?php
                            $day = new DateTime($_SESSION['game_date']);
                            echo '<b>' .  $_SESSION['game_name'] . '</b>: ' . $_SESSION['abbreviation1'] . ' 対 ' .  $_SESSION['abbreviation2'] . ' (' . $day->format('Y年m月d日') . ')';
                            ?>
    </div>
    <div class="container-fluid">
        <div class="col-12">
            <div class="row">
                <div class="row justify-content-start">
                    <div class="col-8" id="player_area">
                        <?php
                        if ($youtube_id != "none") {
                            echo '<div id="player"></div>';
                        } else {
                            echo '<div id="timer_area"></div>';
                        }
                        ?>
                    </div>
                    <div class="col-2" id="game_time_set">
                        <p id="timeset_text">試合時間の設定</p>
                        <?php
                        if ($youtube_id != "none") {
                            echo '<div class="form-check">
    <input class="form-check-input" type="radio" name="timeRadio" id="firstHalf" value="0">
    <label class="form-check-label" for="firstHalf" name="time_info">前半開始';
                            if ($first_start != null || $first_start != '') {
                                echo '(' . $first_start . ')';
                            }
                            echo '</label>
</div>
<div class="form-check">
    <input class="form-check-input" type="radio" name="timeRadio" id="secondHalf" value="1">
    <label class="form-check-label" for="secondHalf" name="time_info">後半開始';
                            if ($latter_start != '') {
                                echo '(' . $latter_start . ')';
                            }
                            echo '</label>
</div>
<div class="form-check">
    <input class="form-check-input" type="radio" name="timeRadio" id="exFirstHalf" value="2">
    <label class="form-check-label" for="exFirstHalf" name="time_info">延長前半開始';
                            if ($ex_first_start != '') {
                                echo '(' . $ex_first_start . ')';
                            }
                            echo '</label>
</div>
<div class="form-check">
    <input class="form-check-input" type="radio" name="timeRadio" id="exSecondHalf" value="3">
    <label class="form-check-label" for="exSecondHalf" name="time_info">延長後半開始';
                            if ($ex_latter_start != '') {
                                echo '(' . $ex_latter_start . ')';
                            }
                            echo '</label>
</div>';
                        } else {
                            echo '<div class="form-check"><input class="form-check-input" type="radio" name="timeRadio" id="exFirstHalf" value="2"><label class="form-check-label" for="exFirstHalf" name="time_info">延長戦開始</label></div>';
                        }
                        ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="timeRadio" id="restTimeStart1" value="4">
                            <label class="form-check-label" for="restTimeStart1" name="time_info" id="restTime1"><?php echo $_SESSION['abbreviation1']; ?>のタイムアウト
                                <?php while ($index < count($timeout1)) {
                                    $element = s2h($timeout1[$index]);
                                    echo '(' . $element . ')';
                                    $index++;
                                }
                                $index = 0;
                                ?></label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="timeRadio" id="restTimeStart2" value="5">
                            <label class="form-check-label" for="restTimeStart2" name="time_info" id="restTime2"><?php echo $_SESSION['abbreviation2']; ?>のタイムアウト
                                <?php while ($index < count($timeout2)) {
                                    $element = s2h($timeout2[$index]);
                                    echo '(' . $element . ')';
                                    $index++;
                                } ?></label>
                        </div>
                        <button type="submit" class="btn btn-primary" id="setVideoTime">時刻設定</button>
                        <button type="submit" class="btn btn-danger" id="clearVideoTime">時刻クリア</button>
                    </div>
                    <div class="col-2">
                        <div class="shoot_history_tb">
                            <table id="shoot_history">
                                <thead>
                                    <th>時刻</th>
                                    <th>得点</th>
                                    <th>チーム</th>
                                </thead>
                                <tbody id="shoot_body">
                                    <?php
                                    if ($dbh) {
                                        //データベースへの問い合わせSQL文(文字列)
                                        $sql = 'SELECT * FROM `shoot_tb` WHERE game_id=' . $_SESSION['game_id'] . ' ORDER BY video_time';
                                        $teamname_sql = 'SELECT * FROM `team_tb`';
                                        $sth = $dbh->query($sql);
                                        $sth2 = $dbh->query($teamname_sql);
                                        $teamname_list = $sth2->fetchAll(PDO::FETCH_ASSOC);
                                        while ($row = $sth->fetch()) {
                                            $team_id = $row['shoot_team_id'];
                                            $seconds = $row['video_time'];
                                            $time = s2h($seconds);
                                            $pointed_flag = $row['pointed_flag'];
                                            $gool_judge = '○';
                                            if ($pointed_flag == 0) {
                                                $gool_judge = '×';
                                            }
                                            echo '<tr id="' . $row['id'] . '"><th>' . $time . '</th><td>' . $gool_judge . '</td><td>' . $teamname_list[$team_id - 1]['abbreviation'] . '</td></tr>';
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="button_area d-flex justify-content-start">
                            <button class="btn btn-danger" id="delete">削除</button>
                            <button type="button" class="btn btn-success" id="edit">更新</button>
                        </div>
                    </div>
                </div>
                <div class="row" id="input_area">
                    <div class="col-6 text-center">
                        <div class="row">
                            <div class="row">
                                <div class="col-3">
                                    <?php if ($ua_flag != 'PC' && $youtube_id != "none") {
                                        echo '<button id="player_action">動画再生</button>';
                                    } ?>
                                </div>
                                <?php if ($ua_flag != 'PC' && $youtube_id != "none") {
                                    echo '<div class="col-3 text-center">';
                                    echo '<button id="player_back">5秒前へ</button>';
                                    echo '</div>';
                                    echo '<div class="col-3 text-center">';
                                    echo '<button id="switch_dir">コート替え</button>';
                                    echo '</div>';
                                    echo '<div class="col-3 text-center">';
                                    echo '<button id="player_forward">5秒後へ</button>';
                                    echo '</div>';
                                } else {
                                    echo '<div class="col-8 text-center">';
                                    echo '<button id="switch_dir">コート替え</button>';
                                    echo '</div>';
                                } ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-3"></div>
                            <div class="col-8 text-center h6" id="attack_dir_info">
                                <?php echo '◀︎ ' . $_SESSION['abbreviation1']; ?>---<?php echo $_SESSION['abbreviation2'] . ' ▶︎'; ?>
                            </div>
                        </div>
                        <div class="row" id="canvas_area">
                            <div class="col-2 mt-auto" id="seven_button">
                                <input type="checkbox" id="7m-throw" name="7m-throw" autocomplete="off">
                                <label class="w-100 mx-auto label-sm" for="7m-throw">7m</label>
                            </div>
                            <div class="col-10" id="coat">
                                <canvas id="canvas"></canvas>
                            </div>
                            <!-- <div class="col-1"></div> -->
                        </div>
                        <div class="row" id="shooter_position">
                            <div class="col-3">
                            </div>
                            <div class="col-8 text-center">
                                <?php
                                if ($dbh) {
                                    //データベースへの問い合わせSQL文(文字列)
                                    $sql = 'SELECT * FROM `shooter_kind_tb`';
                                    //echo $sql;
                                    $sth = $dbh->query($sql);
                                    $i = 0;
                                    while ($row = $sth->fetch()) {
                                        echo '<input type="radio" name="shooter_position" id="shooter' . $row['id'] . '" class="shooter_position" value="' . $row['id'] . '"><label
                                    for="' . 'shooter' . $row['id'] . '" class="px-2';
                                        if ($i != 0 && $i % 3 == 0) {
                                            echo ' ms-2';
                                        }
                                        echo ' py-1">' . $row['shooter_kind'] . '</label>';
                                        $i++;
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-3"></div>
                            <div class="col-3 text-end goal-result-input" id="goal_team1">
                                <input type="radio" name="shoot_result" id="team1_goal" class="goal_result" value="1"><label for="team1_goal">得点</label>
                                <input type="radio" name="shoot_result" id="team1_failure" class="failure_result" value="2"><label for="team1_failure">失敗</label>
                            </div>
                            <div class="col-3 text-end goal-result-input result-right" id="goal_team2">
                                <input type="radio" name="shoot_result" id="team2_goal" value="3" class="goal_result"><label for="team2_goal">得点</label>
                                <input type="radio" name="shoot_result" id="team2_failure" class="failure_result" value="4"><label for="team2_failure">失敗</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="row" id="Goal_Course">
                            <table class="table table-bordered border-primary" id="ball_around_goal">
                                <tr>
                                    <td colspan="4" class="text-center out_of_goal" value="5" id="out_top">枠外T</td>
                                </tr>
                                <tr>
                                    <td rowspan="2" class="out_of_goal" value="6">枠外L</td>
                                    <td class="in_goal" value="1">TL</td>
                                    <td class="in_goal" value="2">TR</td>
                                    <td rowspan="2" class="out_of_goal" value="7">枠外R</td>
                                </tr>
                                <tr>
                                    <td class="in_goal" value="3">BL</td>
                                    <td class="in_goal" value="4">BR</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-12">
                            <div class="text-center">
                                <input type="checkbox" id="swift_attack" class="d-inline block_option" disabled><label for="swift_attack" class="d-inline">速攻</label>
                                <input type="checkbox" class="d-inline block_option" id="GK_block"><label for="GK_block" class="d-inline">GKブロック</label>
                                <input type="checkbox" id="DF_block" class="d-inline block_option"><label for="DF_block" class="d-inline">DFブロック</label>
                                <input type="checkbox" id="rebound" class="d-inline block_option" disabled><label for="rebound" class="d-inline">リバウンド</label>
                                <input type="checkbox" id="empty_shoot" class="d-inline block_option" disabled><label for="empty_shoot" class="d-inline">エンプティシュート</label>
                            </div>
                            <div class="col-12 text-center">
                                <input type="number" class="d-inline block_option" id="shoot_tag" step="1" placeholder="選手の背番号">
                                <input type="text" class="d-inline block_option" id="shoot_memo" placeholder="メモ">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-end">
                                <!-- <button class="btn btn-danger" id="delete">削除</button>
                <button type="button" class="btn btn-primary" id="edit">更新</button> -->
                                <button class="btn btn-outline-secondary display_btn" id="clear">クリア</button>
                                <button type="button" class="btn btn-primary display_btn" id="submit">登録</button>
                            </div>
                        </div>
                        <div class="col-12 text-end" id="menu_btn">
                            <button class="btn btn-success" onclick="location.href='user_menu.php'">ユーザメニューに戻る</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script>
        let game_id = '<?php echo $_SESSION['game_id']; ?>';
        let team_name1 = '<?php echo $_SESSION['team_name1']; ?>';
        let team_name2 = '<?php echo $_SESSION['team_name2']; ?>';
        let abbreviation1 = '<?php echo $_SESSION['abbreviation1']; ?>';
        let abbreviation2 = '<?php echo $_SESSION['abbreviation2']; ?>';
        let team_id1 = <?php echo $_SESSION['team_id1']; ?>;
        let team_id2 = <?php echo $_SESSION['team_id2']; ?>;
        let youtube_id = '<?php echo $youtube_id; ?>';
        let video_flag;
        <?php if ($youtube_id == "none") { ?>
            video_flag = true;
        <?php } else { ?>
            video_flag = false;
        <?php }
        ?>
    </script>
    <script src="./js/scriptShoot.js"></script>
    <?php
    if ($youtube_id != "none") {
        echo '<script src="./js/youtube_player.js"></script>';
    } else {
        echo '<script src="./js/timer.js"></script>';
    }
    ?>
    <script>
        // 時間から秒へ変換(00:00:00→00000秒)
        function hour_to_sec(time) {
            h = parseInt(time.slice(0, 2), 10);
            m = parseInt(time.slice(3, 5), 10);
            s = parseInt(time.slice(6, 8), 10);
            return (h * 60 * 60) + (m * 60) + s;
        }
    </script>
</body>

</html>