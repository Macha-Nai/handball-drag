<?php

require_once(__DIR__ . '/config.php');

//データベース(ユーザ)に接続
function connectDB()
{
    try {
        return new PDO(DSN, DB_USER, DB_PASSWORD);
    } catch (PDOException $e) {
        echo $e->getMessage();
        exit;
    }
}


function s2h($seconds)
{
    if ((string)$seconds == '0') {
        return '0:00:00';
    }
    if ($seconds == null || $seconds == '' || $seconds == false) {
        return '';
    }
    $hours = floor((int)($seconds / 3600)); // 明示的に整数にキャスト
    $minutes = floor(((int)($seconds / 60)) % 60); // 明示的に整数にキャスト
    $second = (int)$seconds % 60; // 明示的に整数にキャスト

    $hms = '0:00:00';

    if ($seconds >= 1) {
        $hms = sprintf("%d:%02d:%02d", $hours, $minutes, $second);
    }

    return $hms;
}

// function s2h($seconds)
// {
//   $hours = floor($seconds / 3600);
//   $minutes = floor(($seconds / 60) % 60);
//   $seconds = $seconds % 60;

//   $hms = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);

//   return $hms;
// }

//チーム数の取得
function countTeamNumber()
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) as `num` FROM `team_tb`';
    $sth = $dbh->query($sql);
    $result = $sth->fetch(); //データを取り出す
    $dbh = null;
    $sth = null;
    if ($result["num"] > 0) {
        //$team_selection_flag = true;
        return ($result["num"]);
    }
    return 0;
}

//チームリストの取得
function getTeamList($user_id)
{
    //DBへの接続
    $dbh = connectDB();
    //チームリストの取得
    $sql = 'SELECT * FROM `team_tb` WHERE `user_id`=' . $user_id;
    $sth = $dbh->query($sql);
    $dbh = null;
    return ($sth);
}

//チームの登録
function insertTeam($team_name, $abbreviation, $user_id)
{
    //チーム名からチームが存在するか確認
    $team_exists = confirmTeamFromName($team_name, $user_id);
    if ($team_exists == true) { //存在している
        return false;
    }
    //DBへの接続
    $dbh = connectDB();
    if ($dbh) {
        $sql = 'INSERT INTO `team_tb` (`team_name`, `abbreviation`, `user_id`) VALUES ("' . $team_name . '", "' . $abbreviation . '", ' . $user_id . ')';
        $sth = $dbh->query($sql);  //SQLの実行
        $sth = null;
    } else {
        return false; //チームの登録失敗
    }

    $dbh = null;
    return true; //チームの登録成功
}
//チーム名からチームが存在するか確認
function confirmTeamFromName($team_name, $user_id)
{
    //DBへの接続
    $dbh = connectDB();
    if ($dbh) {
        //既に登録されていないか確認
        $sql = 'SELECT count(*) as `num` FROM `team_tb` WHERE `team_name`="' . $team_name . '" AND `user_id`=' . $user_id;
        $sth = $dbh->query($sql);  //SQLの実行
        $result = $sth->fetch(PDO::FETCH_ASSOC);

        if ($result['num'] > 0) { //存在している
            $sth = null;
            $dbh = null;
            return true; //チームの登録失敗
        }
    }
    $dbh = null;
    return false; //存在していない
}

//チームIDからチーム名検索
function searchTeamNameFromID($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    if ($dbh) {
        $sql = 'SELECT * FROM `team_tb` WHERE `id`=' . $team_id;
        $sth = $dbh->query($sql);  //SQLの実行
        $result = $sth->fetch(); //データを取り出す
        $dbh = null;
        $sth = null;
        return ($result['team_name']);
    }
    $dbh = null;
    return ""; //失敗
}

//チーム名からID取得。$user_idは登録ユーザ
function getTeamIdFromName($team_name, $user_id)
{
    //DBへの接続
    $dbh = connectDB();
    if ($dbh) {
        $sql = 'SELECT `id` FROM `team_tb` WHERE `team_name`="' . $team_name . '" AND `user_id`=' . $user_id;
        $sth = $dbh->query($sql);  //SQLの実行
        $result = $sth->fetch(); //データを取り出す
        $dbh = null;
        $sth = null;
        return ($result['id']);
    }
    $dbh = null;
    return ""; //失敗
}

//ユーザ登録
function registerUser($user_name, $user_mail, $user_password, $admin_flag, $shoot_time_flag)
{
    //DBへの接続
    $dbh = connectDB();
    if ($dbh) {
        //データベースへの問い合わせSQL文(文字列)
        $sql = 'INSERT INTO `user_tb`(`user_name`, `user_mail`, `user_password`';
        if ($admin_flag == 1) {
            $sql .= ', `admin_flag`';
        }

        if ($shoot_time_flag == 1) {
            $sql .= ', `shoot_time_flag`';
        }

        $sql .= ') VALUES("' . $user_name . '","' . $user_mail . '","' . $user_password . '"';
        if ($admin_flag == 1) {
            $sql .= ', 1';
        }
        if ($shoot_time_flag == 1) {
            $sql .= ', 1';
        }
        $sql .=  ')';

        $sth = $dbh->query($sql);  //SQLの実行
        $sth = null;
        $dbh = null;
    } else {
        $dbh = null;
        return false;
    }
    return true;
}

//試合の登録
function registerGame($user_id, $game_name, $game_date, $team_id1, $team_id2, $url)
{
    //DBへの接続
    $dbh = connectDB();
    if ($dbh) {
        $sql = 'INSERT INTO `game_tb` (`user_id`, `name`, `date`, `team_id1`, `team_id2`, `url`) VALUES (' . $user_id . ', "' . $game_name . '", "' . $game_date . '", ' . $team_id1 . ', ' . $team_id2 . ', "' . $url . '"' . ')';
        $sth = $dbh->query($sql);  //SQLの実行
        $sth = null;
    } else {
        return -1; //チームの登録失敗
    }
    //試合IDの取得
    $game_id = $dbh->lastInsertId();

    $dbh = null;
    return $game_id; //チームの登録成功
}

function get_all_shoot($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id` = :game_id AND `shoot_team_id` = :team_id';
    $sth = $dbh->prepare($sql);

    $sth->bindValue(':game_id', $_SESSION['game_id'], PDO::PARAM_INT);
    $sth->bindValue(':team_id', $team_id, PDO::PARAM_INT);

    $sth->execute();

    $json1 = $sth->fetchAll(PDO::FETCH_ASSOC);

    $arrays = array();

    $id = array();
    $xy_explode = array();
    $pointed_flag = array();
    $rebound = array();
    $swift_attack = array();
    $empty_shoot = array();
    $GK_block = array();
    $DF_block = array();
    $seven = array();
    $shooter_kind = array();
    $goal_position = array();
    $number = array();
    $video_time = array();

    for ($i = 0; $i < count($json1); $i++) {
        // echo $json1[$i]['position_xy'];
        //括弧を削除
        $json2 = str_replace('[', '', $json1[$i]['position_xy']);
        $json3 = str_replace(']', '', $json2);

        // 文字列をカンマで分割
        array_push($id, $json1[$i]['id']);
        array_push($xy_explode, explode(",", $json3));
        array_push($pointed_flag, $json1[$i]['pointed_flag']);
        array_push($rebound, $json1[$i]['rebound']);
        array_push($swift_attack, $json1[$i]['swift_attack']);
        array_push($empty_shoot, $json1[$i]['empty_shoot']);
        array_push($GK_block, $json1[$i]['GK_block']);
        array_push($DF_block, $json1[$i]['DF_block']);
        array_push($seven, $json1[$i]['7m_shoot']);
        array_push($shooter_kind, $json1[$i]['shooter_kind']);
        array_push($goal_position, $json1[$i]['goal_position']);
        array_push($number, $json1[$i]['tag']);
        array_push($video_time, $json1[$i]['video_time']);
    }

    $arrays = [
        "id" => $id,
        "position_xy" => $xy_explode,
        "pointed_flag" => $pointed_flag,
        "rebound" => $rebound,
        "swift_attack" => $swift_attack,
        "empty_shoot" => $empty_shoot,
        "GK_block" => $GK_block,
        "DF_block" => $DF_block,
        "seven" => $seven,
        "shooter_kind" => $shooter_kind,
        "goal_position" => $goal_position,
        "number" => $number,
        "video_time" => $video_time
    ];

    $dbh = null;

    return $arrays;
}


function get_LatterBeginTime()
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT `video_time` FROM `video_time_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `time_kind`=1';
    $sth = $dbh->query($sql);
    $time = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $time;
}

function get_thirdBeginTime()
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT `video_time` FROM `video_time_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `time_kind`=2';
    $sth = $dbh->query($sql);
    $time = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $time;
}

function get_fourthBeginTime()
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT `video_time` FROM `video_time_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `time_kind`=3';
    $sth = $dbh->query($sql);
    $time = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $time;
}

function get_FirstShootData($kind, $time, $team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind`=' . $kind . ' AND `video_time` < ' . $time;
    $sth = $dbh->query($sql);
    $position = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $position;
}

function get_FirstShootData_pointed($kind, $time, $team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind`=' . $kind . ' AND `video_time` < ' . $time . ' AND `pointed_flag`=1';
    $sth = $dbh->query($sql);
    $position = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $position;
}

function get_LatterShootData($kind, $time, $team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind`=' . $kind . ' AND `video_time` >= ' . $time;
    $sth = $dbh->query($sql);
    $position = $sth->fetch(PDO::FETCH_COLUMN);
    return $position;
}

function get_LatterShootData_pointed($kind, $time, $team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind`=' . $kind . ' AND `video_time` >= ' . $time . ' AND `pointed_flag`=1';
    $sth = $dbh->query($sql);
    $position = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $position;
}

// function get_Locus($team_id)
// {
//   //DBへの接続
//   $dbh = connectDB();
//   $sql = 'SELECT `id`, `position_xy`, `pointed_flag`, `video_time`, `empty_shoot`, `shooter_kind`, `goal_position`
//             FROM `shoot_tb`
//             WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 ORDER BY `video_time`';
//   $sth = $dbh->query($sql);  //SQLの実行
//   //データの取得
//   $json1 = $sth->fetchAll(PDO::FETCH_ASSOC);

//   $arrays = array();

//   $id = array();
//   $xy_explode = array();
//   $pointed_flag = array();
//   $empty_shoot = array();
//   $shooter_kind = array();
//   $video_time = array();
//   $goal_position = array();

//   for ($i = 0; $i < count($json1); $i++) {
//     // echo $json1[$i]['position_xy'];
//     //括弧を削除
//     $json2 = str_replace('[', '', $json1[$i]['position_xy']);
//     $json3 = str_replace(']', '', $json2);

//     // 文字列をカンマで分割
//     $id[$i] = $json1[$i]['id'];
//     $xy_explode[$i] = explode(",", $json3);
//     $pointed_flag[$i] = $json1[$i]['pointed_flag'];
//     $goal_position[$i] = $json1[$i]['goal_position'];
//     $video_time[$i] = $json1[$i]['video_time'];
//     $empty_shoot[$i] = $json1[$i]['empty_shoot'];
//     $shooter_kind[$i] = $json1[$i]['shooter_kind'];
//   }

//   $arrays = [
//     "id" => $id,
//     "position_xy" => $xy_explode,
//     "pointed_flag" => $pointed_flag,
//     "goal_position" => $goal_position,
//     "video_time" => $video_time,
//     "empty_shoot" => $empty_shoot,
//     "shooter_kind" => $shooter_kind
//   ];

//   $dbh = null;

//   return $arrays;
// }

// function get_FirstLocus($team_id, $time)
// {
//   //DBへの接続
//   $dbh = connectDB();
//   $sql = 'SELECT `id`, `position_xy`, `pointed_flag`, `video_time`, `empty_shoot`, `shooter_kind`, `goal_position`
//             FROM `shoot_tb`
//             WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `video_time` < ' . $time . ' ORDER BY `video_time`';
//   $sth = $dbh->query($sql);  //SQLの実行
//   //データの取得
//   $json1 = $sth->fetchAll(PDO::FETCH_ASSOC);

//   $arrays = array();

//   $id = array();
//   $xy_explode = array();
//   $pointed_flag = array();
//   $empty_shoot = array();
//   $shooter_kind = array();
//   $video_time = array();
//   $goal_position = array();

//   for ($i = 0; $i < count($json1); $i++) {
//     // echo $json1[$i]['position_xy'];
//     //括弧を削除
//     $json2 = str_replace('[', '', $json1[$i]['position_xy']);
//     $json3 = str_replace(']', '', $json2);

//     // 文字列をカンマで分割
//     $id[$i] = $json1[$i]['id'];
//     $xy_explode[$i] = explode(",", $json3);
//     $pointed_flag[$i] = $json1[$i]['pointed_flag'];
//     $goal_position[$i] = $json1[$i]['goal_position'];
//     $video_time[$i] = $json1[$i]['video_time'];
//     $empty_shoot[$i] = $json1[$i]['empty_shoot'];
//     $shooter_kind[$i] = $json1[$i]['shooter_kind'];
//   }

//   $arrays = [
//     "id" => $id,
//     "position_xy" => $xy_explode,
//     "pointed_flag" => $pointed_flag,
//     "goal_position" => $goal_position,
//     "video_time" => $video_time,
//     "empty_shoot" => $empty_shoot,
//     "shooter_kind" => $shooter_kind
//   ];

//   $dbh = null;

//   return $arrays;
// }

// function get_LatterLocus($team_id, $time)
// {
//   //DBへの接続
//   $dbh = connectDB();
//   $sql = 'SELECT `id`, `position_xy`, `pointed_flag`, `video_time`, `empty_shoot`, `shooter_kind`, `goal_position`
//             FROM `shoot_tb`
//             WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `video_time` >= ' . $time . ' ORDER BY `video_time`';
//   $sth = $dbh->query($sql);  //SQLの実行
//   //データの取得
//   $json1 = $sth->fetchAll(PDO::FETCH_ASSOC);

//   $arrays = array();

//   $id = array();
//   $xy_explode = array();
//   $pointed_flag = array();
//   $empty_shoot = array();
//   $shooter_kind = array();
//   $video_time = array();
//   $goal_position = array();

//   for ($i = 0; $i < count($json1); $i++) {
//     // echo $json1[$i]['position_xy'];
//     //括弧を削除
//     $json2 = str_replace('[', '', $json1[$i]['position_xy']);
//     $json3 = str_replace(']', '', $json2);

//     // 文字列をカンマで分割
//     $id[$i] = $json1[$i]['id'];
//     $xy_explode[$i] = explode(",", $json3);
//     $pointed_flag[$i] = $json1[$i]['pointed_flag'];
//     $goal_position[$i] = $json1[$i]['goal_position'];
//     $video_time[$i] = $json1[$i]['video_time'];
//     $empty_shoot[$i] = $json1[$i]['empty_shoot'];
//     $shooter_kind[$i] = $json1[$i]['shooter_kind'];
//   }

//   $arrays = [
//     "id" => $id,
//     "position_xy" => $xy_explode,
//     "pointed_flag" => $pointed_flag,
//     "goal_position" => $goal_position,
//     "video_time" => $video_time,
//     "empty_shoot" => $empty_shoot,
//     "shooter_kind" => $shooter_kind
//   ];

//   $dbh = null;

//   return $arrays;
// }

function get_first_goal($position, $time, $team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=' . $position . ' AND `video_time` < ' . $time;
    $sth = $dbh->query($sql);
    $goal_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $goal_count;
}

function get_first_goal_s($position, $time, $team_id)
{
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=' . $position . ' AND `video_time` < ' . $time . ' AND `pointed_flag`=1';
    $sth = $dbh->query($sql);
    $goal_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $goal_count;
}

function get_latter_goal($position, $time, $team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=' . $position . ' AND `video_time` >= ' . $time;
    $sth = $dbh->query($sql);
    $goal_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $goal_count;
}

function get_latter_goal_s($position, $time, $team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=' . $position . ' AND `video_time` >= ' . $time . ' AND `pointed_flag`=1';
    $sth = $dbh->query($sql);
    $goal_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $goal_count;
}

function get_goal($position, $team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=' . $position;
    $sth = $dbh->query($sql);
    $goal_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $goal_count;
}

function get_goal_s($position, $team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=' . $position . ' AND `pointed_flag`=1';
    $sth = $dbh->query($sql);
    $goal_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $goal_count;
}

//フルタイム
function get_parse_all($game_id, $position, $team_id, $swift_flag)
{
    //DBへの接続
    $dbh = connectDB();
    if ($swift_flag == 1) {
        $sql = 'SELECT `id`, `position_xy`, `pointed_flag`, `video_time`, `empty_shoot`, `shooter_kind`, `goal_position`
            FROM `shoot_tb`
            WHERE `game_id`=' . $game_id . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `shooter_kind`=' . $position . ' ORDER BY `video_time`';
    } else if ($swift_flag == 2) {
        $sql = 'SELECT `id`, `position_xy`, `pointed_flag`, `video_time`, `empty_shoot`, `shooter_kind`, `goal_position`
            FROM `shoot_tb`
            WHERE `game_id`=' . $game_id . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `swift_attack`=0 AND `shooter_kind`=' . $position . ' ORDER BY `video_time`';
    } else {
        $sql = 'SELECT `id`, `position_xy`, `pointed_flag`, `video_time`, `empty_shoot`, `shooter_kind`, `goal_position`
            FROM `shoot_tb`
            WHERE `game_id`=' . $game_id . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `swift_attack`=1 AND `shooter_kind`=' . $position . ' ORDER BY `video_time`';
    }
    $sth = $dbh->query($sql);  //SQLの実行
    //データの取得
    $json1 = $sth->fetchAll(PDO::FETCH_ASSOC);

    $arrays = array();

    $id = array();
    $xy_explode = array();
    $pointed_flag = array();
    $empty_shoot = array();
    $shooter_kind = array();
    $video_time = array();
    $goal_position = array();

    for ($i = 0; $i < count($json1); $i++) {
        // echo $json1[$i]['position_xy'];
        //括弧を削除
        $json2 = str_replace('[', '', $json1[$i]['position_xy']);
        $json3 = str_replace(']', '', $json2);

        // 文字列をカンマで分割
        $id[$i] = $json1[$i]['id'];
        $xy_explode[$i] = explode(",", $json3);
        $pointed_flag[$i] = $json1[$i]['pointed_flag'];
        $goal_position[$i] = $json1[$i]['goal_position'];
        $video_time[$i] = $json1[$i]['video_time'];
        $empty_shoot[$i] = $json1[$i]['empty_shoot'];
        $shooter_kind[$i] = $json1[$i]['shooter_kind'];
    }

    $arrays = [
        "id" => $id,
        "position_xy" => $xy_explode,
        "pointed_flag" => $pointed_flag,
        "goal_position" => $goal_position,
        "video_time" => $video_time,
        "empty_shoot" => $empty_shoot,
        "shooter_kind" => $shooter_kind
    ];

    $dbh = null;

    return $arrays;
}

//前半
function get_parse_first($game_id, $position, $team_id, $latter_time, $swift_flag)
{
    //DBへの接続
    $dbh = connectDB();
    if ($swift_flag == 1) {
        $sql = 'SELECT `id`, `position_xy`, `pointed_flag`, `video_time`, `empty_shoot`, `shooter_kind`, `goal_position`
            FROM `shoot_tb`
            WHERE `game_id`=' . $game_id . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `shooter_kind`=' . $position . ' AND `video_time` < ' . $latter_time . ' ORDER BY `video_time`';
    } else if ($swift_flag == 2) {
        $sql = 'SELECT `id`, `position_xy`, `pointed_flag`, `video_time`, `empty_shoot`, `shooter_kind`, `goal_position`
            FROM `shoot_tb`
            WHERE `game_id`=' . $game_id . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` < ' . $latter_time . ' ORDER BY `video_time`';
    } else {
        $sql = 'SELECT `id`, `position_xy`, `pointed_flag`, `video_time`, `empty_shoot`, `shooter_kind`, `goal_position`
            FROM `shoot_tb`
            WHERE `game_id`=' . $game_id . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` < ' . $latter_time . ' ORDER BY `video_time`';
    }
    $sth = $dbh->query($sql);  //SQLの実行
    //データの取得
    $json1 = $sth->fetchAll(PDO::FETCH_ASSOC);

    $arrays = array();

    $id = array();
    $xy_explode = array();
    $pointed_flag = array();
    $empty_shoot = array();
    $shooter_kind = array();
    $video_time = array();
    $goal_position = array();

    for ($i = 0; $i < count($json1); $i++) {
        // echo $json1[$i]['position_xy'];
        //括弧を削除
        $json2 = str_replace('[', '', $json1[$i]['position_xy']);
        $json3 = str_replace(']', '', $json2);

        // 文字列をカンマで分割
        $id[$i] = $json1[$i]['id'];
        $xy_explode[$i] = explode(",", $json3);
        $pointed_flag[$i] = $json1[$i]['pointed_flag'];
        $goal_position[$i] = $json1[$i]['goal_position'];
        $video_time[$i] = $json1[$i]['video_time'];
        $empty_shoot[$i] = $json1[$i]['empty_shoot'];
        $shooter_kind[$i] = $json1[$i]['shooter_kind'];
    }

    $arrays = [
        "id" => $id,
        "position_xy" => $xy_explode,
        "pointed_flag" => $pointed_flag,
        "goal_position" => $goal_position,
        "video_time" => $video_time,
        "empty_shoot" => $empty_shoot,
        "shooter_kind" => $shooter_kind
    ];

    $dbh = null;

    return $arrays;
}

//後半
function get_parse_latter($game_id, $position, $team_id, $latter_time, $swift_flag)
{
    //DBへの接続
    $dbh = connectDB();
    if ($swift_flag == 1) {
        $sql = 'SELECT `id`, `position_xy`, `pointed_flag`, `video_time`, `empty_shoot`, `shooter_kind`, `goal_position`
            FROM `shoot_tb`
            WHERE `game_id`=' . $game_id . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `shooter_kind`=' . $position . ' AND `video_time` >= ' . $latter_time . ' ORDER BY `video_time`';
    } else if ($swift_flag == 2) {
        $sql = 'SELECT `id`, `position_xy`, `pointed_flag`, `video_time`, `empty_shoot`, `shooter_kind`, `goal_position`
            FROM `shoot_tb`
            WHERE `game_id`=' . $game_id . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` >= ' . $latter_time . ' ORDER BY `video_time`';
    } else {
        $sql = 'SELECT `id`, `position_xy`, `pointed_flag`, `video_time`, `empty_shoot`, `shooter_kind`, `goal_position`
            FROM `shoot_tb`
            WHERE `game_id`=' . $game_id . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` >= ' . $latter_time . ' ORDER BY `video_time`';
    }
    $sth = $dbh->query($sql);  //SQLの実行

    //データの取得
    $json1 = $sth->fetchAll(PDO::FETCH_ASSOC);

    $arrays = array();

    $id = array();
    $xy_explode = array();
    $pointed_flag = array();
    $empty_shoot = array();
    $shooter_kind = array();
    $video_time = array();
    $goal_position = array();

    for ($i = 0; $i < count($json1); $i++) {
        // echo $json1[$i]['position_xy'];
        //括弧を削除
        $json2 = str_replace('[', '', $json1[$i]['position_xy']);
        $json3 = str_replace(']', '', $json2);

        // 文字列をカンマで分割
        $id[$i] = $json1[$i]['id'];
        $xy_explode[$i] = explode(",", $json3);
        $pointed_flag[$i] = $json1[$i]['pointed_flag'];
        $goal_position[$i] = $json1[$i]['goal_position'];
        $video_time[$i] = $json1[$i]['video_time'];
        $empty_shoot[$i] = $json1[$i]['empty_shoot'];
        $shooter_kind[$i] = $json1[$i]['shooter_kind'];
    }

    $arrays = [
        "id" => $id,
        "position_xy" => $xy_explode,
        "pointed_flag" => $pointed_flag,
        "goal_position" => $goal_position,
        "video_time" => $video_time,
        "empty_shoot" => $empty_shoot,
        "shooter_kind" => $shooter_kind
    ];

    $dbh = null;

    return $arrays;
}

function get_shoot_course($position, $team_id, $swift_flag)
{
    //DBへの接続
    $dbh = connectDB();
    if ($position == "seven") {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `7m_shoot`=1';
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `7m_shoot`=1';
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `7m_shoot`=1';
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `7m_shoot`=1';
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=5 AND `7m_shoot`=1';
        $sth = $dbh->query($sql);
        $T = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=6 AND `7m_shoot`=1';
        $sth = $dbh->query($sql);
        $L = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=7 AND `7m_shoot`=1';
        $sth = $dbh->query($sql);
        $R = $sth->fetch(PDO::FETCH_COLUMN);
    } else {
        if ($swift_flag == 1) {
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `shooter_kind`=' . $position;
            $sth = $dbh->query($sql);
            $TL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `shooter_kind`=' . $position;
            $sth = $dbh->query($sql);
            $TR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `shooter_kind`=' . $position;
            $sth = $dbh->query($sql);
            $BL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `shooter_kind`=' . $position;
            $sth = $dbh->query($sql);
            $BR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=5 AND `shooter_kind`=' . $position;
            $sth = $dbh->query($sql);
            $T = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=6 AND `shooter_kind`=' . $position;
            $sth = $dbh->query($sql);
            $L = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=7 AND `shooter_kind`=' . $position;
            $sth = $dbh->query($sql);
            $R = $sth->fetch(PDO::FETCH_COLUMN);
        } else if ($swift_flag == 2) {
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `swift_attack`=0 AND `shooter_kind`=' . $position;
            $sth = $dbh->query($sql);
            $TL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `swift_attack`=0 AND `shooter_kind`=' . $position;
            $sth = $dbh->query($sql);
            $TR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `swift_attack`=0 AND `shooter_kind`=' . $position;
            $sth = $dbh->query($sql);
            $BL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `swift_attack`=0 AND `shooter_kind`=' . $position;
            $sth = $dbh->query($sql);
            $BR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=5 AND `swift_attack`=0 AND `shooter_kind`=' . $position;
            $sth = $dbh->query($sql);
            $T = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=6 AND `swift_attack`=0 AND `shooter_kind`=' . $position;
            $sth = $dbh->query($sql);
            $L = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=7 AND `swift_attack`=0 AND `shooter_kind`=' . $position;
            $sth = $dbh->query($sql);
            $R = $sth->fetch(PDO::FETCH_COLUMN);
        } else {
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `swift_attack`=1 AND `shooter_kind`=' . $position;
            $sth = $dbh->query($sql);
            $TL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `swift_attack`=1 AND `shooter_kind`=' . $position;
            $sth = $dbh->query($sql);
            $TR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `swift_attack`=1 AND `shooter_kind`=' . $position;
            $sth = $dbh->query($sql);
            $BL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `swift_attack`=1 AND `shooter_kind`=' . $position;
            $sth = $dbh->query($sql);
            $BR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=5 AND `swift_attack`=1 AND `shooter_kind`=' . $position;
            $sth = $dbh->query($sql);
            $T = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=6 AND `swift_attack`=1 AND `shooter_kind`=' . $position;
            $sth = $dbh->query($sql);
            $L = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=7 AND `swift_attack`=1 AND `shooter_kind`=' . $position;
            $sth = $dbh->query($sql);
            $R = $sth->fetch(PDO::FETCH_COLUMN);
        }
    }

    $goal_cnt = [
        "TL" => $TL,
        "TR" => $TR,
        "BL" => $BL,
        "BR" => $BR,
        "T" => $T,
        "L" => $L,
        "R" => $R,
    ];

    $dbh = null;

    return $goal_cnt;
}

function get_shoot_course_s($position, $team_id, $swift_flag)
{
    //DBへの接続
    $dbh = connectDB();
    if ($position == "seven") {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `7m_shoot`=1' . ' AND `pointed_flag`=1';
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `7m_shoot`=1' . ' AND `pointed_flag`=1';
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `7m_shoot`=1' . ' AND `pointed_flag`=1';
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `7m_shoot`=1' . ' AND `pointed_flag`=1';
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
    } else {
        if ($swift_flag == 1) {
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `shooter_kind`=' . $position . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $TL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `shooter_kind`=' . $position . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $TR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `shooter_kind`=' . $position . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $BL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `shooter_kind`=' . $position . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $BR = $sth->fetch(PDO::FETCH_COLUMN);
        } else if ($swift_flag == 2) {
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `swift_attack`=0 AND `shooter_kind`=' . $position . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $TL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `swift_attack`=0 AND `shooter_kind`=' . $position . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $TR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `swift_attack`=0 AND `shooter_kind`=' . $position . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $BL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `swift_attack`=0 AND `shooter_kind`=' . $position . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $BR = $sth->fetch(PDO::FETCH_COLUMN);
        } else {
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `swift_attack`=1 AND `shooter_kind`=' . $position . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $TL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `swift_attack`=1 AND `shooter_kind`=' . $position . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $TR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `swift_attack`=1 AND `shooter_kind`=' . $position . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $BL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `swift_attack`=1 AND `shooter_kind`=' . $position . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $BR = $sth->fetch(PDO::FETCH_COLUMN);
        }
    }

    $goal_cnt = [
        "TL" => $TL,
        "TR" => $TR,
        "BL" => $BL,
        "BR" => $BR,
    ];

    $dbh = null;

    return $goal_cnt;
}

function get_shoot_course_first($position, $team_id, $latter_time, $swift_flag)
{
    //DBへの接続
    $dbh = connectDB();
    if ($position == "seven") {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `7m_shoot`=1' . ' AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `7m_shoot`=1' . ' AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `7m_shoot`=1' . ' AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `7m_shoot`=1' . ' AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=5 AND `7m_shoot`=1' . ' AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $T = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=6 AND `7m_shoot`=1' . ' AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $L = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=7 AND `7m_shoot`=1' . ' AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $R = $sth->fetch(PDO::FETCH_COLUMN);
    } else {
        if ($swift_flag == 1) {
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `shooter_kind`=' . $position . ' AND `video_time` < ' . $latter_time;
            $sth = $dbh->query($sql);
            $TL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `shooter_kind`=' . $position . ' AND `video_time` < ' . $latter_time;
            $sth = $dbh->query($sql);
            $TR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `shooter_kind`=' . $position . ' AND `video_time` < ' . $latter_time;
            $sth = $dbh->query($sql);
            $BL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `shooter_kind`=' . $position . ' AND `video_time` < ' . $latter_time;
            $sth = $dbh->query($sql);
            $BR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=5 AND `shooter_kind`=' . $position . ' AND `video_time` < ' . $latter_time;
            $sth = $dbh->query($sql);
            $T = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=6 AND `shooter_kind`=' . $position . ' AND `video_time` < ' . $latter_time;
            $sth = $dbh->query($sql);
            $L = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=7 AND `shooter_kind`=' . $position . ' AND `video_time` < ' . $latter_time;
            $sth = $dbh->query($sql);
            $R = $sth->fetch(PDO::FETCH_COLUMN);
        } else if ($swift_flag == 2) {
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
            $sth = $dbh->query($sql);
            $TL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
            $sth = $dbh->query($sql);
            $TR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
            $sth = $dbh->query($sql);
            $BL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
            $sth = $dbh->query($sql);
            $BR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=5 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
            $sth = $dbh->query($sql);
            $T = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=6 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
            $sth = $dbh->query($sql);
            $L = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=7 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
            $sth = $dbh->query($sql);
            $R = $sth->fetch(PDO::FETCH_COLUMN);
        } else {
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
            $sth = $dbh->query($sql);
            $TL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
            $sth = $dbh->query($sql);
            $TR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
            $sth = $dbh->query($sql);
            $BL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
            $sth = $dbh->query($sql);
            $BR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=5 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
            $sth = $dbh->query($sql);
            $T = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=6 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
            $sth = $dbh->query($sql);
            $L = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=7 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
            $sth = $dbh->query($sql);
            $R = $sth->fetch(PDO::FETCH_COLUMN);
        }
    }

    $goal_cnt = [
        "TL" => $TL,
        "TR" => $TR,
        "BL" => $BL,
        "BR" => $BR,
        "T" => $T,
        "L" => $L,
        "R" => $R,
    ];

    $dbh = null;

    return $goal_cnt;
}

function get_shoot_course_first_s($position, $team_id, $latter_time, $swift_flag)
{
    //DBへの接続
    $dbh = connectDB();
    if ($position == "seven") {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `7m_shoot`=1' . ' AND `video_time` < ' . $latter_time . ' AND `pointed_flag`=1';
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `7m_shoot`=1' . ' AND `video_time` < ' . $latter_time . ' AND `pointed_flag`=1';
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `7m_shoot`=1' . ' AND `video_time` < ' . $latter_time . ' AND `pointed_flag`=1';
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `7m_shoot`=1' . ' AND `video_time` < ' . $latter_time . ' AND `pointed_flag`=1';
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
    } else {
        if ($swift_flag == 1) {
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `shooter_kind`=' . $position . ' AND `video_time` < ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $TL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `shooter_kind`=' . $position . ' AND `video_time` < ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $TR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `shooter_kind`=' . $position . ' AND `video_time` < ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $BL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `shooter_kind`=' . $position . ' AND `video_time` < ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $BR = $sth->fetch(PDO::FETCH_COLUMN);
        } else if ($swift_flag == 2) {
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` < ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $TL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` < ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $TR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` < ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $BL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` < ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $BR = $sth->fetch(PDO::FETCH_COLUMN);
        } else {
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` < ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $TL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` < ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $TR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` < ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $BL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` < ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $BR = $sth->fetch(PDO::FETCH_COLUMN);
        }
    }

    $goal_cnt = [
        "TL" => $TL,
        "TR" => $TR,
        "BL" => $BL,
        "BR" => $BR,
    ];

    $dbh = null;

    return $goal_cnt;
}

function get_shoot_course_latter($position, $team_id, $latter_time, $swift_flag)
{
    //DBへの接続
    $dbh = connectDB();
    if ($position == "seven") {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `7m_shoot`=1' . ' AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `7m_shoot`=1' . ' AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `7m_shoot`=1' . ' AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `7m_shoot`=1' . ' AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=5 AND `7m_shoot`=1' . ' AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $T = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=6 AND `7m_shoot`=1' . ' AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $L = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=7 AND `7m_shoot`=1' . ' AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $R = $sth->fetch(PDO::FETCH_COLUMN);
    } else {
        if ($swift_flag == 1) {
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `shooter_kind`=' . $position . ' AND `video_time` >= ' . $latter_time;
            $sth = $dbh->query($sql);
            $TL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `shooter_kind`=' . $position . ' AND `video_time` >= ' . $latter_time;
            $sth = $dbh->query($sql);
            $TR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `shooter_kind`=' . $position . ' AND `video_time` >= ' . $latter_time;
            $sth = $dbh->query($sql);
            $BL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `shooter_kind`=' . $position . ' AND `video_time` >= ' . $latter_time;
            $sth = $dbh->query($sql);
            $BR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=5 AND `shooter_kind`=' . $position . ' AND `video_time` >= ' . $latter_time;
            $sth = $dbh->query($sql);
            $T = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=6 AND `shooter_kind`=' . $position . ' AND `video_time` >= ' . $latter_time;
            $sth = $dbh->query($sql);
            $L = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=7 AND `shooter_kind`=' . $position . ' AND `video_time` >= ' . $latter_time;
            $sth = $dbh->query($sql);
            $R = $sth->fetch(PDO::FETCH_COLUMN);
        } else if ($swift_flag == 2) {
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
            $sth = $dbh->query($sql);
            $TL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
            $sth = $dbh->query($sql);
            $TR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
            $sth = $dbh->query($sql);
            $BL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
            $sth = $dbh->query($sql);
            $BR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=5 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
            $sth = $dbh->query($sql);
            $T = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=6 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
            $sth = $dbh->query($sql);
            $L = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=7 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
            $sth = $dbh->query($sql);
            $R = $sth->fetch(PDO::FETCH_COLUMN);
        } else {
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
            $sth = $dbh->query($sql);
            $TL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
            $sth = $dbh->query($sql);
            $TR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
            $sth = $dbh->query($sql);
            $BL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
            $sth = $dbh->query($sql);
            $BR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=5 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
            $sth = $dbh->query($sql);
            $T = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=6 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
            $sth = $dbh->query($sql);
            $L = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=7 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
            $sth = $dbh->query($sql);
            $R = $sth->fetch(PDO::FETCH_COLUMN);
        }
    }

    $goal_cnt = [
        "TL" => $TL,
        "TR" => $TR,
        "BL" => $BL,
        "BR" => $BR,
        "T" => $T,
        "L" => $L,
        "R" => $R,
    ];

    $dbh = null;

    return $goal_cnt;
}

function get_shoot_course_latter_s($position, $team_id, $latter_time, $swift_flag)
{
    //DBへの接続
    $dbh = connectDB();
    if ($position == "seven") {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `7m_shoot`=1' . ' AND `video_time` >= ' . $latter_time . ' AND `pointed_flag`=1';
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `7m_shoot`=1' . ' AND `video_time` >= ' . $latter_time . ' AND `pointed_flag`=1';
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `7m_shoot`=1' . ' AND `video_time` >= ' . $latter_time . ' AND `pointed_flag`=1';
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `7m_shoot`=1' . ' AND `video_time` >= ' . $latter_time . ' AND `pointed_flag`=1';
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
    } else {
        if ($swift_flag == 1) {
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `shooter_kind`=' . $position . ' AND `video_time` >= ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $TL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `shooter_kind`=' . $position . ' AND `video_time` >= ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $TR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `shooter_kind`=' . $position . ' AND `video_time` >= ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $BL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `shooter_kind`=' . $position . ' AND `video_time` >= ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $BR = $sth->fetch(PDO::FETCH_COLUMN);
        } else if ($swift_flag == 2) {
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` >= ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $TL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` >= ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $TR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` >= ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $BL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `shooter_kind`=' . $position . ' AND `swift_attack`=0 AND `video_time` >= ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $BR = $sth->fetch(PDO::FETCH_COLUMN);
        } else {
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` >= ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $TL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` >= ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $TR = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` >= ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $BL = $sth->fetch(PDO::FETCH_COLUMN);
            $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `shooter_kind`=' . $position . ' AND `swift_attack`=1 AND `video_time` >= ' . $latter_time . ' AND `pointed_flag`=1';
            $sth = $dbh->query($sql);
            $BR = $sth->fetch(PDO::FETCH_COLUMN);
        }
    }

    $goal_cnt = [
        "TL" => $TL,
        "TR" => $TR,
        "BL" => $BL,
        "BR" => $BR,
    ];

    $dbh = null;

    return $goal_cnt;
}

function check_video($game_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT `url`
            FROM `game_tb`
            WHERE `id`=' . $game_id;
    $sth = $dbh->query($sql);
    $url = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    if ($url == "none") {
        return true;
    } else {
        return false;
    }
}

function get_YoutubeId($game_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT `url`
            FROM `game_tb`
            WHERE `id`=' . $game_id;
    $sth = $dbh->query($sql);
    $url = $sth->fetch(PDO::FETCH_COLUMN);
    $youtube_id = getYoutubeIdFromUrl($url);

    $dbh = null;

    return $youtube_id;
}

function getYoutubeIdFromUrl($youtube_url)
{
    preg_match('/(http(s|):|)\/\/(www\.|)yout(.*?)\/(embed\/|watch.*?v=|)([a-z_A-Z0-9\-]{11})/i', $youtube_url, $results);
    return $results[6];
}

function get_FirstTime($game_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT `video_time`
            FROM `video_time_tb`
            WHERE `game_id`=' . $game_id . ' AND `time_kind`=0';
    $sth = $dbh->query($sql);
    $first_time = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $first_time;
}

function get_LatterTime($game_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT `video_time`
            FROM `video_time_tb`
            WHERE `game_id`=' . $game_id . ' AND `time_kind`=1';

    $sth = $dbh->query($sql);
    $latter_time = $sth->fetch(PDO::FETCH_COLUMN);

    if ($latter_time == null || $latter_time == '') {
        $latter_time = $_SESSION['first_time'] + 20000;
    }

    $dbh = null;

    return $latter_time;
}

function get_shoot_table($first_time, $latter_time, $team_id1, $team_id2, $team_name1, $team_name2)
{
    $shoot_table = [];
    $id_list = [];
    $time_list = [];
    $team_name_list = [];
    $goal_judge_list = [];
    $player_num = [];
    $video_time_list = [];
    $team_name = "";
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `7m_shoot`=0 ORDER BY `video_time`';
    $sth = $dbh->query($sql);
    while ($row = $sth->fetch()) {
        $team_id = $row['shoot_team_id'];
        if ($team_id == $team_id1) {
            $team_name = $team_name1;
        } else if ($team_id == $team_id2) {
            $team_name = $team_name2;
        }
        $seconds = $row['video_time'];
        if ($seconds < $latter_time) {
            $time = s2h($seconds - $first_time);
        } else {
            $time = s2h($seconds - $latter_time);
        }
        $pointed_flag = $row['pointed_flag'];
        $goal_judge = '○';
        if ($pointed_flag == 0) {
            $goal_judge = '×';
        }
        array_push($id_list, $row['id']);
        array_push($time_list, $time);
        array_push($goal_judge_list, $goal_judge);
        array_push($team_name_list, $team_name);
        array_push($player_num, $row['tag']);
        array_push($video_time_list, $seconds);
    }
    $shoot_table = [
        "id" => $id_list,
        "time" => $time_list,
        "goal_judge" => $goal_judge_list,
        "team_name" => $team_name_list,
        "player_num" => $player_num,
        "video_time" => $video_time_list,
    ];

    $dbh = null;

    return $shoot_table;
}

function get_shoot_table_first($shoot_tb, $first_time, $latter_time, $team_id1, $team_id2, $team_name1, $team_name2)
{
    $shoot_table = [];
    $id_list = [];
    $time_list = [];
    $team_name_list = [];
    $goal_judge_list = [];
    $player_num = [];
    $video_time_list = [];
    $team_name = "";
    //DBへの接続
    // $dbh = connectDB();
    // $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `7m_shoot`=0 AND `video_time` < ' . $latter_time . ' ORDER BY `video_time`';
    // $sth = $dbh->query($sql);
    foreach ($shoot_tb as $row) {
        $team_id = $row['shoot_team_id'];
        if ($team_id == $team_id1) {
            $team_name = $team_name1;
        } else if ($team_id == $team_id2) {
            $team_name = $team_name2;
        }
        $seconds = $row['video_time'];
        if ($seconds < $latter_time) {
            $time = s2h($seconds - $first_time);
        } else {
            $time = s2h($seconds - $latter_time);
        }
        $pointed_flag = $row['pointed_flag'];
        $goal_judge = '○';
        if ($pointed_flag == 0) {
            $goal_judge = '×';
        }
        array_push($id_list, $row['id']);
        array_push($time_list, $time);
        array_push($goal_judge_list, $goal_judge);
        array_push($team_name_list, $team_name);
        array_push($player_num, $row['tag']);
        array_push($video_time_list, $seconds);
    }
    $shoot_table = [
        "id" => $id_list,
        "time" => $time_list,
        "goal_judge" => $goal_judge_list,
        "team_name" => $team_name_list,
        "player_num" => $player_num,
        "video_time" => $video_time_list,
    ];

    return $shoot_table;
}

function get_shoot_table_latter($shoot_tb, $first_time, $latter_time, $team_id1, $team_id2, $team_name1, $team_name2)
{
    $shoot_table = [];
    $id_list = [];
    $time_list = [];
    $team_name_list = [];
    $goal_judge_list = [];
    $player_num = [];
    $video_time_list = [];
    $team_name = "";
    //DBへの接続
    // $dbh = connectDB();
    // $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `7m_shoot`=0 AND `video_time` >= ' . $latter_time . ' ORDER BY `video_time`';
    // $sth = $dbh->query($sql);
    foreach ($shoot_tb as $row) {
        $team_id = $row['shoot_team_id'];
        if ($team_id == $team_id1) {
            $team_name = $team_name1;
        } else if ($team_id == $team_id2) {
            $team_name = $team_name2;
        }
        $seconds = $row['video_time'];
        if ($seconds < $latter_time) {
            $time = s2h($seconds - $first_time);
        } else {
            $time = s2h($seconds - $latter_time);
        }
        $pointed_flag = $row['pointed_flag'];
        $goal_judge = '○';
        if ($pointed_flag == 0) {
            $goal_judge = '×';
        }
        array_push($id_list, $row['id']);
        array_push($time_list, $time);
        array_push($goal_judge_list, $goal_judge);
        array_push($team_name_list, $team_name);
        array_push($player_num, $row['tag']);
        array_push($video_time_list, $seconds);
    }
    $shoot_table = [
        "id" => $id_list,
        "time" => $time_list,
        "goal_judge" => $goal_judge_list,
        "team_name" => $team_name_list,
        "player_num" => $player_num,
        "video_time" => $video_time_list,
    ];

    return $shoot_table;
}

function get_shoot_table_select($position, $first_time, $latter_time, $team_id, $team_name, $swift_flag)
{
    $shoot_table = [];
    $id_list = [];
    $time_list = [];
    $team_name_list = [];
    $goal_judge_list = [];
    $player_num = [];
    $video_time_list = [];
    //DBへの接続
    $dbh = connectDB();
    if ($position == "seven") {
        $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=1' . ' ORDER BY `video_time`';
    } else if ($swift_flag == 1) {
        $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind`=' . $position . ' ORDER BY `video_time`';
    } else if ($swift_flag == 2) {
        $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `swift_attack`=0 AND `shooter_kind`=' . $position . ' ORDER BY `video_time`';
    } else {
        $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `swift_attack`=1 AND `shooter_kind`=' . $position . ' ORDER BY `video_time`';
    }
    $sth = $dbh->query($sql);
    while ($row = $sth->fetch()) {
        $seconds = $row['video_time'];
        if ($seconds < $latter_time) {
            $time = s2h($seconds - $first_time);
        } else {
            $time = s2h($seconds - $latter_time);
        }
        $pointed_flag = $row['pointed_flag'];
        $goal_judge = '○';
        if ($pointed_flag == 0) {
            $goal_judge = '×';
        }
        array_push($id_list, $row['id']);
        array_push($time_list, $time);
        array_push($goal_judge_list, $goal_judge);
        array_push($team_name_list, $team_name);
        array_push($player_num, $row['tag']);
        array_push($video_time_list, $seconds);
    }
    $shoot_table = [
        "id" => $id_list,
        "time" => $time_list,
        "goal_judge" => $goal_judge_list,
        "team_name" => $team_name_list,
        "player_num" => $player_num,
        "video_time" => $video_time_list,
    ];

    $dbh = null;

    return $shoot_table;
}

function get_shoot_table_select_first($position, $first_time, $latter_time, $team_id, $team_name, $swift_flag)
{
    $shoot_table = [];
    $id_list = [];
    $time_list = [];
    $team_name_list = [];
    $goal_judge_list = [];
    $player_num = [];
    $video_time_list = [];
    //DBへの接続
    $dbh = connectDB();
    if ($position == "seven") {
        $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `video_time` < ' . $latter_time . ' AND `7m_shoot`=1' . ' ORDER BY `video_time`';
    } else if ($swift_flag == 1) {
        $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `video_time` < ' . $latter_time . ' AND `shooter_kind`=' . $position . ' ORDER BY `video_time`';
    } else if ($swift_flag == 2) {
        $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `video_time` < ' . $latter_time . ' AND `swift_attack`=0 AND `shooter_kind`=' . $position . ' ORDER BY `video_time`';
    } else {
        $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `video_time` < ' . $latter_time . ' AND `swift_attack`=1 AND `shooter_kind`=' . $position . ' ORDER BY `video_time`';
    }
    $sth = $dbh->query($sql);
    while ($row = $sth->fetch()) {
        $seconds = $row['video_time'];
        if ($seconds < $latter_time) {
            $time = s2h($seconds - $first_time);
        } else {
            $time = s2h($seconds - $latter_time);
        }
        $pointed_flag = $row['pointed_flag'];
        $goal_judge = '○';
        if ($pointed_flag == 0) {
            $goal_judge = '×';
        }
        array_push($id_list, $row['id']);
        array_push($time_list, $time);
        array_push($goal_judge_list, $goal_judge);
        array_push($team_name_list, $team_name);
        array_push($player_num, $row['tag']);
        array_push($video_time_list, $seconds);
    }

    $shoot_table = [
        "id" => $id_list,
        "time" => $time_list,
        "goal_judge" => $goal_judge_list,
        "team_name" => $team_name_list,
        "player_num" => $player_num,
        "video_time" => $video_time_list,
    ];

    $dbh = null;

    return $shoot_table;
}

function get_shoot_table_select_latter($position, $first_time, $latter_time, $team_id, $team_name, $swift_flag)
{
    $shoot_table = [];
    $id_list = [];
    $time_list = [];
    $team_name_list = [];
    $goal_judge_list = [];
    $player_num = [];
    $video_time_list = [];
    //DBへの接続
    $dbh = connectDB();
    if ($position == "seven") {
        $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `video_time` >= ' . $latter_time . ' AND `7m_shoot`=1' . ' ORDER BY `video_time`';
    } else if ($swift_flag == 1) {
        $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `video_time` >= ' . $latter_time . ' AND `shooter_kind`=' . $position . ' ORDER BY `video_time`';
    } else if ($swift_flag == 2) {
        $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `video_time` >= ' . $latter_time . ' AND `swift_attack`=0 AND `shooter_kind`=' . $position . ' ORDER BY `video_time`';
    } else {
        $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `video_time` >= ' . $latter_time . ' AND `swift_attack`=1 AND `shooter_kind`=' . $position . ' ORDER BY `video_time`';
    }
    $sth = $dbh->query($sql);
    while ($row = $sth->fetch()) {
        $seconds = $row['video_time'];
        if ($seconds < $latter_time) {
            $time = s2h($seconds - $first_time);
        } else {
            $time = s2h($seconds - $latter_time);
        }
        $pointed_flag = $row['pointed_flag'];
        $goal_judge = '○';
        if ($pointed_flag == 0) {
            $goal_judge = '×';
        }
        array_push($id_list, $row['id']);
        array_push($time_list, $time);
        array_push($goal_judge_list, $goal_judge);
        array_push($team_name_list, $team_name);
        array_push($player_num, $row['tag']);
        array_push($video_time_list, $seconds);
    }
    $shoot_table = [
        "id" => $id_list,
        "time" => $time_list,
        "goal_judge" => $goal_judge_list,
        "team_name" => $team_name_list,
        "player_num" => $player_num,
        "video_time" => $video_time_list,
    ];

    $dbh = null;

    return $shoot_table;
}

function get_shoot_count($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0';
    $sth = $dbh->query($sql);
    $shoot_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $shoot_count;
}

function get_shoot_count_s($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `pointed_flag`=1';
    $sth = $dbh->query($sql);
    $shoot_count_s = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $shoot_count_s;
}

function get_block_count($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `DF_block`=1';
    $sth = $dbh->query($sql);
    $block_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $block_count;
}

function get_7m_shoot_count($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=1';
    $sth = $dbh->query($sql);
    $shoot_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $shoot_count;
}

function get_7m_shoot_count_s($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=1 AND `pointed_flag`=1';
    $sth = $dbh->query($sql);
    $shoot_count_s = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $shoot_count_s;
}

function get_in_goal_count($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position` NOT IN (5, 6, 7) AND `7m_shoot`=0';
    $sth = $dbh->query($sql);
    $in_goal_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $in_goal_count;
}

function get_save_count($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `GK_block`=1';
    $sth = $dbh->query($sql);
    $save_count = $sth->fetch(PDO::FETCH_COLUMN);
    return $save_count;
}

function get_out_goal_count($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position` NOT IN (1, 2, 3, 4) AND `7m_shoot`=0';
    $sth = $dbh->query($sql);
    $out_goal_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $out_goal_count;
}

function get_first_start()
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT `video_time`
            FROM `video_time_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `time_kind`=0';
    $sth = $dbh->query($sql);
    $first_start_time = $sth->fetch(PDO::FETCH_COLUMN);
    return $first_start_time;
}

function get_latter_start()
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT `video_time`
            FROM `video_time_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `time_kind`=1';
    $sth = $dbh->query($sql);
    $first_latter_time = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $first_latter_time;
}

function get_ex_first_start()
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT `video_time`
            FROM `video_time_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `time_kind`=2';
    $sth = $dbh->query($sql);
    $ex_first_start_time = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $ex_first_start_time;
}

function get_ex_latter_start()
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT `video_time`
            FROM `video_time_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `time_kind`=3';
    $sth = $dbh->query($sql);
    $ex_latter_start_time = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $ex_latter_start_time;
}

function get_timeout($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $timeout_list = [];
    $sql = 'SELECT `video_time`
            FROM `video_time_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `team_id`=' . $team_id . ' AND `time_kind`=4';
    $sth = $dbh->query($sql);
    while ($row = $sth->fetch()) {
        array_push($timeout_list, $row['video_time']);
    }

    $dbh = null;

    return $timeout_list;
}

function get_position($team_id, $swift_flag)
{
    $dbh = connectDB();

    $array = array();

    if ($swift_flag == 1) {
        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=1';
        $sth = $dbh->query($sql);
        //データの取得
        $LW = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=1 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        //データの取得
        $LW_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=3';
        $sth = $dbh->query($sql);
        $RW = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=3 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $RW_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=2';
        $sth = $dbh->query($sql);
        $PV = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=2 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $PV_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=4';
        $sth = $dbh->query($sql);
        $L6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=4 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $L6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=5';
        $sth = $dbh->query($sql);
        $C6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=5 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $C6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=6';
        $sth = $dbh->query($sql);
        $R6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=6 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $R6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=7';
        $sth = $dbh->query($sql);
        $L9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=7 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $L9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=8';
        $sth = $dbh->query($sql);
        $C9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=8 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $C9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=9';
        $sth = $dbh->query($sql);
        $R9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=9 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $R9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND 7m_shoot=1';
        $sth = $dbh->query($sql);
        $seven = $sth->fetch(PDO::FETCH_COLUMN);


        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND 7m_shoot=1 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $seven_s = $sth->fetch(PDO::FETCH_COLUMN);
    } else if ($swift_flag == 2) {
        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=1 AND `swift_attack`=0';
        $sth = $dbh->query($sql);
        //データの取得
        $LW = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=1  AND `swift_attack`=0 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        //データの取得
        $LW_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=3 AND `swift_attack`=0';
        $sth = $dbh->query($sql);
        $RW = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=3 AND `swift_attack`=0 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $RW_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=2 AND `swift_attack`=0';
        $sth = $dbh->query($sql);
        $PV = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=2 AND `swift_attack`=0 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $PV_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=4 AND `swift_attack`=0';
        $sth = $dbh->query($sql);
        $L6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=4 AND `swift_attack`=0 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $L6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=5 AND `swift_attack`=0';
        $sth = $dbh->query($sql);
        $C6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=5 AND `swift_attack`=0 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $C6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=6 AND `swift_attack`=0';
        $sth = $dbh->query($sql);
        $R6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=6 AND `swift_attack`=0 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $R6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=7 AND `swift_attack`=0';
        $sth = $dbh->query($sql);
        $L9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=7 AND `swift_attack`=0 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $L9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=8 AND `swift_attack`=0';
        $sth = $dbh->query($sql);
        $C9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=8 AND `swift_attack`=0 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $C9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=9 AND `swift_attack`=0';
        $sth = $dbh->query($sql);
        $R9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=9 AND `swift_attack`=0 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $R9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND 7m_shoot=1';
        $sth = $dbh->query($sql);
        $seven = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND 7m_shoot=1 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $seven_s = $sth->fetch(PDO::FETCH_COLUMN);
    } else {
        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=1 AND `swift_attack`=1';
        $sth = $dbh->query($sql);
        //データの取得
        $LW = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=1 AND `swift_attack`=1 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        //データの取得
        $LW_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=3 AND `swift_attack`=1';
        $sth = $dbh->query($sql);
        $RW = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=3 AND `swift_attack`=1 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $RW_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=2 AND `swift_attack`=1';
        $sth = $dbh->query($sql);
        $PV = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=2 AND `swift_attack`=1 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $PV_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=4 AND `swift_attack`=1';
        $sth = $dbh->query($sql);
        $L6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=4 AND `swift_attack`=1 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $L6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=5 AND `swift_attack`=1';
        $sth = $dbh->query($sql);
        $C6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=5 AND `swift_attack`=1 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $C6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=6 AND `swift_attack`=1';
        $sth = $dbh->query($sql);
        $R6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=6 AND `swift_attack`=1 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $R6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=7 AND `swift_attack`=1';
        $sth = $dbh->query($sql);
        $L9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=7 AND `swift_attack`=1 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $L9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=8 AND `swift_attack`=1';
        $sth = $dbh->query($sql);
        $C9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=8 AND `swift_attack`=1 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $C9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=9 AND `swift_attack`=1';
        $sth = $dbh->query($sql);
        $R9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=9 AND `swift_attack`=1 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $R9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND 7m_shoot=1';
        $sth = $dbh->query($sql);
        $seven = $sth->fetch(PDO::FETCH_COLUMN);


        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND 7m_shoot=1 AND pointed_flag=1';
        $sth = $dbh->query($sql);
        $seven_s = $sth->fetch(PDO::FETCH_COLUMN);
    }
    if ($LW == 0) {
        $LW_r = "NAN";
    } else {
        $LW_r = round($LW_s / $LW * 100, 1);
    }
    if ($RW == 0) {
        $RW_r = "NAN";
    } else {
        $RW_r = round($RW_s / $RW * 100, 1);
    }
    if ($PV == 0) {
        $PV_r = "NAN";
    } else {
        $PV_r = round($PV_s / $PV * 100, 1);
    }
    if ($L6 == 0) {
        $L6_r = "NAN";
    } else {
        $L6_r = round($L6_s / $L6 * 100, 1);
    }
    if ($C6 == 0) {
        $C6_r = "NAN";
    } else {
        $C6_r = round($C6_s / $C6 * 100, 1);
    }
    if ($R6 == 0) {
        $R6_r = "NAN";
    } else {
        $R6_r = round($R6_s / $R6 * 100, 1);
    }
    if ($L9 == 0) {
        $L9_r = "NAN";
    } else {
        $L9_r = round($L9_s / $L9 * 100, 1);
    }
    if ($C9 == 0) {
        $C9_r = "NAN";
    } else {
        $C9_r = round($C9_s / $C9 * 100, 1);
    }
    if ($R9 == 0) {
        $R9_r = "NAN";
    } else {
        $R9_r = round($R9_s / $R9 * 100, 1);
    }
    if ($seven == 0) {
        $seven_r = "NAN";
    } else {
        $seven_r = round($seven_s / $seven * 100, 1);
    }

    $array = [
        "LW" => $LW,
        "LW_s" => $LW_s,
        "RW" => $RW,
        "RW_s" => $RW_s,
        "PV" => $PV,
        "PV_s" => $PV_s,
        "L6" => $L6,
        "L6_s" => $L6_s,
        "C6" => $C6,
        "C6_s" => $C6_s,
        "R6" => $R6,
        "R6_s" => $R6_s,
        "L9" => $L9,
        "L9_s" => $L9_s,
        "C9" => $C9,
        "C9_s" => $C9_s,
        "R9" => $R9,
        "R9_s" => $R9_s,
        "seven" => $seven,
        "seven_s" => $seven_s,
        "LW_r" => $LW_r,
        "RW_r" => $RW_r,
        "PV_r" => $PV_r,
        "L6_r" => $L6_r,
        "C6_r" => $C6_r,
        "R6_r" => $R6_r,
        "L9_r" => $L9_r,
        "C9_r" => $C9_r,
        "R9_r" => $R9_r,
        "seven_r" => $seven_r
    ];

    $dbh = null;

    return $array;
}

function get_position_first($team_id, $swift_flag, $latter_time)
{
    $dbh = connectDB();

    $array = array();

    if ($swift_flag == 1) {
        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        //データの取得
        $LW = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=1 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        //データの取得
        $LW_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=3 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $RW = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=3 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $RW_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=2 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $PV = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=2 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $PV_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=4 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $L6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=4 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $L6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=5 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $C6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=5 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $C6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=6 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $R6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=6 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $R6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=7 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $L9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=7 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $L9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=8 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $C9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=8 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $C9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=9 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $R9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=9 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $R9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND 7m_shoot=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $seven = $sth->fetch(PDO::FETCH_COLUMN);


        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND 7m_shoot=1 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $seven_s = $sth->fetch(PDO::FETCH_COLUMN);
    } else if ($swift_flag == 2) {
        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=1 AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        //データの取得
        $LW = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=1  AND `swift_attack`=0 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        //データの取得
        $LW_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=3 AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $RW = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=3 AND `swift_attack`=0 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $RW_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=2 AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $PV = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=2 AND `swift_attack`=0 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $PV_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=4 AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $L6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=4 AND `swift_attack`=0 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $L6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=5 AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $C6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=5 AND `swift_attack`=0 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $C6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=6 AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $R6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=6 AND `swift_attack`=0 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $R6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=7 AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $L9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=7 AND `swift_attack`=0 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $L9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=8 AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $C9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=8 AND `swift_attack`=0 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $C9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=9 AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $R9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=9 AND `swift_attack`=0 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $R9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND 7m_shoot=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $seven = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND 7m_shoot=1 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $seven_s = $sth->fetch(PDO::FETCH_COLUMN);
    } else {
        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=1 AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        //データの取得
        $LW = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=1 AND `swift_attack`=1 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        //データの取得
        $LW_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=3 AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $RW = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=3 AND `swift_attack`=1 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $RW_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=2 AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $PV = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=2 AND `swift_attack`=1 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $PV_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=4 AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $L6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=4 AND `swift_attack`=1 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $L6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=5 AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $C6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=5 AND `swift_attack`=1 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $C6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=6 AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $R6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=6 AND `swift_attack`=1 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $R6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=7 AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $L9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=7 AND `swift_attack`=1 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $L9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=8 AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $C9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=8 AND `swift_attack`=1 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $C9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=9 AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $R9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=9 AND `swift_attack`=1 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $R9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND 7m_shoot=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $seven = $sth->fetch(PDO::FETCH_COLUMN);


        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND 7m_shoot=1 AND pointed_flag=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $seven_s = $sth->fetch(PDO::FETCH_COLUMN);
    }
    if ($LW == 0) {
        $LW_r = "NAN";
    } else {
        $LW_r = round($LW_s / $LW * 100, 1);
    }
    if ($RW == 0) {
        $RW_r = "NAN";
    } else {
        $RW_r = round($RW_s / $RW * 100, 1);
    }
    if ($PV == 0) {
        $PV_r = "NAN";
    } else {
        $PV_r = round($PV_s / $PV * 100, 1);
    }
    if ($L6 == 0) {
        $L6_r = "NAN";
    } else {
        $L6_r = round($L6_s / $L6 * 100, 1);
    }
    if ($C6 == 0) {
        $C6_r = "NAN";
    } else {
        $C6_r = round($C6_s / $C6 * 100, 1);
    }
    if ($R6 == 0) {
        $R6_r = "NAN";
    } else {
        $R6_r = round($R6_s / $R6 * 100, 1);
    }
    if ($L9 == 0) {
        $L9_r = "NAN";
    } else {
        $L9_r = round($L9_s / $L9 * 100, 1);
    }
    if ($C9 == 0) {
        $C9_r = "NAN";
    } else {
        $C9_r = round($C9_s / $C9 * 100, 1);
    }
    if ($R9 == 0) {
        $R9_r = "NAN";
    } else {
        $R9_r = round($R9_s / $R9 * 100, 1);
    }
    if ($seven == 0) {
        $seven_r = "NAN";
    } else {
        $seven_r = round($seven_s / $seven * 100, 1);
    }

    $array = [
        "LW" => $LW,
        "LW_s" => $LW_s,
        "RW" => $RW,
        "RW_s" => $RW_s,
        "PV" => $PV,
        "PV_s" => $PV_s,
        "L6" => $L6,
        "L6_s" => $L6_s,
        "C6" => $C6,
        "C6_s" => $C6_s,
        "R6" => $R6,
        "R6_s" => $R6_s,
        "L9" => $L9,
        "L9_s" => $L9_s,
        "C9" => $C9,
        "C9_s" => $C9_s,
        "R9" => $R9,
        "R9_s" => $R9_s,
        "seven" => $seven,
        "seven_s" => $seven_s,
        "LW_r" => $LW_r,
        "RW_r" => $RW_r,
        "PV_r" => $PV_r,
        "L6_r" => $L6_r,
        "C6_r" => $C6_r,
        "R6_r" => $R6_r,
        "L9_r" => $L9_r,
        "C9_r" => $C9_r,
        "R9_r" => $R9_r,
        "seven_r" => $seven_r
    ];

    $dbh = null;

    return $array;
}

function get_position_latter($team_id, $swift_flag, $latter_time)
{
    $dbh = connectDB();

    $array = array();

    if ($swift_flag == 1) {
        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        //データの取得
        $LW = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=1 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        //データの取得
        $LW_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=3 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $RW = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=3 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $RW_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=2 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $PV = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=2 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $PV_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=4 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $L6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=4 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $L6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=5 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $C6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=5 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $C6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=6 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $R6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=6 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $R6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=7 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $L9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=7 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $L9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=8 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $C9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=8 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $C9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=9 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $R9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=9 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $R9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND 7m_shoot=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $seven = $sth->fetch(PDO::FETCH_COLUMN);


        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND 7m_shoot=1 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $seven_s = $sth->fetch(PDO::FETCH_COLUMN);
    } else if ($swift_flag == 2) {
        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=1 AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        //データの取得
        $LW = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=1  AND `swift_attack`=0 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        //データの取得
        $LW_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=3 AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $RW = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=3 AND `swift_attack`=0 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $RW_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=2 AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $PV = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=2 AND `swift_attack`=0 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $PV_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=4 AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $L6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=4 AND `swift_attack`=0 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $L6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=5 AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $C6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=5 AND `swift_attack`=0 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $C6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=6 AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $R6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=6 AND `swift_attack`=0 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $R6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=7 AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $L9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=7 AND `swift_attack`=0 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $L9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=8 AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $C9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=8 AND `swift_attack`=0 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $C9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=9 AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $R9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=9 AND `swift_attack`=0 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $R9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND 7m_shoot=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $seven = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND 7m_shoot=1 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $seven_s = $sth->fetch(PDO::FETCH_COLUMN);
    } else {
        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=1 AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        //データの取得
        $LW = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=1 AND `swift_attack`=1 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        //データの取得
        $LW_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=3 AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $RW = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=3 AND `swift_attack`=1 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $RW_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=2 AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $PV = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=2 AND `swift_attack`=1 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $PV_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=4 AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $L6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=4 AND `swift_attack`=1 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $L6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=5 AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $C6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=5 AND `swift_attack`=1 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $C6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=6 AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $R6 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=6 AND `swift_attack`=1 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $R6_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=7 AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $L9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=7 AND `swift_attack`=1 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $L9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=8 AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $C9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=8 AND `swift_attack`=1 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $C9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=9 AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $R9 = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND shooter_kind=9 AND `swift_attack`=1 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $R9_s = $sth->fetch(PDO::FETCH_COLUMN);

        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND 7m_shoot=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $seven = $sth->fetch(PDO::FETCH_COLUMN);


        $sql = 'SELECT count(*)
        FROM shoot_tb
        WHERE game_id=' . $_SESSION['game_id'] . ' AND shoot_team_id=' . $team_id . ' AND 7m_shoot=1 AND pointed_flag=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $seven_s = $sth->fetch(PDO::FETCH_COLUMN);
    }
    if ($LW == 0) {
        $LW_r = "NAN";
    } else {
        $LW_r = round($LW_s / $LW * 100, 1);
    }
    if ($RW == 0) {
        $RW_r = "NAN";
    } else {
        $RW_r = round($RW_s / $RW * 100, 1);
    }
    if ($PV == 0) {
        $PV_r = "NAN";
    } else {
        $PV_r = round($PV_s / $PV * 100, 1);
    }
    if ($L6 == 0) {
        $L6_r = "NAN";
    } else {
        $L6_r = round($L6_s / $L6 * 100, 1);
    }
    if ($C6 == 0) {
        $C6_r = "NAN";
    } else {
        $C6_r = round($C6_s / $C6 * 100, 1);
    }
    if ($R6 == 0) {
        $R6_r = "NAN";
    } else {
        $R6_r = round($R6_s / $R6 * 100, 1);
    }
    if ($L9 == 0) {
        $L9_r = "NAN";
    } else {
        $L9_r = round($L9_s / $L9 * 100, 1);
    }
    if ($C9 == 0) {
        $C9_r = "NAN";
    } else {
        $C9_r = round($C9_s / $C9 * 100, 1);
    }
    if ($R9 == 0) {
        $R9_r = "NAN";
    } else {
        $R9_r = round($R9_s / $R9 * 100, 1);
    }
    if ($seven == 0) {
        $seven_r = "NAN";
    } else {
        $seven_r = round($seven_s / $seven * 100, 1);
    }

    $array = [
        "LW" => $LW,
        "LW_s" => $LW_s,
        "RW" => $RW,
        "RW_s" => $RW_s,
        "PV" => $PV,
        "PV_s" => $PV_s,
        "L6" => $L6,
        "L6_s" => $L6_s,
        "C6" => $C6,
        "C6_s" => $C6_s,
        "R6" => $R6,
        "R6_s" => $R6_s,
        "L9" => $L9,
        "L9_s" => $L9_s,
        "C9" => $C9,
        "C9_s" => $C9_s,
        "R9" => $R9,
        "R9_s" => $R9_s,
        "seven" => $seven,
        "seven_s" => $seven_s,
        "LW_r" => $LW_r,
        "RW_r" => $RW_r,
        "PV_r" => $PV_r,
        "L6_r" => $L6_r,
        "C6_r" => $C6_r,
        "R6_r" => $R6_r,
        "L9_r" => $L9_r,
        "C9_r" => $C9_r,
        "R9_r" => $R9_r,
        "seven_r" => $seven_r
    ];

    $dbh = null;

    return $array;
}

function get_goal_pos($team_id, $swift_flag)
{
    $dbh = connectDB();
    if ($swift_flag == 1) {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1';
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2';
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3';
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4';
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=5';
        $sth = $dbh->query($sql);
        $T = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=6';
        $sth = $dbh->query($sql);
        $L = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=7';
        $sth = $dbh->query($sql);
        $R = $sth->fetch(PDO::FETCH_COLUMN);
    } else if ($swift_flag == 2) {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `swift_attack`=0';
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `swift_attack`=0';
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `swift_attack`=0';
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `swift_attack`=0';
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=5 AND `swift_attack`=0';
        $sth = $dbh->query($sql);
        $T = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=6 AND `swift_attack`=0';
        $sth = $dbh->query($sql);
        $L = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=7 AND `swift_attack`=0';
        $sth = $dbh->query($sql);
        $R = $sth->fetch(PDO::FETCH_COLUMN);
    } else {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `swift_attack`=1';
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `swift_attack`=1';
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `swift_attack`=1';
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `swift_attack`=1';
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=5 AND `swift_attack`=1';
        $sth = $dbh->query($sql);
        $T = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=6 AND `swift_attack`=1';
        $sth = $dbh->query($sql);
        $L = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=7 AND `swift_attack`=1';
        $sth = $dbh->query($sql);
        $R = $sth->fetch(PDO::FETCH_COLUMN);
    }

    $goal_cnt = [
        "TL" => $TL,
        "TR" => $TR,
        "BL" => $BL,
        "BR" => $BR,
        "T" => $T,
        "L" => $L,
        "R" => $R,
    ];

    $dbh = null;

    return $goal_cnt;
}

function get_goal_pos_first($team_id, $swift_flag, $latter_time)
{
    $dbh = connectDB();
    if ($swift_flag == 1) {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=5 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $T = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=6 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $L = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=7 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $R = $sth->fetch(PDO::FETCH_COLUMN);
    } else if ($swift_flag == 2) {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=5 AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $T = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=6 AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $L = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=7 AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $R = $sth->fetch(PDO::FETCH_COLUMN);
    } else {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=5 AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $T = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=6 AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $L = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=7 AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $R = $sth->fetch(PDO::FETCH_COLUMN);
    }

    $goal_cnt = [
        "TL" => $TL,
        "TR" => $TR,
        "BL" => $BL,
        "BR" => $BR,
        "T" => $T,
        "L" => $L,
        "R" => $R,
    ];

    $dbh = null;

    return $goal_cnt;
}

function get_goal_pos_latter($team_id, $swift_flag, $latter_time)
{
    $dbh = connectDB();
    if ($swift_flag == 1) {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=5 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $T = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=6 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $L = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=7 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $R = $sth->fetch(PDO::FETCH_COLUMN);
    } else if ($swift_flag == 2) {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=5 AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $T = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=6 AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $L = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=7 AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $R = $sth->fetch(PDO::FETCH_COLUMN);
    } else {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=5 AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $T = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=6 AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $L = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=7 AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $R = $sth->fetch(PDO::FETCH_COLUMN);
    }

    $goal_cnt = [
        "TL" => $TL,
        "TR" => $TR,
        "BL" => $BL,
        "BR" => $BR,
        "T" => $T,
        "L" => $L,
        "R" => $R,
    ];

    $dbh = null;

    return $goal_cnt;
}

function get_goal_pos_s($team_id, $swift_flag)
{
    $dbh = connectDB();
    if ($swift_flag == 1) {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `pointed_flag`=1';
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `pointed_flag`=1';
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `pointed_flag`=1';
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `pointed_flag`=1';
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
    } else if ($swift_flag == 2) {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `pointed_flag`=1 AND `swift_attack`=0';
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `pointed_flag`=1 AND `swift_attack`=0';
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `pointed_flag`=1 AND `swift_attack`=0';
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `pointed_flag`=1 AND `swift_attack`=0';
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
    } else {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `pointed_flag`=1 AND `swift_attack`=1';
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `pointed_flag`=1 AND `swift_attack`=1';
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `pointed_flag`=1 AND `swift_attack`=1';
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `pointed_flag`=1 AND `swift_attack`=1';
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
    }

    $goal_cnt_s = [
        "TL" => $TL,
        "TR" => $TR,
        "BL" => $BL,
        "BR" => $BR,
    ];

    $dbh = null;

    return $goal_cnt_s;
}

function get_goal_pos_s_first($team_id, $swift_flag, $latter_time)
{
    $dbh = connectDB();
    if ($swift_flag == 1) {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `pointed_flag`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `pointed_flag`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `pointed_flag`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `pointed_flag`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
    } else if ($swift_flag == 2) {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `pointed_flag`=1 AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `pointed_flag`=1 AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `pointed_flag`=1 AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `pointed_flag`=1 AND `swift_attack`=0 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
    } else {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `pointed_flag`=1 AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `pointed_flag`=1 AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `pointed_flag`=1 AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `pointed_flag`=1 AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
    }

    $goal_cnt_s = [
        "TL" => $TL,
        "TR" => $TR,
        "BL" => $BL,
        "BR" => $BR,
    ];

    $dbh = null;

    return $goal_cnt_s;
}

function get_goal_pos_s_latter($team_id, $swift_flag, $latter_time)
{
    $dbh = connectDB();
    if ($swift_flag == 1) {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `pointed_flag`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `pointed_flag`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `pointed_flag`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `pointed_flag`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
    } else if ($swift_flag == 2) {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `pointed_flag`=1 AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `pointed_flag`=1 AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `pointed_flag`=1 AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `pointed_flag`=1 AND `swift_attack`=0 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
    } else {
        $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=1 AND `pointed_flag`=1 AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $TL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=2 AND `pointed_flag`=1 AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $TR = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=3 AND `pointed_flag`=1 AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $BL = $sth->fetch(PDO::FETCH_COLUMN);
        $sql = 'SELECT count(*)
              FROM `shoot_tb`
              WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position`=4 AND `pointed_flag`=1 AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
        $sth = $dbh->query($sql);
        $BR = $sth->fetch(PDO::FETCH_COLUMN);
    }

    $goal_cnt_s = [
        "TL" => $TL,
        "TR" => $TR,
        "BL" => $BL,
        "BR" => $BR,
    ];

    $dbh = null;

    return $goal_cnt_s;
}

function get_table($swift_flag, $first_time, $latter_time, $team_id1, $team_id2, $team_name1, $team_name2)
{
    $shoot_table = [];
    $id_list = [];
    $time_list = [];
    $team_name_list = [];
    $goal_judge_list = [];
    $player_num = [];
    $video_time_list = [];
    $team_name = "";
    //DBへの接続
    $dbh = connectDB();
    if ($swift_flag == 1) {
        $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `7m_shoot`=0 ORDER BY `video_time`';
    } else if ($swift_flag == 2) {
        $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `7m_shoot`=0 AND `swift_attack`=0 ORDER BY `video_time`';
    } else {
        $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `7m_shoot`=0 AND `swift_attack`=1 ORDER BY `video_time`';
    }
    $sth = $dbh->query($sql);
    while ($row = $sth->fetch()) {
        $team_id = $row['shoot_team_id'];
        if ($team_id == $team_id1) {
            $team_name = $team_name1;
        } else if ($team_id == $team_id2) {
            $team_name = $team_name2;
        }
        $seconds = $row['video_time'];
        if ($seconds < $latter_time) {
            $time = s2h($seconds - $first_time);
        } else {
            $time = s2h($seconds - $latter_time);
        }
        $pointed_flag = $row['pointed_flag'];
        $goal_judge = '○';
        if ($pointed_flag == 0) {
            $goal_judge = '×';
        }
        array_push($id_list, $row['id']);
        array_push($time_list, $time);
        array_push($goal_judge_list, $goal_judge);
        array_push($team_name_list, $team_name);
        array_push($player_num, $row['tag']);
        array_push($video_time_list, $seconds);
    }
    $shoot_table = [
        "id" => $id_list,
        "time" => $time_list,
        "goal_judge" => $goal_judge_list,
        "team_name" => $team_name_list,
        "player_num" => $player_num,
        "video_time" => $video_time_list,
    ];

    $dbh = null;

    return $shoot_table;
}

function get_table_first($swift_flag, $first_time, $latter_time, $team_id1, $team_id2, $team_name1, $team_name2)
{
    $shoot_table = [];
    $id_list = [];
    $time_list = [];
    $team_name_list = [];
    $goal_judge_list = [];
    $player_num = [];
    $video_time_list = [];
    $team_name = "";
    //DBへの接続
    $dbh = connectDB();
    if ($swift_flag == 1) {
        $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `7m_shoot`=0 AND `video_time` < ' . $latter_time . ' ORDER BY `video_time`';
    } else if ($swift_flag == 2) {
        $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `7m_shoot`=0 AND `swift_attack`=0 AND `video_time` < ' . $latter_time . ' ORDER BY `video_time`';
    } else {
        $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `7m_shoot`=0 AND `swift_attack`=1 AND `video_time` < ' . $latter_time . ' ORDER BY `video_time`';
    }
    $sth = $dbh->query($sql);
    while ($row = $sth->fetch()) {
        $team_id = $row['shoot_team_id'];
        if ($team_id == $team_id1) {
            $team_name = $team_name1;
        } else if ($team_id == $team_id2) {
            $team_name = $team_name2;
        }
        $seconds = $row['video_time'];
        if ($seconds < $latter_time) {
            $time = s2h($seconds - $first_time);
        } else {
            $time = s2h($seconds - $latter_time);
        }
        $pointed_flag = $row['pointed_flag'];
        $goal_judge = '○';
        if ($pointed_flag == 0) {
            $goal_judge = '×';
        }
        array_push($id_list, $row['id']);
        array_push($time_list, $time);
        array_push($goal_judge_list, $goal_judge);
        array_push($team_name_list, $team_name);
        array_push($player_num, $row['tag']);
        array_push($video_time_list, $seconds);
    }
    $shoot_table = [
        "id" => $id_list,
        "time" => $time_list,
        "goal_judge" => $goal_judge_list,
        "team_name" => $team_name_list,
        "player_num" => $player_num,
        "video_time" => $video_time_list,
    ];

    return $shoot_table;
}

function get_table_latter($swift_flag, $first_time, $latter_time, $team_id1, $team_id2, $team_name1, $team_name2)
{
    $shoot_table = [];
    $id_list = [];
    $time_list = [];
    $team_name_list = [];
    $goal_judge_list = [];
    $player_num = [];
    $video_time_list = [];
    $team_name = "";
    //DBへの接続
    $dbh = connectDB();
    if ($swift_flag == 1) {
        $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `7m_shoot`=0 AND `video_time` >= ' . $latter_time . ' ORDER BY `video_time`';
    } else if ($swift_flag == 2) {
        $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `7m_shoot`=0 AND `swift_attack`=0 AND `video_time` >= ' . $latter_time . ' ORDER BY `video_time`';
    } else {
        $sql = 'SELECT * FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `7m_shoot`=0 AND `swift_attack`=1 AND `video_time` >= ' . $latter_time . ' ORDER BY `video_time`';
    }
    $sth = $dbh->query($sql);
    while ($row = $sth->fetch()) {
        $team_id = $row['shoot_team_id'];
        if ($team_id == $team_id1) {
            $team_name = $team_name1;
        } else if ($team_id == $team_id2) {
            $team_name = $team_name2;
        }
        $seconds = $row['video_time'];
        if ($seconds < $latter_time) {
            $time = s2h($seconds - $first_time);
        } else {
            $time = s2h($seconds - $latter_time);
        }
        $pointed_flag = $row['pointed_flag'];
        $goal_judge = '○';
        if ($pointed_flag == 0) {
            $goal_judge = '×';
        }
        array_push($id_list, $row['id']);
        array_push($time_list, $time);
        array_push($goal_judge_list, $goal_judge);
        array_push($team_name_list, $team_name);
        array_push($player_num, $row['tag']);
        array_push($video_time_list, $seconds);
    }
    $shoot_table = [
        "id" => $id_list,
        "time" => $time_list,
        "goal_judge" => $goal_judge_list,
        "team_name" => $team_name_list,
        "player_num" => $player_num,
        "video_time" => $video_time_list,
    ];

    $dbh = null;

    return $shoot_table;
}

function get_All($team_id, $swift_flag)
{
    $dbh = connectDB();

    $result = [];

    if ($swift_flag == 1) {
        $sql = 'SELECT `id`, `position_xy`, `pointed_flag`, `video_time`, `empty_shoot`, `shooter_kind`, `goal_position`
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 ORDER BY `video_time`';
    } else if ($swift_flag == 2) {
        $sql = 'SELECT `id`, `position_xy`, `pointed_flag`, `video_time`, `empty_shoot`, `shooter_kind`, `goal_position`
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `swift_attack`=0 ORDER BY `video_time`';
    } else {
        $sql = 'SELECT `id`, `position_xy`, `pointed_flag`, `video_time`, `empty_shoot`, `shooter_kind`, `goal_position`
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `swift_attack`=1 ORDER BY `video_time`';
    }

    $sth = $dbh->query($sql);  //SQLの実行
    //データの取得
    $json1 = $sth->fetchAll(PDO::FETCH_ASSOC);

    $parse = array();

    $id = array();
    $xy_explode = array();
    $pointed_flag = array();
    $empty_shoot = array();
    $shooter_kind = array();
    $video_time = array();
    $goal_position = array();

    for ($i = 0; $i < count($json1); $i++) {
        // echo $json1[$i]['position_xy'];
        //括弧を削除
        $json2 = str_replace('[', '', $json1[$i]['position_xy']);
        $json3 = str_replace(']', '', $json2);

        // 文字列をカンマで分割
        $id[$i] = $json1[$i]['id'];
        $xy_explode[$i] = explode(",", $json3);
        $pointed_flag[$i] = $json1[$i]['pointed_flag'];
        $goal_position[$i] = $json1[$i]['goal_position'];
        $video_time[$i] = $json1[$i]['video_time'];
        $empty_shoot[$i] = $json1[$i]['empty_shoot'];
        $shooter_kind[$i] = $json1[$i]['shooter_kind'];
    }

    $parse = [
        "id" => $id,
        "position_xy" => $xy_explode,
        "pointed_flag" => $pointed_flag,
        "goal_position" => $goal_position,
        "video_time" => $video_time,
        "empty_shoot" => $empty_shoot,
        "shooter_kind" => $shooter_kind
    ];

    $position = array();

    $position = get_position($team_id, $swift_flag);

    $goal_pos = get_goal_pos($team_id, $swift_flag);

    $goal_pos_s = get_goal_pos_s($team_id, $swift_flag);

    $result = [
        "parse" => $parse,
        "position" => $position,
        "goal_pos" => $goal_pos,
        "goal_pos_s" => $goal_pos_s,
    ];

    $dbh = null;

    return $result;
}

function get_First($team_id, $swift_flag, $latter_time)
{
    $dbh = connectDB();

    $result = array();

    if ($swift_flag == 1) {
        $sql = 'SELECT `id`, `position_xy`, `pointed_flag`, `video_time`, `empty_shoot`, `shooter_kind`, `goal_position`
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `video_time` < ' . $latter_time . ' ORDER BY `video_time`';
    } else if ($swift_flag == 2) {
        $sql = 'SELECT `id`, `position_xy`, `pointed_flag`, `video_time`, `empty_shoot`, `shooter_kind`, `goal_position`
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `swift_attack`=0 AND `video_time` < ' . $latter_time . ' ORDER BY `video_time`';
    } else {
        $sql = 'SELECT `id`, `position_xy`, `pointed_flag`, `video_time`, `empty_shoot`, `shooter_kind`, `goal_position`
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `swift_attack`=1 AND `video_time` < ' . $latter_time . ' ORDER BY `video_time`';
    }

    $sth = $dbh->query($sql);  //SQLの実行
    //データの取得
    $json1 = $sth->fetchAll(PDO::FETCH_ASSOC);

    $parse = array();

    $id = array();
    $xy_explode = array();
    $pointed_flag = array();
    $empty_shoot = array();
    $shooter_kind = array();
    $video_time = array();
    $goal_position = array();

    for ($i = 0; $i < count($json1); $i++) {
        // echo $json1[$i]['position_xy'];
        //括弧を削除
        $json2 = str_replace('[', '', $json1[$i]['position_xy']);
        $json3 = str_replace(']', '', $json2);

        // 文字列をカンマで分割
        $id[$i] = $json1[$i]['id'];
        $xy_explode[$i] = explode(",", $json3);
        $pointed_flag[$i] = $json1[$i]['pointed_flag'];
        $goal_position[$i] = $json1[$i]['goal_position'];
        $video_time[$i] = $json1[$i]['video_time'];
        $empty_shoot[$i] = $json1[$i]['empty_shoot'];
        $shooter_kind[$i] = $json1[$i]['shooter_kind'];
    }

    $parse = [
        "id" => $id,
        "position_xy" => $xy_explode,
        "pointed_flag" => $pointed_flag,
        "goal_position" => $goal_position,
        "video_time" => $video_time,
        "empty_shoot" => $empty_shoot,
        "shooter_kind" => $shooter_kind
    ];

    $position = array();

    $position = get_position_first($team_id, $swift_flag, $latter_time);

    $goal_pos = get_goal_pos_first($team_id, $swift_flag, $latter_time);

    $goal_pos_s = get_goal_pos_s_first($team_id, $swift_flag, $latter_time);

    $result = [
        "parse" => $parse,
        "position" => $position,
        "goal_pos" => $goal_pos,
        "goal_pos_s" => $goal_pos_s,
    ];

    $dbh = null;

    return $result;
}

function get_Latter($team_id, $swift_flag, $latter_time)
{
    $dbh = connectDB();

    $result = array();

    if ($swift_flag == 1) {
        $sql = 'SELECT `id`, `position_xy`, `pointed_flag`, `video_time`, `empty_shoot`, `shooter_kind`, `goal_position`
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `video_time` >= ' . $latter_time . ' ORDER BY `video_time`';
    } else if ($swift_flag == 2) {
        $sql = 'SELECT `id`, `position_xy`, `pointed_flag`, `video_time`, `empty_shoot`, `shooter_kind`, `goal_position`
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `swift_attack`=0 AND `video_time` >= ' . $latter_time . ' ORDER BY `video_time`';
    } else {
        $sql = 'SELECT `id`, `position_xy`, `pointed_flag`, `video_time`, `empty_shoot`, `shooter_kind`, `goal_position`
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `swift_attack`=1 AND `video_time` >= ' . $latter_time . ' ORDER BY `video_time`';
    }

    $sth = $dbh->query($sql);  //SQLの実行
    //データの取得
    $json1 = $sth->fetchAll(PDO::FETCH_ASSOC);

    $parse = array();

    $id = array();
    $xy_explode = array();
    $pointed_flag = array();
    $empty_shoot = array();
    $shooter_kind = array();
    $video_time = array();
    $goal_position = array();

    for ($i = 0; $i < count($json1); $i++) {
        // echo $json1[$i]['position_xy'];
        //括弧を削除
        $json2 = str_replace('[', '', $json1[$i]['position_xy']);
        $json3 = str_replace(']', '', $json2);

        // 文字列をカンマで分割
        $id[$i] = $json1[$i]['id'];
        $xy_explode[$i] = explode(",", $json3);
        $pointed_flag[$i] = $json1[$i]['pointed_flag'];
        $goal_position[$i] = $json1[$i]['goal_position'];
        $video_time[$i] = $json1[$i]['video_time'];
        $empty_shoot[$i] = $json1[$i]['empty_shoot'];
        $shooter_kind[$i] = $json1[$i]['shooter_kind'];
    }

    $parse = [
        "id" => $id,
        "position_xy" => $xy_explode,
        "pointed_flag" => $pointed_flag,
        "goal_position" => $goal_position,
        "video_time" => $video_time,
        "empty_shoot" => $empty_shoot,
        "shooter_kind" => $shooter_kind
    ];

    $position = array();

    $position = get_position_latter($team_id, $swift_flag, $latter_time);

    $goal_pos = get_goal_pos_latter($team_id, $swift_flag, $latter_time);

    $goal_pos_s = get_goal_pos_s_latter($team_id, $swift_flag, $latter_time);

    $result = [
        "parse" => $parse,
        "position" => $position,
        "goal_pos" => $goal_pos,
        "goal_pos_s" => $goal_pos_s,
    ];

    $dbh = null;

    return $result;
}

function get_save_count_first($team_id, $latter_time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `GK_block`=1 AND `video_time` < ' . $latter_time;
    $sth = $dbh->query($sql);
    $save_count_first = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $save_count_first;
}

function get_save_count_latter($team_id, $latter_time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `GK_block`=1 AND `video_time` >= ' . $latter_time;
    $sth = $dbh->query($sql);
    $save_count_latter = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $save_count_latter;
}

function get_swift_count($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `swift_attack`=1';
    $sth = $dbh->query($sql);
    $swift_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $swift_count;
}

function get_swift_count_first($team_id, $latter_time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `swift_attack`=1 AND `video_time` < ' . $latter_time;
    $sth = $dbh->query($sql);
    $swift_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $swift_count;
}

function get_swift_count_latter($team_id, $latter_time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `swift_attack`=1 AND `video_time` >= ' . $latter_time;
    $sth = $dbh->query($sql);
    $swift_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $swift_count;
}

function get_swift_count_s($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `swift_attack`=1 AND `pointed_flag`=1';
    $sth = $dbh->query($sql);
    $swift_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $swift_count;
}

function get_swift_count_s_first($team_id, $latter_time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `swift_attack`=1 AND `pointed_flag`=1 AND `video_time` < ' . $latter_time;
    $sth = $dbh->query($sql);
    $swift_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $swift_count;
}

function get_swift_count_s_latter($team_id, $latter_time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `swift_attack`=1 AND `pointed_flag`=1 AND `video_time` >= ' . $latter_time;
    $sth = $dbh->query($sql);
    $swift_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $swift_count;
}

function get_shoot_count_first($team_id, $latter_time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `video_time` < ' . $latter_time;
    $sth = $dbh->query($sql);
    $shoot_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $shoot_count;
}

function get_shoot_count_latter($team_id, $latter_time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `video_time` >= ' . $latter_time;
    $sth = $dbh->query($sql);
    $shoot_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $shoot_count;
}

function get_shoot_count_s_first($team_id, $latter_time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `pointed_flag`=1 AND `video_time` < ' . $latter_time;
    $sth = $dbh->query($sql);
    $shoot_count_s = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $shoot_count_s;
}

function get_shoot_count_s_latter($team_id, $latter_time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=0 AND `pointed_flag`=1 AND `video_time` >= ' . $latter_time;
    $sth = $dbh->query($sql);
    $shoot_count_s = $sth->fetch(PDO::FETCH_COLUMN);
    return $shoot_count_s;
}

function get_block_count_first($team_id, $latter_time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `DF_block`=1 AND `video_time` < ' . $latter_time;
    $sth = $dbh->query($sql);
    $block_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $block_count;
}

function get_block_count_latter($team_id, $latter_time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `DF_block`=1 AND `video_time` >= ' . $latter_time;
    $sth = $dbh->query($sql);
    $block_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $block_count;
}

function get_7m_shoot_count_first($team_id, $latter_time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=1 AND `video_time` < ' . $latter_time;
    $sth = $dbh->query($sql);
    $shoot_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $shoot_count;
}

function get_7m_shoot_count_latter($team_id, $latter_time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=1 AND `video_time` >= ' . $latter_time;
    $sth = $dbh->query($sql);
    $shoot_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $shoot_count;
}

function get_7m_shoot_count_s_first($team_id, $latter_time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=1 AND `pointed_flag`=1 AND `video_time` < ' . $latter_time;
    $sth = $dbh->query($sql);
    $shoot_count_s = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $shoot_count_s;
}

function get_7m_shoot_count_s_latter($team_id, $latter_time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=1 AND `pointed_flag`=1 AND `video_time` >= ' . $latter_time;
    $sth = $dbh->query($sql);
    $shoot_count_s = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $shoot_count_s;
}

function get_in_goal_count_first($team_id, $latter_time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position` NOT IN (5, 6, 7) AND `7m_shoot`=0 AND `video_time` < ' . $latter_time;
    $sth = $dbh->query($sql);
    $in_goal_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $in_goal_count;
}

function get_in_goal_count_latter($team_id, $latter_time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position` NOT IN (5, 6, 7) AND `7m_shoot`=0 AND `video_time` >= ' . $latter_time;
    $sth = $dbh->query($sql);
    $in_goal_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $in_goal_count;
}

function get_out_goal_count_first($team_id, $latter_time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position` NOT IN (1, 2, 3, 4) AND `7m_shoot`=0 AND `video_time` < ' . $latter_time;
    $sth = $dbh->query($sql);
    $out_goal_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $out_goal_count;
}

function get_out_goal_count_latter($team_id, $latter_time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `goal_position` NOT IN (1, 2, 3, 4) AND `7m_shoot`=0 AND `video_time` >= ' . $latter_time;
    $sth = $dbh->query($sql);
    $out_goal_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $out_goal_count;
}

function division_check($success, $total)
{
    if ($total != 0) {
        $result = floor(($success / $total) * 1000) / 10;
        return $result;
    } else {
        return 0;
    }
}

function get_ShootData($kind, $team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind`=' . $kind;
    $sth = $dbh->query($sql);
    $position = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $position;
}

function get_ShootData_s($kind, $team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind`=' . $kind . ' AND `pointed_flag`=1';
    $sth = $dbh->query($sql);
    $position = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $position;
}

function get_side($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind` IN (1, 3)';
    $sth = $dbh->query($sql);
    $side_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $side_count;
}

function get_side_first($team_id, $time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind` IN (1, 3) AND `video_time` < ' . $time;
    $sth = $dbh->query($sql);
    $side_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $side_count;
}

function get_side_latter($team_id, $time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind` IN (1, 3) AND `video_time` >= ' . $time;
    $sth = $dbh->query($sql);
    $side_count = $sth->fetch(PDO::FETCH_COLUMN);

    return $side_count;
}

function get_side_s($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind` IN (1, 3) AND `pointed_flag`=1';
    $sth = $dbh->query($sql);
    $side_count_s = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $side_count_s;
}

function get_side_first_s($team_id, $time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind` IN (1, 3) AND `pointed_flag`=1 AND `video_time` < ' . $time;
    $sth = $dbh->query($sql);
    $side_count_s = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $side_count_s;
}

function get_side_latter_s($team_id, $time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind` IN (1, 3) AND `pointed_flag`=1 AND `video_time` >= ' . $time;
    $sth = $dbh->query($sql);
    $side_count_s = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $side_count_s;
}

function get_long($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind` IN (7, 8, 9)';
    $sth = $dbh->query($sql);
    $long_count_s = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $long_count_s;
}

function get_long_first($team_id, $time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind` IN (7, 8, 9) AND `video_time` < ' . $time;
    $sth = $dbh->query($sql);
    $long_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $long_count;
}

function get_long_latter($team_id, $time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind` IN (7, 8, 9) AND `video_time` >= ' . $time;
    $sth = $dbh->query($sql);
    $long_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $long_count;
}

function get_long_s($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind` IN (7, 8, 9) AND `pointed_flag`=1';
    $sth = $dbh->query($sql);
    $long_count_s = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $long_count_s;
}

function get_long_first_s($team_id, $time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind` IN (7, 8, 9) AND `pointed_flag`=1 AND `video_time` < ' . $time;
    $sth = $dbh->query($sql);
    $long_count_s = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $long_count_s;
}

function get_long_latter_s($team_id, $time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*)
            FROM `shoot_tb`
            WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind` IN (7, 8, 9) AND `pointed_flag`=1 AND `video_time` >= ' . $time;
    $sth = $dbh->query($sql);
    $long_count_s = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $long_count_s;
}

function get_all_shoot_count($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `shoot_team_id`=' . $team_id;
    $sth = $dbh->query($sql);
    $shoot_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $shoot_count;
}

function get_all_shoot_count_s($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `shoot_team_id`=' . $team_id . ' AND `pointed_flag`=1';
    $sth = $dbh->query($sql);
    $shoot_count_s = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $shoot_count_s;
}

function get_all_block_count($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `DF_block`=1 AND `shoot_team_id`=' . $team_id;
    $sth = $dbh->query($sql);
    $block_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $block_count;
}

function get_all_seven_count($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=1';
    $sth = $dbh->query($sql);
    $seven_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $seven_count;
}

function get_all_seven_count_s($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `shoot_team_id`=' . $team_id . ' AND `7m_shoot`=1 AND `pointed_flag`=1';
    $sth = $dbh->query($sql);
    $seven_count_s = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $seven_count_s;
}

function get_all_in_goal_count($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `shoot_team_id`=' . $team_id . ' AND `goal_position` NOT IN (5, 6, 7)';
    $sth = $dbh->query($sql);
    $in_goal_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $in_goal_count;
}

function get_all_out_goal_count($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `shoot_team_id`=' . $team_id . ' AND `goal_position` IN (5, 6, 7)';
    $sth = $dbh->query($sql);
    $out_goal_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $out_goal_count;
}

function get_all_swift_count($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `shoot_team_id`=' . $team_id . ' AND `swift_attack`=1';
    $sth = $dbh->query($sql);
    $swift_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $swift_count;
}

function get_all_swift_count_s($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `shoot_team_id`=' . $team_id . ' AND `swift_attack`=1 AND `pointed_flag`=1';
    $sth = $dbh->query($sql);
    $swift_count_s = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $swift_count_s;
}

function get_all_side($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `shoot_team_id`=' . $team_id . ' AND `shooter_kind` IN (1, 3)';
    $sth = $dbh->query($sql);
    $side_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $side_count;
}

function get_all_side_s($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `shoot_team_id`=' . $team_id . ' AND `shooter_kind` IN (1, 3) AND `pointed_flag`=1';
    $sth = $dbh->query($sql);
    $side_count_s = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $side_count_s;
}

function get_all_long($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `shoot_team_id`=' . $team_id . ' AND `shooter_kind` IN (7, 8, 9)';
    $sth = $dbh->query($sql);
    $long_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $long_count;
}

function get_all_long_s($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `shoot_team_id`=' . $team_id . ' AND `shooter_kind` IN (7, 8, 9) AND `pointed_flag`=1';
    $sth = $dbh->query($sql);
    $long_count_s = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $long_count_s;
}

function get_tikou($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `swift_attack`=0 AND `7m_shoot`=0';
    $sth = $dbh->query($sql);
    $tikou_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $tikou_count;
}

function get_all_tikou($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `shoot_team_id`=' . $team_id . ' AND `swift_attack`=0 AND `7m_shoot`=0';
    $sth = $dbh->query($sql);
    $tikou_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $tikou_count;
}

function get_tikou_s($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `swift_attack`=0 AND `7m_shoot`=0 AND `pointed_flag`=1';
    $sth = $dbh->query($sql);
    $tikou_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $tikou_count;
}

function get_all_tikou_s($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `shoot_team_id`=' . $team_id . ' AND `swift_attack`=0 AND `7m_shoot`=0 AND `pointed_flag`=1';
    $sth = $dbh->query($sql);
    $tikou_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $tikou_count;
}

function get_tikou_first($team_id, $time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `swift_attack`=0 AND `7m_shoot`=0 AND `video_time`<' . $time;
    $sth = $dbh->query($sql);
    $tikou_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $tikou_count;
}

function get_tikou_first_s($team_id, $time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `swift_attack`=0 AND `7m_shoot`=0 AND `pointed_flag`=1 AND `video_time`<' . $time;
    $sth = $dbh->query($sql);
    $tikou_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $tikou_count;
}

function get_tikou_latter($team_id, $time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `swift_attack`=0 AND `7m_shoot`=0 AND `video_time`>=' . $time;
    $sth = $dbh->query($sql);
    $tikou_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $tikou_count;
}

function get_tikou_latter_s($team_id, $time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `swift_attack`=0 AND `7m_shoot`=0 AND `pointed_flag`=1 AND `video_time`>=' . $time;
    $sth = $dbh->query($sql);
    $tikou_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $tikou_count;
}

function get_middle($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind` IN (4, 5, 6)';
    $sth = $dbh->query($sql);
    $middle_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $middle_count;
}

function get_middle_s($team_id)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind` IN (4, 5, 6) AND `pointed_flag`=1';
    $sth = $dbh->query($sql);
    $middle_count_s = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $middle_count_s;
}

function get_middle_first($team_id, $time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind` IN (4, 5, 6) AND `video_time`<' . $time;
    $sth = $dbh->query($sql);
    $middle_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $middle_count;
}

function get_middle_first_s($team_id, $time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind` IN (4, 5, 6) AND `pointed_flag`=1 AND `video_time`<' . $time;
    $sth = $dbh->query($sql);
    $middle_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $middle_count;
}

function get_middle_latter($team_id, $time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind` IN (4, 5, 6) AND `video_time`>=' . $time;
    $sth = $dbh->query($sql);
    $middle_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $middle_count;
}

function get_middle_latter_s($team_id, $time)
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `shoot_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `shoot_team_id`=' . $team_id . ' AND `shooter_kind` IN (4, 5, 6) AND `pointed_flag`=1 AND `video_time`>=' . $time;
    $sth = $dbh->query($sql);
    $middle_count = $sth->fetch(PDO::FETCH_COLUMN);

    $dbh = null;

    return $middle_count;
}

function check_extension()
{
    //DBへの接続
    $dbh = connectDB();
    $sql = 'SELECT count(*) FROM `video_time_tb` WHERE `game_id`=' . $_SESSION['game_id'] . ' AND `time_kind`=2';
    $sth = $dbh->query($sql);
    $count = $sth->fetch(PDO::FETCH_COLUMN);
    if ($count == 0) {
        $extension_flag = 0;
    } else {
        $extension_flag = 1;
    }

    $dbh = null;

    return $extension_flag;
}
