<?php
// error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
//セッションの生成
session_start();
//ログインの確認
if (!(isset($_SESSION['login']) && ($_SESSION['login'] == 'OK'))) {
    //ログインフォームへ
    header('Location: login.php');
}

//接続用関数の呼び出し
require_once(__DIR__ . '/functions.php');

//DBへの接続
$dbh = connectDB();

// $game_id = $_SESSION['game_id'];

if (isset($_POST['game_id']) && $_POST['game_id'] != '') {
    $_SESSION['game_id'] = $_POST['game_id'];
    $game_id = $_POST['game_id'];
} else {
    header('Location: user_menu.php');
}

$_SESSION['first_time'] = 0;
$_SESSION['latter_time'] = 0;

$first_time = 0;
$latter_time = 0;
$third_time = 0;
$fourth_time = 0;

$_SESSION['game_name'] = 'none';

if ($dbh) {
    $_SESSION['extension'] = check_extension();
    //動画の有無の確認(動画がなかったらtrue)
    $_SESSION['video_flag'] = check_video($game_id);
    // echo $_SESSION['video_flag'];
    if ($_SESSION['extension']) {
        if ($_SESSION['video_flag'] == 1) {
            $latter_time = 1800;
            $first_time = 0;
            $third_time = 3600;
            $fourth_time = 3900;
        } else {
            $first_time = get_FirstTime($game_id);
            $_SESSION['first_time'] = $first_time;
            $latter_time = get_LatterTime($game_id);
            $_SESSION['latter_time'] = $latter_time;
            $third_time = get_thirdBeginTime();
            $fourth_time = get_fourthBeginTime();
        }
    } else {
        if ($_SESSION['video_flag'] == 1) {
            $latter_time = 1800;
            $first_time = 0;
        } else {
            $first_time = get_FirstTime($game_id);
            $_SESSION['first_time'] = $first_time;
            $latter_time = get_LatterTime($game_id);
            $_SESSION['latter_time'] = $latter_time;
        }
    }

    $set_first_time_flag = false;
    // $first_timeがnullまたは空の文字列""であるかどうかをチェック
    if ($first_time == 0) {
        if ((string) $first_time == '0') {
            $set_first_time_flag = true;
        }
    } else {
        $set_first_time_flag = true;
    }
    if (!$set_first_time_flag) {
        echo "前半の試合開始時間が設定されていません。シュート情報入力画面で設定してください。";
        exit; // これ以降のプログラムの実行を停止
    }

    //試合名
    $sql = 'SELECT name
            FROM game_tb
            WHERE id=' . $_SESSION['game_id'];
    $sth = $dbh->query($sql);  //SQLの実行
    $game_name = $sth->fetch(PDO::FETCH_COLUMN);
    $_SESSION['game_name'] = $game_name;
    //対戦チームの検索
    //チーム１
    $sql = 'SELECT team_id1
            FROM game_tb
            WHERE id=' . $_SESSION['game_id'];
    $sth = $dbh->query($sql);  //SQLの実行
    //データの取得
    $team_id1 = $sth->fetch(PDO::FETCH_COLUMN);
    $sql = 'SELECT abbreviation
            FROM team_tb
            WHERE id="' . $team_id1 . '"';
    $sth = $dbh->query($sql);  //SQLの実行
    //データの取得
    $team_name1 = $sth->fetch(PDO::FETCH_COLUMN);

    //チーム２
    $sql = 'SELECT team_id2
            FROM game_tb
            WHERE id=' . $_SESSION['game_id'];
    $sth = $dbh->query($sql);  //SQLの実行
    //データの取得
    $team_id2 = $sth->fetch(PDO::FETCH_COLUMN);
    $sql = 'SELECT abbreviation
            FROM team_tb
            WHERE id="' . $team_id2 . '"';
    $sth = $dbh->query($sql);  //SQLの実行
    //データの取得
    $team_name2 = $sth->fetch(PDO::FETCH_COLUMN);

    $youtube_id = 0;
    if ($_SESSION['video_flag'] == 0) {
        $youtube_id = get_YoutubeId($_SESSION['game_id']);
    }

    $get_all_shoot1 = get_all_shoot($team_id1);
    $get_all_shoot2 = get_all_shoot($team_id2);
    $all_shoot1 = json_encode($get_all_shoot1);
    $all_shoot2 = json_encode($get_all_shoot2);

    $id_f = [];
    $xy_explode_f = [];
    $pointed_flag_f = [];
    $rebound_f = [];
    $swift_attack_f = [];
    $empty_shoot_f = [];
    $GK_block_f = [];
    $DF_block_f = [];
    $seven_f = [];
    $shooter_kind_f = [];
    $goal_position_f = [];
    $number_f = [];
    $video_time_f = [];

    $id_l = [];
    $xy_explode_l = [];
    $pointed_flag_l = [];
    $rebound_l = [];
    $swift_attack_l = [];
    $empty_shoot_l = [];
    $GK_block_l = [];
    $DF_block_l = [];
    $seven_l = [];
    $shooter_kind_l = [];
    $goal_position_l = [];
    $number_l = [];
    $video_time_l = [];

    $id_t = [];
    $xy_explode_t = [];
    $pointed_flag_t = [];
    $rebound_t = [];
    $swift_attack_t = [];
    $empty_shoot_t = [];
    $GK_block_t = [];
    $DF_block_t = [];
    $seven_t = [];
    $shooter_kind_t = [];
    $goal_position_t = [];
    $number_t = [];
    $video_time_t = [];

    $id_fo = [];
    $xy_explode_fo = [];
    $pointed_flag_fo = [];
    $rebound_fo = [];
    $swift_attack_fo = [];
    $empty_shoot_fo = [];
    $GK_block_fo = [];
    $DF_block_fo = [];
    $seven_fo = [];
    $shooter_kind_fo = [];
    $goal_position_fo = [];
    $number_fo = [];
    $video_time_fo = [];

    $get_first_shoot1 = [];
    $get_first_shoot2 = [];
    $get_latter_shoot1 = [];
    $get_latter_shoot2 = [];
    $get_third_shoot1 = [];
    $get_third_shoot2 = [];
    $get_fourth_shoot1 = [];
    $get_fourth_shoot2 = [];

    $first_shoot1;
    $first_shoot2;
    $latter_shoot1;
    $latter_shoot2;
    $third_shoot1;
    $third_shoot2;
    $fourth_shoot1;
    $fourth_shoot2;

    if ($_SESSION['extension'] == 1) {
        for ($i = 0; $i < count($get_all_shoot1['id']); $i++) {
            if ($get_all_shoot1['video_time'][$i] < $latter_time) {
                array_push($id_f, $get_all_shoot1['id'][$i]);
                array_push($xy_explode_f, $get_all_shoot1['position_xy'][$i]);
                array_push($pointed_flag_f, $get_all_shoot1['pointed_flag'][$i]);
                array_push($rebound_f, $get_all_shoot1['rebound'][$i]);
                array_push($swift_attack_f, $get_all_shoot1['swift_attack'][$i]);
                array_push($empty_shoot_f, $get_all_shoot1['empty_shoot'][$i]);
                array_push($GK_block_f, $get_all_shoot1['GK_block'][$i]);
                array_push($DF_block_f, $get_all_shoot1['DF_block'][$i]);
                array_push($seven_f, $get_all_shoot1['seven'][$i]);
                array_push($shooter_kind_f, $get_all_shoot1['shooter_kind'][$i]);
                array_push($goal_position_f, $get_all_shoot1['goal_position'][$i]);
                array_push($number_f, $get_all_shoot1['number'][$i]);
                array_push($video_time_f, $get_all_shoot1['video_time'][$i]);
            } else if ($get_all_shoot1['video_time'][$i] >= $latter_time && $get_all_shoot1['video_time'][$i] < $third_time) {
                array_push($id_l, $get_all_shoot1['id'][$i]);
                array_push($xy_explode_l, $get_all_shoot1['position_xy'][$i]);
                array_push($pointed_flag_l, $get_all_shoot1['pointed_flag'][$i]);
                array_push($rebound_l, $get_all_shoot1['rebound'][$i]);
                array_push($swift_attack_l, $get_all_shoot1['swift_attack'][$i]);
                array_push($empty_shoot_l, $get_all_shoot1['empty_shoot'][$i]);
                array_push($GK_block_l, $get_all_shoot1['GK_block'][$i]);
                array_push($DF_block_l, $get_all_shoot1['DF_block'][$i]);
                array_push($seven_l, $get_all_shoot1['seven'][$i]);
                array_push($shooter_kind_l, $get_all_shoot1['shooter_kind'][$i]);
                array_push($goal_position_l, $get_all_shoot1['goal_position'][$i]);
                array_push($number_l, $get_all_shoot1['number'][$i]);
                array_push($video_time_l, $get_all_shoot1['video_time'][$i]);
            } else if ($get_all_shoot1['video_time'][$i] >= $third_time && $get_all_shoot1['video_time'][$i] < $fourth_time) {
                array_push($id_t, $get_all_shoot1['id'][$i]);
                array_push($xy_explode_t, $get_all_shoot1['position_xy'][$i]);
                array_push($pointed_flag_t, $get_all_shoot1['pointed_flag'][$i]);
                array_push($rebound_t, $get_all_shoot1['rebound'][$i]);
                array_push($swift_attack_t, $get_all_shoot1['swift_attack'][$i]);
                array_push($empty_shoot_t, $get_all_shoot1['empty_shoot'][$i]);
                array_push($GK_block_t, $get_all_shoot1['GK_block'][$i]);
                array_push($DF_block_t, $get_all_shoot1['DF_block'][$i]);
                array_push($seven_t, $get_all_shoot1['seven'][$i]);
                array_push($shooter_kind_t, $get_all_shoot1['shooter_kind'][$i]);
                array_push($goal_position_t, $get_all_shoot1['goal_position'][$i]);
                array_push($number_t, $get_all_shoot1['number'][$i]);
                array_push($video_time_t, $get_all_shoot1['video_time'][$i]);
            } else if ($get_all_shoot1['video_time'][$i] >= $fourth_time) {
                array_push($id_fo, $get_all_shoot1['id'][$i]);
                array_push($xy_explode_fo, $get_all_shoot1['position_xy'][$i]);
                array_push($pointed_flag_fo, $get_all_shoot1['pointed_flag'][$i]);
                array_push($rebound_fo, $get_all_shoot1['rebound'][$i]);
                array_push($swift_attack_fo, $get_all_shoot1['swift_attack'][$i]);
                array_push($empty_shoot_fo, $get_all_shoot1['empty_shoot'][$i]);
                array_push($GK_block_fo, $get_all_shoot1['GK_block'][$i]);
                array_push($DF_block_fo, $get_all_shoot1['DF_block'][$i]);
                array_push($seven_fo, $get_all_shoot1['seven'][$i]);
                array_push($shooter_kind_fo, $get_all_shoot1['shooter_kind'][$i]);
                array_push($goal_position_fo, $get_all_shoot1['goal_position'][$i]);
                array_push($number_fo, $get_all_shoot1['number'][$i]);
                array_push($video_time_fo, $get_all_shoot1['video_time'][$i]);
            }
        }
        $get_first_shoot1 = [
            "id" => $id_f,
            "position_xy" => $xy_explode_f,
            "pointed_flag" => $pointed_flag_f,
            "rebound" => $rebound_f,
            "swift_attack" => $swift_attack_f,
            "empty_shoot" => $empty_shoot_f,
            "GK_block" => $GK_block_f,
            "DF_block" => $DF_block_f,
            "seven" => $seven_f,
            "shooter_kind" => $shooter_kind_f,
            "goal_position" => $goal_position_f,
            "number" => $number_f,
            "video_time" => $video_time_f
        ];
        $get_latter_shoot1 = [
            "id" => $id_l,
            "position_xy" => $xy_explode_l,
            "pointed_flag" => $pointed_flag_l,
            "rebound" => $rebound_l,
            "swift_attack" => $swift_attack_l,
            "empty_shoot" => $empty_shoot_l,
            "GK_block" => $GK_block_l,
            "DF_block" => $DF_block_l,
            "seven" => $seven_l,
            "shooter_kind" => $shooter_kind_l,
            "goal_position" => $goal_position_l,
            "number" => $number_l,
            "video_time" => $video_time_l
        ];
        $get_third_shoot1 = [
            "id" => $id_t,
            "position_xy" => $xy_explode_t,
            "pointed_flag" => $pointed_flag_t,
            "rebound" => $rebound_t,
            "swift_attack" => $swift_attack_t,
            "empty_shoot" => $empty_shoot_t,
            "GK_block" => $GK_block_t,
            "DF_block" => $DF_block_t,
            "seven" => $seven_t,
            "shooter_kind" => $shooter_kind_t,
            "goal_position" => $goal_position_t,
            "number" => $number_t,
            "video_time" => $video_time_t
        ];
        $get_fourth_shoot1 = [
            "id" => $id_fo,
            "position_xy" => $xy_explode_fo,
            "pointed_flag" => $pointed_flag_fo,
            "rebound" => $rebound_fo,
            "swift_attack" => $swift_attack_fo,
            "empty_shoot" => $empty_shoot_fo,
            "GK_block" => $GK_block_fo,
            "DF_block" => $DF_block_fo,
            "seven" => $seven_fo,
            "shooter_kind" => $shooter_kind_fo,
            "goal_position" => $goal_position_fo,
            "number" => $number_fo,
            "video_time" => $video_time_fo
        ];
        //初期化
        $id_f = [];
        $xy_explode_f = [];
        $pointed_flag_f = [];
        $rebound_f = [];
        $swift_attack_f = [];
        $empty_shoot_f = [];
        $GK_block_f = [];
        $DF_block_f = [];
        $seven_f = [];
        $shooter_kind_f = [];
        $goal_position_f = [];
        $number_f = [];
        $video_time_f = [];

        $id_l = [];
        $xy_explode_l = [];
        $pointed_flag_l = [];
        $rebound_l = [];
        $swift_attack_l = [];
        $empty_shoot_l = [];
        $GK_block_l = [];
        $DF_block_l = [];
        $seven_l = [];
        $shooter_kind_l = [];
        $goal_position_l = [];
        $number_l = [];
        $video_time_l = [];

        $id_t = [];
        $xy_explode_t = [];
        $pointed_flag_t = [];
        $rebound_t = [];
        $swift_attack_t = [];
        $empty_shoot_t = [];
        $GK_block_t = [];
        $DF_block_t = [];
        $seven_t = [];
        $shooter_kind_t = [];
        $goal_position_t = [];
        $number_t = [];
        $video_time_t = [];

        $id_fo = [];
        $xy_explode_fo = [];
        $pointed_flag_fo = [];
        $rebound_fo = [];
        $swift_attack_fo = [];
        $empty_shoot_fo = [];
        $GK_block_fo = [];
        $DF_block_fo = [];
        $seven_fo = [];
        $shooter_kind_fo = [];
        $goal_position_fo = [];
        $number_fo = [];
        $video_time_fo = [];
        for ($i = 0; $i < count($get_all_shoot2['id']); $i++) {
            if ($get_all_shoot2['video_time'][$i] < $latter_time) {
                array_push($id_f, $get_all_shoot2['id'][$i]);
                array_push($xy_explode_f, $get_all_shoot2['position_xy'][$i]);
                array_push($pointed_flag_f, $get_all_shoot2['pointed_flag'][$i]);
                array_push($rebound_f, $get_all_shoot2['rebound'][$i]);
                array_push($swift_attack_f, $get_all_shoot2['swift_attack'][$i]);
                array_push($empty_shoot_f, $get_all_shoot2['empty_shoot'][$i]);
                array_push($GK_block_f, $get_all_shoot2['GK_block'][$i]);
                array_push($DF_block_f, $get_all_shoot2['DF_block'][$i]);
                array_push($seven_f, $get_all_shoot2['seven'][$i]);
                array_push($shooter_kind_f, $get_all_shoot2['shooter_kind'][$i]);
                array_push($goal_position_f, $get_all_shoot2['goal_position'][$i]);
                array_push($number_f, $get_all_shoot2['number'][$i]);
                array_push($video_time_f, $get_all_shoot2['video_time'][$i]);
            } else if ($get_all_shoot2['video_time'][$i] >= $latter_time && $get_all_shoot2['video_time'][$i] < $third_time) {
                array_push($id_l, $get_all_shoot2['id'][$i]);
                array_push($xy_explode_l, $get_all_shoot2['position_xy'][$i]);
                array_push($pointed_flag_l, $get_all_shoot2['pointed_flag'][$i]);
                array_push($rebound_l, $get_all_shoot2['rebound'][$i]);
                array_push($swift_attack_l, $get_all_shoot2['swift_attack'][$i]);
                array_push($empty_shoot_l, $get_all_shoot2['empty_shoot'][$i]);
                array_push($GK_block_l, $get_all_shoot2['GK_block'][$i]);
                array_push($DF_block_l, $get_all_shoot2['DF_block'][$i]);
                array_push($seven_l, $get_all_shoot2['seven'][$i]);
                array_push($shooter_kind_l, $get_all_shoot2['shooter_kind'][$i]);
                array_push($goal_position_l, $get_all_shoot2['goal_position'][$i]);
                array_push($number_l, $get_all_shoot2['number'][$i]);
                array_push($video_time_l, $get_all_shoot2['video_time'][$i]);
            } else if ($get_all_shoot2['video_time'][$i] >= $third_time && $get_all_shoot2['video_time'][$i] < $fourth_time) {
                array_push($id_t, $get_all_shoot2['id'][$i]);
                array_push($xy_explode_t, $get_all_shoot2['position_xy'][$i]);
                array_push($pointed_flag_t, $get_all_shoot2['pointed_flag'][$i]);
                array_push($rebound_t, $get_all_shoot2['rebound'][$i]);
                array_push($swift_attack_t, $get_all_shoot2['swift_attack'][$i]);
                array_push($empty_shoot_t, $get_all_shoot2['empty_shoot'][$i]);
                array_push($GK_block_t, $get_all_shoot2['GK_block'][$i]);
                array_push($DF_block_t, $get_all_shoot2['DF_block'][$i]);
                array_push($seven_t, $get_all_shoot2['seven'][$i]);
                array_push($shooter_kind_t, $get_all_shoot2['shooter_kind'][$i]);
                array_push($goal_position_t, $get_all_shoot2['goal_position'][$i]);
                array_push($number_t, $get_all_shoot2['number'][$i]);
                array_push($video_time_t, $get_all_shoot2['video_time'][$i]);
            } else {
                array_push($id_fo, $get_all_shoot2['id'][$i]);
                array_push($xy_explode_fo, $get_all_shoot2['position_xy'][$i]);
                array_push($pointed_flag_fo, $get_all_shoot2['pointed_flag'][$i]);
                array_push($rebound_fo, $get_all_shoot2['rebound'][$i]);
                array_push($swift_attack_fo, $get_all_shoot2['swift_attack'][$i]);
                array_push($empty_shoot_fo, $get_all_shoot2['empty_shoot'][$i]);
                array_push($GK_block_fo, $get_all_shoot2['GK_block'][$i]);
                array_push($DF_block_fo, $get_all_shoot2['DF_block'][$i]);
                array_push($seven_fo, $get_all_shoot2['seven'][$i]);
                array_push($shooter_kind_fo, $get_all_shoot2['shooter_kind'][$i]);
                array_push($goal_position_fo, $get_all_shoot2['goal_position'][$i]);
                array_push($number_fo, $get_all_shoot2['number'][$i]);
                array_push($video_time_fo, $get_all_shoot2['video_time'][$i]);
            }
        }
        $get_first_shoot2 = [
            "id" => $id_f,
            "position_xy" => $xy_explode_f,
            "pointed_flag" => $pointed_flag_f,
            "rebound" => $rebound_f,
            "swift_attack" => $swift_attack_f,
            "empty_shoot" => $empty_shoot_f,
            "GK_block" => $GK_block_f,
            "DF_block" => $DF_block_f,
            "seven" => $seven_f,
            "shooter_kind" => $shooter_kind_f,
            "goal_position" => $goal_position_f,
            "number" => $number_f,
            "video_time" => $video_time_f
        ];
        $get_latter_shoot2 = [
            "id" => $id_l,
            "position_xy" => $xy_explode_l,
            "pointed_flag" => $pointed_flag_l,
            "rebound" => $rebound_l,
            "swift_attack" => $swift_attack_l,
            "empty_shoot" => $empty_shoot_l,
            "GK_block" => $GK_block_l,
            "DF_block" => $DF_block_l,
            "seven" => $seven_l,
            "shooter_kind" => $shooter_kind_l,
            "goal_position" => $goal_position_l,
            "number" => $number_l,
            "video_time" => $video_time_l
        ];
        $get_third_shoot2 = [
            "id" => $id_t,
            "position_xy" => $xy_explode_t,
            "pointed_flag" => $pointed_flag_t,
            "rebound" => $rebound_t,
            "swift_attack" => $swift_attack_t,
            "empty_shoot" => $empty_shoot_t,
            "GK_block" => $GK_block_t,
            "DF_block" => $DF_block_t,
            "seven" => $seven_t,
            "shooter_kind" => $shooter_kind_t,
            "goal_position" => $goal_position_t,
            "number" => $number_t,
            "video_time" => $video_time_t
        ];
        $get_fourth_shoot2 = [
            "id" => $id_fo,
            "position_xy" => $xy_explode_fo,
            "pointed_flag" => $pointed_flag_fo,
            "rebound" => $rebound_fo,
            "swift_attack" => $swift_attack_fo,
            "empty_shoot" => $empty_shoot_fo,
            "GK_block" => $GK_block_fo,
            "DF_block" => $DF_block_fo,
            "seven" => $seven_fo,
            "shooter_kind" => $shooter_kind_fo,
            "goal_position" => $goal_position_fo,
            "number" => $number_fo,
            "video_time" => $video_time_fo
        ];
        $first_shoot1 = json_encode($get_first_shoot1);
        $first_shoot2 = json_encode($get_first_shoot2);
        $latter_shoot1 = json_encode($get_latter_shoot1);
        $latter_shoot2 = json_encode($get_latter_shoot2);
        $third_shoot1 = json_encode($get_third_shoot1);
        $third_shoot2 = json_encode($get_third_shoot2);
        $fourth_shoot1 = json_encode($get_fourth_shoot1);
        $fourth_shoot2 = json_encode($get_fourth_shoot2);
    } else {
        for ($i = 0; $i < count($get_all_shoot1['id']); $i++) {
            if ($get_all_shoot1['video_time'][$i] < $latter_time) {
                array_push($id_f, $get_all_shoot1['id'][$i]);
                array_push($xy_explode_f, $get_all_shoot1['position_xy'][$i]);
                array_push($pointed_flag_f, $get_all_shoot1['pointed_flag'][$i]);
                array_push($rebound_f, $get_all_shoot1['rebound'][$i]);
                array_push($swift_attack_f, $get_all_shoot1['swift_attack'][$i]);
                array_push($empty_shoot_f, $get_all_shoot1['empty_shoot'][$i]);
                array_push($GK_block_f, $get_all_shoot1['GK_block'][$i]);
                array_push($DF_block_f, $get_all_shoot1['DF_block'][$i]);
                array_push($seven_f, $get_all_shoot1['seven'][$i]);
                array_push($shooter_kind_f, $get_all_shoot1['shooter_kind'][$i]);
                array_push($goal_position_f, $get_all_shoot1['goal_position'][$i]);
                array_push($number_f, $get_all_shoot1['number'][$i]);
                array_push($video_time_f, $get_all_shoot1['video_time'][$i]);
            } else if ($get_all_shoot1['video_time'][$i] >= $latter_time) {
                array_push($id_l, $get_all_shoot1['id'][$i]);
                array_push($xy_explode_l, $get_all_shoot1['position_xy'][$i]);
                array_push($pointed_flag_l, $get_all_shoot1['pointed_flag'][$i]);
                array_push($rebound_l, $get_all_shoot1['rebound'][$i]);
                array_push($swift_attack_l, $get_all_shoot1['swift_attack'][$i]);
                array_push($empty_shoot_l, $get_all_shoot1['empty_shoot'][$i]);
                array_push($GK_block_l, $get_all_shoot1['GK_block'][$i]);
                array_push($DF_block_l, $get_all_shoot1['DF_block'][$i]);
                array_push($seven_l, $get_all_shoot1['seven'][$i]);
                array_push($shooter_kind_l, $get_all_shoot1['shooter_kind'][$i]);
                array_push($goal_position_l, $get_all_shoot1['goal_position'][$i]);
                array_push($number_l, $get_all_shoot1['number'][$i]);
                array_push($video_time_l, $get_all_shoot1['video_time'][$i]);
            }
        }
        $get_first_shoot1 = [
            "id" => $id_f,
            "position_xy" => $xy_explode_f,
            "pointed_flag" => $pointed_flag_f,
            "rebound" => $rebound_f,
            "swift_attack" => $swift_attack_f,
            "empty_shoot" => $empty_shoot_f,
            "GK_block" => $GK_block_f,
            "DF_block" => $DF_block_f,
            "seven" => $seven_f,
            "shooter_kind" => $shooter_kind_f,
            "goal_position" => $goal_position_f,
            "number" => $number_f,
            "video_time" => $video_time_f
        ];
        $get_latter_shoot1 = [
            "id" => $id_l,
            "position_xy" => $xy_explode_l,
            "pointed_flag" => $pointed_flag_l,
            "rebound" => $rebound_l,
            "swift_attack" => $swift_attack_l,
            "empty_shoot" => $empty_shoot_l,
            "GK_block" => $GK_block_l,
            "DF_block" => $DF_block_l,
            "seven" => $seven_l,
            "shooter_kind" => $shooter_kind_l,
            "goal_position" => $goal_position_l,
            "number" => $number_l,
            "video_time" => $video_time_l
        ];
        //初期化
        $id_f = [];
        $xy_explode_f = [];
        $pointed_flag_f = [];
        $rebound_f = [];
        $swift_attack_f = [];
        $empty_shoot_f = [];
        $GK_block_f = [];
        $DF_block_f = [];
        $seven_f = [];
        $shooter_kind_f = [];
        $goal_position_f = [];
        $number_f = [];
        $video_time_f = [];

        $id_l = [];
        $xy_explode_l = [];
        $pointed_flag_l = [];
        $rebound_l = [];
        $swift_attack_l = [];
        $empty_shoot_l = [];
        $GK_block_l = [];
        $DF_block_l = [];
        $seven_l = [];
        $shooter_kind_l = [];
        $goal_position_l = [];
        $number_l = [];
        $video_time_l = [];
        for ($i = 0; $i < count($get_all_shoot2['id']); $i++) {
            if ($get_all_shoot2['video_time'][$i] < $latter_time) {
                array_push($id_f, $get_all_shoot2['id'][$i]);
                array_push($xy_explode_f, $get_all_shoot2['position_xy'][$i]);
                array_push($pointed_flag_f, $get_all_shoot2['pointed_flag'][$i]);
                array_push($rebound_f, $get_all_shoot2['rebound'][$i]);
                array_push($swift_attack_f, $get_all_shoot2['swift_attack'][$i]);
                array_push($empty_shoot_f, $get_all_shoot2['empty_shoot'][$i]);
                array_push($GK_block_f, $get_all_shoot2['GK_block'][$i]);
                array_push($DF_block_f, $get_all_shoot2['DF_block'][$i]);
                array_push($seven_f, $get_all_shoot2['seven'][$i]);
                array_push($shooter_kind_f, $get_all_shoot2['shooter_kind'][$i]);
                array_push($goal_position_f, $get_all_shoot2['goal_position'][$i]);
                array_push($number_f, $get_all_shoot2['number'][$i]);
                array_push($video_time_f, $get_all_shoot2['video_time'][$i]);
            } else if ($get_all_shoot2['video_time'][$i] >= $latter_time) {
                array_push($id_l, $get_all_shoot2['id'][$i]);
                array_push($xy_explode_l, $get_all_shoot2['position_xy'][$i]);
                array_push($pointed_flag_l, $get_all_shoot2['pointed_flag'][$i]);
                array_push($rebound_l, $get_all_shoot2['rebound'][$i]);
                array_push($swift_attack_l, $get_all_shoot2['swift_attack'][$i]);
                array_push($empty_shoot_l, $get_all_shoot2['empty_shoot'][$i]);
                array_push($GK_block_l, $get_all_shoot2['GK_block'][$i]);
                array_push($DF_block_l, $get_all_shoot2['DF_block'][$i]);
                array_push($seven_l, $get_all_shoot2['seven'][$i]);
                array_push($shooter_kind_l, $get_all_shoot2['shooter_kind'][$i]);
                array_push($goal_position_l, $get_all_shoot2['goal_position'][$i]);
                array_push($number_l, $get_all_shoot2['number'][$i]);
                array_push($video_time_l, $get_all_shoot2['video_time'][$i]);
            }
        }
        $get_first_shoot2 = [
            "id" => $id_f,
            "position_xy" => $xy_explode_f,
            "pointed_flag" => $pointed_flag_f,
            "rebound" => $rebound_f,
            "swift_attack" => $swift_attack_f,
            "empty_shoot" => $empty_shoot_f,
            "GK_block" => $GK_block_f,
            "DF_block" => $DF_block_f,
            "seven" => $seven_f,
            "shooter_kind" => $shooter_kind_f,
            "goal_position" => $goal_position_f,
            "number" => $number_f,
            "video_time" => $video_time_f
        ];
        $get_latter_shoot2 = [
            "id" => $id_l,
            "position_xy" => $xy_explode_l,
            "pointed_flag" => $pointed_flag_l,
            "rebound" => $rebound_l,
            "swift_attack" => $swift_attack_l,
            "empty_shoot" => $empty_shoot_l,
            "GK_block" => $GK_block_l,
            "DF_block" => $DF_block_l,
            "seven" => $seven_l,
            "shooter_kind" => $shooter_kind_l,
            "goal_position" => $goal_position_l,
            "number" => $number_l,
            "video_time" => $video_time_l
        ];
        $first_shoot1 = json_encode($get_first_shoot1);
        $first_shoot2 = json_encode($get_first_shoot2);
        $latter_shoot1 = json_encode($get_latter_shoot1);
        $latter_shoot2 = json_encode($get_latter_shoot2);
    }

    //シュートの情報
    $shoot_tb = get_shoot_table($first_time, $latter_time, $team_id1, $team_id2, $team_name1, $team_name2);
    $shoot_table = json_encode($shoot_tb);

    $id_f = [];
    $time_f = [];
    $goal_judge_f = [];
    $team_name_f = [];
    $player_num_f = [];
    $video_time_f = [];

    $id_l = [];
    $time_l = [];
    $goal_judge_l = [];
    $team_name_l = [];
    $player_num_l = [];
    $video_time_l = [];

    $id_t = [];
    $time_t = [];
    $goal_judge_t = [];
    $team_name_t = [];
    $player_num_t = [];
    $video_time_t = [];

    $id_fo = [];
    $time_fo = [];
    $goal_judge_fo = [];
    $team_name_fo = [];
    $player_num_fo = [];
    $video_time_fo = [];

    $shoot_tb_first = array();
    $shoot_tb_latter = array();
    $shoot_tb_third = array();
    $shoot_tb_fourth = array();

    $shoot_table_first = array();
    $shoot_table_latter = array();
    $shoot_table_third = array();
    $shoot_table_fourth = array();

    if ($_SESSION['extension'] == 1) {
        for ($i = 0; $i < count($shoot_tb['id']); $i++) {
            if ($shoot_tb['video_time'][$i] < $latter_time) {
                array_push($id_f, $shoot_tb['id'][$i]);
                array_push($time_f, $shoot_tb['time'][$i]);
                array_push($goal_judge_f, $shoot_tb['goal_judge'][$i]);
                array_push($team_name_f, $shoot_tb['team_name'][$i]);
                array_push($player_num_f, $shoot_tb['player_num'][$i]);
                array_push($video_time_f, $shoot_tb['video_time'][$i]);
            } else if ($shoot_tb['video_time'][$i] >= $latter_time && $shoot_tb['video_time'][$i] < $third_time) {
                array_push($id_l, $shoot_tb['id'][$i]);
                array_push($time_l, $shoot_tb['time'][$i]);
                array_push($goal_judge_l, $shoot_tb['goal_judge'][$i]);
                array_push($team_name_l, $shoot_tb['team_name'][$i]);
                array_push($player_num_l, $shoot_tb['player_num'][$i]);
                array_push($video_time_l, $shoot_tb['video_time'][$i]);
            } else if ($shoot_tb['video_time'][$i] >= $third_time && $shoot_tb['video_time'][$i] < $fourth_time) {
                array_push($id_t, $shoot_tb['id'][$i]);
                array_push($time_t, $shoot_tb['time'][$i]);
                array_push($goal_judge_t, $shoot_tb['goal_judge'][$i]);
                array_push($team_name_t, $shoot_tb['team_name'][$i]);
                array_push($player_num_t, $shoot_tb['player_num'][$i]);
                array_push($video_time_t, $shoot_tb['video_time'][$i]);
            } else {
                array_push($id_fo, $shoot_tb['id'][$i]);
                array_push($time_fo, $shoot_tb['time'][$i]);
                array_push($goal_judge_fo, $shoot_tb['goal_judge'][$i]);
                array_push($team_name_fo, $shoot_tb['team_name'][$i]);
                array_push($player_num_fo, $shoot_tb['player_num'][$i]);
                array_push($video_time_fo, $shoot_tb['video_time'][$i]);
            }
        }
        $shoot_tb_first = [
            "id" => $id_f,
            "time" => $time_f,
            "goal_judge" => $goal_judge_f,
            "team_name" => $team_name_f,
            "player_num" => $player_num_f,
            "video_time" => $video_time_f,
        ];
        $shoot_tb_latter = [
            "id" => $id_l,
            "time" => $time_l,
            "goal_judge" => $goal_judge_l,
            "team_name" => $team_name_l,
            "player_num" => $player_num_l,
            "video_time" => $video_time_l,
        ];
        $shoot_tb_third = [
            "id" => $id_t,
            "time" => $time_t,
            "goal_judge" => $goal_judge_t,
            "team_name" => $team_name_t,
            "player_num" => $player_num_t,
            "video_time" => $video_time_t,
        ];
        $shoot_tb_fourth = [
            "id" => $id_fo,
            "time" => $time_fo,
            "goal_judge" => $goal_judge_fo,
            "team_name" => $team_name_fo,
            "player_num" => $player_num_fo,
            "video_time" => $video_time_fo,
        ];
        $shoot_table_first = json_encode($shoot_tb_first);
        $shoot_table_latter = json_encode($shoot_tb_latter);
        $shoot_table_third = json_encode($shoot_tb_third);
        $shoot_table_fourth = json_encode($shoot_tb_fourth);
    } else {
        for ($i = 0; $i < count($shoot_tb['id']); $i++) {
            if ($shoot_tb['video_time'][$i] < $latter_time) {
                array_push($id_f, $shoot_tb['id'][$i]);
                array_push($time_f, $shoot_tb['time'][$i]);
                array_push($goal_judge_f, $shoot_tb['goal_judge'][$i]);
                array_push($team_name_f, $shoot_tb['team_name'][$i]);
                array_push($player_num_f, $shoot_tb['player_num'][$i]);
                array_push($video_time_f, $shoot_tb['video_time'][$i]);
            } else if ($shoot_tb['video_time'][$i] >= $latter_time) {
                array_push($id_l, $shoot_tb['id'][$i]);
                array_push($time_l, $shoot_tb['time'][$i]);
                array_push($goal_judge_l, $shoot_tb['goal_judge'][$i]);
                array_push($team_name_l, $shoot_tb['team_name'][$i]);
                array_push($player_num_l, $shoot_tb['player_num'][$i]);
                array_push($video_time_l, $shoot_tb['video_time'][$i]);
            }
        }
        $shoot_tb_first = [
            "id" => $id_f,
            "time" => $time_f,
            "goal_judge" => $goal_judge_f,
            "team_name" => $team_name_f,
            "player_num" => $player_num_f,
            "video_time" => $video_time_f,
        ];
        $shoot_tb_latter = [
            "id" => $id_l,
            "time" => $time_l,
            "goal_judge" => $goal_judge_l,
            "team_name" => $team_name_l,
            "player_num" => $player_num_l,
            "video_time" => $video_time_l,
        ];
        $shoot_table_first = json_encode($shoot_tb_first);
        $shoot_table_latter = json_encode($shoot_tb_latter);
    }

    //シュートコースごとのシュート決定率
    $TL1 = get_goal(1, $team_id1);
    $TL_s1 = get_goal_s(1, $team_id1);
    $FTL1 = get_first_goal(1, $latter_time, $team_id1);
    $FTL_s1 = get_first_goal_s(1, $latter_time, $team_id1);
    $LTL1 = get_latter_goal(1, $latter_time, $team_id1);
    $LTL_s1 = get_latter_goal_s(1, $latter_time, $team_id1);
    $TR1 = get_goal(2, $team_id1);
    $TR_s1 = get_goal_s(2, $team_id1);
    $FTR1 = get_first_goal(2, $latter_time, $team_id1);
    $FTR_s1 = get_first_goal_s(2, $latter_time, $team_id1);
    $LTR1 = get_latter_goal(2, $latter_time, $team_id1);
    $LTR_s1 = get_latter_goal_s(2, $latter_time, $team_id1);
    $BL1 = get_goal(3, $team_id1);
    $BL_s1 = get_goal_s(3, $team_id1);
    $FBL1 = get_first_goal(3, $latter_time, $team_id1);
    $FBL_s1 = get_first_goal_s(3, $latter_time, $team_id1);
    $LBL1 = get_latter_goal(3, $latter_time, $team_id1);
    $LBL_s1 = get_latter_goal_s(3, $latter_time, $team_id1);
    $BR1 = get_goal(4, $team_id1);
    $BR_s1 = get_goal_s(4, $team_id1);
    $FBR1 = get_first_goal(4, $latter_time, $team_id1);
    $FBR_s1 = get_first_goal_s(4, $latter_time, $team_id1);
    $LBR1 = get_latter_goal(4, $latter_time, $team_id1);
    $LBR_s1 = get_latter_goal_s(4, $latter_time, $team_id1);
    $T1 = get_goal(5, $team_id1);
    $FT1 = get_first_goal(5, $latter_time, $team_id1);
    $LT1 = get_latter_goal(5, $latter_time, $team_id1);
    $L1 = get_goal(6, $team_id1);
    $FL1 = get_first_goal(6, $latter_time, $team_id1);
    $LL1 = get_latter_goal(6, $latter_time, $team_id1);
    $R1 = get_goal(7, $team_id1);
    $FR1 = get_first_goal(7, $latter_time, $team_id1);
    $LR1 = get_latter_goal(7, $latter_time, $team_id1);
    $TL_r1 = division_check($TL_s1, $TL1);
    $FTL_r1 = division_check($FTL_s1, $FTL1);
    $LTL_r1 = division_check($LTL_s1, $LTL1);
    $TR_r1 = division_check($TR_s1, $TR1);
    $FTR_r1 = division_check($FTR_s1, $FTR1);
    $LTR_r1 = division_check($LTR_s1, $LTR1);
    $BL_r1 = division_check($BL_s1, $BL1);
    $FBL_r1 = division_check($FBL_s1, $FBL1);
    $LBL_r1 = division_check($LBL_s1, $LBL1);
    $BR_r1 = division_check($BR_s1, $BR1);
    $FBR_r1 = division_check($FBR_s1, $FBR1);
    $LBR_r1 = division_check($LBR_s1, $LBR1);

    $TL2 = get_goal(1, $team_id2);
    $TL_s2 = get_goal_s(1, $team_id2);
    $FTL2 = get_first_goal(1, $latter_time, $team_id2);
    $FTL_s2 = get_first_goal_s(1, $latter_time, $team_id2);
    $LTL2 = get_latter_goal(1, $latter_time, $team_id2);
    $LTL_s2 = get_latter_goal_s(1, $latter_time, $team_id2);
    $TR2 = get_goal(2, $team_id2);
    $TR_s2 = get_goal_s(2, $team_id2);
    $FTR2 = get_first_goal(2, $latter_time, $team_id2);
    $FTR_s2 = get_first_goal_s(2, $latter_time, $team_id2);
    $LTR2 = get_latter_goal(2, $latter_time, $team_id2);
    $LTR_s2 = get_latter_goal_s(2, $latter_time, $team_id2);
    $BL2 = get_goal(3, $team_id2);
    $BL_s2 = get_goal_s(3, $team_id2);
    $FBL2 = get_first_goal(3, $latter_time, $team_id2);
    $FBL_s2 = get_first_goal_s(3, $latter_time, $team_id2);
    $LBL2 = get_latter_goal(3, $latter_time, $team_id2);
    $LBL_s2 = get_latter_goal_s(3, $latter_time, $team_id2);
    $BR2 = get_goal(4, $team_id2);
    $BR_s2 = get_goal_s(4, $team_id2);
    $FBR2 = get_first_goal(4, $latter_time, $team_id2);
    $FBR_s2 = get_first_goal_s(4, $latter_time, $team_id2);
    $LBR2 = get_latter_goal(4, $latter_time, $team_id2);
    $LBR_s2 = get_latter_goal_s(4, $latter_time, $team_id2);
    $T2 = get_goal(5, $team_id2);
    $FT2 = get_first_goal(5, $latter_time, $team_id2);
    $LT2 = get_latter_goal(5, $latter_time, $team_id2);
    $L2 = get_goal(6, $team_id2);
    $FL2 = get_first_goal(6, $latter_time, $team_id2);
    $LL2 = get_latter_goal(6, $latter_time, $team_id2);
    $R2 = get_goal(7, $team_id2);
    $FR2 = get_first_goal(7, $latter_time, $team_id2);
    $LR2 = get_latter_goal(7, $latter_time, $team_id2);
    $TL_r2 = division_check($TL_s2, $TL2);
    $FTL_r2 = division_check($FTL_s2, $FTL2);
    $LTL_r2 = division_check($LTL_s2, $LTL2);
    $TR_r2 = division_check($TR_s2, $TR2);
    $FTR_r2 = division_check($FTR_s2, $FTR2);
    $LTR_r2 = division_check($LTR_s2, $LTR2);
    $BL_r2 = division_check($BL_s2, $BL2);
    $FBL_r2 = division_check($FBL_s2, $FBL2);
    $LBL_r2 = division_check($LBL_s2, $LBL2);
    $BR_r2 = division_check($BR_s2, $BR2);
    $FBR_r2 = division_check($FBR_s2, $FBR2);
    $LBR_r2 = division_check($LBR_s2, $LBR2);

    //両チームの得点
    $sql = 'SELECT count(*) as ct FROM shoot_tb WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id1 . ' AND pointed_flag=1';
    $sth = $dbh->query($sql);
    $team1_point = $sth->fetch(PDO::FETCH_COLUMN);

    $sql = 'SELECT count(*) as ct FROM shoot_tb WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id2 . ' AND pointed_flag=1';
    $sth = $dbh->query($sql);
    $team2_point = $sth->fetch(PDO::FETCH_COLUMN);

    $sql = 'SELECT count(*) as ct FROM shoot_tb WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id1 . ' AND pointed_flag=1 AND video_time < ' . $latter_time;
    $sth = $dbh->query($sql);
    $team1_first = $sth->fetch(PDO::FETCH_COLUMN);

    $sql = 'SELECT count(*) as ct FROM shoot_tb WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id1 . ' AND pointed_flag=1 AND video_time >= ' . $latter_time;
    $sth = $dbh->query($sql);
    $team1_latter = $sth->fetch(PDO::FETCH_COLUMN);

    $sql = 'SELECT count(*) as ct FROM shoot_tb WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id2 . ' AND pointed_flag=1 AND video_time < ' . $latter_time;
    $sth = $dbh->query($sql);
    $team2_first = $sth->fetch(PDO::FETCH_COLUMN);

    $sql = 'SELECT count(*) as ct FROM shoot_tb WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id2 . ' AND pointed_flag=1 AND video_time >= ' . $latter_time;
    $sth = $dbh->query($sql);
    $team2_latter = $sth->fetch(PDO::FETCH_COLUMN);

    //ここからはシュート率表示のcanvasについて
    $FLW1 = get_FirstShootData(1, $latter_time, $team_id1);
    $FLW_s1 = get_FirstShootData_pointed(1, $latter_time, $team_id1);
    $LLW1 = get_LatterShootData(1, $latter_time, $team_id1);
    $LLW_s1 = get_LatterShootData_pointed(1, $latter_time, $team_id1);
    $FPV1 = get_FirstShootData(2, $latter_time, $team_id1);
    $FPV_s1 = get_FirstShootData_pointed(2, $latter_time, $team_id1);
    $LPV1 = get_LatterShootData(2, $latter_time, $team_id1);
    $LPV_s1 = get_LatterShootData_pointed(2, $latter_time, $team_id1);
    $FRW1 = get_FirstShootData(3, $latter_time, $team_id1);
    $FRW_s1 = get_FirstShootData_pointed(3, $latter_time, $team_id1);
    $LRW1 = get_LatterShootData(3, $latter_time, $team_id1);
    $LRW_s1 = get_LatterShootData_pointed(3, $latter_time, $team_id1);
    $FL61 = get_FirstShootData(4, $latter_time, $team_id1);
    $FL6_s1 = get_FirstShootData_pointed(4, $latter_time, $team_id1);
    $LL61 = get_LatterShootData(4, $latter_time, $team_id1);
    $LL6_s1 = get_LatterShootData_pointed(4, $latter_time, $team_id1);
    $FC61 = get_FirstShootData(5, $latter_time, $team_id1);
    $FC6_s1 = get_FirstShootData_pointed(5, $latter_time, $team_id1);
    $LC61 = get_LatterShootData(5, $latter_time, $team_id1);
    $LC6_s1 = get_LatterShootData_pointed(5, $latter_time, $team_id1);
    $FR61 = get_FirstShootData(6, $latter_time, $team_id1);
    $FR6_s1 = get_FirstShootData_pointed(6, $latter_time, $team_id1);
    $LR61 = get_LatterShootData(6, $latter_time, $team_id1);
    $LR6_s1 = get_LatterShootData_pointed(6, $latter_time, $team_id1);
    $FL91 = get_FirstShootData(7, $latter_time, $team_id1);
    $FL9_s1 = get_FirstShootData_pointed(7, $latter_time, $team_id1);
    $LL91 = get_LatterShootData(7, $latter_time, $team_id1);
    $LL9_s1 = get_LatterShootData_pointed(7, $latter_time, $team_id1);
    $FC91 = get_FirstShootData(8, $latter_time, $team_id1);
    $FC9_s1 = get_FirstShootData_pointed(8, $latter_time, $team_id1);
    $LC91 = get_LatterShootData(8, $latter_time, $team_id1);
    $LC9_s1 = get_LatterShootData_pointed(8, $latter_time, $team_id1);
    $FR91 = get_FirstShootData(9, $latter_time, $team_id1);
    $FR9_s1 = get_FirstShootData_pointed(9, $latter_time, $team_id1);
    $LR91 = get_LatterShootData(9, $latter_time, $team_id1);
    $LR9_s1 = get_LatterShootData_pointed(9, $latter_time, $team_id1);

    //team2前半
    $FLW2 = get_FirstShootData(1, $latter_time, $team_id2);
    $FLW_s2 = get_FirstShootData_pointed(1, $latter_time, $team_id2);
    $LLW2 = get_LatterShootData(1, $latter_time, $team_id2);
    $LLW_s2 = get_LatterShootData_pointed(1, $latter_time, $team_id2);
    $FPV2 = get_FirstShootData(2, $latter_time, $team_id2);
    $FPV_s2 = get_FirstShootData_pointed(2, $latter_time, $team_id2);
    $LPV2 = get_LatterShootData(2, $latter_time, $team_id2);
    $LPV_s2 = get_LatterShootData_pointed(2, $latter_time, $team_id2);
    $FRW2 = get_FirstShootData(3, $latter_time, $team_id2);
    $FRW_s2 = get_FirstShootData_pointed(3, $latter_time, $team_id2);
    $LRW2 = get_LatterShootData(3, $latter_time, $team_id2);
    $LRW_s2 = get_LatterShootData_pointed(3, $latter_time, $team_id2);
    $FL62 = get_FirstShootData(4, $latter_time, $team_id2);
    $FL6_s2 = get_FirstShootData_pointed(4, $latter_time, $team_id2);
    $LL62 = get_LatterShootData(4, $latter_time, $team_id2);
    $LL6_s2 = get_LatterShootData_pointed(4, $latter_time, $team_id2);
    $FC62 = get_FirstShootData(5, $latter_time, $team_id2);
    $FC6_s2 = get_FirstShootData_pointed(5, $latter_time, $team_id2);
    $LC62 = get_LatterShootData(5, $latter_time, $team_id2);
    $LC6_s2 = get_LatterShootData_pointed(5, $latter_time, $team_id2);
    $FR62 = get_FirstShootData(6, $latter_time, $team_id2);
    $FR6_s2 = get_FirstShootData_pointed(6, $latter_time, $team_id2);
    $LR62 = get_LatterShootData(6, $latter_time, $team_id2);
    $LR6_s2 = get_LatterShootData_pointed(6, $latter_time, $team_id2);
    $FL92 = get_FirstShootData(7, $latter_time, $team_id2);
    $FL9_s2 = get_FirstShootData_pointed(7, $latter_time, $team_id2);
    $LL92 = get_LatterShootData(7, $latter_time, $team_id2);
    $LL9_s2 = get_LatterShootData_pointed(7, $latter_time, $team_id2);
    $FC92 = get_FirstShootData(8, $latter_time, $team_id2);
    $FC9_s2 = get_FirstShootData_pointed(8, $latter_time, $team_id2);
    $LC92 = get_LatterShootData(8, $latter_time, $team_id2);
    $LC9_s2 = get_LatterShootData_pointed(8, $latter_time, $team_id2);
    $FR92 = get_FirstShootData(9, $latter_time, $team_id2);
    $FR9_s2 = get_FirstShootData_pointed(9, $latter_time, $team_id2);
    $LR92 = get_LatterShootData(9, $latter_time, $team_id2);
    $LR9_s2 = get_LatterShootData_pointed(9, $latter_time, $team_id2);

    $sql = 'SELECT count(*)
            FROM shoot_tb
            WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id1 . ' AND 7m_shoot=1 AND video_time < ' . $latter_time;
    $sth = $dbh->query($sql);
    $Fseven1 = $sth->fetch(PDO::FETCH_COLUMN);

    $sql = 'SELECT count(*)
            FROM shoot_tb
            WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id1 . ' AND 7m_shoot=1 AND pointed_flag=1 AND video_time < ' . $latter_time;
    $sth = $dbh->query($sql);
    $Fseven_s1 = $sth->fetch(PDO::FETCH_COLUMN);

    $sql = 'SELECT count(*)
            FROM shoot_tb
            WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id1 . ' AND 7m_shoot=1 AND video_time >= ' . $latter_time;
    $sth = $dbh->query($sql);
    $Lseven1 = $sth->fetch(PDO::FETCH_COLUMN);

    $sql = 'SELECT count(*)
            FROM shoot_tb
            WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id1 . ' AND 7m_shoot=1 AND pointed_flag=1 AND video_time >= ' . $latter_time;
    $sth = $dbh->query($sql);
    $Lseven_s1 = $sth->fetch(PDO::FETCH_COLUMN);

    $sql = 'SELECT count(*)
            FROM shoot_tb
            WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id2 . ' AND 7m_shoot=1 AND video_time < ' . $latter_time;
    $sth = $dbh->query($sql);
    $Fseven2 = $sth->fetch(PDO::FETCH_COLUMN);


    $sql = 'SELECT count(*)
            FROM shoot_tb
            WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id2 . ' AND 7m_shoot=1 AND pointed_flag=1 AND video_time < ' . $latter_time;
    $sth = $dbh->query($sql);
    $Fseven_s2 = $sth->fetch(PDO::FETCH_COLUMN);

    $sql = 'SELECT count(*)
            FROM shoot_tb
            WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id2 . ' AND 7m_shoot=1 AND video_time >= ' . $latter_time;
    $sth = $dbh->query($sql);
    $Lseven2 = $sth->fetch(PDO::FETCH_COLUMN);

    $sql = 'SELECT count(*)
            FROM shoot_tb
            WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id2 . ' AND 7m_shoot=1 AND pointed_flag=1 AND video_time >= ' . $latter_time;
    $sth = $dbh->query($sql);
    $Lseven_s2 = $sth->fetch(PDO::FETCH_COLUMN);

    $LW1 = get_ShootData(1, $team_id1);
    $LW_s1 = get_ShootData_s(1, $team_id1);
    $PV1 = get_ShootData(2, $team_id1);
    $PV_s1 = get_ShootData_s(2, $team_id1);
    $RW1 = get_ShootData(3, $team_id1);
    $RW_s1 = get_ShootData_s(3, $team_id1);
    $L61 = get_ShootData(4, $team_id1);
    $L6_s1 = get_ShootData_s(4, $team_id1);
    $C61 = get_ShootData(5, $team_id1);
    $C6_s1 = get_ShootData_s(5, $team_id1);
    $R61 = get_ShootData(6, $team_id1);
    $R6_s1 = get_ShootData_s(6, $team_id1);
    $L91 = get_ShootData(7, $team_id1);
    $L9_s1 = get_ShootData_s(7, $team_id1);
    $C91 = get_ShootData(8, $team_id1);
    $C9_s1 = get_ShootData_s(8, $team_id1);
    $R91 = get_ShootData(9, $team_id1);
    $R9_s1 = get_ShootData_s(9, $team_id1);
    $LW_r1 = division_check($LW_s1, $LW1);
    $PV_r1 = division_check($PV_s1, $PV1);
    $RW_r1 = division_check($RW_s1, $RW1);
    $L6_r1 = division_check($L6_s1, $L61);
    $C6_r1 = division_check($C6_s1, $C61);
    $R6_r1 = division_check($R6_s1, $R61);
    $L9_r1 = division_check($L9_s1, $L91);
    $C9_r1 = division_check($C9_s1, $C91);
    $R9_r1 = division_check($R9_s1, $R91);

    $sql = 'SELECT count(*)
            FROM shoot_tb
            WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id1 . ' AND 7m_shoot=1';
    $sth = $dbh->query($sql);
    $seven1 = $sth->fetch(PDO::FETCH_COLUMN);


    $sql = 'SELECT count(*)
            FROM shoot_tb
            WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id1 . ' AND 7m_shoot=1 AND pointed_flag=1';
    $sth = $dbh->query($sql);
    $seven_s1 = $sth->fetch(PDO::FETCH_COLUMN);

    $seven_r1 = division_check($seven_s1, $seven1);

    $LW2 = get_ShootData(1, $team_id2);
    $LW_s2 = get_ShootData_s(1, $team_id2);
    $PV2 = get_ShootData(2, $team_id2);
    $PV_s2 = get_ShootData_s(2, $team_id2);
    $RW2 = get_ShootData(3, $team_id2);
    $RW_s2 = get_ShootData_s(3, $team_id2);
    $L62 = get_ShootData(4, $team_id2);
    $L6_s2 = get_ShootData_s(4, $team_id2);
    $C62 = get_ShootData(5, $team_id2);
    $C6_s2 = get_ShootData_s(5, $team_id2);
    $R62 = get_ShootData(6, $team_id2);
    $R6_s2 = get_ShootData_s(6, $team_id2);
    $L92 = get_ShootData(7, $team_id2);
    $L9_s2 = get_ShootData_s(7, $team_id2);
    $C92 = get_ShootData(8, $team_id2);
    $C9_s2 = get_ShootData_s(8, $team_id2);
    $R92 = get_ShootData(9, $team_id2);
    $R9_s2 = get_ShootData_s(9, $team_id2);
    $LW_r2 = division_check($LW_s2, $LW2);
    $PV_r2 = division_check($PV_s2, $PV2);
    $RW_r2 = division_check($RW_s2, $RW2);
    $L6_r2 = division_check($L6_s2, $L62);
    $C6_r2 = division_check($C6_s2, $C62);
    $R6_r2 = division_check($R6_s2, $R62);
    $L9_r2 = division_check($L9_s2, $L92);
    $C9_r2 = division_check($C9_s2, $C92);
    $R9_r2 = division_check($R9_s2, $R92);

    $sql = 'SELECT count(*)
            FROM shoot_tb
            WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id2 . ' AND 7m_shoot=1';
    $sth = $dbh->query($sql);
    $seven2 = $sth->fetch(PDO::FETCH_COLUMN);


    $sql = 'SELECT count(*)
            FROM shoot_tb
            WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id2 . ' AND 7m_shoot=1 AND pointed_flag=1';
    $sth = $dbh->query($sql);
    $seven_s2 = $sth->fetch(PDO::FETCH_COLUMN);

    $seven_r2 = division_check($seven_s2, $seven2);

    //チーム名検索
    //（＊＊他ファイルと繋げる際に変更＊＊）
    $sql = 'SELECT team_name
            FROM team_tb
            WHERE id=' . $team_id1 . ' AND user_id=' . $_SESSION['user_id'];
    $sth = $dbh->query($sql);  //SQLの実行
    //データの取得
    $team1 = $sth->fetch(PDO::FETCH_ASSOC);

    $sql = 'SELECT team_name
            FROM team_tb
            WHERE id=' . $team_id2 . ' AND user_id=' . $_SESSION['user_id'];
    $sth = $dbh->query($sql);  //SQLの実行
    //データの取得
    $team2 = $sth->fetch(PDO::FETCH_ASSOC);

    $sql = 'SELECT count(*) FROM video_time_tb WHERE time_kind=2 AND game_id=' . $_SESSION['game_id'];
    $sth = $dbh->query($sql);
    $entyo_flag = $sth->fetch(PDO::FETCH_COLUMN);
    $section_cnt = 0;

    //ここからスタッツ
    $shoot_count1 = get_shoot_count($team_id1);
    $shoot_count_first1 = get_shoot_count_first($team_id1, $latter_time);
    $shoot_count_latter1 = get_shoot_count_latter($team_id1, $latter_time);
    $shoot_count2 = get_shoot_count($team_id2);
    $shoot_count_first2 = get_shoot_count_first($team_id2, $latter_time);
    $shoot_count_latter2 = get_shoot_count_latter($team_id2, $latter_time);

    $shoot_count_s1 = get_shoot_count_s($team_id1);
    $shoot_count_s_first1 = get_shoot_count_s_first($team_id1, $latter_time);
    $shoot_count_s_latter1 = get_shoot_count_s_latter($team_id1, $latter_time);
    $shoot_count_s2 = get_shoot_count_s($team_id2);
    $shoot_count_s_first2 = get_shoot_count_s_first($team_id2, $latter_time);
    $shoot_count_s_latter2 = get_shoot_count_s_latter($team_id2, $latter_time);

    $shoot_count_r1 = division_check($shoot_count_s1, $shoot_count1);
    $shoot_count_r_first1 = division_check($shoot_count_s_first1, $shoot_count_first1);
    $shoot_count_r_latter1 = division_check($shoot_count_s_latter1, $shoot_count_latter1);
    $shoot_count_r2 = division_check($shoot_count_s2, $shoot_count2);
    $shoot_count_r_first2 = division_check($shoot_count_s_first2, $shoot_count_first2);
    $shoot_count_r_latter2 = division_check($shoot_count_s_latter2, $shoot_count_latter2);

    $block_count1 = get_block_count($team_id2);
    $block_count_first1 = get_block_count_first($team_id2, $latter_time);
    $block_count_latter1 = get_block_count_latter($team_id2, $latter_time);
    $block_count2 = get_block_count($team_id1);
    $block_count_first2 = get_block_count_first($team_id1, $latter_time);
    $block_count_latter2 = get_block_count_latter($team_id1, $latter_time);

    $seven_count1 = get_7m_shoot_count($team_id1);
    $seven_count_first1 = get_7m_shoot_count_first($team_id1, $latter_time);
    $seven_count_latter1 = get_7m_shoot_count_latter($team_id1, $latter_time);
    $seven_count2 = get_7m_shoot_count($team_id2);
    $seven_count_first2 = get_7m_shoot_count_first($team_id2, $latter_time);
    $seven_count_latter2 = get_7m_shoot_count_latter($team_id2, $latter_time);

    $seven_count_s1 = get_7m_shoot_count_s($team_id1);
    $seven_count_s_first1 = get_7m_shoot_count_s_first($team_id1, $latter_time);
    $seven_count_s_latter1 = get_7m_shoot_count_s_latter($team_id1, $latter_time);
    $seven_count_s2 = get_7m_shoot_count_s($team_id2);
    $seven_count_s_first2 = get_7m_shoot_count_s_first($team_id2, $latter_time);
    $seven_count_s_latter2 = get_7m_shoot_count_s_latter($team_id2, $latter_time);

    $seven_count_r1 = division_check($seven_count_s1, $seven_count1);
    $seven_count_r_first1 = division_check($seven_count_s_first1, $seven_count_first1);
    $seven_count_r_latter1 = division_check($seven_count_s_latter1, $seven_count_latter1);
    $seven_count_r2 = division_check($seven_count_s2, $seven_count2);
    $seven_count_r_first2 = division_check($seven_count_s_first2, $seven_count_first2);
    $seven_count_r_latter2 = division_check($seven_count_s_latter2, $seven_count_latter2);

    $save_count1 = get_save_count($team_id2);
    $save_count_first1 = get_save_count_first($team_id2, $latter_time);
    $save_count_latter1 = get_save_count_latter($team_id2, $latter_time);
    $save_count2 = get_save_count($team_id1);
    $save_count_first2 = get_save_count_first($team_id1, $latter_time);
    $save_count_latter2 = get_save_count_latter($team_id1, $latter_time);

    $in_goal_count1 = get_in_goal_count($team_id1);
    $in_goal_count_first1 = get_in_goal_count_first($team_id1, $latter_time);
    $in_goal_count_latter1 = get_in_goal_count_latter($team_id1, $latter_time);
    $in_goal_count2 = get_in_goal_count($team_id2);
    $in_goal_count_first2 = get_in_goal_count_first($team_id2, $latter_time);
    $in_goal_count_latter2 = get_in_goal_count_latter($team_id2, $latter_time);

    $save_count_r1 = division_check($save_count1, $in_goal_count2);
    $save_count_r_first1 = division_check($save_count_first1, $in_goal_count_first2);
    $save_count_r_latter1 = division_check($save_count_latter1, $in_goal_count_latter2);
    $save_count_r2 = division_check($save_count2, $in_goal_count1);
    $save_count_r_first2 = division_check($save_count_first2, $in_goal_count_first1);
    $save_count_r_latter2 = division_check($save_count_latter2, $in_goal_count_latter1);

    $out_goal_count1 = get_out_goal_count($team_id1);
    $out_goal_count_first1 = get_out_goal_count_first($team_id1, $latter_time);
    $out_goal_count_latter1 = get_out_goal_count_latter($team_id1, $latter_time);
    $out_goal_count2 = get_out_goal_count($team_id2);
    $out_goal_count_first2 = get_out_goal_count_first($team_id2, $latter_time);
    $out_goal_count_latter2 = get_out_goal_count_latter($team_id2, $latter_time);
    $swift_count1 = get_swift_count($team_id1);
    $swift_count2 = get_swift_count($team_id2);
    $swift_count_first1 = get_swift_count_first($team_id1, $latter_time);
    $swift_count_first2 = get_swift_count_first($team_id2, $latter_time);
    $swift_count_latter1 = get_swift_count_latter($team_id1, $latter_time);
    $swift_count_latter2 = get_swift_count_latter($team_id2, $latter_time);
    $swift_count_s1 = get_swift_count_s($team_id1);
    $swift_count_s2 = get_swift_count_s($team_id2);
    $swift_count_s_first1 = get_swift_count_s_first($team_id1, $latter_time);
    $swift_count_s_first2 = get_swift_count_s_first($team_id2, $latter_time);
    $swift_count_s_latter1 = get_swift_count_s_latter($team_id1, $latter_time);
    $swift_count_s_latter2 = get_swift_count_s_latter($team_id2, $latter_time);
    $swift_count_r1 = division_check($swift_count_s1, $swift_count1);
    $swift_count_r2 = division_check($swift_count_s2, $swift_count2);
    $swift_count_r_first1 = division_check($swift_count_s_first1, $swift_count_first1);
    $swift_count_r_first2 = division_check($swift_count_s_first2, $swift_count_first2);
    $swift_count_r_latter1 = division_check($swift_count_s_latter1, $swift_count_latter1);
    $swift_count_r_latter2 = division_check($swift_count_s_latter2, $swift_count_latter2);

    $side_count1 = get_side($team_id1);
    $side_count2 = get_side($team_id2);
    $side_count_first1 = get_side_first($team_id1, $latter_time);
    $side_count_first2 = get_side_first($team_id2, $latter_time);
    $side_count_latter1 = get_side_latter($team_id1, $latter_time);
    $side_count_latter2 = get_side_latter($team_id2, $latter_time);
    $side_count_s1 = get_side_s($team_id1);
    $side_count_s2 = get_side_s($team_id2);
    $side_count_first_s1 = get_side_first_s($team_id1, $latter_time);
    $side_count_first_s2 = get_side_first_s($team_id2, $latter_time);
    $side_count_latter_s1 = get_side_latter_s($team_id1, $latter_time);
    $side_count_latter_s2 = get_side_latter_s($team_id2, $latter_time);
    $side_count_r1 = division_check($side_count_s1, $side_count1);
    $side_count_r2 = division_check($side_count_s2, $side_count2);
    $side_count_first_r1 = division_check($side_count_first_s1, $side_count_first1);
    $side_count_first_r2 = division_check($side_count_first_s2, $side_count_first2);
    $side_count_latter_r1 = division_check($side_count_latter_s1, $side_count_latter1);
    $side_count_latter_r2 = division_check($side_count_latter_s2, $side_count_latter2);

    $long_count1 = get_long($team_id1);
    $long_count2 = get_long($team_id2);
    $long_count_first1 = get_long_first($team_id1, $latter_time);
    $long_count_first2 = get_long_first($team_id2, $latter_time);
    $long_count_latter1 = get_long_latter($team_id1, $latter_time);
    $long_count_latter2 = get_long_latter($team_id2, $latter_time);
    $long_count_s1 = get_long_s($team_id1);
    $long_count_s2 = get_long_s($team_id2);
    $long_count_first_s1 = get_long_first_s($team_id1, $latter_time);
    $long_count_first_s2 = get_long_first_s($team_id2, $latter_time);
    $long_count_latter_s1 = get_long_latter_s($team_id1, $latter_time);
    $long_count_latter_s2 = get_long_latter_s($team_id2, $latter_time);
    $long_count_r1 = division_check($long_count_s1, $long_count1);
    $long_count_r2 = division_check($long_count_s2, $long_count2);
    $long_count_first_r1 = division_check($long_count_first_s1, $long_count_first1);
    $long_count_first_r2 = division_check($long_count_first_s2, $long_count_first2);
    $long_count_latter_r1 = division_check($long_count_latter_s1, $long_count_latter1);
    $long_count_latter_r2 = division_check($long_count_latter_s2, $long_count_latter2);

    $tikou_count1 = get_tikou($team_id1);
    $tikou_count2 = get_tikou($team_id2);
    $tikou_count_first1 = get_tikou_first($team_id1, $latter_time);
    $tikou_count_first2 = get_tikou_first($team_id2, $latter_time);
    $tikou_count_latter1 = get_tikou_latter($team_id1, $latter_time);
    $tikou_count_latter2 = get_tikou_latter($team_id2, $latter_time);
    $tikou_count_s1 = get_tikou_s($team_id1);
    $tikou_count_s2 = get_tikou_s($team_id2);
    $tikou_count_first_s1 = get_tikou_first_s($team_id1, $latter_time);
    $tikou_count_first_s2 = get_tikou_first_s($team_id2, $latter_time);
    $tikou_count_latter_s1 = get_tikou_latter_s($team_id1, $latter_time);
    $tikou_count_latter_s2 = get_tikou_latter_s($team_id2, $latter_time);
    $tikou_count_r1 = division_check($tikou_count_s1, $tikou_count1);
    $tikou_count_r2 = division_check($tikou_count_s2, $tikou_count2);
    $tikou_count_first_r1 = division_check($tikou_count_first_s1, $tikou_count_first1);
    $tikou_count_first_r2 = division_check($tikou_count_first_s2, $tikou_count_first2);
    $tikou_count_latter_r1 = division_check($tikou_count_latter_s1, $tikou_count_latter1);
    $tikou_count_latter_r2 = division_check($tikou_count_latter_s2, $tikou_count_latter2);

    $middle_count1 = get_middle($team_id1);
    $middle_count2 = get_middle($team_id2);
    $middle_count_first1 = get_middle_first($team_id1, $latter_time);
    $middle_count_first2 = get_middle_first($team_id2, $latter_time);
    $middle_count_latter1 = get_middle_latter($team_id1, $latter_time);
    $middle_count_latter2 = get_middle_latter($team_id2, $latter_time);
    $middle_count_s1 = get_middle_s($team_id1);
    $middle_count_s2 = get_middle_s($team_id2);
    $middle_count_first_s1 = get_middle_first_s($team_id1, $latter_time);
    $middle_count_first_s2 = get_middle_first_s($team_id2, $latter_time);
    $middle_count_latter_s1 = get_middle_latter_s($team_id1, $latter_time);
    $middle_count_latter_s2 = get_middle_latter_s($team_id2, $latter_time);
    $middle_count_r1 = division_check($middle_count_s1, $middle_count1);
    $middle_count_r2 = division_check($middle_count_s2, $middle_count2);
    $middle_count_first_r1 = division_check($middle_count_first_s1, $middle_count_first1);
    $middle_count_first_r2 = division_check($middle_count_first_s2, $middle_count_first2);
    $middle_count_latter_r1 = division_check($middle_count_latter_s1, $middle_count_latter1);
    $middle_count_latter_r2 = division_check($middle_count_latter_s2, $middle_count_latter2);

    if ($entyo_flag == 0) {
        $section_cnt = 2;
    } else {
        $section_cnt = 4;
    }

    $sql = 'SELECT tag FROM shoot_tb WHERE shoot_team_id=' . $team_id1 . ' AND game_id=' . $_SESSION['game_id'] . ' ORDER BY tag';
    $number1 = $dbh->query($sql);

    $sql = 'SELECT tag FROM shoot_tb WHERE shoot_team_id=' . $team_id2 . ' AND game_id=' . $_SESSION['game_id'] . ' ORDER BY tag';
    $number2 = $dbh->query($sql);

    // 結果の配列を生成
    $number1_sorts = [];
    while ($row = $number1->fetch(PDO::FETCH_ASSOC)) {
        $number = $row['tag'];
        // 空白を無視し、重複を排除
        if (!empty($number) && !in_array($number, $number1_sorts)) {
            $number1_sorts[] = $number;
        }
    }

    // 結果の配列を生成
    $number2_sorts = [];
    while ($row = $number2->fetch(PDO::FETCH_ASSOC)) {
        $number = $row['tag'];
        // 空白を無視し、重複を排除
        if (!empty($number) && !in_array($number, $number2_sorts)) {
            $number2_sorts[] = $number;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <title>分析結果画面</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/game_result.css">
    <link rel="stylesheet" href="./css/common.css">
</head>

<body>
    <div id="loading-screen" class="loading-screen">
        <div class="loader"></div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top navi">
        <button type="button" class="logo" onclick="openMenu()"><?php echo $_SESSION['user_name'][0]; ?></button>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-white" href="user_menu.php">ユーザメニュー</a>
                </li>
                <?php
                if ($_SESSION['shoot_time_flag']) {
                    echo '<li class="nav-item"><a class="nav-link text-white" href="download_ShootTimeData.php" target="_blank">シュート時間測定結果はこちら</a></li>';
                }
                ?>
            </ul>
        </div>
    </nav>
    <!-- ポップアップメニュー -->
    <div id="popupMenu" class="menu-popup" onclick="closeMenu()">
        <div class="menu-content" onclick="event.stopPropagation()">
            <!-- 閉じるボタン -->
            <div class="col-10 user_name"><?php echo $_SESSION['user_name']; ?></div>
            <div class="col-2">
                <span class="closebtn" onclick="closeMenu()">&times;</span>
            </div>
            <br>
            <?php
            if (
                isset($_SESSION['admin_flag']) &&
                $_SESSION['admin_flag'] == true
            ) {
                echo '<a href="newAccount.php">アカウント追加</a>';
            }
            ?>
            <a href="change_pass.php">パスワードの変更</a>
            <a href="logout.php">ログアウト</a>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row" id="canvas_area">
            <div class="hidden" id="popup">
                <span id="close-btn">&times;</span>
                <div id="player"></div>
            </div>
            <div class="row">
                <div class="col-12 text-center game_name">
                    <p><?php echo $game_name; ?></p>
                </div>
                <!-- <p><?php echo $_SESSION['user_id']; ?></p> -->
                <!-- 得点領域 -->
                <div class="col-12">
                    <table class="table table-bordered border-dark">
                        <tr>
                            <td rowspan="4" class="point_team_name" id="point_team1" width="400"><?php echo $team_name1; ?></td>
                            <td rowspan="4" class="all_point" id="point1" width="100"><?php echo $team1_point; ?></td>
                            <td><?php echo $team1_first; ?></td>
                            <td class="section">1st</td>
                            <td><?php echo $team2_first; ?></td>
                            <td rowspan="4" class="all_point" id="point2" width="100"><?php echo $team2_point; ?></td>
                            <td rowspan="4" class="point_team_name" id="point_team2" width="400"><?php echo $team_name2; ?></td>
                        </tr>
                        <tr>
                            <td width="70"><?php echo $team1_latter; ?></td>
                            <td class="section">2nd</td>
                            <td width="70"><?php echo $team2_latter; ?></td>
                        </tr>
                        <?php
                        if ($_SESSION['extension']) {
                            echo '<tr><td width="70">' . $team1_extension_first . '</td><td class="section">3rd</td><td width="70">' . $team2_extension_first . '</td></tr>';
                            echo '<tr><td width="70">' . $team1_extension_latter . '</td><td class="section">4th</td><td width="70">' . $team2_extension_latter . '</td></tr>';
                        }
                        ?>
                    </table>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-12 text-center" id="select">
                    <?php
                    // プルダウンメニューの生成
                    echo '<select id="number1">';
                    echo '<option value="0" selected>選択なし</option>';
                    foreach ($number1_sorts as $number) {
                        echo '<option value="' . htmlspecialchars($number) . '">' . htmlspecialchars($number) . '</option>';
                    }
                    echo '</select>';
                    ?>
                    <input type="radio" name="time_radio" id="all_radio" class="form-check-input" value="all" checked>
                    <label for="all_radio">フルタイム</label>
                    <input type="radio" name="time_radio" id="first_radio" class="form-check-input" value="first">
                    <label for="first_radio">前半</label>
                    <input type="radio" name="time_radio" id="latter_radio" class="form-check-input" value="latter">
                    <label for="latter_radio">後半</label>
                    <?php
                    if ($_SESSION['extension']) {
                        echo '<input type="radio" name="time_radio" id="third_radio" class="form-check-input" value="third"><label for="third_radio">延長前半</label><input type="radio" name="time_radio" id="fourth_radio" class="form-check-input" value="fourth"><label for="fourth_radio">延長後半</label>';
                    }
                    ?>
                    <select id="swift_flag">
                        <option value="default">選択なし</option>
                        <option value="no-swift">遅攻のシュートを表示</option>
                        <option value="swift">速攻のシュートを表示</option>
                    </select>
                    <?php
                    // プルダウンメニューの生成
                    echo '<select id="number2">';
                    echo '<option value="0" selected>選択なし</option>';
                    foreach ($number2_sorts as $number) {
                        echo '<option value="' . htmlspecialchars($number) . '">' . htmlspecialchars($number) . '</option>';
                    }
                    echo '</select>';
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-3 text-end">
                    <canvas id="canvas_position" width="400" height="300"></canvas>
                </div>
                <div class="col-3 custom text-start" id="goal_c">
                    <table class="table table-bordered border-danger" id="ball_around_goal">
                        <tr class="table_top">
                            <td colspan="4" class="text-center out goal1" value="5">
                                <p id="T1"><?php echo $T1; ?></p>
                            </td>
                        </tr>
                        <tr class="table_middle">
                            <td rowspan="2" class="out_of_goal goal1" value="6">
                                <p id="L1"><?php echo $L1; ?></p>
                            </td>
                            <td class="in_goal goal1" value="1">
                                <p id="TL1"><?php echo $TL_s1; ?> / <?php echo $TL1; ?></p>
                                <p id="TL_r1"><?php if ($TL1 == '0') {
                                                    echo "--";
                                                } else {
                                                    echo $TL_r1;
                                                } ?> %</p>
                            </td>
                            <td class="in_goal goal1" value="2">
                                <p id="TR1"><?php echo $TR_s1; ?> / <?php echo $TR1; ?></p>
                                <p id="TR_r1"><?php if ($TR1 == '0') {
                                                    echo "--";
                                                } else {
                                                    echo $TR_r1;
                                                }  ?> %</p>
                            </td>
                            <td rowspan="2" class="out_of_goal goal1" value="7">
                                <p id="R1"><?php echo $R1; ?></p>
                            </td>
                        </tr>
                        <tr class="table_bottom">
                            <td class="in_goal goal1" value="3">
                                <p id="BL1"><?php echo $BL_s1; ?> / <?php echo $BL1; ?></p>
                                <p id="BL_r1"><?php if ($BL1 == '0') {
                                                    echo "--";
                                                } else {
                                                    echo $BL_r1;
                                                }  ?> %</p>
                            </td>
                            <td class="in_goal goal1" value="4">
                                <p id="BR1"><?php echo $BR_s1; ?> / <?php echo $BR1; ?></p>
                                <p id="BR_r1"><?php if ($BR1 == '0') {
                                                    echo "--";
                                                } else {
                                                    echo $BR_r1;
                                                }  ?> %</p>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-3 text-end">
                    <canvas id="canvas_position2" class="text-left flex-item" width="400" height="300"></canvas>
                </div>
                <div class="col-3 custom text-start" id="goal_c2">
                    <table class="table table-bordered border-danger" id="ball_around_goal2">
                        <tr>
                            <td colspan="4" class="text-center out goal2" value="5">
                                <p id="T2"><?php echo $T2; ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td rowspan="2" class="out_of_goal goal2" value="6">
                                <p id="L2"><?php echo $L2; ?></p>
                            </td>
                            <td class="in_goal goal2" id="change_color" value="1">
                                <p id="TL2"><?php echo $TL_s2; ?> / <?php echo $TL2; ?></p>
                                <p id="TL_r2"><?php if ($TL2 == '0') {
                                                    echo "--";
                                                } else {
                                                    echo $TL_r2;
                                                }  ?> %</p>
                            </td>
                            <td class="in_goal goal2" value="2">
                                <p id="TR2"><?php echo $TR_s2; ?> / <?php echo $TR2; ?></p>
                                <p id="TR_r2"><?php if ($TR2 == '0') {
                                                    echo "--";
                                                } else {
                                                    echo $TR_r2;
                                                } ?> %</p>
                            </td>
                            <td rowspan="2" class="out_of_goal goal2" value="7">
                                <p id="R2"><?php echo $R2; ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td class="in_goal goal2" value="3">
                                <p id="BL2"><?php echo $BL_s2; ?> / <?php echo $BL2; ?></p>
                                <p id="BL_r2"><?php if ($BL2 == '0') {
                                                    echo "--";
                                                } else {
                                                    echo $BL_r2;
                                                } ?> %</p>
                            </td>
                            <td class="in_goal goal2" value="4">
                                <p id="BR2"><?php echo $BR_s2; ?> / <?php echo $BR2; ?></p>
                                <p id="BR_r2"><?php if ($BR2 == '0') {
                                                    echo "--";
                                                } else {
                                                    echo $BR_r2;
                                                } ?> %</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row" id="canvas_p">
                <div class="col-5">
                    <canvas id="canvas_path" width="300" height="200"></canvas>
                </div>
                <div class="col-2 shoot_history_tb" id="shoot_history_tb">
                    <table id="shoot_history" class="mx-auto shoot_his">
                        <thead>
                            <tr>
                                <th>時刻</th>
                                <th>得点</th>
                                <th>チーム</th>
                                <th>選手</th>
                            </tr>
                        </thead>
                        <tbody class="shoot_his" id="shoot_body">
                            <?php
                            for ($i = 0; $i < count($shoot_tb["id"]); $i++) {
                                $id = $shoot_tb["id"][$i];
                                $time = $shoot_tb["time"][$i];
                                $goal_judge = $shoot_tb["goal_judge"][$i];
                                $team_name = $shoot_tb["team_name"][$i];
                                $player_num = $shoot_tb["player_num"][$i];
                                echo '<tr class="shoot_his" id="' . $id . '"><th class="shoot_his">' . $time . '</th><td class="shoot_his">' . $goal_judge . '</td><td class="shoot_his" id="team_abbreviation">' . $team_name . '</td><td class="shoot_his">' . $player_num . '</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-5 col-5-left">
                    <canvas id="canvas_path2" width="300" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="row" id="team_hikaku">
            <div class="col-12 text-center hikaku">
                <p>チーム比較</p>
            </div><br><br>
            <div class="col-12" id="stats">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th rowspan="2" class="empty"></th>
                            <?php
                            if ($_SESSION['extension']) {
                                echo '<th colspan="5" id="stats_team_name1">' . $team_name1 . '</th>';
                                echo '<th colspan="5" id="stats_team_name2">' . $team_name2 . '</th>';
                            } else {
                                echo '<th colspan="3" id="stats_team_name1">' . $team_name1 . '</th>';
                                echo '<th colspan="3" id="stats_team_name2">' . $team_name2 . '</th>';
                            }
                            ?>
                        </tr>
                        <tr id="time">
                            <td>前半</td>
                            <td>後半</td>
                            <td>合計</td>
                            <td>前半</td>
                            <td>後半</td>
                            <td>合計</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>シュート数</td>
                            <td><?php echo $shoot_count_first1; ?></td>
                            <td><?php echo $shoot_count_latter1; ?></td>
                            <td><?php echo $shoot_count1; ?></td>
                            <td><?php echo $shoot_count_first2; ?></td>
                            <td><?php echo $shoot_count_latter2; ?></td>
                            <td><?php echo $shoot_count2; ?></td>
                        </tr>
                        <tr>
                            <td>シュート成功率(%)</td>
                            <td>
                                <?php
                                if ($shoot_count_first1 == '0') {
                                    echo "-";
                                } else {
                                    echo $shoot_count_r_first1;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($shoot_count_latter1 == '0') {
                                    echo "-";
                                } else {
                                    echo $shoot_count_r_latter1;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($shoot_count1 == '0') {
                                    echo "-";
                                } else {
                                    echo $shoot_count_r1;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($shoot_count_first2 == '0') {
                                    echo "-";
                                } else {
                                    echo $shoot_count_r_first2;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($shoot_count_latter2 == '0') {
                                    echo "-";
                                } else {
                                    echo $shoot_count_r_latter2;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($shoot_count2 == '0') {
                                    echo "-";
                                } else {
                                    echo $shoot_count_r2;
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>枠外のシュート数</td>
                            <td><?php echo $out_goal_count_first1; ?></td>
                            <td><?php echo $out_goal_count_latter1; ?></td>
                            <td><?php echo $out_goal_count1; ?></td>
                            <td><?php echo $out_goal_count_first2; ?></td>
                            <td><?php echo $out_goal_count_latter2; ?></td>
                            <td><?php echo $out_goal_count2; ?></td>
                        </tr>
                        <tr>
                            <td>サイドシュートの回数</td>
                            <td><?php echo $side_count_first1; ?></td>
                            <td><?php echo $side_count_latter1; ?></td>
                            <td><?php echo $side_count1; ?></td>
                            <td><?php echo $side_count_first2; ?></td>
                            <td><?php echo $side_count_latter2; ?></td>
                            <td><?php echo $side_count2; ?></td>
                        </tr>
                        <tr>
                            <td>サイドシュート成功率(%)</td>
                            <td>
                                <?php
                                if ($side_count_first1 == '0') {
                                    echo "-";
                                } else {
                                    echo $side_count_first_r1;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($side_count_latter1 == '0') {
                                    echo "-";
                                } else {
                                    echo $side_count_latter_r1;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($side_count1 == '0') {
                                    echo "-";
                                } else {
                                    echo $side_count_r1;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($side_count_first2 == '0') {
                                    echo "-";
                                } else {
                                    echo $side_count_first_r2;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($side_count_latter2 == '0') {
                                    echo "-";
                                } else {
                                    echo $side_count_latter_r2;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($side_count2 == '0') {
                                    echo "-";
                                } else {
                                    echo $side_count_r2;
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>ミドルシュートの回数</td>
                            <td><?php echo $middle_count_first1; ?></td>
                            <td><?php echo $middle_count_latter1; ?></td>
                            <td><?php echo $middle_count1; ?></td>
                            <td><?php echo $middle_count_first2; ?></td>
                            <td><?php echo $middle_count_latter2; ?></td>
                            <td><?php echo $middle_count2; ?></td>
                        </tr>
                        <tr>
                            <td>ミドルシュート成功率(%)</td>
                            <td>
                                <?php
                                if ($middle_count_first1 == '0') {
                                    echo "-";
                                } else {
                                    echo $middle_count_first_r1;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($middle_count_latter1 == '0') {
                                    echo "-";
                                } else {
                                    echo $middle_count_latter_r1;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($middle_count1 == '0') {
                                    echo "-";
                                } else {
                                    echo $middle_count_r1;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($middle_count_first2 == '0') {
                                    echo "-";
                                } else {
                                    echo $middle_count_first_r2;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($middle_count_latter2 == '0') {
                                    echo "-";
                                } else {
                                    echo $middle_count_latter_r2;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($middle_count2 == '0') {
                                    echo "-";
                                } else {
                                    echo $middle_count_r2;
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>ロングシュートの回数</td>
                            <td><?php echo $long_count_first1; ?></td>
                            <td><?php echo $long_count_latter1; ?></td>
                            <td><?php echo $long_count1; ?></td>
                            <td><?php echo $long_count_first2; ?></td>
                            <td><?php echo $long_count_latter2; ?></td>
                            <td><?php echo $long_count2; ?></td>
                        </tr>
                        <tr>
                            <td>ロングシュート成功率(%)</td>
                            <td>
                                <?php
                                if ($long_count_first1 == '0') {
                                    echo "-";
                                } else {
                                    echo $long_count_first_r1;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($long_count_latter1 == '0') {
                                    echo "-";
                                } else {
                                    echo $long_count_latter_r1;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($long_count1 == '0') {
                                    echo "-";
                                } else {
                                    echo $long_count_r1;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($long_count_first2 == '0') {
                                    echo "-";
                                } else {
                                    echo $long_count_first_r2;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($long_count_latter2 == '0') {
                                    echo "-";
                                } else {
                                    echo $long_count_latter_r2;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($long_count2 == '0') {
                                    echo "-";
                                } else {
                                    echo $long_count_r2;
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>ペナルティ数</td>
                            <td><?php echo $seven_count_first1; ?></td>
                            <td><?php echo $seven_count_latter1; ?></td>
                            <td><?php echo $seven_count1; ?></td>
                            <td><?php echo $seven_count_first2; ?></td>
                            <td><?php echo $seven_count_latter2; ?></td>
                            <td><?php echo $seven_count2; ?></td>
                        </tr>
                        <tr>
                            <td>ペナルティ成功率(%)</td>
                            <td>
                                <?php
                                if ($seven_count_first1 == '0') {
                                    echo "-";
                                } else {
                                    echo $seven_count_r_first1;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($seven_count_latter1 == '0') {
                                    echo "-";
                                } else {
                                    echo $seven_count_r_latter1;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($seven_count1 == '0') {
                                    echo "-";
                                } else {
                                    echo $seven_count_r1;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($seven_count_first2 == '0') {
                                    echo "-";
                                } else {
                                    echo $seven_count_r_first2;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($seven_count_latter2 == '0') {
                                    echo "-";
                                } else {
                                    echo $seven_count_r_latter2;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($seven_count2 == '0') {
                                    echo "-";
                                } else {
                                    echo $seven_count_r2;
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>ブロック数</td>
                            <td><?php echo $block_count_first1; ?></td>
                            <td><?php echo $block_count_latter1; ?></td>
                            <td><?php echo $block_count1; ?></td>
                            <td><?php echo $block_count_first2; ?></td>
                            <td><?php echo $block_count_latter2; ?></td>
                            <td><?php echo $block_count2; ?></td>
                        </tr>
                        <tr>
                            <td>キーパーセーブ数</td>
                            <td><?php echo $save_count_first1; ?></td>
                            <td><?php echo $save_count_latter1; ?></td>
                            <td><?php echo $save_count1; ?></td>
                            <td><?php echo $save_count_first2; ?></td>
                            <td><?php echo $save_count_latter2; ?></td>
                            <td><?php echo $save_count2; ?></td>
                        </tr>
                        <tr>
                            <td>キーパーセーブ率</td>
                            <td>
                                <?php
                                if ($save_count_first1 == '0') {
                                    echo "-";
                                } else {
                                    echo $save_count_r_first1;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($save_count_latter1 == '0') {
                                    echo "-";
                                } else {
                                    echo $save_count_r_latter1;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($save_count1 == '0') {
                                    echo "-";
                                } else {
                                    echo $save_count_r1;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($save_count_first2 == '0') {
                                    echo "-";
                                } else {
                                    echo $save_count_r_first2;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($save_count_latter2 == '0') {
                                    echo "-";
                                } else {
                                    echo $save_count_r_latter2;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($save_count2 == '0') {
                                    echo "-";
                                } else {
                                    echo $save_count_r2;
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>速攻のシュート回数</td>
                            <td><?php echo $swift_count_first1; ?></td>
                            <td><?php echo $swift_count_latter1; ?></td>
                            <td><?php echo $swift_count1; ?></td>
                            <td><?php echo $swift_count_first2; ?></td>
                            <td><?php echo $swift_count_latter2; ?></td>
                            <td><?php echo $swift_count2; ?></td>
                        </tr>
                        <tr>
                            <td>速攻のシュート成功率(%)</td>
                            <td><?php
                                if ($swift_count_first1 == '0') {
                                    echo "-";
                                } else {
                                    echo $swift_count_r_first1;
                                }
                                ?>
                            </td>
                            <td><?php
                                if ($swift_count_latter1 == '0') {
                                    echo "-";
                                } else {
                                    echo $swift_count_r_latter1;
                                }
                                ?>
                            </td>
                            <td><?php
                                if ($swift_count1 == '0') {
                                    echo "-";
                                } else {
                                    echo $swift_count_r1;
                                }
                                ?>
                            </td>
                            <td><?php
                                if ($swift_count_first2 == '0') {
                                    echo "-";
                                } else {
                                    echo $swift_count_r_first2;
                                }
                                ?>
                            </td>
                            <td><?php
                                if ($swift_count_latter2 == '0') {
                                    echo "-";
                                } else {
                                    echo $swift_count_r_latter2;
                                }
                                ?>
                            </td>
                            <td><?php
                                if ($swift_count2 == '0') {
                                    echo "-";
                                } else {
                                    echo $swift_count_r2;
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>遅攻のシュート回数</td>
                            <td><?php echo $tikou_count_first1; ?></td>
                            <td><?php echo $tikou_count_latter1; ?></td>
                            <td><?php echo $tikou_count1; ?></td>
                            <td><?php echo $tikou_count_first2; ?></td>
                            <td><?php echo $tikou_count_latter2; ?></td>
                            <td><?php echo $tikou_count2; ?></td>
                        </tr>
                        <tr>
                            <td>遅攻のシュート成功率(%)</td>
                            <td><?php
                                if ($tikou_count_first1 == '0') {
                                    echo "-";
                                } else {
                                    echo $tikou_count_first_r1;
                                }
                                ?>
                            </td>
                            <td><?php
                                if ($tikou_count_latter1 == '0') {
                                    echo "-";
                                } else {
                                    echo $tikou_count_latter_r1;
                                }
                                ?>
                            </td>
                            <td><?php
                                if ($tikou_count1 == '0') {
                                    echo "-";
                                } else {
                                    echo $tikou_count_r1;
                                }
                                ?>
                            </td>
                            <td><?php
                                if ($tikou_count_first2 == '0') {
                                    echo "-";
                                } else {
                                    echo $tikou_count_first_r2;
                                }
                                ?>
                            </td>
                            <td><?php
                                if ($tikou_count_latter2 == '0') {
                                    echo "-";
                                } else {
                                    echo $tikou_count_latter_r2;
                                }
                                ?>
                            </td>
                            <td><?php
                                if ($tikou_count2 == '0') {
                                    echo "-";
                                } else {
                                    echo $tikou_count_r2;
                                }
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script>
        const game_id = <?php echo $_SESSION['game_id']; ?>;
        const first_time = <?php echo $first_time; ?>;
        const latter_time = <?php echo $latter_time; ?>;
        const third_time = <?php echo $third_time; ?>;
        const fourth_time = <?php echo $fourth_time; ?>;
        const team_id1 = <?php echo $team_id1; ?>;
        const team_id2 = <?php echo $team_id2; ?>;
        const extension = <?php echo $_SESSION['extension']; ?>;
        const all_shoot1 = JSON.parse('<?php echo $all_shoot1; ?>');
        const all_shoot2 = JSON.parse('<?php echo $all_shoot2; ?>');
        const shoot_tb = JSON.parse('<?php echo $shoot_table; ?>');
        const shoot_tb_first = JSON.parse('<?php echo $shoot_table_first; ?>');
        const shoot_tb_latter = JSON.parse('<?php echo $shoot_table_latter; ?>');
        let first_shoot1 = JSON.parse('<?php echo $first_shoot1; ?>');
        let first_shoot2 = JSON.parse('<?php echo $first_shoot2; ?>');
        let latter_shoot1 = JSON.parse('<?php echo $latter_shoot1; ?>');
        let latter_shoot2 = JSON.parse('<?php echo $latter_shoot2; ?>');
        let third_shoot1;
        let third_shoot2;
        let fourth_shoot1;
        let fourth_shoot2;
        let shoot_tb_third;
        let shoot_tb_fourth;
        if (extension == 1) {
            third_shoot1 = JSON.parse('<?php echo $third_shoot1; ?>');
            third_shoot2 = JSON.parse('<?php echo $third_shoot2; ?>');
            fourth_shoot1 = JSON.parse('<?php echo $fourth_shoot1; ?>');
            fourth_shoot2 = JSON.parse('<?php echo $fourth_shoot2; ?>');

            shoot_tb_third = JSON.parse('<?php echo $shoot_table_third; ?>');
            shoot_tb_fourth = JSON.parse('<?php echo $shoot_table_fourth; ?>');
        }
        const video_flag = "<?php echo $_SESSION['video_flag']; ?>";
        let youtube_id = "<?php echo $youtube_id; ?>";
        const team_name1 = "<?php echo $team_name1; ?>";
        const team_name2 = "<?php echo $team_name2; ?>";
        const popup = document.getElementById('popup');
        //＊＊＊【１】シュート率表示のcanvasについて＊＊＊
        const LW1 = "<?php echo $LW1; ?>";
        const LW_s1 = "<?php echo $LW_s1; ?>";
        const LW_r1 = "<?php echo $LW_r1; ?>";
        const PV1 = "<?php echo $PV1; ?>";
        const PV_s1 = "<?php echo $PV_s1; ?>";
        const PV_r1 = "<?php echo $PV_r1; ?>";
        const RW1 = "<?php echo $RW1; ?>";
        const RW_s1 = "<?php echo $RW_s1; ?>";
        const RW_r1 = "<?php echo $RW_r1; ?>";
        const L61 = "<?php echo $L61; ?>";
        const L6_s1 = "<?php echo $L6_s1; ?>";
        const L6_r1 = "<?php echo $L6_r1; ?>";
        const C61 = "<?php echo $C61; ?>";
        const C6_s1 = "<?php echo $C6_s1; ?>";
        const C6_r1 = "<?php echo $C6_r1; ?>";
        const R61 = "<?php echo $R61; ?>";
        const R6_s1 = "<?php echo $R6_s1; ?>";
        const R6_r1 = "<?php echo $R6_r1; ?>";
        const L91 = "<?php echo $L91; ?>";
        const L9_s1 = "<?php echo $L9_s1; ?>";
        const L9_r1 = "<?php echo $L9_r1; ?>";
        const C91 = "<?php echo $C91; ?>";
        const C9_s1 = "<?php echo $C9_s1; ?>";
        const C9_r1 = "<?php echo $C9_r1; ?>";
        const R91 = "<?php echo $R91; ?>";
        const R9_s1 = "<?php echo $R9_s1; ?>";
        const R9_r1 = "<?php echo $R9_r1; ?>";
        const seven1 = "<?php echo $seven1; ?>";
        const seven_s1 = "<?php echo $seven_s1; ?>";
        const seven_r1 = "<?php echo $seven_r1; ?>";

        const LW2 = "<?php echo $LW2; ?>";
        const LW_s2 = "<?php echo $LW_s2; ?>";
        const LW_r2 = "<?php echo $LW_r2; ?>";
        const PV2 = "<?php echo $PV2; ?>";
        const PV_s2 = "<?php echo $PV_s2; ?>";
        const PV_r2 = "<?php echo $PV_r2; ?>";
        const RW2 = "<?php echo $RW2; ?>";
        const RW_s2 = "<?php echo $RW_s2; ?>";
        const RW_r2 = "<?php echo $RW_r2; ?>";
        const L62 = "<?php echo $L62; ?>";
        const L6_s2 = "<?php echo $L6_s2; ?>";
        const L6_r2 = "<?php echo $L6_r2; ?>";
        const C62 = "<?php echo $C62; ?>";
        const C6_s2 = "<?php echo $C6_s2; ?>";
        const C6_r2 = "<?php echo $C6_r2; ?>";
        const R62 = "<?php echo $R62; ?>";
        const R6_s2 = "<?php echo $R6_s2; ?>";
        const R6_r2 = "<?php echo $R6_r2; ?>";
        const L92 = "<?php echo $L92; ?>";
        const L9_s2 = "<?php echo $L9_s2; ?>";
        const L9_r2 = "<?php echo $L9_r2; ?>";
        const C92 = "<?php echo $C92; ?>";
        const C9_s2 = "<?php echo $C9_s2; ?>";
        const C9_r2 = "<?php echo $C9_r2; ?>";
        const R92 = "<?php echo $R92; ?>";
        const R9_s2 = "<?php echo $R9_s2; ?>";
        const R9_r2 = "<?php echo $R9_r2; ?>";
        const seven2 = "<?php echo $seven2; ?>";
        const seven_s2 = "<?php echo $seven_s2; ?>";
        const seven_r2 = "<?php echo $seven_r2; ?>";

        const FLW1 = "<?php echo $FLW1; ?>";
        const FLW_s1 = "<?php echo $FLW_s1; ?>";
        const FLW_r1 = (FLW_s1 / FLW1 * 100).toFixed(1);
        const FRW1 = "<?php echo $FRW1; ?>";
        const FRW_s1 = "<?php echo $FRW_s1; ?>";
        const FRW_r1 = (FRW_s1 / FRW1 * 100).toFixed(1);
        const FL61 = "<?php echo $FL61; ?>";
        const FL6_s1 = "<?php echo $FL6_s1; ?>";
        const FL6_r1 = (FL6_s1 / FL61 * 100).toFixed(1);
        const FC61 = "<?php echo $FC61; ?>";
        const FC6_s1 = "<?php echo $FC6_s1; ?>";
        const FC6_r1 = (FC6_s1 / FC61 * 100).toFixed(1);
        const FR61 = "<?php echo $FR61; ?>";
        const FR6_s1 = "<?php echo $FR6_s1; ?>";
        const FR6_r1 = (FR6_s1 / FR61 * 100).toFixed(1);
        const FL91 = "<?php echo $FL91; ?>";
        const FL9_s1 = "<?php echo $FL9_s1; ?>";
        const FL9_r1 = (FL9_s1 / FL91 * 100).toFixed(1);
        const FC91 = "<?php echo $FC91; ?>";
        const FC9_s1 = "<?php echo $FC9_s1; ?>";
        const FC9_r1 = (FC9_s1 / FC91 * 100).toFixed(1);
        const FR91 = "<?php echo $FR91; ?>";
        const FR9_s1 = "<?php echo $FR9_s1; ?>";
        const FR9_r1 = (FR9_s1 / FR91 * 100).toFixed(1);
        const FPV1 = "<?php echo $FPV1; ?>";
        const FPV_s1 = "<?php echo $FPV_s1; ?>";
        const FPV_r1 = (FPV_s1 / FPV1 * 100).toFixed(1);
        const Fseven1 = "<?php echo $Fseven1; ?>";
        const Fseven_s1 = "<?php echo $Fseven_s1; ?>";
        const Fseven_r1 = (Fseven_s1 / Fseven1 * 100).toFixed(1);

        const FLW2 = "<?php echo $FLW2; ?>";
        const FLW_s2 = "<?php echo $FLW_s2; ?>";
        const FLW_r2 = (FLW_s2 / FLW2 * 100).toFixed(1);
        const FRW2 = "<?php echo $FRW2; ?>";
        const FRW_s2 = "<?php echo $FRW_s2; ?>";
        const FRW_r2 = (FRW_s2 / FRW2 * 100).toFixed(1);
        const FL62 = "<?php echo $FL62; ?>";
        const FL6_s2 = "<?php echo $FL6_s2; ?>";
        const FL6_r2 = (FL6_s2 / FL62 * 100).toFixed(1);
        const FC62 = "<?php echo $FC62; ?>";
        const FC6_s2 = "<?php echo $FC6_s2; ?>";
        const FC6_r2 = (FC6_s2 / FC62 * 100).toFixed(1);
        const FR62 = "<?php echo $FR62; ?>";
        const FR6_s2 = "<?php echo $FR6_s2; ?>";
        const FR6_r2 = (FR6_s2 / FR62 * 100).toFixed(1);
        const FL92 = "<?php echo $FL92; ?>";
        const FL9_s2 = "<?php echo $FL9_s2; ?>";
        const FL9_r2 = (FL9_s2 / FL92 * 100).toFixed(1);
        const FC92 = "<?php echo $FC92; ?>";
        const FC9_s2 = "<?php echo $FC9_s2; ?>";
        const FC9_r2 = (FC9_s2 / FC92 * 100).toFixed(1);
        const FR92 = "<?php echo $FR92; ?>";
        const FR9_s2 = "<?php echo $FR9_s2; ?>";
        const FR9_r2 = (FR9_s2 / FR92 * 100).toFixed(1);
        const FPV2 = "<?php echo $FPV2; ?>";
        const FPV_s2 = "<?php echo $FPV_s2; ?>";
        const FPV_r2 = (FPV_s2 / FPV2 * 100).toFixed(1);
        const Fseven2 = "<?php echo $Fseven2; ?>";
        const Fseven_s2 = "<?php echo $Fseven_s2; ?>";
        const Fseven_r2 = (Fseven_s2 / Fseven2 * 100).toFixed(1);

        const LLW1 = "<?php echo $LLW1; ?>";
        const LLW_s1 = "<?php echo $LLW_s1; ?>";
        const LLW_r1 = (LLW_s1 / LLW1 * 100).toFixed(1);
        const LRW1 = "<?php echo $LRW1; ?>";
        const LRW_s1 = "<?php echo $LRW_s1; ?>";
        const LRW_r1 = (LRW_s1 / LRW1 * 100).toFixed(1);
        const LL61 = "<?php echo $LL61; ?>";
        const LL6_s1 = "<?php echo $LL6_s1; ?>";
        const LL6_r1 = (LL6_s1 / LL61 * 100).toFixed(1);
        const LC61 = "<?php echo $LC61; ?>";
        const LC6_s1 = "<?php echo $LC6_s1; ?>";
        const LC6_r1 = (LC6_s1 / LC61 * 100).toFixed(1);
        const LR61 = "<?php echo $LR61; ?>";
        const LR6_s1 = "<?php echo $LR6_s1; ?>";
        const LR6_r1 = (LR6_s1 / LR61 * 100).toFixed(1);
        const LL91 = "<?php echo $LL91; ?>";
        const LL9_s1 = "<?php echo $LL9_s1; ?>";
        const LL9_r1 = (LL9_s1 / LL91 * 100).toFixed(1);
        const LC91 = "<?php echo $LC91; ?>";
        const LC9_s1 = "<?php echo $LC9_s1; ?>";
        const LC9_r1 = (LC9_s1 / LC91 * 100).toFixed(1);
        const LR91 = "<?php echo $LR91; ?>";
        const LR9_s1 = "<?php echo $LR9_s1; ?>";
        const LR9_r1 = (LR9_s1 / LR91 * 100).toFixed(1);
        const LPV1 = "<?php echo $LPV1; ?>";
        const LPV_s1 = "<?php echo $LPV_s1; ?>";
        const LPV_r1 = (LPV_s1 / LPV1 * 100).toFixed(1);
        const Lseven1 = "<?php echo $Lseven1; ?>";
        const Lseven_s1 = "<?php echo $Lseven_s1; ?>";
        const Lseven_r1 = (Lseven_s1 / Lseven1 * 100).toFixed(1);

        const LLW2 = "<?php echo $LLW2; ?>";
        const LLW_s2 = "<?php echo $LLW_s2; ?>";
        const LLW_r2 = (LLW_s2 / LLW2 * 100).toFixed(1);
        const LRW2 = "<?php echo $LRW2; ?>";
        const LRW_s2 = "<?php echo $LRW_s2; ?>";
        const LRW_r2 = (LRW_s2 / LRW2 * 100).toFixed(1);
        const LL62 = "<?php echo $LL62; ?>";
        const LL6_s2 = "<?php echo $LL6_s2; ?>";
        const LL6_r2 = (LL6_s2 / LL62 * 100).toFixed(1);
        const LC62 = "<?php echo $LC62; ?>";
        const LC6_s2 = "<?php echo $LC6_s2; ?>";
        const LC6_r2 = (LC6_s2 / LC62 * 100).toFixed(1);
        const LR62 = "<?php echo $LR62; ?>";
        const LR6_s2 = "<?php echo $LR6_s2; ?>";
        const LR6_r2 = (LR6_s2 / LR62 * 100).toFixed(1);
        const LL92 = "<?php echo $LL92; ?>";
        const LL9_s2 = "<?php echo $LL9_s2; ?>";
        const LL9_r2 = (LL9_s2 / LL92 * 100).toFixed(1);
        const LC92 = "<?php echo $LC92; ?>";
        const LC9_s2 = "<?php echo $LC9_s2; ?>";
        const LC9_r2 = (LC9_s2 / LC92 * 100).toFixed(1);
        const LR92 = "<?php echo $LR92; ?>";
        const LR9_s2 = "<?php echo $LR9_s2; ?>";
        const LR9_r2 = (LR9_s2 / LR92 * 100).toFixed(1);
        const LPV2 = "<?php echo $LPV2; ?>";
        const LPV_s2 = "<?php echo $LPV_s2; ?>";
        const LPV_r2 = (LPV_s2 / LPV2 * 100).toFixed(1);
        const Lseven2 = "<?php echo $Lseven2; ?>";
        const Lseven_s2 = "<?php echo $Lseven_s2; ?>";
        const Lseven_r2 = (Lseven_s2 / Lseven2 * 100).toFixed(1);

        const TL1 = "<?php echo $TL1; ?>";
        const TL_s1 = "<?php echo $TL_s1; ?>";
        const FTL1 = "<?php echo $FTL1; ?>";
        const FTL_s1 = "<?php echo $FTL_s1; ?>";
        const LTL1 = "<?php echo $LTL1; ?>";
        const LTL_s1 = "<?php echo $LTL_s1; ?>";
        const TR1 = "<?php echo $TR1; ?>";
        const TR_s1 = "<?php echo $TR_s1; ?>";
        const FTR1 = "<?php echo $FTR1; ?>";
        const FTR_s1 = "<?php echo $FTR_s1; ?>";
        const LTR1 = "<?php echo $LTR1; ?>";
        const LTR_s1 = "<?php echo $LTR_s1; ?>";
        const BL1 = "<?php echo $BL1; ?>";
        const BL_s1 = "<?php echo $BL_s1; ?>";
        const FBL1 = "<?php echo $FBL1; ?>";
        const FBL_s1 = "<?php echo $FBL_s1; ?>";
        const LBL1 = "<?php echo $LBL1; ?>";
        const LBL_s1 = "<?php echo $LBL_s1; ?>";
        const BR1 = "<?php echo $BR1; ?>";
        const BR_s1 = "<?php echo $BR_s1; ?>";
        const FBR1 = "<?php echo $FBR1; ?>";
        const FBR_s1 = "<?php echo $FBR_s1; ?>";
        const LBR1 = "<?php echo $LBR1; ?>";
        const LBR_s1 = "<?php echo $LBR_s1; ?>";
        const T1 = "<?php echo $T1; ?>";
        const FT1 = "<?php echo $FT1; ?>";
        const LT1 = "<?php echo $LT1; ?>";
        const L1 = "<?php echo $L1; ?>";
        const FL1 = "<?php echo $FL1; ?>";
        const LL1 = "<?php echo $LL1; ?>";
        const R1 = "<?php echo $R1; ?>";
        const FR1 = "<?php echo $FR1; ?>";
        const LR1 = "<?php echo $LR1; ?>";
        const TL_r1 = "<?php echo $TL_r1; ?>";
        const FTL_r1 = "<?php echo $FTL_r1; ?>";
        const LTL_r1 = "<?php echo $LTL_r1; ?>";
        const TR_r1 = "<?php echo $TR_r1; ?>";
        const FTR_r1 = "<?php echo $FTR_r1; ?>";
        const LTR_r1 = "<?php echo $LTR_r1; ?>";
        const BL_r1 = "<?php echo $BL_r1; ?>";
        const FBL_r1 = "<?php echo $FBL_r1; ?>";
        const LBL_r1 = "<?php echo $LBL_r1; ?>";
        const BR_r1 = "<?php echo $BR_r1; ?>";
        const FBR_r1 = "<?php echo $FBR_r1; ?>";
        const LBR_r1 = "<?php echo $LBR_r1; ?>";

        const TL2 = "<?php echo $TL2; ?>";
        const TL_s2 = "<?php echo $TL_s2; ?>";
        const FTL2 = "<?php echo $FTL2; ?>";
        const FTL_s2 = "<?php echo $FTL_s2; ?>";
        const LTL2 = "<?php echo $LTL2; ?>";
        const LTL_s2 = "<?php echo $LTL_s2; ?>";
        const TR2 = "<?php echo $TR2; ?>";
        const TR_s2 = "<?php echo $TR_s2; ?>";
        const FTR2 = "<?php echo $FTR2; ?>";
        const FTR_s2 = "<?php echo $FTR_s2; ?>";
        const LTR2 = "<?php echo $LTR2; ?>";
        const LTR_s2 = "<?php echo $LTR_s2; ?>";
        const BL2 = "<?php echo $BL2; ?>";
        const BL_s2 = "<?php echo $BL_s2; ?>";
        const FBL2 = "<?php echo $FBL2; ?>";
        const FBL_s2 = "<?php echo $FBL_s2; ?>";
        const LBL2 = "<?php echo $LBL2; ?>";
        const LBL_s2 = "<?php echo $LBL_s2; ?>";
        const BR2 = "<?php echo $BR2; ?>";
        const BR_s2 = "<?php echo $BR_s2; ?>";
        const FBR2 = "<?php echo $FBR2; ?>";
        const FBR_s2 = "<?php echo $FBR_s2; ?>";
        const LBR2 = "<?php echo $LBR2; ?>";
        const LBR_s2 = "<?php echo $LBR_s2; ?>";
        const T2 = "<?php echo $T2; ?>";
        const FT2 = "<?php echo $FT2; ?>";
        const LT2 = "<?php echo $LT2; ?>";
        const L2 = "<?php echo $L2; ?>";
        const FL2 = "<?php echo $FL2; ?>";
        const LL2 = "<?php echo $LL2; ?>";
        const R2 = "<?php echo $R2; ?>";
        const FR2 = "<?php echo $FR2; ?>";
        const LR2 = "<?php echo $LR2; ?>";
        const TL_r2 = "<?php echo $TL_r2; ?>";
        const FTL_r2 = "<?php echo $FTL_r2; ?>";
        const LTL_r2 = "<?php echo $LTL_r2; ?>";
        const TR_r2 = "<?php echo $TR_r2; ?>";
        const FTR_r2 = "<?php echo $FTR_r2; ?>";
        const LTR_r2 = "<?php echo $LTR_r2; ?>";
        const BL_r2 = "<?php echo $BL_r2; ?>";
        const FBL_r2 = "<?php echo $FBL_r2; ?>";
        const LBL_r2 = "<?php echo $LBL_r2; ?>";
        const BR_r2 = "<?php echo $BR_r2; ?>";
        const FBR_r2 = "<?php echo $FBR_r2; ?>";
        const LBR_r2 = "<?php echo $LBR_r2; ?>";
    </script>
    <?php
    if ($_SESSION['video_flag'] == 0) {
        echo '<script src="./js/youtube_player.js"></script>';
    }
    ?>
    <script src="./js/game_result.js"></script>

</body>

</html>