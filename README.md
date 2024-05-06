# 初期設定

1. データベースの立ち上げ (config.php と合わせる)
2. db_setup.php の実行 (テーブルの作成、初期ユーザが登録される)

# 【ユーザが使用する画面】

- ログイン画面 -> login.html
- ユーザメニュー画面 -> user_menu.php
- 試合情報入力画面 -> inputGamedata.php
- シュート情報入力画面 -> inputShoot.php
- 分析結果表示画面 -> result.php
- 新規アカウント作成(管理者のみ) -> newAccount.php

# 【メモ】

## データベース

- 全テーブル立ち上げ：db_setup.php（ファイルを開くだけでデータベースが自動で作成される）
- その他設定：config.php , function.php

## 新規アカウント作成

- 作成画面：newAccount.php
- アカウントのデータベース送信：insert_account.php

## ログイン

- ログイン画面：login.html
- ログインチェック：check_login.php
- ログイン成功・失敗：pk_login.php , lohin_fail.php

## ログアウト

- ログアウト処理・その後表示する画面：logout.php

## ユーザメニュー

- ユーザメニュー画面：user_menu.php

## 試合情報入力

- 試合情報入力画面：inputGamedata.php
- 試合情報のデータベース送信：insert_gamedata.php
- チーム入力切り替え方法等の動作：scriptGamedata.js

## シュート情報入力

- シュート情報入力画面：inputShoot.php
- シュート情報のデータベース送信：insert_shoot.php
- キャンバス等の動作：scriptShoot.js

## 分析結果表示

- 分析結果表示画面：result.php
