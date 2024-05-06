let team_name_text1 = abbreviation1;
let team_name_text2 = abbreviation2;

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

const table = document.getElementById("shoot_history");

//保存する内容
let shoot_judgement = -1;
let video_time; //映像時刻
let shoot_id = 0;
let label1 = document.getElementById('restTime1');
let label2 = document.getElementById('restTime2');

let catch_click_flag = false;
let release_click_flag = false;
let goal_click_flag = false;

let catch_time = -1;
let release_time = -1;
let shoot_goal_time = -1;
let intervalId = null; // 定期実行のIDを格納する変数

let catch_text = document.getElementById('catch_text');
let release_text = document.getElementById('release_text');
let goal_text = document.getElementById('goal_text');

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

//得点成否ボタン
let goal_bt = document.getElementById("goal");
goal_bt.addEventListener("click", click_goal_bt);
let failure_bt = document.getElementById("failure");
failure_bt.addEventListener("click", click_failure_bt);

// キャッチボタン
let catch_time_bt = document.getElementById("catch");
catch_time_bt.addEventListener("click", click_catch_bt);

//リリースボタン
let release_time_bt = document.getElementById("release");
release_time_bt.addEventListener("click", click_release_bt);

//ゴールボタン
let goal_time_bt = document.getElementById("goal_time");
goal_time_bt.addEventListener("click", click_goal_time_bt);

let select_team1 = document.getElementById("select_team1");
select_team1.addEventListener("focus", click_select_team1);

let select_team2 = document.getElementById("select_team2");
select_team2.addEventListener("focus", click_select_team2);

let shoot_result_flag = false;

function click_select_team1() {
    confirmDataForSubmitting();
    confirmDataForEditing();
}

function click_select_team2() {
    confirmDataForSubmitting();
    confirmDataForEditing();
}

function click_goal_bt() {
    shoot_result_flag = true;
    confirmDataForSubmitting();
    confirmDataForEditing();
}

function click_failure_bt() {
    shoot_result_flag = true;
    confirmDataForSubmitting();
    confirmDataForEditing();
}

function repeated_time() {
    catch_text.innerHTML = player.getCurrentTime().toFixed(2);
}

function click_flag() {
    if (catch_click_flag) {
        catch_time_bt.style.backgroundColor = '#198754';
    } else {
        catch_time_bt.style.backgroundColor = '#007bff';
    }
    if (release_click_flag) {
        release_time_bt.style.backgroundColor = '#198754';
    } else {
        release_time_bt.style.backgroundColor = '#007bff';
    }
    if (goal_click_flag) {
        goal_time_bt.style.backgroundColor = '#198754';
    } else {
        goal_time_bt.style.backgroundColor = '#007bff';
    }
}

function click_catch_bt() {
    if (catch_click_flag) {
        video_time = parseInt(player.getCurrentTime());
        catch_time = player.getCurrentTime().toFixed(2);
        return;
    }
    catch_click_flag = true;
    video_time = parseInt(player.getCurrentTime());
    catch_time = player.getCurrentTime().toFixed(2);
    catch_text.innerHTML = catch_time;
    if (intervalId === null) { // 既に定期実行が設定されていないか確認
        intervalId = setInterval(repeated_time, 1);
    }
    catch_text.style.display = 'block';
    release_text.style.display = 'none';
    goal_text.style.display = 'none';
    confirmDataForEditing();
    confirmDataForSubmitting();
    click_flag();
}

function click_release_bt() {
    if (release_click_flag) {
        video_time = parseInt(player.getCurrentTime());
        release_time = player.getCurrentTime().toFixed(2);
        release_text.innerHTML = release_time;
        return;
    }
    if (catch_click_flag) {
        release_click_flag = true;
        release_time = player.getCurrentTime().toFixed(2);
        release_text.innerHTML = release_time;
        release_text.style.display = 'block';
        confirmDataForEditing();
        confirmDataForSubmitting();
        click_flag();
    }
}

function click_goal_time_bt() {
    if (catch_click_flag) {
        if (release_click_flag) {
            goal_click_flag = true;
            if (intervalId !== null) { // 定期実行が設定されているか確認
                clearInterval(intervalId);
                intervalId = null; // IDをリセット
                console.log("定期実行を停止しました。");
            }
            shoot_goal_time = player.getCurrentTime().toFixed(2);
            goal_text.innerHTML = shoot_goal_time;
            catch_text.innerHTML = catch_time;
            goal_text.style.display = 'block';
            click_flag();
            catch_click_flag = false;
            release_click_flag = false;
            goal_click_flag = false;
            confirmDataForEditing();
            confirmDataForSubmitting();
        }
    }
}

function sleep(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms));
}

function clickDeleteButton() {
    var result = window.confirm('本当にこのデータを削除しますか？');
    if (result) {
        $.ajax({
            //送信方法
            type: "POST",
            //送信先ファイル名
            url: "./ajax/shoot_time/ShootTimeDelete.php",
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
    // ラジオボタンの要素をすべて取得
    const shoot_team_select = document.getElementsByName('select_team');

    let shoot_team_id = 0;

    // 取得した要素の中から選択されたものを検索
    for (let i = 0; i < shoot_team_select.length; i++) {
        if (shoot_team_select[i].checked == true) {
            shoot_team_id = parseInt(shoot_team_select[i].value);
            break; // ループから抜ける
        }
    }

    // ラジオボタンの要素をすべて取得
    const shoot_result_button = document.getElementsByName('shoot_result');

    // 取得した要素の中から選択されたものを検索
    for (let i = 0; i < shoot_result_button.length; i++) {
        if (shoot_result_button[i].checked == true) {
            shoot_judgement = parseInt(shoot_result_button[i].value);
            break; // ループから抜ける
        }
    }
    let pointed_flag = 0;
    if (shoot_judgement == 1) {
        //成功
        pointed_flag = 1;
    } else if (shoot_judgement == 2) {
        //失敗
        pointed_flag = 0;
    }
    //選手の背番号
    let shoot_tag = document.getElementById("shoot_tag").value;
    //メモ
    let shoot_memo = document.getElementById("shoot_memo").value;

    $.ajax({
        //送信方法
        type: "POST",
        //送信先ファイル名
        url: "./ajax/shoot_time/ShootTimeEdit.php",
        //受け取りデータの種類
        datatype: "text",
        //送信データ
        data: {
            shoot_id: shoot_id,
            game_id: game_id,
            pointed_flag: pointed_flag,
            shoot_team_id: shoot_team_id,
            catch_time: catch_time,
            release_time: release_time,
            shoot_goal_time: shoot_goal_time,
            shoot_tag: shoot_tag,
            video_time: parseInt(video_time),
            shoot_memo: shoot_memo
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
    if (select_team1.checked == false && select_team2.checked == false) {
        edit_bt.disabled = true;
        return;
    }
    //シュートの成功失敗未入力
    if (shoot_result_flag == false) {
        edit_bt.disabled = true;
        console.log("shoot_judgement");
        return;
    }
    if (video_time == -1) {
        edit_bt.disabled = true;
        console.log("video_time");
        return;
    }
    if (catch_time == -1) {
        edit_bt.disabled = true;
        console.log("catch_time");
        return;
    }
    if (release_time == -1) {
        edit_bt.disabled = true;
        console.log("release_time");
        return;
    }
    if (shoot_goal_time == -1) {
        edit_bt.disabled = true;
        console.log("shoot_goal_time");
        return;
    }
    edit_bt.disabled = false;
}

//登録ボタンを有効化するかどうか
function confirmDataForSubmitting() {
    //登録ボタン
    let submit_bt = document.getElementById("submit");
    if (select_team1.checked == false && select_team2.checked == false) {
        submit_bt.disabled = true;
        return;
    }
    //シュートの成功失敗未入力
    if (shoot_result_flag == false) {
        submit_bt.disabled = true;
        return;
    }
    if (video_time == -1) {
        submit_bt.disabled = true;
        return;
    }
    if (catch_time == -1) {
        submit_bt.disabled = true;
        return;
    }
    if (release_time == -1) {
        submit_bt.disabled = true;
        return;
    }
    if (shoot_goal_time == -1) {
        submit_bt.disabled = true;
        return;
    }
    submit_bt.disabled = false;
}

//データ送信
function clickSubmitButton() {
    // ラジオボタンの要素をすべて取得
    const shoot_team_select = document.getElementsByName('select_team');

    let shoot_team_id = 0;

    // 取得した要素の中から選択されたものを検索
    for (let i = 0; i < shoot_team_select.length; i++) {
        if (shoot_team_select[i].checked == true) {
            shoot_team_id = parseInt(shoot_team_select[i].value);
            break; // ループから抜ける
        }
    }

    // ラジオボタンの要素をすべて取得
    const shoot_result_button = document.getElementsByName('shoot_result');

    // 取得した要素の中から選択されたものを検索
    for (let i = 0; i < shoot_result_button.length; i++) {
        if (shoot_result_button[i].checked == true) {
            shoot_judgement = parseInt(shoot_result_button[i].value);
            break; // ループから抜ける
        }
    }
    let pointed_flag = 0;
    if (shoot_judgement == 1) {
        //成功
        pointed_flag = 1;
    } else if (shoot_judgement == 2) {
        //失敗
        pointed_flag = 0;
    }
    //選手の背番号
    let shoot_tag = document.getElementById("shoot_tag").value;
    //メモ
    let shoot_memo = document.getElementById("shoot_memo").value;

    $.ajax({
        //送信方法
        type: "POST",
        //送信先ファイル名
        url: "./ajax/shoot_time/ShootTimeRegister.php",
        //受け取りデータの種類
        datatype: "text",
        //送信データ
        data: {
            game_id: game_id,
            pointed_flag: pointed_flag,
            shoot_team_id: shoot_team_id,
            catch_time: catch_time,
            shoot_goal_time: shoot_goal_time,
            release_time: release_time,
            shoot_tag: shoot_tag,
            video_time: parseInt(video_time),
            shoot_memo: shoot_memo
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
            player.seekTo(player.getCurrentTime() + 1, true);
        }
    } else if (evt.which == 37) {
        //左矢印
        if (video_flag) {
            rewindTimer();
        } else {
            player.seekTo(player.getCurrentTime() - 1, true);
        }
    } else if (evt.which == 38) {
        //上矢印
        if (video_flag) {
            seconds += 60;
            checkStopTime(); // 時間の経過を確認
            updateTimerDisplay();
        } else {
            player.seekTo(player.getCurrentTime() + 5, true);
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
            player.seekTo(player.getCurrentTime() - 5, true);
        }
    }
};

//クリアボタンのイベント設定
function clickClearButton() {
    shoot_result_flag = false;
    catch_click_flag = false;
    release_click_flag = false;
    goal_click_flag = false;
    click_flag();
    catch_time = -1;
    release_time = -1;
    shoot_goal_time = -1;
    video_time = -1;
    catch_text.style.display = 'none';
    release_text.style.display = 'none';
    goal_text.style.display = 'none';
    let shoot_tag = document.getElementById("shoot_tag");
    shoot_tag.value = "";
    //メモ
    let shoot_memo = document.getElementById("shoot_memo");
    shoot_memo.value = "";
    let select_team = document.getElementsByName("select_team");
    //select_teamの選択解除
    for (let i = 0; i < select_team.length; i++) {
        select_team[i].checked = false;
    }

    let shoot_result_button = document.getElementsByName('shoot_result');
    for (let i = 0; i < shoot_result_button.length; i++) {
        shoot_result_button[i].checked = false;
    }

    video_time = -1; //映像時刻の初期化
    shoot_judgement = -1; //未入力
    //登録ボタンを有効化するかどうか
    confirmDataForSubmitting();
    confirmDataForEditing();
}

//スクロール禁止処理（ドラッグを有効にするため）
function handle(event) {
    event.preventDefault();
}

function addClickEventToRows() {
    let rows = table.getElementsByTagName('tr');
    for (let i = 0; i < rows.length; i++) {
        rows[i].addEventListener('click', function () {
            const selectedRow = table.querySelector('.selected');
            if (selectedRow) {
                selectedRow.classList.remove('selected');
            }
            this.classList.add('selected');
            shoot_id = parseInt(this.id);
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
        url: "./ajax/shoot_time/LoadShootTime.php",
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
            clickClearButton();
            video_time = parseInt(result.video_time);
            if (video_flag) {
                seconds = video_time;
            }

            let number_input = document.getElementById("shoot_tag");
            let tag_input = document.getElementById("shoot_memo");

            let shoot_team_id_value = parseInt(result.shoot_team_id);
            let pointed_flag_value = parseInt(result.pointed_flag);
            let number = parseInt(result.number);
            let tag = result.tag;

            catch_time = result.catch_time;
            release_time = result.release_time;
            shoot_goal_time = result.shoot_goal_time;

            document.getElementById("catch_text").innerHTML = catch_time.toString();
            document.getElementById("release_text").innerHTML = release_time.toString();
            document.getElementById("goal_text").innerHTML = shoot_goal_time.toString();

            document.getElementById("catch_text").style.display = "block";
            document.getElementById("release_text").style.display = "block";
            document.getElementById("goal_text").style.display = "block";

            if (number != null) {
                number_input.value = number;
            }
            if (tag != null) {
                tag_input.value = tag;
            }

            if (shoot_team_id_value == team_id1) {
                //チーム1を選択する
                document.getElementById("select_team1").checked = true;
            } else {
                //チーム2を選択する
                document.getElementById("select_team2").checked = true;
            }

            if (pointed_flag_value == 1) {
                //成功
                document.getElementById("goal").checked = true;
            } else {
                //失敗
                document.getElementById("failure").checked = true;
            }

            shoot_result_flag = true;

            catch_click_flag = true;
            release_click_flag = true;
            goal_click_flag = true;

            click_flag();

            if (!video_flag) {
                myPauseVideo();
                mySeekTo(video_time);
            } else {
                updateTimerDisplay();
                console.log("video_flag");
            }

            confirmDataForSubmitting();
            confirmDataForEditing();

            catch_click_flag = false;
            release_click_flag = false;
            goal_click_flag = false;
        },
        //エラーの時の処理
        function (XMLHttpRequest, textStatus, errorThrown) {
            console.log("通信失敗!!!");
            console.log("XMLHttpRequest : " + XMLHttpRequest.status);
            console.log("textStatus : " + textStatus);
            console.log("errorThrown : " + errorThrown.message);
        });
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