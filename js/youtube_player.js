//参考URL: https://developers.google.com/youtube/iframe_api_reference?hl=ja

// 2. This code loads the IFrame Player API code asynchronously.
var tag = document.createElement("script");

tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName("script")[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

// 3. This function creates an <iframe> (and YouTube player)
//    after the API code downloads.
var player;
function onYouTubeIframeAPIReady() {
    player = new YT.Player("player", {
        height: "500",
        width: "700",
        //videoId: "M7lc1UVf-VE",
        videoId: youtube_id,
        events: {
            onReady: onPlayerReady,
            //onStateChange: onPlayerStateChange,
        },
    });
}

// 4. The API will call this function when the video player is ready.
function onPlayerReady(event) {
    event.target.pauseVideo();
}

// 5. The API calls this function when the player's state changes.
//    The function indicates that when playing a video (state=1),
//    the player should play for six seconds and then stop.
var done = false;
function onPlayerStateChange(event) {
    if (event.data == YT.PlayerState.PLAYING && !done) {
        setTimeout(stopVideo, 6000);
        done = true;
    }
}
function stopVideo() {
    player.stopVideo();
}
function playVideo() {
    player.playVideo();
}
function myPauseVideo() {
    player.pauseVideo();
}
function mySeekTo(sec) {
    player.seekTo(sec, true);
}

function click_half() {
    player.setPlaybackRate(0.5); // 再生速度を0.5倍に設定
}

function click_quarter() {
    player.setPlaybackRate(0.25); // 再生速度を0.5倍に設定
}

function click_normal() {
    player.setPlaybackRate(1); // 再生速度を1倍に設定
}

//動画時刻を1秒もどす
function click_back() {
    let time = player.getCurrentTime();
    time = time - 1;
    player.seekTo(time, true);
}

//動画時刻を1秒すすめる
function click_forward() {
    let time = player.getCurrentTime();
    time = time + 1;
    player.seekTo(time, true);
}

function click_action() {
    let player_action = document.getElementById('player_action');
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
}

let currentSpeedIndex = 0;
const playbackSpeeds = [1, 0.25, 0.5]; // 再生速度のリスト

// プレーヤーの準備ができたら呼ばれる関数
function onPlayerReady(event) {
    // キーボードイベントを監視
    document.addEventListener('keydown', function (e) {
        if ((e.key === 'q' || e.key === 'Q') && document.activeElement.id !== 'shoot_memo') {
            // テキストフィールドがフォーカスされていない場合のみ再生速度を変更
            currentSpeedIndex = (currentSpeedIndex + 1) % playbackSpeeds.length;
            player.setPlaybackRate(playbackSpeeds[currentSpeedIndex]);
            console.log(`再生速度が${playbackSpeeds[currentSpeedIndex]}に設定されました。`);
        }
        //aを押すとチーム1が選択される
        if (e.key === 'a' && document.activeElement.id !== 'shoot_memo') {
            let button = document.getElementById('select_team1');
            button.click(); // ボタンをプログラムでクリック
        }
        // sを押すとチーム2が選択される
        if (e.key === 's' && document.activeElement.id !== 'shoot_memo') {
            let button = document.getElementById('select_team2');
            button.click(); // ボタンをプログラムでクリック
        }
        // dを押すと成功が選択される
        if (e.key === 'd' && document.activeElement.id !== 'shoot_memo') {
            let button = document.getElementById('goal');
            button.click(); // ボタンをプログラムでクリック
        }
        //　fを押すと失敗が選択される
        if (e.key === 'f' && document.activeElement.id !== 'shoot_memo') {
            let button = document.getElementById('failure');
            button.click(); // ボタンをプログラムでクリック
        }
        // jを押すとキャッチボタンが押される
        if (e.key === 'j' && document.activeElement.id !== 'shoot_memo') {
            let button = document.getElementById('catch');
            button.click(); // ボタンをプログラムでクリック
        }
        // kを押すとリリースボタンが押される
        if (e.key === 'k' && document.activeElement.id !== 'shoot_memo') {
            let button = document.getElementById('release');
            button.click(); // ボタンをプログラムでクリック
        }
        // lを押すとゴールボタンが押される
        if (e.key === 'l' && document.activeElement.id !== 'shoot_memo') {
            let button = document.getElementById('goal_time');
            button.click(); // ボタンをプログラムでクリック
        }
        // hを押すと登録ボタンが押される
        if (e.key === 'h' && document.activeElement.id !== 'shoot_memo') {
            let button = document.getElementById('submit');
            button.click(); // ボタンをプログラムでクリック
        }
        // gを押すとクリアボタンが押される
        if (e.key === 'g' && document.activeElement.id !== 'shoot_memo') {
            let button = document.getElementById('clear');
            button.click(); // ボタンをプログラムでクリック
        }
    });
}