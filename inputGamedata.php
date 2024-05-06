<?php

//セッションの生成
session_start();

//ログインの確認
if (!(isset($_SESSION['login']) &&
    ($_SESSION['login'] == 'OK'))) {
    //ログインフォームへ
    header('Location: login.php');
}
//接続用関数の呼び出し
require_once(__DIR__ . '/functions.php');
//チーム数の取得
$team_number = countTeamNumber();
$team_selection_flag = false; //チーム選択可能
if ($team_number > 0) {
    $team_selection_flag = true;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>試合情報入力画面</title>
    <link rel="stylesheet" href="./css/main.css">
    <link rel="stylesheet" href="./css/common.css">


</head>

<body>
    <div class="container">
        <form action="insert_gamedata.php" method="POST" class="wide-form" onsubmit="return confirmSubmit();">
            <h1 class="text-center">試合情報の入力</h1><br>
            <input placeholder="試合名" type="text" name="game_name" required />
            <input placeholder="試合日" type="date" name="date" onfocus="this.type='date'" onfocusout="this.type='text'" value="<?php echo date('Y-m-d'); ?>" required />
            <div class="form-group">
                <p class="control-label text-center h5"><b>対戦チーム１</b></p>
                <div class="form-check">
                    <input type="radio" name="team1-radio" class="form-check-input" id="team1-radio1" value="radio_new_team1" checked>
                    <label class="form-check-label" for="team1-radio1">チームを新規登録</label>
                    <input placeholder="チーム名" type="text" name="new_team_name1" class="team_name_input">
                    <input placeholder="チーム名の略称(※6文字以内)" type="text" class="team_name_input" name="abbreviation1" maxlength="6">
                </div>
                <hr>
                <?php if ($team_selection_flag) : ?>
                    <div class="form-check">
                        <input type="radio" name="team1-radio" class="form-check-input" value="radio_selected_team1" id="team1-radio2">

                        <!-- 選択チームあり -->
                        <label class="form-check-label" for="team1-radio2">登録済みチームから選択</label>
                        <select name="selected_team1" id="team1-radio1" for="team1-radio2">
                            <option>
                                <?php
                                //チームリストの取得
                                $sth = getTeamList($_SESSION['user_id']);
                                while ($row = $sth->fetch()) {
                                    echo "<option value=" . $row['id'] . ">" . $row['team_name'] . "</option>";
                                }
                                ?>
                            </option>
                        </select>
                    </div>
                <?php else : ?>
                    <!-- 選択チームなし -->
                <?php endif; ?>
            </div>
            <div class="form-group">
                <p class="control-label text-center h5"><b>対戦チーム2</b></p>
                <div class="form-check">
                    <input type="radio" name="team2-radio" class="form-check-input" id="radio_new_team2" value="radio_new_team2" checked>
                    <label class="form-check-label" for="radio_new_team2">チームを新規登録</label>
                    <input placeholder="チーム名" type="text" name="new_team_name2" class="team_name_input">
                    <input placeholder="チーム名の略称(※6文字以内)" type="text" class="team_name_input" name="abbreviation2" maxlength="6">
                </div>
                <hr>
                <?php if ($team_selection_flag) : ?>
                    <div class="form-check">
                        <input type="radio" name="team2-radio" class="form-check-input" value="radio_selected_team2" id="radio_selected_team2">

                        <!-- 選択チームあり -->
                        <label class="form-check-label" for="radio_selected_team2">登録済みチームから選択</label>
                        <select name="selected_team2" id="team2-radio2" for="team2-radio2">
                            <option>
                                <?php
                                //チームリストの取得
                                $sth = getTeamList($_SESSION['user_id']);
                                while ($row = $sth->fetch()) {
                                    echo "<option value=" . $row['id'] . ">" . $row['team_name'] . "</option>";
                                }
                                ?>
                            </option>
                        </select>
                    </div>
                <?php else : ?>
                    <!-- 選択チームなし -->
                <?php endif; ?>
            </div>
            <p class="control-label text-center h5"><b>YouTubeのURL</b></p>
            <input placeholder="試合映像のURL" type="url" name="url" id="url" size="50"><br>
            <input class="btn btn-primary" id="submit_data" type="submit" value="登録してシュート入力画面へ進む">
        </form><br>
        <hr>
        <div id="footer_game">
            <button class="btn btn-success" onclick="location.href='user_menu.php'">ユーザメニューに戻る</button>
            <button class="btn btn-danger" onclick="location.href='logout.php'" id="logout_button">ログアウト</button>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
    <script>
        function confirmSubmit() {
            let url_field = document.getElementById("url").value;
            console.log(url_field);
            if (url_field == "") {
                return confirm("映像なしでシュート情報を入力しますか？");
            }
            return true;
        }
    </script>
</body>

</html>