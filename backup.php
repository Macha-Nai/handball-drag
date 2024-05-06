<?php

header("Content-type: application/json; charset=UTF-8");

date_default_timezone_set('Asia/Tokyo');

//接続用関数の呼び出し
require_once(__DIR__ . '/functions.php');

//DBへの接続
$dbh = connectDB();

$dbHost = 'mysql2105.xserver.jp';
$dbName = 'teamdigicom_hdba';
$dbUser = 'teamdigicom_hdba';
$dbPass = 'cheeMai0';

$filePath = './backup/'; // ファイルを保存するディレクトリ
$fileName = date('ymd').'_'.date('His').'.sql';
$savePath = $filePath.$fileName;

$command = "mysqldump ".$dbName." --host=".$dbHost." --user=".$dbUser." --password=".$dbPass." > ".$savePath;
exec($command);

echo "backup success!";
