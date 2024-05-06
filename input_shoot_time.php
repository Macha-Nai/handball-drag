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
    <link rel="stylesheet" href="./css/input_shoot_time.css">
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
        <div class="row">
            <div class="col-10" id="player_area">
                <?php
                if ($youtube_id != "none") {
                    echo '<div id="player"></div>';
                } else {
                    echo '<div id="timer_area"></div>';
                }
                ?>
            </div>
            <div class="col-2">
                <div class="row">
                    <div class="col-12" id="game_time_set">
                        <p id="timeset_text">試合時間の設定</p>
                        <?php
                        if ($youtube_id != "none") {
                            echo '<div class="form-check">
    <input class="form-check-input" type="radio" name="timeRadio" id="firstHalf" value="0">
    <label class="form-check-label" for="firstHalf" name="time_info">前半開始';
                            if ($first_start != '') {
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
                        <div id="time_button">
                            <button type="submit" class="btn btn-primary" id="setVideoTime">設定</button>
                            <button type="submit" class="btn btn-danger" id="clearVideoTime">クリア</button>
                        </div>
                    </div>
                    <div class="col-12" id="shoot_history_area">
                        <div class="shoot_history_tb">
                            <table id="shoot_history">
                                <thead>
                                    <th>時刻</th>
                                    <th class="score">得点</th>
                                    <th>チーム</th>
                                </thead>
                                <tbody id="shoot_body">
                                    <?php
                                    if ($dbh) {
                                        //データベースへの問い合わせSQL文(文字列)
                                        $sql = 'SELECT * FROM `shoot_time_tb` WHERE game_id=' . $_SESSION['game_id'] . ' ORDER BY video_time';
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
                                            echo '<tr id="' . $row['id'] . '"><th>' . $time . '</th><td class="score">' . $gool_judge . '</td><td>' . $teamname_list[$team_id - 1]['abbreviation'] . '</td></tr>';
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="button_area d-flex justify-content-end">
                            <button class="btn btn-danger" id="delete">削除</button>
                            <button type="button" class="btn btn-success" id="edit">更新</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-10">
                <?php
                echo '<div class="row" id="video_button">';
                ?>
                <?php if ($ua_flag != 'PC' && $youtube_id != "none") {
                    echo '<div class="col-2 text-center">';
                    echo '<button onclick="click_back()">1秒前へ</button>';
                    echo '</div>';
                    echo '<div class="col-2 text-center">';
                    echo '<button onclick="click_action()" id="player_action">動画再生</button>';
                    echo '</div>';
                    echo '<div class="col-2 text-center">';
                    echo '<button onclick="click_forward()">1秒後へ</button>';
                    echo '</div>';
                }
                echo '<div class="col-2 text-center">';
                echo '<button onclick="click_quarter()">0.25倍</button>';
                echo '</div>';
                echo '<div class="col-2 text-center">';
                echo '<button onclick="click_half()">0.5倍</button>';
                echo '</div>';
                echo '<div class="col-2 text-center">';
                echo '<button onclick="click_normal()">通常</button>';
                echo '</div>';
                echo '</div>';
                ?>
            </div>
            <div class="col-10" id="input_area">
                <div class="row">
                    <div class="col-10">
                        <div class="row">
                            <div class="col-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="select_team" id="select_team1" value="<?php echo $_SESSION['team_id1'] ?>">
                                    <label class="form-check-label" for="select_team1" name="team_select" id="team_select1"><?php echo $_SESSION['abbreviation1'] ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="select_team" id="select_team2" value="<?php echo $_SESSION['team_id2'] ?>">
                                    <label class="form-check-label" for="select_team2" name="team_select" id="team_select2"><?php echo $_SESSION['abbreviation2'] ?></label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="goal-result-input text-center" id="result">
                                    <input type="radio" name="shoot_result" id="goal" class="goal_result" value="1"><label for="goal">得点</label>
                                    <input type="radio" name="shoot_result" id="failure" class="failure_result" value="2"><label for="failure">失敗</label>
                                </div>
                            </div>
                            <div class="col-2 text-center">
                                <div class="row justify-content-center align-items-center">
                                    <button type="button" id="catch" class="button-custom">キャッチ</button>
                                </div>
                                <div class="row">
                                    <p id="catch_text"></p>
                                </div>
                            </div>
                            <div class="col-2 text-center">
                                <div class="row justify-content-center align-items-center">
                                    <button type="button" id="release" class="button-custom">リリース</button>
                                </div>
                                <div class="row">
                                    <p class="shoot_time_text" id="release_text"></p>
                                </div>
                            </div>
                            <div class="col-2 text-center">
                                <div class="row justify-content-center align-items-center">
                                    <button type="button" id="goal_time" class="button-custom">ゴール</button>
                                </div>
                                <div class="row">
                                    <p class="shoot_time_text" id="goal_text"></p>
                                </div>
                            </div>
                            <div class="col-3 text-center">
                                <input type="number" class="d-inline block_option text_field" id="shoot_tag" step="1" placeholder="選手の背番号">
                                <input type="text" class="d-inline block_option text_field" id="shoot_memo" placeholder="メモ">
                            </div>
                        </div>
                    </div>
                    <div class="col-2 text-end" id="button_right_area">
                        <button class="btn btn-outline-secondary display_btn" id="clear">クリア</button>
                        <button class="btn btn-primary display_btn" id="submit">登録</button>
                    </div>
                </div>
            </div>
            <div class="col-2 text-end" id="menu_btn">
                <button class="btn btn-success" onclick="location.href='user_menu.php'">ユーザメニューに戻る</button>
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
    <script src="./js/input_shoot_time.js"></script>
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

        function sec_to_hour(time) {
            let h = Math.floor(time / 3600); // 時間
            let m = Math.floor((time % 3600) / 60); // 分
            let s = Math.floor(time % 60); // 秒

            // 二桁になるように先頭に0を追加し、末尾から2文字を切り出す
            return ('0' + h).slice(-2) + ':' + ('0' + m).slice(-2) + ':' + ('0' + s).slice(-2);
        }
    </script>
</body>

</html>