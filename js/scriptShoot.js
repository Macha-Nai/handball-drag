let canvas;
let ctx;

let draw_cross_position;

let team_name_text1 = abbreviation1;
let team_name_text2 = abbreviation2;

let xy_parse;

// タッチイベントがサポートされているかどうかをチェック
let isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

let player_action;
let player_back;
let player_forward;

if (isTouchDevice) {
    player_action = document.getElementById("player_action");
    player_back = document.getElementById("player_back");
    player_forward = document.getElementById("player_forward");
}

let isPressed = false; //マウスが押されているか
//軌跡の描画情報
const line_color = "red";
const line_size = 1;
//シュート位置の描画情報
const cross_color = "black";
const cross_line_size = 2;
const image_name = "img/court.jpg"; //背景画像

const table = document.getElementById("shoot_history");
//保存する内容
let shoot_kind = -1; //未入力: -1, 軌跡: 1, 7m: 2
let shoot_judgement = -1; //未入力: -1, team1の成功: 1, team1の失敗: 2, team2の成功: 3、team2の失敗
let course_info = ""; //コース情報
let save_position_array = []; //保存される軌跡情報(比率)
let save_position_array_JSON; //軌跡 (JSON)
let video_time; //映像時刻
let shoot_id = 0;
let label1 = document.getElementById('restTime1');
let label2 = document.getElementById('restTime2');

//画像の設定
function setImage() {
    let width = canvas.width;
    let height = canvas.height;
    // 画像読み込み
    const img = new Image();
    img.src = image_name; // 画像のURLを指定
    img.onload = () => {
        ctx.drawImage(img, 0, 0, width, height);
    };
}

//canvasに画像貼り付け
window.onload = () => {
    canvas = document.getElementById("canvas");

    // canvas準備
    ctx = canvas.getContext("2d");

    //背景画像の設定
    setImage();
    if (isTouchDevice) {
        // タッチデバイス（iPadなど）の場合の処理
        canvas.addEventListener("touchstart", onTouchStart, false);
        canvas.addEventListener("touchmove", onTouchMove, false);
        canvas.addEventListener("touchend", onTouchEnd, false);
        player_action.addEventListener("touchstart", function (event) { player_action_touchstart(event); }, false);
        player_back.addEventListener("touchstart", function (event) { player_back_touchstart(event); }, false);
        player_forward.addEventListener("touchstart", function (event) { player_forward_touchstart(event); }, false);
        // player_action.addEventListener("touchend", player_touchend, false);
        // player_back.addEventListener("touchend", player_touchend, false);
        // player_forward.addEventListener("touchend", player_touchend, false);
        console.log("タッチデバイスで処理を実行します");
    } else {
        // タッチデバイスではない（PCなど）の場合の処理
        // canvasのマウスムーブイベント設定
        canvas.addEventListener("mousemove", onMouseMove);
        // canvasのマウスダウンイベント設定
        canvas.addEventListener("mousedown", onMouseDown);
        // canvasのマウスアップイベント設定
        canvas.addEventListener("mouseup", onMouseUp);
        console.log("PCで処理を実行します");
    }
    //シュート情報ボタンの初期化 (使用制限)
    restrictShootInfoButton();
    //シューターラジオボタンの初期化 (使用制限)
    restrictShooterInfoButton();

    //7mスローボタンのイベント設定
    let throw_checkbox = document.getElementById("7m-throw");
    throw_checkbox.addEventListener("click", clickThrowCheckbox);

    //表のイベント設定
    //ゴールコース
    let in_goal_td = document.getElementsByClassName("in_goal");
    for (let i = 0; i < in_goal_td.length; i++) {
        in_goal_td[i].addEventListener("click", clickInGoalTable);
    }
    let out_of_goal_td = document.getElementsByClassName("out_of_goal");
    for (let i = 0; i < out_of_goal_td.length; i++) {
        out_of_goal_td[i].addEventListener("click", clickOutOfGoalTable);
    }

    //ゴール成功判定の場合のイベント設定
    let goal_result = document.getElementsByClassName("goal_result");
    for (let i = 0; i < goal_result.length; i++) {
        goal_result[i].addEventListener("click", clickGoalResult);
    }

    //ゴール失敗判定の場合のイベント設定
    let failure_result = document.getElementsByClassName("failure_result");
    for (let i = 0; i < failure_result.length; i++) {
        failure_result[i].addEventListener("click", clickFailureResult);
    }

    //攻守の方向入れ替え
    let switch_dir = document.getElementById("switch_dir");
    switch_dir.addEventListener("click", switchAttackTeamDirection);

    //キー入力監視
    //document.addEventListener("keyup", onKeyUp);

    //クリアボタンのイベント設定
    let clear_bt = document.getElementById("clear");
    clear_bt.addEventListener("click", clickClearButton);
    //登録ボタン
    let submit_bt = document.getElementById("submit");
    submit_bt.addEventListener("click", clickSubmitButton);
    submit_bt.disabled = true;

    let delete_bt = document.getElementById("delete");
    delete_bt.addEventListener("click", clickDeleteButton);

    let edit_bt = document.getElementById("edit");
    edit_bt.addEventListener("click", clickEditButton);
    edit_bt.disabled = true;

    //時刻登録ボタン
    let set_video_time_bt = document.getElementById("setVideoTime");
    set_video_time_bt.addEventListener("click", setVideoTime);

    //時刻クリアボタン
    let clear_video_time_bt = document.getElementById("clearVideoTime");
    clear_video_time_bt.addEventListener("click", clearVideoTime);

    //シューターボタン
    const shooter_buttons = document.getElementsByName("shooter_position");
    for (let i = 0; i < shooter_buttons.length; i++) {
        shooter_buttons[i].addEventListener("click", clickShooterButtons);
    }
};

function sleep(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms));
}

async function player_action_touchstart(event) {
    state = player.getPlayerState();
    if (state == -1 || state == 2 || state == 5) {
        //未開始もしくは停止中から再生
        player.playVideo();
        player_action.innerHTML = "動画停止";
    } else if (state == 1) {
        //再生中から止める
        player.pauseVideo();
        player_action.innerHTML = "動画再生";
    }
    player_action.disabled = true;
    event.preventDefault();
    await sleep(500);
    player_action.disabled = false;
    event.stopPropagation();
}

async function player_back_touchstart(event) {
    player.seekTo(player.getCurrentTime() - 5, true);
    player_back.disabled = true;
    event.preventDefault();
    await sleep(500);
    player_back.disabled = false;
    event.stopPropagation();
}

async function player_forward_touchstart(event) {
    player.seekTo(player.getCurrentTime() + 5, true);
    player_forward.disabled = true;
    event.preventDefault();
    await sleep(500);
    player_forward.disabled = false;
    event.stopPropagation();
}

function clickDeleteButton() {
    var result = window.confirm('本当にこのデータを削除しますか？');
    if (result) {
        $.ajax({
            //送信方法
            type: "POST",
            //送信先ファイル名
            url: "./ajax/ajaxShootDelete.php",
            //受け取りデータの種類
            datatype: "text",
            //送信データ
            data: {
                shoot_id: shoot_id,
                game_id: game_id
            },
        }).then(
            //成功時の処理
            function (result) {
                $('#shoot_body').empty();
                $('#shoot_body').html(result);
                console.log('delete_success');
                addClickEventToRows();
            },
            //エラーの時の処理
            function (XMLHttpRequest, textStatus, errorThrown) {
                console.log("通信失敗!!!");
                console.log("XMLHttpRequest : " + XMLHttpRequest.status);
                console.log("textStatus : " + textStatus);
                console.log("errorThrown : " + errorThrown.message);
            });
    } else {
        console.log("キャンセル");
    }

    //クリアボタンのイベント設定
    clickClearButton();
}

//更新ボタンがクリックされた時
function clickEditButton() {
    // console.log(shoot_id);
    let shooter_position = -1; //シューターの位置
    //軌跡の場合
    if (shoot_kind == 1) {
        //シューターの位置
        const elements = document.getElementsByName("shooter_position");
        //console.log("要素数: " + elements.length);
        for (let i = 0; i < elements.length; i++) {
            if (elements[i].checked == true) {
                shooter_position = elements[i].value;
                break;
            }
        }
    }

    shoot_team_id = -1; //シュートしたチームID
    pointed_flag = -1; //シュート成功/失敗

    if (shoot_judgement == 1) {
        //team1の成功
        shoot_team_id = team_id1;
        pointed_flag = true;
    } else if (shoot_judgement == 2) {
        //team1の失敗
        shoot_team_id = team_id1;
        pointed_flag = false;
    } else if (shoot_judgement == 3) {
        //team2の成功
        shoot_team_id = team_id2;
        pointed_flag = true;
    } else if (shoot_judgement == 4) {
        //team2の失敗
        shoot_team_id = team_id2;
        pointed_flag = false;
    }
    //リバウンドシュートのチェック
    const rebound = document.getElementById("rebound").checked;
    //速攻のチェック
    const swift_attack = document.getElementById("swift_attack").checked;
    //DFブロックのチェック
    const gk_block_option = document.getElementById("GK_block").checked;
    //DFブロックのチェック
    const df_block_option = document.getElementById("DF_block").checked;
    //7mスローのチェック
    let seven_throw_option = document.getElementById("7m-throw").checked;
    //オウンゴールのチェック
    let empty_shoot = document.getElementById("empty_shoot").checked;
    //選手の背番号
    let shoot_tag = document.getElementById("shoot_tag");
    //メモ
    let shoot_memo = document.getElementById("shoot_memo");

    $.ajax({
        //送信方法
        type: "POST",
        //送信先ファイル名
        url: "./ajax/ajaxShootEdit.php",
        //受け取りデータの種類
        datatype: "text",
        //送信データ
        data: {
            shoot_id: shoot_id,
            game_id: game_id,
            position_xy: save_position_array_JSON,
            pointed_flag: pointed_flag,
            shoot_team_id: shoot_team_id,
            rebound: rebound,
            swift_attack: swift_attack,
            empty_shoot: empty_shoot,
            gk_block_option: gk_block_option,
            df_block_option: df_block_option,
            seven_throw_option: seven_throw_option,
            shooter_kind: shooter_position,
            goal_position: course_info,
            shoot_tag: shoot_tag.value,
            video_time: video_time,
            shoot_memo: shoot_memo.value
        },
    }).then(
        //成功時の処理
        function (result) {
            $('#shoot_body').empty();
            $('#shoot_body').html(result);
            console.log(save_position_array_JSON);
            addClickEventToRows();
        },
        //エラーの時の処理
        function (XMLHttpRequest, textStatus, errorThrown) {
            console.log("通信失敗!!!");
            console.log("XMLHttpRequest : " + XMLHttpRequest.status);
            console.log("textStatus : " + textStatus);
            console.log("errorThrown : " + errorThrown.message);
        }
    );

    //クリアボタンのイベント設定
    clickClearButton();
}

//シューターボタン
function clickShooterButtons() {
    //登録ボタンを有効化するかどうか
    confirmDataForSubmitting();
    confirmDataForEditing();
}

//時刻設定
function setVideoTime() {
    if (video_flag) {
        timeout_video_time = seconds; //時刻の取得
    } else {
        timeout_video_time = player.getCurrentTime(); //映像時刻の取得
    }
    //console.log("timeout_video_time" + timeout_video_time);
    let time_radio = -1;
    const elements = document.getElementsByName("timeRadio");
    for (let i = 0; i < elements.length; i++) {
        if (elements[i].checked == true) {
            time_radio = elements[i].value;
            break;
        }
    }
    let sending_time_radio = time_radio;
    if (time_radio == -1) return;
    let time_team_id = -1;
    if (time_radio == 4) {
        time_team_id = team_id1;
    } else if (time_radio == 5) {
        time_team_id = team_id2;
        sending_time_radio = 4; //DB上は4にしておく
    }
    //console.log("time_radio" + time_radio);
    //console.log("time_team_id" + time_team_id);
    $.ajax({
        //送信方法
        type: "POST",
        //送信先ファイル名
        url: "./ajax/ajaxSetVideoTime.php",
        //受け取りデータの種類
        datatype: "text",
        //送信データ
        data: {
            game_id: game_id,
            timeRadio: sending_time_radio,
            team_id: time_team_id,
            videoTime: timeout_video_time,
        },
    }).then(
        //成功時の処理
        function (data) {
            let time_info;
            if (video_flag) {
                if (time_radio == 2) {
                    time_info = document.getElementsByName("time_info")[0];
                } else if (time_radio == 4) {
                    time_info = document.getElementsByName("time_info")[1];
                } else if (time_radio == 5) {
                    time_info = document.getElementsByName("time_info")[2];
                }
            } else {
                time_info = document.getElementsByName("time_info")[time_radio];
            }
            if (time_radio < 4) {
                time_info.innerText = data;
            } else if (time_radio == 4) {
                time_info.innerText = abbreviation1 + data;
            } else if (time_radio == 5) {
                time_info.innerText = abbreviation2 + data;
            }
        },
        //エラーの時の処理
        function (XMLHttpRequest, textStatus, errorThrown) {
            console.log("通信失敗!!!");
            console.log("XMLHttpRequest : " + XMLHttpRequest.status);
            console.log("textStatus : " + textStatus);
            console.log("errorThrown : " + errorThrown.message);
        }
    );
}

function clearVideoTime() {
    let time_radio = -1;
    const elements = document.getElementsByName("timeRadio");
    for (let i = 0; i < elements.length; i++) {
        if (elements[i].checked == true) {
            time_radio = elements[i].value;
            break;
        }
    }
    console.log(time_radio);
    let sending_time_radio = time_radio;
    if (time_radio == -1) return;
    let time_team_id = -1;
    if (time_radio == 4) {
        time_team_id = team_id1;
    } else if (time_radio == 5) {
        time_team_id = team_id2;
        sending_time_radio = 4; //DB上は4にしておく
    }
    $.ajax({
        //送信方法
        type: "POST",
        //送信先ファイル名
        url: "./ajax/ajaxClearVideoTime.php",
        //受け取りデータの種類
        datatype: "text",
        //送信データ
        data: {
            game_id: game_id,
            timeRadio: sending_time_radio,
            team_id: time_team_id,
        },
    }).then(
        //成功時の処理
        function (data) {
            if (data == "none") {
                console.log("削除するデータが指定されていません");
                return;
            }
            let time_info = document.getElementsByName("time_info")[time_radio];
            if (time_radio < 4) {
                time_info.innerText = data;
            } else if (time_radio == 4) {
                time_info.innerText = abbreviation1 + data;
            } else if (time_radio == 5) {
                time_info.innerText = abbreviation2 + data;
            }
            //console.log(data);
        },
        //エラーの時の処理
        function (XMLHttpRequest, textStatus, errorThrown) {
            console.log("通信失敗!!!");
            console.log("XMLHttpRequest : " + XMLHttpRequest.status);
            console.log("textStatus : " + textStatus);
            console.log("errorThrown : " + errorThrown.message);
        }
    );
}

function confirmDataForEditing() {
    //登録ボタン
    let edit_bt = document.getElementById("edit");
    //シュート未入力
    if (shoot_kind == -1) {
        edit_bt.disabled = true;
        return;
    }
    //シュートの成功失敗未入力
    if (shoot_judgement == -1) {
        edit_bt.disabled = true;
        return;
    }
    //シュート成功時にシュートコース未入力
    if ((shoot_judgement == 1 || shoot_judgement == 3) && course_info == "") {
        edit_bt.disabled = true;
        return;
    }

    let shooter_position = -1; //シューターの位置
    //軌跡の場合
    if (shoot_kind == 1) {
        //シューターの位置
        const elements = document.getElementsByName("shooter_position");
        //console.log("要素数: " + elements.length);
        for (let i = 0; i < elements.length; i++) {
            if (elements[i].checked == true) {
                shooter_position = elements[i].value;
                break;
            }
        }
        if (shooter_position == -1) {
            edit_bt.disabled = true;
            return;
        }
    }
    edit_bt.disabled = false;
}

//登録ボタンを有効化するかどうか
function confirmDataForSubmitting() {
    //登録ボタン
    let submit_bt = document.getElementById("submit");
    //シュート未入力
    if (shoot_kind == -1) {
        submit_bt.disabled = true;
        return;
    }
    //シュートの成功失敗未入力
    if (shoot_judgement == -1) {
        submit_bt.disabled = true;
        return;
    }
    //シュート成功時にシュートコース未入力
    if ((shoot_judgement == 1 || shoot_judgement == 3) && course_info == "") {
        submit_bt.disabled = true;
        return;
    }

    let shooter_position = -1; //シューターの位置
    //軌跡の場合
    if (shoot_kind == 1) {
        //シューターの位置
        const elements = document.getElementsByName("shooter_position");
        //console.log("要素数: " + elements.length);
        for (let i = 0; i < elements.length; i++) {
            if (elements[i].checked == true) {
                shooter_position = elements[i].value;
                break;
            }
        }
        if (shooter_position == -1) {
            submit_bt.disabled = true;
            return;
        }
    }
    submit_bt.disabled = false;
}

//データ送信
function clickSubmitButton() {
    let shooter_position = -1; //シューターの位置
    //軌跡の場合
    if (shoot_kind == 1) {
        //シューターの位置
        const elements = document.getElementsByName("shooter_position");
        //console.log("要素数: " + elements.length);
        for (let i = 0; i < elements.length; i++) {
            if (elements[i].checked == true) {
                shooter_position = elements[i].value;
                break;
            }
        }
    }

    shoot_team_id = -1; //シュートしたチームID
    pointed_flag = -1; //シュート成功/失敗

    if (shoot_judgement == 1) {
        //team1の成功
        shoot_team_id = team_id1;
        pointed_flag = true;
    } else if (shoot_judgement == 2) {
        //team1の失敗
        shoot_team_id = team_id1;
        pointed_flag = false;
    } else if (shoot_judgement == 3) {
        //team2の成功
        shoot_team_id = team_id2;
        pointed_flag = true;
    } else if (shoot_judgement == 4) {
        //team2の失敗
        shoot_team_id = team_id2;
        pointed_flag = false;
    }
    //リバウンドのチェック
    const rebound = document.getElementById("rebound").checked;
    //速攻のチェック
    const swift_attack = document.getElementById("swift_attack").checked;
    //DFブロックのチェック
    const gk_block_option = document.getElementById("GK_block").checked;
    //DFブロックのチェック
    const df_block_option = document.getElementById("DF_block").checked;
    //7mスローのチェック
    let seven_throw_option = document.getElementById("7m-throw").checked;
    //オウンゴールのチェック
    let empty_shoot = document.getElementById("empty_shoot").checked;
    //選手の背番号
    let shoot_tag = document.getElementById("shoot_tag");
    //メモ
    let shoot_memo = document.getElementById("shoot_memo");

    $.ajax({
        //送信方法
        type: "POST",
        //送信先ファイル名
        url: "./ajax/ajaxShootRegister.php",
        //受け取りデータの種類
        datatype: "text",
        //送信データ
        data: {
            game_id: game_id,
            position_xy: save_position_array_JSON,
            pointed_flag: pointed_flag,
            shoot_team_id: shoot_team_id,
            rebound: rebound,
            swift_attack: swift_attack,
            empty_shoot: empty_shoot,
            gk_block_option: gk_block_option,
            df_block_option: df_block_option,
            seven_throw_option: seven_throw_option,
            shooter_kind: shooter_position,
            goal_position: course_info,
            shoot_tag: shoot_tag.value,
            video_time: video_time,
            shoot_memo: shoot_memo.value
        },
    }).then(
        //成功時の処理
        function (result) {
            $('#shoot_body').empty();
            $('#shoot_body').html(result);
            addClickEventToRows();
        },
        //エラーの時の処理
        function (XMLHttpRequest, textStatus, errorThrown) {
            console.log("通信失敗!!!");
            console.log("XMLHttpRequest : " + XMLHttpRequest.status);
            console.log("textStatus : " + textStatus);
            console.log("errorThrown : " + errorThrown.message);
        }
    );

    //クリアボタンのイベント設定
    clickClearButton();
}

//攻める方向の切り替え
function switchAttackTeamDirection() {
    //IDの切り替え
    let tmp_team_id = team_id1;
    team_id1 = team_id2;
    team_id2 = tmp_team_id;
    console.log(team_id1);

    //名前の切り替え
    let tmp_team_name = team_name1;
    team_name1 = team_name2;
    team_name2 = tmp_team_name;

    let tmp_abbreviation = abbreviation1;
    abbreviation1 = abbreviation2;
    abbreviation2 = tmp_abbreviation;

    let attack_dir_info = document.getElementById("attack_dir_info");
    attack_dir_info.innerHTML = "◀︎ " + abbreviation1 + "---" + abbreviation2 + " ▶︎";

    let text_tmp = label1.textContent
    label1.innerHTML = label2.textContent;
    label2.innerHTML = text_tmp;

    console.log(abbreviation1);
}

//キー入力監視
window.document.onkeydown = function (evt) {
    if (evt.which == 32) {
        //スペース
        if (video_flag) {
            // console.log(video_flag);
            // console.log(typeof (video_flag));
            if (timerRunning) {
                //タイマーが動いている場合
                stopTimer();
            } else {
                //タイマーが止まっている場合
                startTimer();
            }
        } else {
            state = player.getPlayerState();
            if (state == -1 || state == 2 || state == 5) {
                //未開始もしくは停止中から再生
                player.playVideo();
            } else if (state == 1) {
                //再生中から止める
                player.pauseVideo();
            }
            evt.which = null;
            return false;
        }
    } else if (evt.which == 39) {
        //右矢印
        if (video_flag) {
            skipTimer();
        } else {
            player.seekTo(player.getCurrentTime() + 5, true);
        }
    } else if (evt.which == 37) {
        //左矢印
        if (video_flag) {
            rewindTimer();
        } else {
            player.seekTo(player.getCurrentTime() - 5, true);
        }
    } else if (evt.which == 38) {
        //上矢印
        if (video_flag) {
            seconds += 60;
            checkStopTime(); // 時間の経過を確認
            updateTimerDisplay();
        } else {
            player.seekTo(player.getCurrentTime() + 10, true);
        }
    } else if (evt.which == 40) {
        //下矢印
        if (video_flag) {
            if (seconds >= 60) {
                seconds -= 60;
                checkStopTime(); // 時間の経過を確認
                updateTimerDisplay();
            }
        } else {
            player.seekTo(player.getCurrentTime() - 10, true);
        }
    }
};

//クリアボタンのイベント設定
function clickClearButton() {
    resetGoalCourse(); //ゴールのリセット
    resetCanvas(); //キャンバス初期化
    //シュート情報ボタンの初期化 (使用制限)
    restrictShootInfoButton();
    //シューターラジオボタンの初期化 (使用制限)
    restrictShooterInfoButton();
    //7mスローcheckboxのボタンを外す
    let throw_checkbox = document.getElementById("7m-throw");
    throw_checkbox.checked = false;
    shoot_kind = -1;
    //リバウンドシュートチェックボックスの非活性化
    let rebound = document.getElementById("rebound");
    rebound.checked = false;
    rebound.disabled = true;
    //速攻のチェックボックスの非活性化
    let swift_attack = document.getElementById("swift_attack");
    swift_attack.checked = false;
    swift_attack.disabled = true;
    //オウンゴールチェックボックスの非活性化
    let empty_shoot = document.getElementById("empty_shoot");
    empty_shoot.checked = false;
    empty_shoot.disabled = false;
    const df_block_option = document.getElementById("DF_block");
    df_block_option.checked = false; //DFブロックのチェックを外す
    df_block_option.removeAttribute("disabled"); //DFブロック使用可
    const gk_block_option = document.getElementById("GK_block");
    gk_block_option.checked = false; //GKブロックのチェックを外す
    gk_block_option.removeAttribute("disabled"); //DFブロック使用可
    let shoot_tag = document.getElementById("shoot_tag");
    shoot_tag.value = "";
    //メモ
    let shoot_memo = document.getElementById("shoot_memo");
    shoot_memo.value = "";

    video_time = -1; //映像時刻の初期化
    shoot_judgement = -1; //未入力
    course_info = "";

    // element = document.getElementById('#submit');
    // element.classList.remove('d-none');
    // element = document.getElementById('#clear');
    // element.classList.remove('d-none');
    // element = document.getElementById('#edit');
    // element.classList.add('d-none');
    // element = document.getElementById('#delete');
    // element.classList.add('d-none');
    // element = document.getElementById('#cancel');
    // element.classList.add('d-none');
    //登録ボタンを有効化するかどうか
    confirmDataForSubmitting();
    confirmDataForEditing();
}

//ゴール失敗判定の場合のイベント設定
function clickFailureResult() {
    if (shoot_kind == 2) {
        if (video_flag) {
            video_time = seconds; //時刻の取得
        } else {
            video_time = player.getCurrentTime(); //映像時刻の取得
        }
    }

    course_info = "";
    let failure_result = document.getElementsByClassName("failure_result");
    for (let i = 0; i < failure_result.length; i++) {
        if (failure_result[i].checked == true) {
            shoot_judgement = failure_result[i].value;
            break;
        }
    }
    let out_of_goal_areas = document.getElementsByClassName("out_of_goal");
    for (let i = 0; i < out_of_goal_areas.length; i++) {
        out_of_goal_areas[i].style.background = "#fff";
        out_of_goal_areas[i].style.color = "#f00";
        out_of_goal_areas[i].style.cursor = "default";
    }
    let in_goal_areas = document.getElementsByClassName("in_goal");
    for (let i = 0; i < in_goal_areas.length; i++) {
        in_goal_areas[i].style.background = "#fff";
        in_goal_areas[i].style.color = "#f00";
        in_goal_areas[i].style.cursor = "default";
    }
    //オウンゴールチェックボックスの活性化
    let empty_shoot = document.getElementById("empty_shoot");
    empty_shoot.checked = false;
    empty_shoot.disabled = false;
    //登録ボタンを有効化するかどうか
    confirmDataForSubmitting();
    confirmDataForEditing();
}

//ゴール成功判定の場合のイベント設定
function clickGoalResult() {
    if (shoot_kind == 2) {
        if (video_flag) {
            video_time = seconds; //時刻の取得
        } else {
            video_time = player.getCurrentTime(); //映像時刻の取得
        }
    }

    course_info = "";
    let goal_result = document.getElementsByClassName("goal_result");
    for (let i = 0; i < goal_result.length; i++) {
        if (goal_result[i].checked == true) {
            shoot_judgement = goal_result[i].value;
            break;
        }
    }

    let in_goal_areas = document.getElementsByClassName("in_goal");
    for (let i = 0; i < in_goal_areas.length; i++) {
        in_goal_areas[i].style.background = "#fff";
        in_goal_areas[i].style.color = "#0000ff";
        in_goal_areas[i].style.cursor = "default";
    }
    let out_of_goal_areas = document.getElementsByClassName("out_of_goal");
    for (let i = 0; i < out_of_goal_areas.length; i++) {
        out_of_goal_areas[i].style.background = "#fff";
        out_of_goal_areas[i].style.color = "#aaa";
        out_of_goal_areas[i].style.cursor = "not-allowed";
    }
    //オウンゴールチェックボックスの非活性化
    let empty_shoot = document.getElementById("empty_shoot");
    empty_shoot.disabled = false;

    //登録ボタンを有効化するかどうか
    confirmDataForSubmitting();
    confirmDataForEditing();
}

//ゴールコースをクリックしたら呼ばれる
function clickInGoalTable(e) {
    //ゴール情報の登録
    course_info = e.target.innerHTML;
    let in_goal_areas = document.getElementsByClassName("in_goal");
    //ゴールが成功の場合のみ動作させる
    let goal_result = document.getElementsByClassName("goal_result");
    for (let i = 0; i < goal_result.length; i++) {
        if (goal_result[i].checked == true) {
            shoot_judgement = goal_result[i].value;
            break;
        }
    }
    if (shoot_judgement == -1) {
        return;
    }
    if (shoot_judgement != 1 && shoot_judgement != 3) {
        resetgoalColor();
        for (let i = 0; i < in_goal_areas.length; i++) {
            if (e.target.innerHTML == in_goal_areas[i].innerHTML) {
                in_goal_areas[i].style.background = "#f00";
                in_goal_areas[i].style.color = "#fff";
                continue;
            }
            in_goal_areas[i].style.background = "#fff";
            in_goal_areas[i].style.color = "#f00";
        }
    } else {
        for (let i = 0; i < in_goal_areas.length; i++) {
            if (e.target.innerHTML == in_goal_areas[i].innerHTML) {
                in_goal_areas[i].style.background = "#00f";
                in_goal_areas[i].style.color = "#fff";
                continue;
            }
            in_goal_areas[i].style.background = "#fff";
            in_goal_areas[i].style.color = "#0000ff";
        }
    }

    //登録ボタンを有効化するかどうか
    confirmDataForSubmitting();
    confirmDataForEditing();
}

//ゴール枠外をクリックしたら呼ばれる
function clickOutOfGoalTable(e) {
    //ゴールが失敗の場合のみ動作させる
    if (shoot_judgement != 2 && shoot_judgement != 4) {
        return;
    }
    //ゴール情報の登録
    resetgoalColor();
    course_info = e.target.innerHTML;
    let out_of_goal_areas = document.getElementsByClassName("out_of_goal");
    for (let i = 0; i < out_of_goal_areas.length; i++) {
        if (e.target.innerHTML == out_of_goal_areas[i].innerHTML) {
            out_of_goal_areas[i].style.background = "#f00";
            out_of_goal_areas[i].style.color = "#fff";
            continue;
        }
        out_of_goal_areas[i].style.background = "#fff";
        out_of_goal_areas[i].style.color = "#f00";
    }
    //登録ボタンを有効化するかどうか
    confirmDataForSubmitting();
    confirmDataForEditing();
}

//7mスローチェックボックスがクリックされた場合
function clickThrowCheckbox() {
    if (video_flag) {
        video_time = seconds; //時刻の取得
    } else {
        video_time = player.getCurrentTime(); //映像時刻の取得
    }
    //console.log(video_time);
    const df_block_option = document.getElementById("DF_block");
    let throw_checkbox = document.getElementById("7m-throw");
    shoot_kind = 2; //7m
    if (throw_checkbox.checked == true) {
        save_position_array = []; //配列を空にする
        //シューターラジオボタンの初期化 (使用制限)
        restrictShooterInfoButton();
        //シュート情報ボタンの初期化 (使用制限)
        restrictShootInfoButton();
        //シュートの結果ラジオボタンの活性化
        activeShootInfoButton();
        resetCanvas(); //キャンバス初期化
        let rebound = document.getElementById("rebound");
        rebound.checked = false;
        rebound.disabled = true;
        //速攻のチェックボックスの非活性化
        let swift_attack = document.getElementById("swift_attack");
        swift_attack.checked = false;
        swift_attack.disabled = true;
        //DFブロック使用不可
        df_block_option.checked = false;
        df_block_option.setAttribute("disabled", true);
    } else {
        //7mスローがキャンセル
        //シュート情報ボタンの初期化 (使用制限)
        restrictShootInfoButton();
        //シューターラジオボタンの初期化 (使用制限)
        restrictShooterInfoButton();
        //リバウンドシュートボックスの非活性化
        let rebound = document.getElementById("rebound");
        rebound.checked = false;
        rebound.disabled = false;
        //速攻のチェックボックスの非活性化
        let swift_attack = document.getElementById("swift_attack");
        swift_attack.checked = false;
        swift_attack.disabled = false;
        //DFブロック使用可
        df_block_option.removeAttribute("disabled");
    }
    shoot_judgement = -1; //シュート未入力状態
    resetGoalCourse(); //ゴールのリセット
    //登録ボタンを有効化するかどうか
    confirmDataForSubmitting();
    confirmDataForEditing();
}

function resetgoalColor() {
    let in_goal_areas = document.getElementsByClassName("in_goal");
    for (let i = 0; i < in_goal_areas.length; i++) {
        in_goal_areas[i].style.background = "#fff";
        in_goal_areas[i].style.color = "#f00"
    }
    let out_of_goal_areas = document.getElementsByClassName("out_of_goal");
    for (let i = 0; i < out_of_goal_areas.length; i++) {
        out_of_goal_areas[i].style.background = "#fff";
        out_of_goal_areas[i].style.color = "#f00";
    }
}

//ウィンドウの座標から比率座標に変換
//参考URL: https://qiita.com/yukiB/items/cc533fbbf3bb8372a924#1-getboundingclientrectlefttopとeventclientxyを使用
function convertPositionFromWindow2Ratio(ex, ey) {
    var rect = canvas.getBoundingClientRect();
    //   ブラウザ上の座標
    var view_x = ex - rect.left;
    var view_y = ey - rect.top;
    // キャンパス上の比率
    const ratio_x = view_x / canvas.clientWidth;
    const ratio_y = view_y / canvas.clientHeight;

    return { x: ratio_x, y: ratio_y };
}

//比率座標からキャンバスの座標に変換
function convertPositionFromRatio2Canvas(rx, ry) {
    return {
        x: Math.floor(rx * canvas.width),
        y: Math.floor(ry * canvas.height),
    };
}

// マウスを押す
function onMouseDown(e) {
    resetCanvas(); //canvasの初期化
    save_position_array = []; //配列を空にする
    if (video_flag) {
        video_time = seconds; //時刻の取得
    } else {
        video_time = player.getCurrentTime(); //映像時刻の取得
    }
    shoot_kind = 1; //軌跡入力
    ratio_pos = convertPositionFromWindow2Ratio(e.clientX, e.clientY);
    pos = convertPositionFromRatio2Canvas(ratio_pos.x, ratio_pos.y);
    save_position_array.push([ratio_pos.x, ratio_pos.y]); //座標の格納
    // 色の設定
    ctx.strokeStyle = line_color;
    // 太さの設定
    ctx.lineWidth = line_size;
    ctx.beginPath(); //描画開始
    ctx.moveTo(pos.x, pos.y);
    isPressed = true;
    //7mスローcheckboxのボタンを外す
    let throw_checkbox = document.getElementById("7m-throw");
    throw_checkbox.checked = false;
    //リバウンドシュートチェックボックスの活性化
    let rebound = document.getElementById("rebound");
    rebound.checked = false;
    rebound.disabled = false;
    //速攻のチェックボックスの活性化
    let swift_attack = document.getElementById("swift_attack");
    swift_attack.checked = false;
    swift_attack.disabled = false;
    //シュート情報ボタンの初期化 (使用制限)
    // restrictShootInfoButton();
    //シューターラジオボタンの初期化 (使用制限)
    // restrictShooterInfoButton();
}

// マウスを離す
function onMouseUp(e) {
    ratio_pos = convertPositionFromWindow2Ratio(e.clientX, e.clientY);
    save_position_array.push([ratio_pos.x, ratio_pos.y]); //座標の格納
    pos = convertPositionFromRatio2Canvas(ratio_pos.x, ratio_pos.y);
    ctx.lineTo(pos.x, pos.y);
    ctx.stroke();
    drawCross(pos.x, pos.y);
    save_position_array_JSON = JSON.stringify(save_position_array);
    // console.log(save_position_array_JSON);
    // console.log(save_position_array);
    isPressed = false;
    //シュートの結果ラジオボタンの活性化
    activeShootInfoButton();
    //シューターポジションのラジオボタンの活性化
    activeShooterInfoButton();
    //登録ボタンを有効化するかどうか
    confirmDataForSubmitting();
    confirmDataForEditing();
}

//バツ印を描画
function drawCross(x, y) {
    //線の太さ
    ctx.lineWidth = cross_line_size;
    //線の色
    ctx.strokeStyle = cross_color;

    // Stroked Cross
    ctx.beginPath();
    ctx.moveTo(x - 10, y - 10);
    ctx.lineTo(x + 10, y + 10);
    ctx.stroke();
    ctx.beginPath();
    ctx.moveTo(x + 10, y - 10);
    ctx.lineTo(x - 10, y + 10);
    ctx.stroke();
}

// ドラッグしたところに円を書く
function onMouseMove(e) {
    if (isPressed == true) {
        ratio_pos = convertPositionFromWindow2Ratio(e.clientX, e.clientY);
        save_position_array.push([ratio_pos.x, ratio_pos.y]);
        pos = convertPositionFromRatio2Canvas(ratio_pos.x, ratio_pos.y);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
    }
}

// タッチダウンイベント
function onTouchStart(e) {
    console.log("touchstart");
    e.preventDefault();

    resetCanvas();
    save_position_array = [];
    if (video_flag) {
        video_time = seconds; //時刻の取得
    } else {
        video_time = player.getCurrentTime(); //映像時刻の取得
    }
    shoot_kind = 1;

    var touch = e.touches[0];
    ratio_pos = convertPositionFromWindow2Ratio(touch.clientX, touch.clientY);
    pos = convertPositionFromRatio2Canvas(ratio_pos.x, ratio_pos.y);
    save_position_array.push([ratio_pos.x, ratio_pos.y]);

    ctx.strokeStyle = line_color;
    ctx.lineWidth = line_size;
    ctx.beginPath();
    ctx.moveTo(pos.x, pos.y);

    isPressed = true;

    //7mスローcheckboxのボタンを外す
    let throw_checkbox = document.getElementById("7m-throw");
    throw_checkbox.checked = false;
    //リバウンドシュートチェックボックスの活性化
    let rebound = document.getElementById("rebound");
    rebound.checked = false;
    rebound.disabled = false;
    //速攻のチェックボックスの活性化
    let swift_attack = document.getElementById("swift_attack");
    swift_attack.checked = false;
    swift_attack.disabled = false;
    //シュート情報ボタンの初期化 (使用制限)
    restrictShootInfoButton();
    //シューターラジオボタンの初期化 (使用制限)
    restrictShooterInfoButton();
}

// タッチムーブイベント
function onTouchMove(e) {
    console.log("touchmove");
    e.preventDefault();

    if (isPressed) {
        var touch = e.touches[0];
        ratio_pos = convertPositionFromWindow2Ratio(touch.clientX, touch.clientY);
        pos = convertPositionFromRatio2Canvas(ratio_pos.x, ratio_pos.y);
        save_position_array.push([ratio_pos.x, ratio_pos.y]);

        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
    }
}

// タッチアップイベント
function onTouchEnd(e) {
    console.log("touchend");
    e.preventDefault();

    var touch = e.changedTouches[0];
    ratio_pos = convertPositionFromWindow2Ratio(touch.clientX, touch.clientY);
    save_position_array.push([ratio_pos.x, ratio_pos.y]); // 座標の格納
    pos = convertPositionFromRatio2Canvas(ratio_pos.x, ratio_pos.y);

    ctx.lineTo(pos.x, pos.y);
    ctx.stroke();
    drawCross(pos.x, pos.y);
    save_position_array_JSON = JSON.stringify(save_position_array);

    isPressed = false;
    //シュートの結果ラジオボタンの活性化
    activeShootInfoButton();

    // シューターポジションのラジオボタンの活性化
    activeShooterInfoButton();

    // 登録ボタンを有効化するかどうか
    confirmDataForSubmitting();
    confirmDataForEditing();
}

//canvasのリセット
function resetCanvas() {
    ctx.clearRect(0, 0, canvas.clientWidth, canvas.clientHeight);
    setImage(); //画像の設定
}

//シュートの結果(成功・失敗)ラジオボタンの活性化
function activeShootInfoButton() {
    //console.log("シュートの結果ラジオボタンの活性化");
    const elements = document.getElementsByName("shoot_result");
    //console.log("要素数: " + elements.length);
    for (let i = 0; i < elements.length; i++) {
        elements[i].removeAttribute("disabled");
    }
}

//シュート情報(成功・失敗)ボタンの初期化 (使用制限)
function restrictShootInfoButton() {
    //console.log("シュート情報ボタンの初期化 (使用制限)");
    const elements = document.getElementsByName("shoot_result");
    //console.log("要素数: " + elements.length);
    for (let i = 0; i < elements.length; i++) {
        elements[i].checked = false;
        elements[i].setAttribute("disabled", true);
    }
}

//シューターポジションのラジオボタンの活性化
function activeShooterInfoButton() {
    const elements = document.getElementsByName("shooter_position");
    //console.log("要素数: " + elements.length);
    for (let i = 0; i < elements.length; i++) {
        elements[i].removeAttribute("disabled");
    }
}
//シューターラジオボタンの初期化 (使用制限)
function restrictShooterInfoButton() {
    const elements = document.getElementsByName("shooter_position");
    //console.log("要素数: " + elements.length);
    for (let i = 0; i < elements.length; i++) {
        elements[i].checked = false;
        elements[i].setAttribute("disabled", true);
    }
}

//スクロール禁止処理（ドラッグを有効にするため）
function handle(event) {
    event.preventDefault();
}

function addClickEventToRows() {
    console.log("addclickevent");
    let rows = table.getElementsByTagName('tr');
    for (let i = 0; i < rows.length; i++) {
        rows[i].addEventListener('click', function () {
            const selectedRow = table.querySelector('.selected');
            if (selectedRow) {
                selectedRow.classList.remove('selected');
            }
            this.classList.add('selected');
            shoot_id = parseInt(this.id);
            resetCanvas();
            Click_Table();
        });
    }
}

addClickEventToRows();

//テーブルがクリックされた時にデータベースからそのシュートの情報を持ってくる
function Click_Table() {
    console.log(shoot_id);
    $.ajax({
        //送信方法
        type: "POST",
        //送信先ファイル名
        url: "./ajax/ajaxLoadShootData.php",
        //受け取りデータの種類
        datatype: "json",
        //送信データ
        data: {
            shoot_id: shoot_id,
            game_id: game_id
        },
    }).then(
        //成功時の処理
        function (result) {
            // clickClearButton();
            xy_parse = result.position_xy;
            save_position_array = [];
            save_position_array = convertToNestedArray(xy_parse, 2);
            save_position_array_JSON = JSON.stringify(save_position_array);
            video_time = parseInt(result.video_time);
            if (video_flag) {
                seconds = video_time;
            }

            //リバウンドのチェック
            let rebound_bt = document.getElementById("rebound");
            //速攻のチェック
            let swift_attack_bt = document.getElementById("swift_attack");
            //DFブロックのチェック
            let gk_block_option_bt = document.getElementById("GK_block");
            //DFブロックのチェック
            let df_block_option_bt = document.getElementById("DF_block");
            //オウンゴールのチェック
            let empty_shoot_bt = document.getElementById("empty_shoot");
            let shoot_tag_bt = document.getElementById("shoot_tag");
            let shoot_memo_bt = document.getElementById("shoot_memo");

            rebound_bt.checked = false;
            swift_attack_bt.checked = false;
            empty_shoot_bt.checked = false;
            df_block_option_bt.checked = false;
            gk_block_option_bt.checked = false;

            let shoot_team_id_value = parseInt(result.shoot_team_id);
            let pointed_flag_value = parseInt(result.pointed_flag);
            let rebound_value = parseInt(result.rebound);
            let swift_attack_value = parseInt(result.swift_attack);
            let empty_shoot_value = parseInt(result.empty_shoot);
            let df_block_value = parseInt(result.DF_block);
            let gk_block_value = parseInt(result.GK_block);
            let goal_position_value = parseInt(result.goal_position);
            let seven_value = parseInt(result.seven_shoot);
            let shooter_kind_value = parseInt(result.shooter_kind);
            let tag = parseInt(result.tag);
            let memo = result.memo;

            if (rebound_value) {
                rebound_bt.checked = true;
            }
            if (swift_attack_value) {
                swift_attack_bt.checked = true;
            }
            if (empty_shoot_value) {
                empty_shoot_bt.checked = true;
            }
            if (df_block_value) {
                df_block_option_bt.checked = true;
            }
            if (gk_block_value) {
                gk_block_option_bt.checked = true;
            }
            if (tag != null) {
                shoot_tag_bt.value = tag;
            }
            if (memo != null) {
                shoot_memo_bt.value = memo;
            }

            const shooter_buttons = document.getElementsByName("shooter_position");

            //7mスロー
            let seven_throw_option_bt = document.getElementById("7m-throw");

            if (seven_value == 1) {
                seven_throw_option_bt.checked = true;
                clickThrowCheckbox();
                activeShootInfoButton();
                for (let i = 0; i < shooter_buttons.length; i++) {
                    //shooter_buttonをdisabledにする
                    shooter_buttons[i].setAttribute("disabled", true);
                }
                save_position_array = []; //配列を空にする
                resetCanvas();
                shoot_kind = 2;
            } else {
                seven_throw_option_bt.checked = false;
                shooterkind_select_button(shooter_kind_value);
                activeShootInfoButton();
                activeShooterInfoButton();
                drawCanvasPath();
                shoot_kind = 1;
                swift_attack_bt.disabled = false;
                rebound_bt.disabled = false;
                empty_shoot_bt.disabled = false;
                df_block_option_bt.disabled = false;
            }


            if (shoot_team_id_value == 1) {
                if (team_name_text1 == abbreviation1) {
                    if (pointed_flag_value == 1) {
                        document.getElementById("team1_goal").checked = true;
                    } else {
                        document.getElementById("team1_failure").checked = true;
                    }
                } else {
                    if (pointed_flag_value == 1) {
                        document.getElementById("team2_goal").checked = true;
                    } else {
                        document.getElementById("team2_failure").checked = true;
                    }
                }
            } else {
                if (team_name_text2 == abbreviation2) {
                    if (pointed_flag_value == 1) {
                        document.getElementById("team2_goal").checked = true;
                    } else {
                        document.getElementById("team2_failure").checked = true;
                    }
                } else {
                    if (pointed_flag_value == 1) {
                        document.getElementById("team1_goal").checked = true;
                    } else {
                        document.getElementById("team1_failure").checked = true;
                    }
                }
            }

            if (pointed_flag_value == 1) {
                clickGoalResult();
            } else {
                clickFailureResult();
            }

            if (goal_position_value == 1) {
                course_info = "TL";
            } else if (goal_position_value == 2) {
                course_info = "TR";
            } else if (goal_position_value == 3) {
                course_info = "BL";
            } else if (goal_position_value == 4) {
                course_info = "BR";
            } else if (goal_position_value == 5) {
                course_info = "枠外T";
            } else if (goal_position_value == 6) {
                course_info = "枠外L";
            } else if (goal_position_value == 7) {
                course_info = "枠外R";
            }

            let out_of_goal_areas = document.getElementsByClassName("out_of_goal");
            let in_goal_areas = document.getElementsByClassName("in_goal");

            if (pointed_flag_value == 1) {
                for (let i = 0; i < in_goal_areas.length; i++) {
                    if (course_info == in_goal_areas[i].innerHTML) {
                        in_goal_areas[i].style.background = "#00f";
                        in_goal_areas[i].style.color = "#fff";
                    }
                }
            } else {
                for (let i = 0; i < out_of_goal_areas.length; i++) {
                    if (course_info == out_of_goal_areas[i].innerHTML) {
                        out_of_goal_areas[i].style.background = "#f00";
                        out_of_goal_areas[i].style.color = "#fff";
                    }
                }
                for (let i = 0; i < in_goal_areas.length; i++) {
                    if (course_info == in_goal_areas[i].innerHTML) {
                        in_goal_areas[i].style.background = "#f00";
                        in_goal_areas[i].style.color = "#fff";
                    }
                }
            }

            if (!video_flag) {
                myPauseVideo();
                mySeekTo(video_time);
            } else {
                updateTimerDisplay();
                console.log("video_flag");
            }

            confirmDataForSubmitting();
            confirmDataForEditing();
        },
        //エラーの時の処理
        function (XMLHttpRequest, textStatus, errorThrown) {
            console.log("通信失敗!!!");
            console.log("XMLHttpRequest : " + XMLHttpRequest.status);
            console.log("textStatus : " + textStatus);
            console.log("errorThrown : " + errorThrown.message);
        });
}

function shooterkind_select_button(value) {
    console.log("select");
    const shooter_buttons = document.getElementsByName("shooter_position");
    for (let i = 1; i < shooter_buttons.length + 1; i++) {
        if (i == value) {
            shooter_buttons[i - 1].checked = true;
            break;
        }
    }
}

function convertToNestedArray(arr, size) {
    const nestedArray = [];
    for (let i = 0; i < arr.length; i += size) {
        nestedArray.push(arr.slice(i, i + size));
    }
    return nestedArray;
}

//ゴールのリセット
function resetGoalCourse() {
    let in_goal_areas = document.getElementsByClassName("in_goal");
    for (let i = 0; i < in_goal_areas.length; i++) {
        console.log(in_goal_areas[i].innerHTML);
        in_goal_areas[i].style.background = "#fff";
        in_goal_areas[i].style.color = "#aaa";
        in_goal_areas[i].style.cursor = "not-allowed";
    }
    let out_of_goal_areas = document.getElementsByClassName("out_of_goal");
    for (let i = 0; i < out_of_goal_areas.length; i++) {
        console.log(out_of_goal_areas[i].innerHTML);
        out_of_goal_areas[i].style.background = "#fff";
        out_of_goal_areas[i].style.color = "#aaa";
        out_of_goal_areas[i].style.cursor = "not-allowed";
    }
}

function drawCanvasPath() {
    let first;
    let posi;
    ctx.strokeStyle = "red";
    ctx.lineWidth = 2;
    first = convertPositionFromRatio2Canvas(xy_parse[0], xy_parse[1]);
    ctx.beginPath();
    ctx.moveTo(first.x, first.y);
    for (let j = 0; j < xy_parse.length; j++) {
        posi = convertPositionFromRatio2Canvas(xy_parse[j], xy_parse[j + 1]);
        if (xy_parse.length == 2 || j == xy_parse.length - 2) {
            ctx.lineTo(posi.x, posi.y);
            ctx.stroke();
            draw_cross_position = xy_parse[j];
            //バツ印の描画
            drawCross(posi.x, posi.y);
        } else { //それ以外の座標
            ctx.lineTo(posi.x, posi.y);
            ctx.stroke();
        }
        j++;
    }
}

document.getElementById('shoot_tag').addEventListener('input', function (e) {
    var value = e.target.value;

    // 少数点を含む値を整数に変換
    value = value.replace(/\..*$/, '');

    // 先頭のゼロを削除（ただし、0自体は許可）
    if (value.length > 1) {
        value = value.replace(/^0+/, '');
    }

    e.target.value = value;
});
