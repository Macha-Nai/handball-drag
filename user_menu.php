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

//DBへの接続
$dbh = connectDB();
//データベースの接続確認
if (!$dbh) {  //接続できていない場合
    echo 'DBに接続できていません。';
    return;
}

//試合一覧を取り出す
$user_id = $_SESSION['user_id'];
$sql = 'SELECT * FROM game_tb WHERE user_id=' . $user_id . ' ORDER BY date';
$game = $dbh->query($sql);

//user_nameを取り出す
$sql = 'SELECT `user_name` FROM `user_tb` WHERE `id`=' . $user_id;
$stmt = $dbh->query($sql);
$user_name = $stmt->fetch(PDO::FETCH_COLUMN);

$_SESSION['user_name'] = $user_name;

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
    <title>ユーザメニュー</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Bootstrap 5.0.2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- 自作CSS -->
    <link href="./css/menu.css" rel="stylesheet">
    <link href="./css/common.css" rel="stylesheet">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top navi">
            <button type="button" class="logo" onclick="openMenu()"><?php echo $_SESSION['user_name'][0]; ?></button>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="inputGamedata.php">試合情報の入力</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
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
        <div class="row" id="team_table">
            <h1 class="team text-center">チームごとの分析結果</h1>
        </div>
        <div class="row" id="game_table">
            <h1 class="all_game text-center">試合一覧</h1>
            <table class="table">
                <?php
                echo '<thead>';
                echo '<tr id="thead-tr">';
                echo '<th scope="col"><b>日付</b></th>';
                echo '<th scope="col"><b>試合</b></th>';
                echo '<th scope="col"><b>チーム</b></th>';
                echo '<th></th>';
                echo '<th></th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                while ($rows = $game->fetch()) {
                    //team1の名前を取得
                    $team1_id = $rows['team_id1'];
                    $team2_id = $rows['team_id2'];
                    $sql = 'SELECT abbreviation FROM team_tb WHERE id=' . $team1_id;
                    $sth_team1 = $dbh->query($sql);
                    $team1_name = $sth_team1->fetch(PDO::FETCH_COLUMN);

                    //team2の名前を取得
                    $sql = 'SELECT abbreviation FROM team_tb WHERE id=' . $team2_id;
                    $sth_team2 = $dbh->query($sql);
                    $team2_name = $sth_team2->fetch(PDO::FETCH_COLUMN);

                    $game_date = mb_substr($rows['date'], 0, 10);

                    //選手の所属を取得
                    echo '<tr>';
                    // echo '<td>' . $rows['id'] . '</td>';
                    echo '<th>' . $game_date . '</th>';
                    echo '<td>' . $rows['name'] . '</td>';
                    echo '<td>' . $team1_name  . ' vs ' . $team2_name . '</td>';
                    echo '<td class="result"><form method="post" name="game_id" action="game_result.php">';
                    echo '<input type="hidden" name="game_id" value="' . $rows["id"] . '">';
                    echo '<button type="submit" class="btn btn-primary" title="分析結果を表示"><i class="bi bi-graph-up-arrow"></i></button></form></td>';
                    if ($_SESSION['shoot_time_flag']) {
                        echo '<td class="input"><form method="post" name="game_id" action="input_shoot_time.php">';
                        echo '<input type="hidden" name="game_id" value="' . $rows["id"] . '">';
                        echo '<input type="hidden" name="url" value="' . $rows['url'] . '">';
                        echo '<button type="submit" class="btn btn-success" title="シュート時刻を計測"><i class="bi bi-stopwatch"></button></form></td>';
                    }
                    echo '<td class="input"><form method="post" name="game_id" action="inputShoot.php">';
                    echo '<input type="hidden" name="game_id" value="' . $rows["id"] . '">';
                    echo '<input type="hidden" name="url" value="' . $rows['url'] . '">';
                    echo '<button type="submit" class="btn btn-success" title="シュート情報を編集"><i class="bi bi-pencil"></button></form></td>';
                    echo '<td class="delete"><button type="submit" class="btn btn-danger" name="delete_btn" title="試合情報を削除" value="' . $rows["id"] . '"><i class="bi bi-trash"></i></button></td>';
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/user_menu.js"></script>
</body>

</html>