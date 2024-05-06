<?php
//接続用関数の呼び出し
require_once(__DIR__ . '/functions.php');
createTable(); //テーブルの生成

//テーブルの生成
function createTable()
{
    //DBへの接続
    $dbh = connectDB();
    //データベースの接続確認
    if (!$dbh) {  //接続できていない場合
        echo 'DBに接続できていません．';
        return;
    }

    //テーブルが存在するかを確認するSQL文
    $sql = "show tables";
    $sth = $dbh->query($sql); //SQLの実行
    $result = $sth->fetchAll(PDO::FETCH_ASSOC);
    if (0 < count($result)) {
        //データベース構築済み
        return;
    }
    //---------------------------------------------------------------------------
    //シュートテーブル
    $sql = "CREATE TABLE IF NOT EXISTS `shoot_tb` ( `id` INT NOT NULL AUTO_INCREMENT, `game_id` INT COMMENT '試合ID', `position_xy` TEXT COMMENT 'JSON形式の座標群', `pointed_flag` tinyint(1) DEFAULT NULL COMMENT 'シュート成功失敗',`shoot_team_id` INT DEFAULT NULL COMMENT 'シュートしたチームのID', `rebound` tinyint(1) DEFAULT NULL COMMENT 'リバウンドシュート', `swift_attack` tinyint(1) DEFAULT NULL COMMENT '速攻', `own_goal` tinyint(1) DEFAULT NULL COMMENT 'オウンゴール', `GK_block` tinyint(1) DEFAULT NULL COMMENT 'GKによるブロック',`DF_block` tinyint(1) DEFAULT NULL COMMENT 'DFによるブロック', `7m_shoot` tinyint(1) DEFAULT NULL COMMENT '7mスロー', `shooter_kind` INT COMMENT 'シューターポジション(どのポジションの人がシュートしたか)', `goal_position` INT COMMENT 'ゴール位置', `tag` TEXT COMMENT '選手の背番号', `memo` TEXT COMMENT 'メモ', `video_time` FLOAT COMMENT '映像の経過時間', PRIMARY KEY (`id`)) ENGINE = InnoDB";
    $dbh->exec($sql); //SQLの実行

    //シュート時刻計測用テーブル
    $sql = "CREATE TABLE IF NOT EXISTS `shoot_time_tb` ( `id` INT NOT NULL AUTO_INCREMENT, `game_id` INT COMMENT '試合ID', `pointed_flag` tinyint(1) DEFAULT NULL COMMENT 'シュート成否', `shoot_team_id` INT DEFAULT NULL COMMENT 'シュートしたチームのID', `number` INT DEFAULT NULL COMMENT '選手の背番号', `tag` TEXT DEFAULT NULL COMMENT 'タグ', `catch_time` FLOAT DEFAULT NULL COMMENT 'キャッチした時刻', `release_time` FLOAT DEFAULT NULL COMMENT 'リリースした時刻', `goal_time` FLOAT DEFAULT NULL COMMENT 'ゴールに到達した時刻', `video_time` INT DEFAULT NULL COMMENT 'キャッチした映像時刻(整数)', PRIMARY KEY (id)) ENGINE = InnoDB";
    // SQLを実行
    $dbh->exec($sql);

    //ゴール位置の種類
    $sql = "CREATE TABLE IF NOT EXISTS `goal_position_kind_tb` ( `id` INT NOT NULL AUTO_INCREMENT, `goal_position_kind` VARCHAR(100) COMMENT 'ゴール位置の種類', `explanation` VARCHAR(1000) COMMENT 'シュートの種類の説明', PRIMARY KEY (`id`)) ENGINE = InnoDB";
    $dbh->exec($sql); //SQLの実行
    //ゴール位置の種類の事前登録
    $sql = "INSERT INTO `goal_position_kind_tb` (`id`, `goal_position_kind`, `explanation`) VALUES (NULL, 'TL', '左上')";
    $dbh->exec($sql); //SQLの実行
    // $sql = "INSERT INTO `goal_position_kind_tb` (`id`, `goal_position_kind`, `explanation`) VALUES (NULL, 'TC', '中央上')";
    // $dbh->exec($sql); //SQLの実行
    $sql = "INSERT INTO `goal_position_kind_tb` (`id`, `goal_position_kind`, `explanation`) VALUES (NULL, 'TR', '右上')";
    $dbh->exec($sql); //SQLの実行
    // $sql = "INSERT INTO `goal_position_kind_tb` (`id`, `goal_position_kind`, `explanation`) VALUES (NULL, 'CL', '左中央')";
    // $dbh->exec($sql); //SQLの実行
    // $sql = "INSERT INTO `goal_position_kind_tb` (`id`, `goal_position_kind`, `explanation`) VALUES (NULL, 'CC', 'ど真ん中')";
    // $dbh->exec($sql); //SQLの実行
    // $sql = "INSERT INTO `goal_position_kind_tb` (`id`, `goal_position_kind`, `explanation`) VALUES (NULL, 'CR', '右中央')";
    // $dbh->exec($sql); //SQLの実行
    $sql = "INSERT INTO `goal_position_kind_tb` (`id`, `goal_position_kind`, `explanation`) VALUES (NULL, 'BL', '左下')";
    $dbh->exec($sql); //SQLの実行
    // $sql = "INSERT INTO `goal_position_kind_tb` (`id`, `goal_position_kind`, `explanation`) VALUES (NULL, 'BC', '中央下')";
    // $dbh->exec($sql); //SQLの実行
    $sql = "INSERT INTO `goal_position_kind_tb` (`id`, `goal_position_kind`, `explanation`) VALUES (NULL, 'BR', '右下')";
    $dbh->exec($sql); //SQLの実行
    $sql = "INSERT INTO `goal_position_kind_tb` (`id`, `goal_position_kind`, `explanation`) VALUES (NULL, '枠外T', '枠外(ゴール枠より上)')";
    $dbh->exec($sql); //SQLの実行
    $sql = "INSERT INTO `goal_position_kind_tb` (`id`, `goal_position_kind`, `explanation`) VALUES (NULL, '枠外L', '枠外(ゴール枠より左)')";
    $dbh->exec($sql); //SQLの実行
    $sql = "INSERT INTO `goal_position_kind_tb` (`id`, `goal_position_kind`, `explanation`) VALUES (NULL, '枠外R', '枠外(ゴール枠より右')";
    $dbh->exec($sql); //SQLの実行

    //シューターポジションの種類
    $sql = "CREATE TABLE IF NOT EXISTS `shooter_kind_tb` ( `id` INT NOT NULL AUTO_INCREMENT, `shooter_kind` VARCHAR(100) COMMENT 'シューターポジションの種類', `explanation` VARCHAR(1000) COMMENT 'シュートの種類の説明', PRIMARY KEY (`id`)) ENGINE = InnoDB";
    $dbh->exec($sql); //SQLの実行
    //シューターポジションの種類の事前登録
    $sql = "INSERT INTO `shooter_kind_tb` (`id`, `shooter_kind`, `explanation`) VALUES (NULL, 'LW', '左サイド')";
    $dbh->exec($sql); //SQLの実行
    $sql = "INSERT INTO `shooter_kind_tb` (`id`, `shooter_kind`, `explanation`) VALUES (NULL, 'PV', 'ポスト')";
    $dbh->exec($sql); //SQLの実行
    $sql = "INSERT INTO `shooter_kind_tb` (`id`, `shooter_kind`, `explanation`) VALUES (NULL, 'RW', '右サイド')";
    $dbh->exec($sql); //SQLの実行
    $sql = "INSERT INTO `shooter_kind_tb` (`id`, `shooter_kind`, `explanation`) VALUES (NULL, 'L6', '左側6m')";
    $dbh->exec($sql); //SQLの実行
    $sql = "INSERT INTO `shooter_kind_tb` (`id`, `shooter_kind`, `explanation`) VALUES (NULL, 'C6', 'センター6m')";
    $dbh->exec($sql); //SQLの実行
    $sql = "INSERT INTO `shooter_kind_tb` (`id`, `shooter_kind`, `explanation`) VALUES (NULL, 'R6', '右側6m')";
    $dbh->exec($sql); //SQLの実行
    $sql = "INSERT INTO `shooter_kind_tb` (`id`, `shooter_kind`, `explanation`) VALUES (NULL, 'L9', '左側9m')";
    $dbh->exec($sql); //SQLの実行
    $sql = "INSERT INTO `shooter_kind_tb` (`id`, `shooter_kind`, `explanation`) VALUES (NULL, 'C9', 'センター9m')";
    $dbh->exec($sql); //SQLの実行
    $sql = "INSERT INTO `shooter_kind_tb` (`id`, `shooter_kind`, `explanation`) VALUES (NULL, 'R9', '右側9m')";
    $dbh->exec($sql); //SQLの実行

    //試合テーブル
    $sql = "CREATE TABLE IF NOT EXISTS `game_tb` ( `id` INT NOT NULL AUTO_INCREMENT, `user_id` INT COMMENT '登録したユーザのID', `name` VARCHAR(255) COMMENT '試合名', `date` DATETIME COMMENT '試合日', `team_id1` VARCHAR(1000) COMMENT '対戦チーム1', `team_id2` VARCHAR(1000) COMMENT '対戦チーム2', `url` VARCHAR(1000) COMMENT '動画のURLまたはファイル名', `entry_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '登録時刻', PRIMARY KEY (`id`)) ENGINE = InnoDB";
    $dbh->exec($sql); //SQLの実行

    //映像時刻テーブル
    $sql = "CREATE TABLE IF NOT EXISTS `video_time_tb` ( `id` INT NOT NULL AUTO_INCREMENT, `game_id` INT COMMENT '試合ID', `time_kind` INT COMMENT '時刻の種類。0: 前半開始、1: 後半開始、2: 延長前半開始、3: 延長後半開始、4: タイムアウト, 5: other', `team_id` INT DEFAULT NULL COMMENT 'タイムアウトしたチームのID', `video_time` FLOAT COMMENT '映像の経過時間', PRIMARY KEY (`id`)) ENGINE = InnoDB";
    $dbh->exec($sql); //SQLの実行

    //シュートした結果のテーブル
    //$sql = "CREATE TABLE IF NOT EXISTS `after_shoot_tb` ( `id` INT NOT NULL AUTO_INCREMENT, `name` INT(10) NOT NULL COMMENT '0:ゴール 1:キーパーブロック ブロック後のボールが -> 2:攻手 3:守備', PRIMARY KEY (`id`)) ENGINE = InnoDB";
    //$dbh->exec($sql); //SQLの実行

    //入力履歴テーブル
    //$sql = "CREATE TABLE IF NOT EXISTS `history_tb` ( `id` INT NOT NULL AUTO_INCREMENT, `user_id` INT COMMENT 'ユーザテーブルでの入力者のID', `team_id` INT COMMENT 'チームテーブルでの入力者チームのID', `half_time` BOOLEAN DEFAULT 0 COMMENT '試合前半:0, 後半:1', `team_which` BOOLEAN DEFAULT 0 COMMENT '入力者が自分チーム:0, 相手チーム:1 (=ゲームテーブルのteam1:0,team2:1)', `after_shoot_id` INT COMMENT 'シュート結果テーブルのID', `mov_time` TIME COMMENT '動画の経過時間', `input_time` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '入力時の現在時刻', PRIMARY KEY (`id`)) ENGINE = InnoDB";
    //$dbh->exec($sql); //SQLの実行

    //ユーザテーブル
    $sql = "CREATE TABLE IF NOT EXISTS `user_tb` ( `id` INT NOT NULL AUTO_INCREMENT, `user_name` VARCHAR(255) NOT NULL COMMENT 'ユーザ名', `user_mail` VARCHAR(255) COMMENT 'ユーザのメールアドレス', `user_password` VARCHAR(255) COMMENT 'ユーザのパスワード', `admin_flag` tinyint(1) DEFAULT NULL, `shoot_time_flag` tinyint(1) DEFAULT NULL COMMENT 'シュート時間測定機能使用ユーザ' PRIMARY KEY (`id`)) ENGINE = InnoDB";
    $dbh->exec($sql); //SQLの実行

    //ユーザグループテーブル
    $sql = "CREATE TABLE IF NOT EXISTS `user_group_tb` ( `id` INT NOT NULL AUTO_INCREMENT, `group_name` VARCHAR(255) NOT NULL COMMENT 'グループ名', PRIMARY KEY (`id`)) ENGINE = InnoDB";
    $dbh->exec($sql); //SQLの実行

    //所属テーブル
    $sql = "CREATE TABLE IF NOT EXISTS `affiliation_tb` ( `id` INT NOT NULL AUTO_INCREMENT, `group_id` INT NOT NULL COMMENT 'groupのID', `user_id` INT NOT NULL COMMENT '所属するユーザのID', PRIMARY KEY (`id`)) ENGINE = InnoDB";
    $dbh->exec($sql); //SQLの実行

    //チームテーブル
    $sql = "CREATE TABLE IF NOT EXISTS `team_tb` ( `id` INT NOT NULL AUTO_INCREMENT, `team_name` VARCHAR(255) NOT NULL COMMENT 'チーム名', `abbreviation` VARCHAR(255) NOT NULL COMMENT '略称', `user_id` INT COMMENT '登録したユーザのID', PRIMARY KEY (`id`)) ENGINE = InnoDB";
    $dbh->exec($sql); //SQLの実行

    //初期ユーザ (管理者)
    $sql = "INSERT INTO `user_tb` (`id`, `user_name`, `user_mail`, `user_password`, `admin_flag`) VALUES (NULL, 'sawanolab', 'dummy@aitech.ac.jp', '" . password_hash('webphp', PASSWORD_DEFAULT) . "', 1)";
    $dbh->exec($sql); //SQLの実行
    //echo $sql;

    //初期ユーザ (一般ユーザ)
    $sql = "INSERT INTO `user_tb` (`id`, `user_name`, `user_mail`, `user_password`) VALUES (NULL, 'student', 'dummy2@aitech.ac.jp', '" . password_hash('webphp', PASSWORD_DEFAULT) . "')";
    $dbh->exec($sql); //SQLの実行
}
echo '更新終了';
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h5><a href='login.php'>◀︎ ログイン画面に移動</a></h5>
</body>

</html>