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

$team_id2 = 11;

$team_id1 = 10;

$team_id3 = 14;

$team_id4 = 15;

$sql = 'SELECT abbreviation FROM team_tb WHERE id=' . $team_id1;

$stmt = $dbh->query($sql);

$team_name1 = $stmt->fetch(PDO::FETCH_COLUMN);

$sql = 'SELECT abbreviation FROM team_tb WHERE id=' . $team_id2;

$stmt = $dbh->query($sql);

$team_name2 = $stmt->fetch(PDO::FETCH_COLUMN);

$sql = 'SELECT abbreviation FROM team_tb WHERE id=' . $team_id3;

$stmt = $dbh->query($sql);

$team_name3 = $stmt->fetch(PDO::FETCH_COLUMN);

$sql = 'SELECT abbreviation FROM team_tb WHERE id=' . $team_id4;

$stmt = $dbh->query($sql);

$team_name4 = $stmt->fetch(PDO::FETCH_COLUMN);

$shoot_count1 = get_all_shoot_count($team_id1);

$shoot_count_s1 = get_all_shoot_count_s($team_id1);

$shoot_count_r1 = division_check($shoot_count_s1, $shoot_count1);

$seven_count1 = get_all_seven_count($team_id1);

$seven_count_s1 = get_all_seven_count_s($team_id1);

$seven_count_r1 = division_check($seven_count_s1, $seven_count1);

$in_goal_count1 = get_all_in_goal_count($team_id1);

$out_goal_count1 = get_all_out_goal_count($team_id1);

$swift_count1 = get_all_swift_count($team_id1);

$swift_count_s1 = get_all_swift_count_s($team_id1);

$swift_count_r1 = division_check($swift_count_s1, $swift_count1);

$side_count1 = get_all_side($team_id1);

$side_count_s1 = get_all_side_s($team_id1);

$side_count_r1 = division_check($side_count_s1, $side_count1);

$long_count1 = get_all_long($team_id1);

$long_count_s1 = get_all_long_s($team_id1);

$long_count_r1 = division_check($long_count_s1, $long_count1);

$shoot_count2 = get_all_shoot_count($team_id2);

$shoot_count_s2 = get_all_shoot_count_s($team_id2);

$shoot_count_r2 = division_check($shoot_count_s2, $shoot_count2);

$seven_count2 = get_all_seven_count($team_id2);

$seven_count_s2 = get_all_seven_count_s($team_id2);

$seven_count_r2 = division_check($seven_count_s2, $seven_count2);

$in_goal_count2 = get_all_in_goal_count($team_id2);

$out_goal_count2 = get_all_out_goal_count($team_id2);

$swift_count2 = get_all_swift_count($team_id2);

$swift_count_s2 = get_all_swift_count_s($team_id2);

$swift_count_r2 = division_check($swift_count_s2, $swift_count2);

$side_count2 = get_all_side($team_id2);

$side_count_s2 = get_all_side_s($team_id2);

$side_count_r2 = division_check($side_count_s2, $side_count2);

$long_count2 = get_all_long($team_id2);

$long_count_s2 = get_all_long_s($team_id2);

$long_count_r2 = division_check($long_count_s2, $long_count2);

$tikou_count1 = get_all_tikou($team_id1);
$tikou_count2 = get_all_tikou($team_id2);

$tikou_count_s1 = get_all_tikou_s($team_id1);
$tikou_count_s2 = get_all_tikou_s($team_id2);

$tikou_count_r1 = division_check($tikou_count_s1, $tikou_count1);
$tikou_count_r2 = division_check($tikou_count_s2, $tikou_count2);

$shoot_count3 = get_all_shoot_count($team_id3);

$shoot_count_s3 = get_all_shoot_count_s($team_id3);

$shoot_count_r3 = division_check($shoot_count_s3, $shoot_count3);

$seven_count3 = get_all_seven_count($team_id3);

$seven_count_s3 = get_all_seven_count_s($team_id3);

$seven_count_r3 = division_check($seven_count_s3, $seven_count3);

$in_goal_count3 = get_all_in_goal_count($team_id3);

$out_goal_count3 = get_all_out_goal_count($team_id3);

$swift_count3 = get_all_swift_count($team_id3);

$swift_count_s3 = get_all_swift_count_s($team_id3);

$swift_count_r3 = division_check($swift_count_s3, $swift_count3);

$side_count3 = get_all_side($team_id3);

$side_count_s3 = get_all_side_s($team_id3);

$side_count_r3 = division_check($side_count_s3, $side_count3);

$long_count3 = get_all_long($team_id3);

$long_count_s3 = get_all_long_s($team_id3);

$long_count_r3 = division_check($long_count_s3, $long_count3);

$tikou_count3 = get_all_tikou($team_id3);

$tikou_count_s3 = get_all_tikou_s($team_id3);

$tikou_count_r3 = division_check($tikou_count_s3, $tikou_count3);

$shoot_count4 = get_all_shoot_count($team_id4);

$shoot_count_s4 = get_all_shoot_count_s($team_id4);

$shoot_count_r4 = division_check($shoot_count_s4, $shoot_count4);

$seven_count4 = get_all_seven_count($team_id4);

$seven_count_s4 = get_all_seven_count_s($team_id4);

$seven_count_r4 = division_check($seven_count_s4, $seven_count4);

$in_goal_count4 = get_all_in_goal_count($team_id4);

$out_goal_count4 = get_all_out_goal_count($team_id4);

$swift_count4 = get_all_swift_count($team_id4);

$swift_count_s4 = get_all_swift_count_s($team_id4);

$swift_count_r4 = division_check($swift_count_s4, $swift_count4);

$side_count4 = get_all_side($team_id4);

$side_count_s4 = get_all_side_s($team_id4);

$side_count_r4 = division_check($side_count_s4, $side_count4);

$long_count4 = get_all_long($team_id4);

$long_count_s4 = get_all_long_s($team_id4);

$long_count_r4 = division_check($long_count_s4, $long_count4);

$tikou_count4 = get_all_tikou($team_id4);

$tikou_count_s4 = get_all_tikou_s($team_id4);

$tikou_count_r4 = division_check($tikou_count_s4, $tikou_count4);

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
</head>

<body>
  <div class="container-fluid">
    <div class="row" id="team_hikaku">
      <div class="col-12 text-center hikaku">
        <p>チーム比較</p>
      </div><br><br>
      <div class="col-12" id="stats">
        <table class="table table-striped">
          <thead>
            <tr>
              <th class="empty"></th>
              <th><?php echo $team_name1; ?></th>
              <th><?php echo $team_name2; ?></th>
              <th><?php echo $team_name3; ?></th>
              <th><?php echo $team_name4; ?></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>シュート数</td>
              <td><?php echo $shoot_count1; ?></td>
              <td><?php echo $shoot_count2; ?></td>
              <td><?php echo $shoot_count3; ?></td>
              <td><?php echo $shoot_count4; ?></td>
            </tr>
            <tr>
              <td>シュート成功率(%)</td>
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
                if ($shoot_count2 == '0') {
                  echo "-";
                } else {
                  echo $shoot_count_r2;
                }
                ?>
              </td>
              <td>
                <?php
                if ($shoot_count3 == '0') {
                  echo "-";
                } else {
                  echo $shoot_count_r3;
                }
                ?>
              </td>
              <td>
                <?php
                if ($shoot_count4 == '0') {
                  echo "-";
                } else {
                  echo $shoot_count_r4;
                }
                ?>
              </td>
            </tr>
            <tr>
              <td>枠外のシュート数</td>
              <td><?php echo $out_goal_count1; ?></td>
              <td><?php echo $out_goal_count2; ?></td>
              <td><?php echo $out_goal_count3; ?></td>
              <td><?php echo $out_goal_count4; ?></td>
            </tr>
            <tr>
              <td>サイドシュート成功率(%)</td>
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
                if ($side_count2 == '0') {
                  echo "-";
                } else {
                  echo $side_count_r2;
                }
                ?>
              </td>
              <td>
                <?php
                if ($side_count3 == '0') {
                  echo "-";
                } else {
                  echo $side_count_r3;
                }
                ?>
              </td>
              <td>
                <?php
                if ($side_count4 == '0') {
                  echo "-";
                } else {
                  echo $side_count_r4;
                }
                ?>
              </td>
            </tr>
            <tr>
              <td>ロングシュート成功率(%)</td>
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
                if ($long_count2 == '0') {
                  echo "-";
                } else {
                  echo $long_count_r2;
                }
                ?>
              </td>
              <td>
                <?php
                if ($long_count3 == '0') {
                  echo "-";
                } else {
                  echo $long_count_r3;
                }
                ?>
              </td>
              <td>
                <?php
                if ($long_count4 == '0') {
                  echo "-";
                } else {
                  echo $long_count_r4;
                }
                ?>
              </td>
            </tr>
            <tr>
              <td>ペナルティ数</td>
              <td><?php echo $seven_count1; ?></td>
              <td><?php echo $seven_count2; ?></td>
              <td><?php echo $seven_count3; ?></td>
              <td><?php echo $seven_count4; ?></td>
            </tr>
            <tr>
              <td>ペナルティ成功率(%)</td>
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
                if ($seven_count2 == '0') {
                  echo "-";
                } else {
                  echo $seven_count_r2;
                }
                ?>
              </td>
              <td>
                <?php
                if ($seven_count3 == '0') {
                  echo "-";
                } else {
                  echo $seven_count_r3;
                }
                ?>
              </td>
              <td>
                <?php
                if ($seven_count4 == '0') {
                  echo "-";
                } else {
                  echo $seven_count_r4;
                }
                ?>
              </td>
            </tr>
            <tr>
              <td>速攻回数</td>
              <td><?php echo $swift_count1; ?></td>
              <td><?php echo $swift_count2; ?></td>
              <td><?php echo $swift_count3; ?></td>
              <td><?php echo $swift_count4; ?></td>
            </tr>
            <tr>
              <td>速攻のシュート成功率(%)</td>
              <td>
                <?php
                if ($swift_count1 == '0') {
                  echo "-";
                } else {
                  echo $swift_count_r1;
                }
                ?>
              </td>
              <td>
                <?php
                if ($swift_count2 == '0') {
                  echo "-";
                } else {
                  echo $swift_count_r2;
                }
                ?>
              </td>
              <td>
                <?php
                if ($swift_count3 == '0') {
                  echo "-";
                } else {
                  echo $swift_count_r3;
                }
                ?>
              </td>
              <td>
                <?php
                if ($swift_count4 == '0') {
                  echo "-";
                } else {
                  echo $swift_count_r4;
                }
                ?>
              </td>
            </tr>
            <tr>
              <td>遅攻回数</td>
              <td><?php echo $tikou_count1; ?></td>
              <td><?php echo $tikou_count2; ?></td>
              <td><?php echo $tikou_count3; ?></td>
              <td><?php echo $tikou_count4; ?></td>
            </tr>
            <tr>
              <td>遅攻のシュート成功率(%)</td>
              <td>
                <?php
                if ($tikou_count1 == '0') {
                  echo "-";
                } else {
                  echo $tikou_count_r1;
                }
                ?>
              </td>
              <td>
                <?php
                if ($tikou_count2 == '0') {
                  echo "-";
                } else {
                  echo $tikou_count_r2;
                }
                ?>
              </td>
              <td>
                <?php
                if ($tikou_count3 == '0') {
                  echo "-";
                } else {
                  echo $tikou_count_r3;
                }
                ?>
              </td>
              <td>
                <?php
                if ($tikou_count4 == '0') {
                  echo "-";
                } else {
                  echo $tikou_count_r4;
                }
                ?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>

</html>