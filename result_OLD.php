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



        //DBへの接続
        $dbh = connectDB();
        if ($dbh) {
            //（＊＊＊あとでgame_id, shoot_team_idの置き換えをすること＊＊＊）
            $sql = 'SELECT count(*)
                    FROM shoot_tb
                    WHERE game_id=1 AND shoot_team_id=1 AND shooter_kind=1';
            $sth = $dbh->query($sql);  //SQLの実行
            //データの取得
            $LW = $sth->fetch(PDO::FETCH_ASSOC);

            $sql = 'SELECT count(*)
                    FROM shoot_tb
                    WHERE game_id=1 AND shoot_team_id=1 AND shooter_kind=1 AND pointed_flag=1';
            $sth = $dbh->query($sql);  //SQLの実行
            //データの取得
            $LW_seikou = $sth->fetch(PDO::FETCH_ASSOC);

            $sql = 'SELECT count(*)
                    FROM shoot_tb
                    WHERE game_id=1 AND shoot_team_id=1 AND shooter_kind=2';
            $sth = $dbh->query($sql);
            $RW = $sth->fetch(PDO::FETCH_ASSOC);

            $sql = 'SELECT count(*)
                    FROM shoot_tb
                    WHERE game_id=1 AND shoot_team_id=1 AND shooter_kind=2 AND pointed_flag=1';
            $sth = $dbh->query($sql);
            $RW_seikou = $sth->fetch(PDO::FETCH_ASSOC);

            $sql = 'SELECT count(*)
                    FROM shoot_tb
                    WHERE game_id=1 AND shoot_team_id=1 AND shooter_kind=3';
            $sth = $dbh->query($sql);
            $PV = $sth->fetch(PDO::FETCH_ASSOC);

            $sql = 'SELECT count(*)
                    FROM shoot_tb
                    WHERE game_id=1 AND shoot_team_id=1 AND shooter_kind=3 AND pointed_flag=1';
            $sth = $dbh->query($sql);
            $PV_seikou = $sth->fetch(PDO::FETCH_ASSOC);

            $sql = 'SELECT count(*)
                    FROM shoot_tb
                    WHERE game_id=1 AND shoot_team_id=1 AND shooter_kind=4';
            $sth = $dbh->query($sql);
            $L6 = $sth->fetch(PDO::FETCH_ASSOC);

            $sql = 'SELECT count(*)
                    FROM shoot_tb
                    WHERE game_id=1 AND shoot_team_id=1 AND shooter_kind=4 AND pointed_flag=1';
            $sth = $dbh->query($sql);
            $L6_seikou = $sth->fetch(PDO::FETCH_ASSOC);

            $sql = 'SELECT count(*)
                    FROM shoot_tb
                    WHERE game_id=1 AND shoot_team_id=1 AND shooter_kind=5';
            $sth = $dbh->query($sql);
            $C6 = $sth->fetch(PDO::FETCH_ASSOC);

            $sql = 'SELECT count(*)
                    FROM shoot_tb
                    WHERE game_id=1 AND shoot_team_id=1 AND shooter_kind=5 AND pointed_flag=1';
            $sth = $dbh->query($sql);
            $C6_seikou = $sth->fetch(PDO::FETCH_ASSOC);

            $sql = 'SELECT count(*)
                    FROM shoot_tb
                    WHERE game_id=1 AND shoot_team_id=1 AND shooter_kind=6';
            $sth = $dbh->query($sql);
            $R6 = $sth->fetch(PDO::FETCH_ASSOC);

            $sql = 'SELECT count(*)
                    FROM shoot_tb
                    WHERE game_id=1 AND shoot_team_id=1 AND shooter_kind=6 AND pointed_flag=1';
            $sth = $dbh->query($sql);
            $R6_seikou = $sth->fetch(PDO::FETCH_ASSOC);

            $sql = 'SELECT count(*)
                    FROM shoot_tb
                    WHERE game_id=1 AND shoot_team_id=1 AND shooter_kind=7';
            $sth = $dbh->query($sql);
            $L9 = $sth->fetch(PDO::FETCH_ASSOC);

            $sql = 'SELECT count(*)
                    FROM shoot_tb
                    WHERE game_id=1 AND shoot_team_id=1 AND shooter_kind=7 AND pointed_flag=1';
            $sth = $dbh->query($sql);
            $L9_seikou = $sth->fetch(PDO::FETCH_ASSOC);

            $sql = 'SELECT count(*)
                    FROM shoot_tb
                    WHERE game_id=1 AND shoot_team_id=1 AND shooter_kind=8';
            $sth = $dbh->query($sql);
            $C9 = $sth->fetch(PDO::FETCH_ASSOC);

            $sql = 'SELECT count(*)
                    FROM shoot_tb
                    WHERE game_id=1 AND shoot_team_id=1 AND shooter_kind=8 AND pointed_flag=1';
            $sth = $dbh->query($sql);
            $C9_seikou = $sth->fetch(PDO::FETCH_ASSOC);

            $sql = 'SELECT count(*)
                    FROM shoot_tb
                    WHERE game_id=1 AND shoot_team_id=1 AND shooter_kind=9';
            $sth = $dbh->query($sql);
            $R9 = $sth->fetch(PDO::FETCH_ASSOC);

            $sql = 'SELECT count(*)
                    FROM shoot_tb
                    WHERE game_id=1 AND shoot_team_id=1 AND shooter_kind=9 AND pointed_flag=1';
            $sth = $dbh->query($sql);
            $R9_seikou = $sth->fetch(PDO::FETCH_ASSOC);
            



            //チーム名検索
            //（＊＊他ファイルと繋げる際に変更＊＊）
            $sql = 'SELECT team_name
                    FROM team_tb
                    WHERE id=1 AND user_id=1';
            $sth = $dbh->query($sql);  //SQLの実行
            //データの取得
            $team1 = $sth->fetch(PDO::FETCH_ASSOC);

            $sql = 'SELECT team_name
                    FROM team_tb
                    WHERE id=2 AND user_id=1';
            $sth = $dbh->query($sql);  //SQLの実行
            //データの取得
            $team2 = $sth->fetch(PDO::FETCH_ASSOC);
        }


?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <title>(旧)分析結果画面</title>
    <link rel="stylesheet" href="main0126.css">
</head>
<body>
    <header>
        <h3>
            <b>
            <?php echo $_SESSION['user_name']; ?>
            </b>
            としてログイン中
        </h3>
        <a href="logout.php" class="logout-button">ログアウト→</a>
    </header>
    <br>
    <div>
        
    </div>

    <label class="bunseki-team" for="team-label">表示チームを選択</label>
        <select name="" id="bunseki-team" for="bunseki-team">
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
        <br>

    <canvas id="canvas_position" class="inline-contents" width="700" height="500"></canvas>


    <div class="other-result inline-contents">
        7mスロー:　<?php echo '2 / 3'; ?>
        <br>
        PV:　<?php echo '2 / 3'; ?>
        <br>
        DFブロック:　<?php echo '2 / 3'; ?>
        <br>
        GKブロック:　<?php echo '2 / 3'; ?>
    </div>



    <footer>
        <div>
            <h5><a href="user_menu.php">◀︎ ユーザメニューに戻る</a></h5>
        </div>
	</footer>
    <div>
        </div>
        
        <script>
            
        const LW = "<?php echo current($LW); ?>";
        const LW_seikou = "<?php echo current($LW_seikou); ?>";
        const LW_ratio = (LW_seikou / LW * 100).toFixed(1);
        const RW = "<?php echo current($RW); ?>";
        const RW_seikou = "<?php echo current($RW_seikou); ?>";
        const RW_ratio = (RW_seikou / RW * 100).toFixed(1);
        const L6 = "<?php echo current($L6); ?>";
        const L6_seikou = "<?php echo current($L6_seikou); ?>";
        const L6_ratio = (L6_seikou / L6 * 100).toFixed(1);
        const C6 = "<?php echo current($C6); ?>";
        const C6_seikou = "<?php echo current($C6_seikou); ?>";
        const C6_ratio = (C6_seikou / C6 * 100).toFixed(1);
        const R6 = "<?php echo current($R6); ?>";
        const R6_seikou = "<?php echo current($R6_seikou); ?>";
        const R6_ratio = (R6_seikou / R6 * 100).toFixed(1);
        const L9 = "<?php echo current($L9); ?>";
        const L9_seikou = "<?php echo current($L9_seikou); ?>";
        const L9_ratio = (L9_seikou / L9 * 100).toFixed(1);
        const C9 = "<?php echo current($C9); ?>";
        const C9_seikou = "<?php echo current($C9_seikou); ?>";
        const C9_ratio = (C9_seikou / C9 * 100).toFixed(1);
        const R9 = "<?php echo current($R9); ?>";
        const R9_seikou = "<?php echo current($R9_seikou); ?>";
        const R9_ratio = (R9_seikou / R9 * 100).toFixed(1);

        window.addEventListener('DOMContentLoaded',
            function() {
            if (HTMLCanvasElement) {
            var cv = document.querySelector('#canvas_position');
            var c = cv.getContext('2d');
            //***px,pyにはcanvasの縦横サイズが入る***
            var px = 700;
            var py = 500;

            //左サイド
            c.beginPath();
            c.moveTo(0, 0);
            c.lineTo(0.12*px, 0);
            c.lineTo(0.21*px, 0.2*py);
            c.lineTo(0, 0.45*py);
            // 塗りつぶしスタイルを設定
            c.fillStyle = "#7C82F0";
            c.globalAlpha = 1.0;
            // パスに沿って塗りつぶし
            c.fill();
            c.closePath();

            //右サイド
            c.beginPath();
            c.moveTo(px, 0);
            c.lineTo(0.88*px, 0);
            c.lineTo(0.79*px, 0.2*py);
            c.lineTo(px, 0.45*py);
            // 塗りつぶしスタイルを設定
            c.fillStyle = "#7C82F0";
            c.globalAlpha = 1.0;
            // パスに沿って塗りつぶし
            c.fill();
            c.closePath();

            //左上
            c.beginPath();
            c.moveTo(0, 0.45*py);
            c.lineTo(0.21*px, 0.2*py);
            c.lineTo(0.36*px, 0.27*py);
            c.lineTo(0.36*px, 0.7*py);
            c.lineTo(0.15*px, 0.63*py);
            // 塗りつぶしスタイルを設定
            c.fillStyle = "#666BD9";
            c.globalAlpha = 1.0;
            // パスに沿って塗りつぶし
            c.fill();
            c.closePath();
            
            //左下
            c.beginPath();
            c.moveTo(0, 0.45*py);
            c.lineTo(0.15*px, 0.63*py);
            c.lineTo(0.36*px, 0.7*py);
            c.lineTo(0.36*px, py);
            c.lineTo(0, py);
            // 塗りつぶしスタイルを設定
            c.fillStyle = "#7C82F0";
            c.globalAlpha = 1.0;
            // パスに沿って塗りつぶし
            c.fill();
            c.closePath();

            //中央上
            c.beginPath();
            c.moveTo(0.36*px, 0.27*py);
            c.lineTo(0.64*px, 0.27*py);
            c.lineTo(0.64*px, 0.7*py);
            c.lineTo(0.36*px, 0.7*py);
            // 塗りつぶしスタイルを設定
            c.fillStyle = "#7C82F0";
            c.globalAlpha = 1.0;
            // パスに沿って塗りつぶし
            c.fill();
            c.closePath();

            //中央下
            c.beginPath();
            c.moveTo(0.36*px, 0.7*py);
            c.lineTo(0.64*px, 0.7*py);
            c.lineTo(0.64*px, py);
            c.lineTo(0.36*px, py);
            // 塗りつぶしスタイルを設定
            c.fillStyle = "#666BD9";
            c.globalAlpha = 1.0;
            // パスに沿って塗りつぶし
            c.fill();
            c.closePath();

            //右上
            c.beginPath();
            c.moveTo(px, 0.45*py);
            c.lineTo(0.79*px, 0.2*py);
            c.lineTo(0.64*px, 0.27*py);
            c.lineTo(0.64*px, 0.7*py);
            c.lineTo(0.95*px, 0.63*py);
            // 塗りつぶしスタイルを設定
            c.fillStyle = "#666BD9";
            c.globalAlpha = 1.0;
            // パスに沿って塗りつぶし
            c.fill();
            c.closePath();

            //右下
            c.beginPath();
            c.moveTo(px, 0.45*py);
            c.lineTo(0.85*px, 0.63*py);
            c.lineTo(0.64*px, 0.7*py);
            c.lineTo(0.64*px, py);
            c.lineTo(px, py);
            // 塗りつぶしスタイルを設定
            c.fillStyle = "#7C82F0";
            c.globalAlpha = 1.0;
            // パスに沿って塗りつぶし
            c.fill();
            c.closePath();


            //PTスロー線
            // パスの開始（1）
            c.beginPath();
            // 始点／終点を設定（2）
            c.moveTo(0.45*px, 0.2*py);
            c.lineTo(0.55*px, 0.2*py);
            // 塗りつぶしスタイルを設定
            c.fillStyle = "Black";
            c.lineWidth = 3;
            // パスに沿って直線を描画（3）
            c.stroke();


            //＊＊＊＊＊シュート数・パーセンテージを変数の式で書き換える＊＊＊＊＊
            //左サイド、右サイド、左中央右の上下の順
            //シュート成功回数 / シュート回数
            c.fillStyle = "White";
            c.font = '20px sans-serif';
            c.fillText(LW_seikou + ' / ' + LW, 0.05*px, 0.15*py);
            c.fillText(RW_seikou + ' / ' + RW, 0.88*px, 0.15*py);
            c.font = '24px sans-serif';
            c.fillText(L6_seikou + ' / ' + L6, 0.145*px, 0.4*py);
            c.fillText(L9_seikou + ' / ' + L9, 0.145*px, 0.8*py);
            c.fillText(C6_seikou + ' / ' + C6, 0.46*px, 0.46*py);
            c.fillText(C9_seikou + ' / ' + C9, 0.46*px, 0.8*py);
            c.fillText(R6_seikou + ' / ' + R6, 0.77*px, 0.4*py);
            c.fillText(R9_seikou + ' / ' + R9, 0.77*px, 0.8*py);

            //シュート率　(%)
            c.font = '26px sans-serif';
            if(LW_ratio == 'NaN') {
                c.fillText('-- %', 0.05*px, 0.23*py);
            } else {
                c.fillText(LW_ratio + '%', 0.03*px, 0.23*py);
            }
            if(RW_ratio == 'NaN') {
                c.fillText('-- %', 0.88*px, 0.23*py);
            } else {
                c.fillText(RW_ratio + '%', 0.86*px, 0.23*py);
            }
            //サイド以外はフォントサイズを少し大きく
            c.font = '30px sans-serif';
            if(L6_ratio == 'NaN') {
                c.fillText('-- %', 0.15*px, 0.5*py);
            } else {
                c.fillText(L6_ratio + '%', 0.12*px, 0.5*py);
            }
            if(C6_ratio == 'NaN') {
                c.fillText('-- %', 0.14*px, 0.9*py);
            } else {
                c.fillText(C6_ratio + '%', 0.12*px, 0.9*py);
            }
            if(R6_ratio == 'NaN') {
                c.fillText('-- %', 0.46*px, 0.56*py);
            } else {
                c.fillText(R6_ratio + '%', 0.44*px, 0.56*py);
            }
            if(L9_ratio == 'NaN') {
                c.fillText('-- %', 0.46*px, 0.9*py);
            } else {
                c.fillText(L9_ratio + '%', 0.44*px, 0.9*py);
            }
            if(C9_ratio == 'NaN') {
                c.fillText('-- %', 0.77*px, 0.5*py);
            } else {
                c.fillText(C9_ratio + '%', 0.75*px, 0.5*py);
            }
            if(R9_ratio == 'NaN') {
                c.fillText('-- %', 0.77*px, 0.9*py);
            } else {
                c.fillText(R9_ratio + '%', 0.75*px, 0.9*py);
            }

            // 画像読み込み
            // const chara = new Image();
            // chara.src = "img/a.png";  // 画像のURLを指定
            // chara.onload = () => {
            // c.drawImage(chara, 0, 0, 700, 500);
            // };
            }
            }
        );

        

    
    </script>


</body>
</html>