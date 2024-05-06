<?php
//セッションの生成
session_start();
//ログインの確認
if (!(isset($_SESSION['login']) && ($_SESSION['login'] == 'OK'))) {
    //ログインフォームへ
    header('Location: login.php');
}

// if (
//     !isset($_SESSION['game_id']) || $_SESSION['game_id'] == "" ||
//     !isset($_SESSION['url']) || $_SESSION['url'] == ""
// ) {
//     //game_id, URLがなかったら飛ばす
//     header('Location: inputGamedata.php');
// } else {
//     $url = $_SESSION['url'];
//     $youtube_id = getYoutubeIdFromUrl($url);
// }
// //YouTubeのURLからIDの取得
// function getYoutubeIdFromUrl($youtube_url)
// {
//     preg_match('/(http(s|):|)\/\/(www\.|)yout(.*?)\/(embed\/|watch.*?v=|)([a-z_A-Z0-9\-]{11})/i', $youtube_url, $results);
//     return $results[6];
// }

//接続用関数の呼び出し
require_once(__DIR__ . '/functions.php');

//DBへの接続
$dbh = connectDB();

//軌跡の検索
//仮にgame_id=1として検索している
$sql = 'SELECT position_xy
FROM shoot_tb
WHERE game_id=1';
$sth = $dbh->query($sql);  //SQLの実行
//データの取得
$json1 = $sth->fetchall(PDO::FETCH_ASSOC);
//print_r($json1);
for($i=0; $i<count($json1); $i++) {
    // echo $json1[$i]['position_xy'];
    //括弧を削除
    $json2 = str_replace('[', '', $json1[$i]['position_xy']);
    $json3 = str_replace(']', '', $json2);

    // 文字列をカンマで分割
    $xy_explode[$i] = explode (",",$json3);
}
$xy_encode = json_encode($xy_explode);
//配列の次元がいくつか
//$array_num = count($xy_explode);

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>(旧)ドラッグ履歴画面</title>
    <link rel="stylesheet" href="./game_result.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <div class="row">
            <header>
                <span class="login_status">
                    <b>
                        <?php echo $_SESSION['user_name']; ?>
                    </b>
                </span>
                <a href="logout.php" class="logout-button">ログアウト→</a>
            </header>

            <div class="container">
                <div class="row">
                    <!-- <div class="col"><?php echo $_SESSION['team_name1']; ?></div>
                    <div class="col migiyose"><?php echo $_SESSION['team_name2']; ?></div> -->
                    <div class="col path-team1">チームa</div>
                    <div class="col path-team2 migiyose">チームb</div>
                </div>
                <div class="row">
                    <div class="col"></div>
                    <div class="col-7"><canvas id="canvas_path"></canvas></div>
                    <div class="col"></div>
                </div>
            </div>

        <footer class="fixed-bottom">
            <div>
                <h5><a href="user_menu.php">◀︎ ユーザメニューに戻る</a></h5>
            </div>
        </footer>

    </div>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    

    <script>
        let canvas
        //軌跡の描画情報
        const line_color = "red";
        const line_size = 1;
        //シュート位置の描画情報
        const cross_color = "black";
        const cross_line_size = 2;
        const image_name = "img/court_half.png"; //背景画像


        //軌跡(円)の描画
        function drawPath(x,y) {
            // パスをリセット
            ctx.beginPath ();
            // 円の中心座標: (x,y)
            // 半径: 1.5
            // 開始角度: 0度 (0 * Math.PI / 180)
            // 終了角度: 360度 (360 * Math.PI / 180)
            // 方向: true=反時計回りの円、false=時計回りの円
            ctx.arc( x, y, 1.5, 0 * Math.PI / 180, 360 * Math.PI / 180, false );
            // 塗りつぶしの色
            ctx.fillStyle = "rgba(255,0,0,0.8)";
            // 塗りつぶしを実行
            ctx.fill();
            // // 線の色
            // ctx.strokeStyle = "rgba(255,0,0,0.8)";
            // // 線の太さ
            // ctx.lineWidth = 0;
            // // 線を描画を実行
            // ctx.stroke();
        }
    
        
        //バツ印を描画
        function drawCross(x, y) {
            //線の太さ
            ctx.lineWidth = cross_line_size;
            //線の色
            ctx.strokeStyle = cross_color;

            // Stroked Cross
            ctx.beginPath();
            ctx.moveTo(x - 5, y - 5);
            ctx.lineTo(x + 5, y + 5);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(x + 5, y - 5);
            ctx.lineTo(x - 5, y + 5);
            ctx.stroke();
        }


        window.onload = () => {
            canvas_path = document.getElementById("canvas_path");
            // canvas準備
            ctx = canvas_path.getContext("2d");

            //背景画像の設定
            let width = canvas_path.width;
            let height = canvas_path.height;
            // 画像読み込み
            const img = new Image();
            img.src = image_name; // 画像のURLを指定
            img.onload = () => {
                ctx.drawImage(img, 0, 0, width, height);
                // drawCross(100, 100)
                // drawCross(101, 100)
                // drawCross(102, 101)
                
                //PHPから軌跡の配列を持ってくる
                const xy_parse = JSON.parse('<?php echo $xy_encode; ?>');

                for(let i=0; i<xy_parse.length; i++) {
                    for(let j=0; j<xy_parse[i].length; j++) {

                        //どちらのチームが選択されているかによって場合分けが必要
                        //if()

                        //配列数が2(=軌跡なし)または、配列の最終要素の場合
                        if(xy_parse[i].length==2 || j==xy_parse[i].length-2) {
                            //バツ印の描画
                            drawCross(width*xy_parse[i][j], height*xy_parse[i][j+1])
                            console.log(width*xy_parse[i][j], height*xy_parse[i][j+1])
                        } else { //それ以外の座標
                            //軌跡(一点分)の描画
                            drawPath(width*xy_parse[i][j], height*xy_parse[i][j+1])
                        }
                        j++

                        //if(j<xy_parse[i].length-2) {
                        //}
                    }
                }
            };
        };


        //canvasのリセット
        // function resetCanvas() {
        // ctx.clearRect(0, 0, canvas_path.clientWidth, canvas_path.clientHeight);
        // setImage(); //画像の設定
        // }

        // function onMouseMove(e) {
        //     if (isPressed == true) {
        //         ratio_pos = convertPositionFromWindow2Ratio(e.clientX, e.clientY);
        //         save_position_array.push([ratio_pos.x, ratio_pos.y]);
        //         pos = convertPositionFromRatio2Canvas(ratio_pos.x, ratio_pos.y);
        //         ctx.lineTo(pos.x, pos.y);
        //         ctx.stroke();
        //     }
        // }
    </script>

</body>

</html>