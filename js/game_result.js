let screen_width = window.innerWidth;
console.log(screen_width);
let isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0 || /iPhone|iPad|iPod|Android/.test(navigator.userAgent);

let originalWidth;
let originalHeight;

let show_popup_count = 0;

let pv_select1 = false;
let pv_select2 = false;

let click_cross = 0;

let goal1 = document.getElementsByClassName('goal1');
let goal2 = document.getElementsByClassName('goal2');

let pv_click1 = false;
let pv_click2 = false;

let select_num1 = 0;
let select_num2 = 0;

let area1;
let area2;

let course1;
let course2;

// for (let i = 0; i < first_shoot1.id.length; i++) {
//   console.log(first_shoot1.number[i]);
// }

const cv2 = document.querySelector('#canvas_position2');
const c2 = cv2.getContext('2d');
const cv = document.querySelector('#canvas_position');
const c = cv.getContext('2d');
canvas_path = document.getElementById("canvas_path");
// canvas準備
ctx = canvas_path.getContext("2d");
canvas_path2 = document.getElementById("canvas_path2");
ctx2 = canvas_path2.getContext("2d");
if (isTouchDevice) {
    cv.width = window.innerWidth * 0.24;
    cv.height = window.innerHeight * 0.28;
    cv2.width = window.innerWidth * 0.24;
    cv2.height = window.innerHeight * 0.28;
    canvas_path.width = window.innerWidth * 0.4;
    canvas_path.height = window.innerHeight * 0.4;
    canvas_path2.width = window.innerWidth * 0.4;
    canvas_path2.height = window.innerHeight * 0.4;
    console.log("1280未満");
} else {
    cv.width = window.innerWidth * 0.22;
    cv.height = window.innerHeight * 0.28;
    cv2.width = window.innerWidth * 0.22;
    cv2.height = window.innerHeight * 0.28;
    canvas_path.width = window.innerWidth * 0.39;
    canvas_path.height = window.innerHeight * 0.43;
    canvas_path2.width = window.innerWidth * 0.39;
    canvas_path2.height = window.innerHeight * 0.43;
    console.log("1280以上");
}

const pElements = document.querySelectorAll('ball_around_goal p');
const pElements2 = document.querySelectorAll('ball_around_goal2 p');
var px = cv.width;
var py = cv.height;
let cross_position1 = [];
let cross_position2 = [];
let pos1 = 11;
let pos2 = 11;
const select_line_size = 4;
const shoot_body = document.getElementById("shoot_body");
let tbody_html = '';
let show_table = shoot_tb;
const table = document.getElementById('shoot_history');
let rows = table.getElementsByTagName('tr');
let swift_flag = 1;
let position_lists = [];
let position_lists2 = [];

//＊＊＊【２】ドラッグ履歴のcanvasについて＊＊＊
let canvas;
//軌跡の描画情報
// const line_color = "RGB(0,0,0)";
const line_size = 4;
const line_size_i = 3;
//シュート位置の描画情報
const cross_color = "black";
const cross_line_size = 3;
const cross_line_size_i = 2;
const image_name = "img/court_half.png"; //背景画像
const selected_team = 1; //表示チーム

var isDragging = false;
var lastX = 0;

//PHPから軌跡の配列を持ってくる
let xy_parse = all_shoot1;
let xy_parse2 = all_shoot2;
let select = 1;
// let select2 = 1;
let All = document.getElementById("all_radio");
let First = document.getElementById("first_radio");
let Latter = document.getElementById("latter_radio");
let third;
let fourth;
if (extension == 1) {
    third = document.getElementById("third_radio");
    fourth = document.getElementById("fourth_radio");
    All.addEventListener("click", shoot_filtering);
    First.addEventListener("click", shoot_filtering);
    Latter.addEventListener("click", shoot_filtering);
    third.addEventListener("click", shoot_filtering);
    fourth.addEventListener("click", shoot_filtering);
} else {
    All.addEventListener("click", shoot_filtering);
    First.addEventListener("click", shoot_filtering);
    Latter.addEventListener("click", shoot_filtering);
}

let position = 1;
let seven_flag = false;
let seven_state_flag1 = false;
let seven_state_flag2 = false;

//popupを表示する
function showPopup() {
    console.log("show");
    console.log(originalWidth);
    popup.classList.remove('hidden');
    if (show_popup_count == 0) {
        originalWidth = popup.offsetWidth;
        originalHeight = popup.offsetHeight;
    }
    show_popup_count++;
    popup.style.width = originalWidth + 'px';
    popup.style.height = originalHeight + 'px';
}

// popupを隠す
function hidePopup() {
    console.log("hide");
    popup.classList.add('hidden');
    popup.style.width = originalWidth + 'px';
    popup.style.height = originalHeight + 'px';
    for (let n = 0; n < goal1.length; n++) {
        if (goal1[n].id.includes('change_color_cell')) {
            goal1[n].removeAttribute('id');
        }
        if (goal2[n].id.includes('change_color_cell')) {
            goal2[n].removeAttribute('id');
        }
    }
}

if (isTouchDevice) {
    var resizeHandle = document.createElement('div');
    resizeHandle.className = 'resize-handle';
    popup.appendChild(resizeHandle);

    let isDragging = false;
    let isResizing = false;
    let startX = 0;
    let startY = 0;
    let offsetX = 0;
    let offsetY = 0;

    // ドラッグの開始
    popup.addEventListener('touchstart', function (e) {
        if (isResizing) return;
        isDragging = true;
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
        offsetX = popup.offsetLeft;
        offsetY = popup.offsetTop;
    });

    // ドラッグ中
    popup.addEventListener('touchmove', function (e) {
        if (isResizing) return;
        if (!isDragging) return;

        var diffX = e.touches[0].clientX - startX;
        var diffY = e.touches[0].clientY - startY;
        popup.style.left = offsetX + diffX + 'px';
        popup.style.top = offsetY + diffY + 'px';
        e.preventDefault();
    });

    // ドラッグの終了
    popup.addEventListener('touchend', function () {
        if (isResizing) return;
        isDragging = false;
    });

    var startWidth = 0;
    var startHeight = 0;

    // サイズ変更の開始（右下の角）
    var resizeHandle = document.createElement('div');
    resizeHandle.className = 'resize-handle';
    popup.appendChild(resizeHandle);

    // サイズ変更の開始（右下の角）
    resizeHandle.addEventListener('touchstart', function (e) {
        isResizing = true;
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
        startWidth = popup.offsetWidth;
        startHeight = popup.offsetHeight;
        e.preventDefault();
    });

    // サイズ変更中
    popup.addEventListener('touchmove', function (e) {
        if (!isResizing) return;

        var diffX = e.touches[0].clientX - startX;
        var diffY = e.touches[0].clientY - startY;
        var newWidth = startWidth + diffX;
        var newHeight = startHeight + diffY;
        if (newHeight <= originalHeight) return;
        popup.style.width = newWidth + 'px';
        popup.style.height = newHeight + 'px';
        e.preventDefault();
    });

    // サイズ変更の終了
    popup.addEventListener('touchend', function () {
        isResizing = false;
    });
} else {
    var isDragging = false;
    var isResizing = false;
    var startX = 0;
    var startY = 0;
    var offsetX = 0;
    var offsetY = 0;

    // ドラッグの開始
    popup.addEventListener('mousedown', function (e) {
        if (isResizing) return;
        isDragging = true;
        startX = e.clientX;
        startY = e.clientY;
        offsetX = popup.offsetLeft;
        offsetY = popup.offsetTop;
    });

    // ドラッグ中
    document.addEventListener('mousemove', function (e) {
        if (isResizing) return;
        if (!isDragging) return;

        var diffX = e.clientX - startX;
        var diffY = e.clientY - startY;
        popup.style.left = offsetX + diffX + 'px';
        popup.style.top = offsetY + diffY + 'px';
    });

    // ドラッグの終了
    document.addEventListener('mouseup', function () {
        if (isResizing) return;
        isDragging = false;
    });

    var startWidth = 0;
    var startHeight = 0;

    // サイズ変更の開始（右下の角）
    var resizeHandle = document.createElement('div');
    resizeHandle.className = 'resize-handle';
    popup.appendChild(resizeHandle);

    resizeHandle.addEventListener('mousedown', function (e) {
        isResizing = true;
        startX = e.clientX;
        startY = e.clientY;
        startWidth = popup.offsetWidth;
        startHeight = popup.offsetHeight;

        // マウスカーソルが要素外に移動しても対応するため、window 上で mouseup イベントをリスン
        window.addEventListener('mouseup', handleWindowMouseUp, { capture: true });
    });

    // window 上の mouseup イベントハンドラ
    function handleWindowMouseUp(e) {
        console.log("up");
        if (isResizing) {
            isResizing = false;
            window.removeEventListener('mouseup', handleWindowMouseUp, { capture: true });
        }
    }

    // サイズ変更中
    document.addEventListener('mousemove', function (e) {
        if (!isResizing) return;

        var diffX = e.clientX - startX;
        var diffY = e.clientY - startY;
        var newWidth = startWidth + diffX;
        var newHeight = startHeight + diffY;
        if (newHeight <= originalHeight) return;
        popup.style.width = newWidth + 'px';
        popup.style.height = newHeight + 'px';
    });

    // // サイズ変更の終了
    // document.addEventListener('mouseup', function () {
    //   console.log("処理終了");
    //   isResizing = false;
    // });
}

function drawCanvasPosition() {
    //***px,pyにはcanvasの縦横サイズが入る***
    var px = cv.width;
    var py = cv.height;
    if (HTMLCanvasElement) {
        //左サイド
        c.beginPath();
        c.moveTo(0, 0);
        c.lineTo(0.12 * px, 0);
        c.lineTo(0.21 * px, 0.2 * py);
        c.lineTo(0, 0.45 * py);
        // 塗りつぶしスタイルを設定
        Style(LW_r1, LW1);
        c.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c.fill();
        c.closePath();

        //右サイド
        c.beginPath();
        c.moveTo(px, 0);
        c.lineTo(0.88 * px, 0);
        c.lineTo(0.79 * px, 0.2 * py);
        c.lineTo(px, 0.45 * py);
        // 塗りつぶしスタイルを設定
        Style(RW_r1, RW1);
        c.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c.fill();
        c.closePath();

        //左上
        c.beginPath();
        c.moveTo(0, 0.45 * py);
        c.lineTo(0.21 * px, 0.2 * py);
        c.lineTo(0.36 * px, 0.27 * py);
        c.lineTo(0.36 * px, 0.7 * py);
        c.lineTo(0.15 * px, 0.63 * py);
        // 塗りつぶしスタイルを設定
        Style(L6_r1, L61);
        c.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c.fill();
        c.closePath();

        //左下
        c.beginPath();
        c.moveTo(0, 0.45 * py);
        c.lineTo(0.15 * px, 0.63 * py);
        c.lineTo(0.36 * px, 0.7 * py);
        c.lineTo(0.36 * px, py);
        c.lineTo(0, py);
        // 塗りつぶしスタイルを設定
        Style(L9_r1, L91);
        c.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c.fill();
        c.closePath();

        //中央上
        c.beginPath();
        c.moveTo(0.36 * px, 0.27 * py);
        c.lineTo(0.64 * px, 0.27 * py);
        c.lineTo(0.64 * px, 0.7 * py);
        c.lineTo(0.36 * px, 0.7 * py);
        // 塗りつぶしスタイルを設定
        Style(C6_r1, C61);
        c.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c.fill();
        c.closePath();

        //中央下
        c.beginPath();
        c.moveTo(0.36 * px, 0.7 * py);
        c.lineTo(0.64 * px, 0.7 * py);
        c.lineTo(0.64 * px, py);
        c.lineTo(0.36 * px, py);
        // 塗りつぶしスタイルを設定
        Style(C9_r1, C91);
        c.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c.fill();
        c.closePath();

        //右上
        c.beginPath();
        c.moveTo(px, 0.45 * py);
        c.lineTo(0.79 * px, 0.2 * py);
        c.lineTo(0.64 * px, 0.27 * py);
        c.lineTo(0.64 * px, 0.7 * py);
        c.lineTo(0.85 * px, 0.63 * py);
        // 塗りつぶしスタイルを設定
        Style(R6_r1, R61);
        c.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c.fill();
        c.closePath();

        //右下
        c.beginPath();
        c.moveTo(px, 0.45 * py);
        c.lineTo(0.85 * px, 0.63 * py);
        c.lineTo(0.64 * px, 0.7 * py);
        c.lineTo(0.64 * px, py);
        c.lineTo(px, py);
        // 塗りつぶしスタイルを設定
        Style(R9_r1, R91);
        c.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c.fill();
        c.closePath();

        //7mスロー
        // パスの開始（1）
        c.beginPath();
        // 始点／終点を設定（2）
        c.moveTo(0.21 * px, 0 * py);
        c.lineTo(0.79 * px, 0 * py);
        c.lineTo(0.79 * px, 0.09 * py);
        c.lineTo(0.21 * px, 0.09 * py);
        c.lineTo(0.21 * px, 0 * py);
        // 塗りつぶしスタイルを設定
        Style(seven_r1, seven1);
        c.linewidth = 0.5;
        c.fill();
        // パスに沿って直線を描画（3）
        c.stroke();


        //PTスロー線
        // パスの開始（1）
        c.beginPath();
        // 始点／終点を設定（2）
        c.moveTo(0.21 * px, 0.11 * py);
        c.lineTo(0.79 * px, 0.11 * py);
        c.lineTo(0.79 * px, 0.2 * py);
        c.lineTo(0.21 * px, 0.2 * py);
        c.lineTo(0.21 * px, 0.11 * py);
        // 塗りつぶしスタイルを設定
        Style(PV_r1, PV1);
        c.linewidth = 0.5;
        c.fill()
        // パスに沿って直線を描画（3）
        c.stroke();


        //領域同士の境界線
        // パスの開始（1）
        c.beginPath();
        // 始点／終点を設定（2）
        c.moveTo(0.21 * px, 0.2 * py);
        c.lineTo(0, 0.45 * py);
        c.lineTo(0.15 * px, 0.63 * py);
        c.lineTo(0.36 * px, 0.7 * py);
        c.lineTo(0.64 * px, 0.7 * py);
        c.lineTo(0.85 * px, 0.63 * py);
        c.lineTo(px, 0.45 * py);
        c.lineTo(0.79 * px, 0.2 * py);
        // 塗りつぶしスタイルを設定
        c.fillStyle = "#FFFFFF";
        c.linewidth = 0.5;
        // パスに沿って直線を描画（3）
        c.stroke();

        // パスの開始（1）
        c.beginPath();
        // 始点／終点を設定（2）
        c.moveTo(0.12 * px, 0);
        c.lineTo(0.21 * px, 0.2 * py);
        c.lineTo(0.36 * px, 0.27 * py);
        c.lineTo(0.64 * px, 0.27 * py);
        c.lineTo(0.79 * px, 0.2 * py);
        c.lineTo(0.88 * px, 0);
        // 塗りつぶしスタイルを設定
        c.fillStyle = "#FFFFFF";
        c.linewidth = 0.5;
        // パスに沿って直線を描画（3）
        c.stroke();

        // パスの開始（1）
        c.beginPath();
        // 始点／終点を設定（2）
        c.moveTo(0.36 * px, 0.27 * py);
        c.lineTo(0.36 * px, py);
        // 塗りつぶしスタイルを設定
        c.fillStyle = "#FFFFFF";
        c.linewidth = 0.5;
        // パスに沿って直線を描画（3）
        c.stroke();

        // パスの開始（1）
        c.beginPath();
        // 始点／終点を設定（2）
        c.moveTo(0.64 * px, 0.27 * py);
        c.lineTo(0.64 * px, py);
        // 塗りつぶしスタイルを設定
        c.fillStyle = "#FFFFFF";
        c.linewidth = 0.5;
        // パスに沿って直線を描画（3）
        c.stroke();
        //＊＊＊＊＊シュート数・パーセンテージを変数の式で書き換える＊＊＊＊＊
        //左サイド、右サイド、左中央右の上下の順
        //シュート成功回数 / シュート回数
        c.fillStyle = "White";
        if (isTouchDevice) {
            c.font = '10px sans-serif';
            c.fillText(LW_s1 + ' / ' + LW1, 0.05 * px, 0.15 * py);
            c.fillText(RW_s1 + ' / ' + RW1, 0.88 * px, 0.15 * py);
            c.fillText('PV ：', 0.25 * px, 0.18 * py);
            c.fillText('7m ：', 0.25 * px, 0.075 * py);
            c.fillText(PV_s1 + ' / ' + PV1, 0.43 * px, 0.18 * py);
            c.fillText(seven_s1 + ' / ' + seven1, 0.43 * px, 0.075 * py);
            c.font = '12px sans-serif';
            c.fillText(L6_s1 + ' / ' + L61, 0.145 * px, 0.4 * py);
            c.fillText(L9_s1 + ' / ' + L91, 0.145 * px, 0.8 * py);
            c.fillText(C6_s1 + ' / ' + C61, 0.46 * px, 0.46 * py);
            c.fillText(C9_s1 + ' / ' + C91, 0.46 * px, 0.8 * py);
            c.fillText(R6_s1 + ' / ' + R61, 0.72 * px, 0.4 * py);
            c.fillText(R9_s1 + ' / ' + R91, 0.77 * px, 0.8 * py);

            //シュート率　(%)
            c.font = '10px sans-serif';
            if (LW1 == '0') {
                c.fillText('-- %', 0.05 * px, 0.23 * py);
            } else {
                c.fillText(LW_r1 + ' %', 0.03 * px, 0.23 * py);
            }
            if (RW1 == '0') {
                c.fillText('-- %', 0.86 * px, 0.23 * py);
            } else {
                c.fillText(RW_r1 + ' %', 0.86 * px, 0.23 * py);
            }
            if (PV1 == '0') {
                c.fillText('-- %', 0.62 * px, 0.18 * py);
            } else {
                c.fillText(PV_r1 + ' %', 0.62 * px, 0.18 * py);
            }
            if (seven1 == '0') {
                c.fillText('-- %', 0.62 * px, 0.08 * py);
            } else {
                c.fillText(seven_r1 + ' %', 0.62 * px, 0.08 * py);
            }
            //サイド以外はフォントサイズを少し大きく
            c.font = '12px sans-serif';
            if (L61 == '0') {
                c.fillText('-- %', 0.15 * px, 0.5 * py);
            } else {
                c.fillText(L6_r1 + ' %', 0.12 * px, 0.5 * py);
            }
            if (L91 == '0') {
                c.fillText('-- %', 0.14 * px, 0.9 * py);
            } else {
                c.fillText(L9_r1 + ' %', 0.12 * px, 0.9 * py);
            }
            if (C61 == '0') {
                c.fillText('-- %', 0.46 * px, 0.56 * py);
            } else {
                c.fillText(C6_r1 + ' %', 0.44 * px, 0.56 * py);
            }
            if (C91 == '0') {
                c.fillText('-- %', 0.46 * px, 0.9 * py);
            } else {
                c.fillText(C9_r1 + ' %', 0.44 * px, 0.9 * py);
            }
            if (R61 == '0') {
                c.fillText('-- %', 0.75 * px, 0.5 * py);
            } else {
                c.fillText(R6_r1 + ' %', 0.75 * px, 0.5 * py);
            }
            if (R91 == '0') {
                c.fillText('-- %', 0.77 * px, 0.9 * py);
            } else {
                c.fillText(R9_r1 + ' %', 0.75 * px, 0.9 * py);
            }
        } else {
            c.font = '14px sans-serif';
            c.fillText(LW_s1 + ' / ' + LW1, 0.05 * px, 0.15 * py);
            c.fillText(RW_s1 + ' / ' + RW1, 0.88 * px, 0.15 * py);
            c.fillText(PV_s1 + ' / ' + PV1, 0.43 * px, 0.18 * py);
            c.fillText(seven_s1 + ' / ' + seven1, 0.43 * px, 0.075 * py);
            c.fillText('PV ：', 0.25 * px, 0.18 * py);
            c.fillText('7m ：', 0.25 * px, 0.075 * py);
            c.font = '16px sans-serif';
            c.fillText(L6_s1 + ' / ' + L61, 0.145 * px, 0.4 * py);
            c.fillText(L9_s1 + ' / ' + L91, 0.145 * px, 0.8 * py);
            c.fillText(C6_s1 + ' / ' + C61, 0.46 * px, 0.46 * py);
            c.fillText(C9_s1 + ' / ' + C91, 0.46 * px, 0.8 * py);
            c.fillText(R6_s1 + ' / ' + R61, 0.72 * px, 0.4 * py);
            c.fillText(R9_s1 + ' / ' + R91, 0.77 * px, 0.8 * py);

            //シュート率　(%)
            c.font = '14px sans-serif';
            if (LW1 == '0') {
                c.fillText('-- %', 0.05 * px, 0.23 * py);
            } else {
                c.fillText(LW_r1 + ' %', 0.03 * px, 0.23 * py);
            }
            if (RW1 == '0') {
                c.fillText('-- %', 0.86 * px, 0.23 * py);
            } else {
                c.fillText(RW_r1 + ' %', 0.86 * px, 0.23 * py);
            }
            if (PV1 == '0') {
                c.fillText('-- %', 0.62 * px, 0.18 * py);
            } else {
                c.fillText(PV_r1 + ' %', 0.62 * px, 0.18 * py);
            }
            if (seven1 == '0') {
                c.fillText('-- %', 0.62 * px, 0.08 * py);
            } else {
                c.fillText(seven_r1 + ' %', 0.62 * px, 0.08 * py);
            }
            //サイド以外はフォントサイズを少し大きく
            c.font = '16px sans-serif';
            if (L61 == '0') {
                c.fillText('-- %', 0.15 * px, 0.5 * py);
            } else {
                c.fillText(L6_r1 + ' %', 0.12 * px, 0.5 * py);
            }
            if (L91 == '0') {
                c.fillText('-- %', 0.14 * px, 0.9 * py);
            } else {
                c.fillText(L9_r1 + ' %', 0.12 * px, 0.9 * py);
            }
            if (C61 == '0') {
                c.fillText('-- %', 0.46 * px, 0.56 * py);
            } else {
                c.fillText(C6_r1 + ' %', 0.44 * px, 0.56 * py);
            }
            if (C91 == '0') {
                c.fillText('-- %', 0.46 * px, 0.9 * py);
            } else {
                c.fillText(C9_r1 + ' %', 0.44 * px, 0.9 * py);
            }
            if (R61 == '0') {
                c.fillText('-- %', 0.75 * px, 0.5 * py);
            } else {
                c.fillText(R6_r1 + ' %', 0.75 * px, 0.5 * py);
            }
            if (R91 == '0') {
                c.fillText('-- %', 0.77 * px, 0.9 * py);
            } else {
                c.fillText(R9_r1 + ' %', 0.75 * px, 0.9 * py);
            }
        }
    }
}

function draw_pos() {
    //***px,pyにはcanvasの縦横サイズが入る***
    var px = cv.width;
    var py = cv.height;
    //左サイド
    c.beginPath();
    c.moveTo(0, 0);
    c.lineTo(0.12 * px, 0);
    c.lineTo(0.21 * px, 0.2 * py);
    c.lineTo(0, 0.45 * py);
    // 塗りつぶしスタイルを設定
    Style(area1.LW_R, area1.LW);
    c.globalAlpha = 1.0;
    // パスに沿って塗りつぶし
    c.fill();
    c.closePath();

    //右サイド
    c.beginPath();
    c.moveTo(px, 0);
    c.lineTo(0.88 * px, 0);
    c.lineTo(0.79 * px, 0.2 * py);
    c.lineTo(px, 0.45 * py);
    // 塗りつぶしスタイルを設定
    Style(area1.RW_R, area1.RW);
    c.globalAlpha = 1.0;
    // パスに沿って塗りつぶし
    c.fill();
    c.closePath();

    //左上
    c.beginPath();
    c.moveTo(0, 0.45 * py);
    c.lineTo(0.21 * px, 0.2 * py);
    c.lineTo(0.36 * px, 0.27 * py);
    c.lineTo(0.36 * px, 0.7 * py);
    c.lineTo(0.15 * px, 0.63 * py);
    // 塗りつぶしスタイルを設定
    Style(area1.L6_R, area1.L6);
    c.globalAlpha = 1.0;
    // パスに沿って塗りつぶし
    c.fill();
    c.closePath();

    //左下
    c.beginPath();
    c.moveTo(0, 0.45 * py);
    c.lineTo(0.15 * px, 0.63 * py);
    c.lineTo(0.36 * px, 0.7 * py);
    c.lineTo(0.36 * px, py);
    c.lineTo(0, py);
    // 塗りつぶしスタイルを設定
    Style(area1.L9_R, area1.L9);
    c.globalAlpha = 1.0;
    // パスに沿って塗りつぶし
    c.fill();
    c.closePath();

    //中央上
    c.beginPath();
    c.moveTo(0.36 * px, 0.27 * py);
    c.lineTo(0.64 * px, 0.27 * py);
    c.lineTo(0.64 * px, 0.7 * py);
    c.lineTo(0.36 * px, 0.7 * py);
    // 塗りつぶしスタイルを設定
    Style(area1.C6_R, area1.C6);
    c.globalAlpha = 1.0;
    // パスに沿って塗りつぶし
    c.fill();
    c.closePath();

    //中央下
    c.beginPath();
    c.moveTo(0.36 * px, 0.7 * py);
    c.lineTo(0.64 * px, 0.7 * py);
    c.lineTo(0.64 * px, py);
    c.lineTo(0.36 * px, py);
    // 塗りつぶしスタイルを設定
    Style(area1.C9_R, area1.C9);
    c.globalAlpha = 1.0;
    // パスに沿って塗りつぶし
    c.fill();
    c.closePath();

    //右上
    c.beginPath();
    c.moveTo(px, 0.45 * py);
    c.lineTo(0.79 * px, 0.2 * py);
    c.lineTo(0.64 * px, 0.27 * py);
    c.lineTo(0.64 * px, 0.7 * py);
    c.lineTo(0.85 * px, 0.63 * py);
    // 塗りつぶしスタイルを設定
    Style(area1.R6_R, area1.R6);
    c.globalAlpha = 1.0;
    // パスに沿って塗りつぶし
    c.fill();
    c.closePath();

    //右下
    c.beginPath();
    c.moveTo(px, 0.45 * py);
    c.lineTo(0.85 * px, 0.63 * py);
    c.lineTo(0.64 * px, 0.7 * py);
    c.lineTo(0.64 * px, py);
    c.lineTo(px, py);
    // 塗りつぶしスタイルを設定
    Style(area1.R9_R, area1.R9);
    c.globalAlpha = 1.0;
    // パスに沿って塗りつぶし
    c.fill();
    c.closePath();

    //7mスロー
    // パスの開始（1）
    c.beginPath();
    // 始点／終点を設定（2）
    c.moveTo(0.21 * px, 0 * py);
    c.lineTo(0.79 * px, 0 * py);
    c.lineTo(0.79 * px, 0.09 * py);
    c.lineTo(0.21 * px, 0.09 * py);
    c.lineTo(0.21 * px, 0 * py);
    // 塗りつぶしスタイルを設定
    Style(area1.seven_R, area1.seven);
    c.linewidth = 0.5;
    c.fill();
    // パスに沿って直線を描画（3）
    c.stroke();


    //PTスロー線
    // パスの開始（1）
    c.beginPath();
    // 始点／終点を設定（2）
    c.moveTo(0.21 * px, 0.11 * py);
    c.lineTo(0.79 * px, 0.11 * py);
    c.lineTo(0.79 * px, 0.2 * py);
    c.lineTo(0.21 * px, 0.2 * py);
    c.lineTo(0.21 * px, 0.11 * py);
    // 塗りつぶしスタイルを設定
    Style(area1.PV_R, area1.PV);
    c.linewidth = 0.5;
    c.fill()
    // パスに沿って直線を描画（3）
    c.stroke();


    //領域同士の境界線
    // パスの開始（1）
    c.beginPath();
    // 始点／終点を設定（2）
    c.moveTo(0.21 * px, 0.2 * py);
    c.lineTo(0, 0.45 * py);
    c.lineTo(0.15 * px, 0.63 * py);
    c.lineTo(0.36 * px, 0.7 * py);
    c.lineTo(0.64 * px, 0.7 * py);
    c.lineTo(0.85 * px, 0.63 * py);
    c.lineTo(px, 0.45 * py);
    c.lineTo(0.79 * px, 0.2 * py);
    // 塗りつぶしスタイルを設定
    c.fillStyle = "#FFFFFF";
    c.linewidth = 0.5;
    // パスに沿って直線を描画（3）
    c.stroke();

    // パスの開始（1）
    c.beginPath();
    // 始点／終点を設定（2）
    c.moveTo(0.12 * px, 0);
    c.lineTo(0.21 * px, 0.2 * py);
    c.lineTo(0.36 * px, 0.27 * py);
    c.lineTo(0.64 * px, 0.27 * py);
    c.lineTo(0.79 * px, 0.2 * py);
    c.lineTo(0.88 * px, 0);
    // 塗りつぶしスタイルを設定
    c.fillStyle = "#FFFFFF";
    c.linewidth = 0.5;
    // パスに沿って直線を描画（3）
    c.stroke();

    // パスの開始（1）
    c.beginPath();
    // 始点／終点を設定（2）
    c.moveTo(0.36 * px, 0.27 * py);
    c.lineTo(0.36 * px, py);
    // 塗りつぶしスタイルを設定
    c.fillStyle = "#FFFFFF";
    c.linewidth = 0.5;
    // パスに沿って直線を描画（3）
    c.stroke();

    // パスの開始（1）
    c.beginPath();
    // 始点／終点を設定（2）
    c.moveTo(0.64 * px, 0.27 * py);
    c.lineTo(0.64 * px, py);
    // 塗りつぶしスタイルを設定
    c.fillStyle = "#FFFFFF";
    c.linewidth = 0.5;
    // パスに沿って直線を描画（3）
    c.stroke();
    //＊＊＊＊＊シュート数・パーセンテージを変数の式で書き換える＊＊＊＊＊
    //左サイド、右サイド、左中央右の上下の順
    //シュート成功回数 / シュート回数
    c.fillStyle = "White";
    if (isTouchDevice) {
        c.font = '10px sans-serif';
        c.fillText(area1.LW_S + ' / ' + area1.LW, 0.05 * px, 0.15 * py);
        c.fillText(area1.RW_S + ' / ' + area1.RW, 0.88 * px, 0.15 * py);
        c.fillText(area1.PV_S + ' / ' + area1.PV, 0.43 * px, 0.18 * py);
        c.fillText(area1.seven_S + ' / ' + area1.seven, 0.43 * px, 0.075 * py);
        c.fillText('PV ：', 0.25 * px, 0.18 * py);
        c.fillText('7m ：', 0.25 * px, 0.075 * py);
        c.font = '12px sans-serif';
        c.fillText(area1.L6_S + ' / ' + area1.L6, 0.145 * px, 0.4 * py);
        c.fillText(area1.L9_S + ' / ' + area1.L9, 0.145 * px, 0.8 * py);
        c.fillText(area1.C6_S + ' / ' + area1.C6, 0.46 * px, 0.46 * py);
        c.fillText(area1.C9_S + ' / ' + area1.C9, 0.46 * px, 0.8 * py);
        c.fillText(area1.R6_S + ' / ' + area1.R6, 0.72 * px, 0.4 * py);
        c.fillText(area1.R9_S + ' / ' + area1.R9, 0.77 * px, 0.8 * py);

        //シュート率　(%)
        c.font = '10px sans-serif';
        if (area1.LW == 0) {
            c.fillText('-- %', 0.05 * px, 0.23 * py);
        } else {
            c.fillText(area1.LW_R + ' %', 0.03 * px, 0.23 * py);
        }
        if (area1.RW == 0) {
            c.fillText('-- %', 0.86 * px, 0.23 * py);
        } else {
            c.fillText(area1.RW_R + ' %', 0.86 * px, 0.23 * py);
        }
        if (area1.PV == 0) {
            c.fillText('-- %', 0.62 * px, 0.18 * py);
        } else {
            c.fillText(area1.PV_R + ' %', 0.62 * px, 0.18 * py);
        }
        if (area1.seven == 0) {
            c.fillText('-- %', 0.62 * px, 0.08 * py);
        } else {
            c.fillText(area1.seven_R + ' %', 0.62 * px, 0.08 * py);
        }
        //サイド以外はフォントサイズを少し大きく
        c.font = '12px sans-serif';
        if (area1.L6 == 0) {
            c.fillText('-- %', 0.15 * px, 0.5 * py);
        } else {
            c.fillText(area1.L6_R + ' %', 0.12 * px, 0.5 * py);
        }
        if (area1.L9 == 0) {
            c.fillText('-- %', 0.14 * px, 0.9 * py);
        } else {
            c.fillText(area1.L9_R + ' %', 0.12 * px, 0.9 * py);
        }
        if (area1.C6 == 0) {
            c.fillText('-- %', 0.46 * px, 0.56 * py);
        } else {
            c.fillText(area1.C6_R + ' %', 0.44 * px, 0.56 * py);
        }
        if (area1.C9 == 0) {
            c.fillText('-- %', 0.46 * px, 0.9 * py);
        } else {
            c.fillText(area1.C9_R + ' %', 0.44 * px, 0.9 * py);
        }
        if (area1.R6 == 0) {
            c.fillText('-- %', 0.75 * px, 0.5 * py);
        } else {
            c.fillText(area1.R6_R + ' %', 0.75 * px, 0.5 * py);
        }
        if (area1.R9 == 0) {
            c.fillText('-- %', 0.77 * px, 0.9 * py);
        } else {
            c.fillText(area1.R9_R + ' %', 0.75 * px, 0.9 * py);
        }
    } else {
        c.font = '14px sans-serif';
        c.fillText(area1.W_S + ' / ' + area1.LW, 0.05 * px, 0.15 * py);
        c.fillText(area1.RW_S + ' / ' + area1.RW, 0.88 * px, 0.15 * py);
        c.fillText(area1.PV_S + ' / ' + area1.PV, 0.43 * px, 0.18 * py);
        c.fillText(area1.seven_S + ' / ' + area1.seven, 0.43 * px, 0.075 * py);
        c.fillText('PV ：', 0.25 * px, 0.18 * py);
        c.fillText('7m ：', 0.25 * px, 0.075 * py);
        c.font = '16px sans-serif';
        c.fillText(area1.L6_S + ' / ' + area1.L6, 0.145 * px, 0.4 * py);
        c.fillText(area1.L9_S + ' / ' + area1.L9, 0.145 * px, 0.8 * py);
        c.fillText(area1.C6_S + ' / ' + area1.C6, 0.46 * px, 0.46 * py);
        c.fillText(area1.C9_S + ' / ' + area1.C9, 0.46 * px, 0.8 * py);
        c.fillText(area1.R6_S + ' / ' + area1.R6, 0.72 * px, 0.4 * py);
        c.fillText(area1.R9_S + ' / ' + area1.R9, 0.77 * px, 0.8 * py);

        //シュート率　(%)
        c.font = '14px sans-serif';
        if (area1.LW == 0) {
            c.fillText('-- %', 0.05 * px, 0.23 * py);
        } else {
            c.fillText(area1.LW_R + ' %', 0.03 * px, 0.23 * py);
        }
        if (area1.RW == 0) {
            c.fillText('-- %', 0.86 * px, 0.23 * py);
        } else {
            c.fillText(area1.RW_R + ' %', 0.86 * px, 0.23 * py);
        }
        if (area1.PV == 0) {
            c.fillText('-- %', 0.62 * px, 0.18 * py);
        } else {
            c.fillText(area1.PV_R + ' %', 0.62 * px, 0.18 * py);
        }
        if (area1.seven == 0) {
            c.fillText('-- %', 0.62 * px, 0.08 * py);
        } else {
            c.fillText(area1.seven_R + ' %', 0.62 * px, 0.08 * py);
        }
        //サイド以外はフォントサイズを少し大きく
        c.font = '16px sans-serif';
        if (area1.L6 == 0) {
            c.fillText('-- %', 0.15 * px, 0.5 * py);
        } else {
            c.fillText(area1.L6_R + ' %', 0.12 * px, 0.5 * py);
        }
        if (area1.L9 == 0) {
            c.fillText('-- %', 0.14 * px, 0.9 * py);
        } else {
            c.fillText(area1.L9_R + ' %', 0.12 * px, 0.9 * py);
        }
        if (area1.C6 == 0) {
            c.fillText('-- %', 0.46 * px, 0.56 * py);
        } else {
            c.fillText(area1.C6_R + ' %', 0.44 * px, 0.56 * py);
        }
        if (area1.C9 == 0) {
            c.fillText('-- %', 0.46 * px, 0.9 * py);
        } else {
            c.fillText(area1.C9_R + ' %', 0.44 * px, 0.9 * py);
        }
        if (area1.R6 == 0) {
            c.fillText('-- %', 0.75 * px, 0.5 * py);
        } else {
            c.fillText(area1.R6_R + ' %', 0.75 * px, 0.5 * py);
        }
        if (area1.R9 == 0) {
            c.fillText('-- %', 0.77 * px, 0.9 * py);
        } else {
            c.fillText(area1.R9_R + ' %', 0.75 * px, 0.9 * py);
        }
    }
}

function draw_pos2() {
    //***px,pyにはcanvasの縦横サイズが入る***
    var px = cv2.width;
    var py = cv2.height;
    //左サイド
    c2.beginPath();
    c2.moveTo(0, 0);
    c2.lineTo(0.12 * px, 0);
    c2.lineTo(0.21 * px, 0.2 * py);
    c2.lineTo(0, 0.45 * py);
    // 塗りつぶしスタイルを設定
    Style2(area2.LW_R, area2.LW);
    c2.globalAlpha = 1.0;
    // パスに沿って塗りつぶし
    c2.fill();
    c2.closePath();

    //右サイド
    c2.beginPath();
    c2.moveTo(px, 0);
    c2.lineTo(0.88 * px, 0);
    c2.lineTo(0.79 * px, 0.2 * py);
    c2.lineTo(px, 0.45 * py);
    // 塗りつぶしスタイルを設定
    Style2(area2.RW_R, area2.RW);
    c2.globalAlpha = 1.0;
    // パスに沿って塗りつぶし
    c2.fill();
    c2.closePath();

    //左上
    c2.beginPath();
    c2.moveTo(0, 0.45 * py);
    c2.lineTo(0.21 * px, 0.2 * py);
    c2.lineTo(0.36 * px, 0.27 * py);
    c2.lineTo(0.36 * px, 0.7 * py);
    c2.lineTo(0.15 * px, 0.63 * py);
    // 塗りつぶしスタイルを設定
    Style2(area2.L6_R, area2.L6);
    c2.globalAlpha = 1.0;
    // パスに沿って塗りつぶし
    c2.fill();
    c2.closePath();

    //左下
    c2.beginPath();
    c2.moveTo(0, 0.45 * py);
    c2.lineTo(0.15 * px, 0.63 * py);
    c2.lineTo(0.36 * px, 0.7 * py);
    c2.lineTo(0.36 * px, py);
    c2.lineTo(0, py);
    // 塗りつぶしスタイルを設定
    Style2(area2.L9_R, area2.L9);
    c2.globalAlpha = 1.0;
    // パスに沿って塗りつぶし
    c2.fill();
    c2.closePath();

    //中央上
    c2.beginPath();
    c2.moveTo(0.36 * px, 0.27 * py);
    c2.lineTo(0.64 * px, 0.27 * py);
    c2.lineTo(0.64 * px, 0.7 * py);
    c2.lineTo(0.36 * px, 0.7 * py);
    // 塗りつぶしスタイルを設定
    Style2(area2.C6_R, area2.C6);
    c2.globalAlpha = 1.0;
    // パスに沿って塗りつぶし
    c2.fill();
    c2.closePath();

    //中央下
    c2.beginPath();
    c2.moveTo(0.36 * px, 0.7 * py);
    c2.lineTo(0.64 * px, 0.7 * py);
    c2.lineTo(0.64 * px, py);
    c2.lineTo(0.36 * px, py);
    // 塗りつぶしスタイルを設定
    Style2(area2.C9_R, area2.C9);
    c2.globalAlpha = 1.0;
    // パスに沿って塗りつぶし
    c2.fill();
    c2.closePath();

    //右上
    c2.beginPath();
    c2.moveTo(px, 0.45 * py);
    c2.lineTo(0.79 * px, 0.2 * py);
    c2.lineTo(0.64 * px, 0.27 * py);
    c2.lineTo(0.64 * px, 0.7 * py);
    c2.lineTo(0.85 * px, 0.63 * py);
    // 塗りつぶしスタイルを設定
    Style2(area2.R6_R, area2.R6);
    c2.globalAlpha = 1.0;
    // パスに沿って塗りつぶし
    c2.fill();
    c2.closePath();

    //右下
    c2.beginPath();
    c2.moveTo(px, 0.45 * py);
    c2.lineTo(0.85 * px, 0.63 * py);
    c2.lineTo(0.64 * px, 0.7 * py);
    c2.lineTo(0.64 * px, py);
    c2.lineTo(px, py);
    // 塗りつぶしスタイルを設定
    Style2(area2.R9_R, area2.R9);
    c2.globalAlpha = 1.0;
    // パスに沿って塗りつぶし
    c2.fill();
    c2.closePath();

    //7mスロー
    // パスの開始（1）
    c2.beginPath();
    // 始点／終点を設定（2）
    c2.moveTo(0.21 * px, 0 * py);
    c2.lineTo(0.79 * px, 0 * py);
    c2.lineTo(0.79 * px, 0.09 * py);
    c2.lineTo(0.21 * px, 0.09 * py);
    c2.lineTo(0.21 * px, 0 * py);
    // 塗りつぶしスタイルを設定
    Style2(area2.seven_R, area2.seven);
    c2.linewidth = 0.5;
    c2.fill();
    // パスに沿って直線を描画（3）
    c2.stroke();


    //PTスロー線
    // パスの開始（1）
    c2.beginPath();
    // 始点／終点を設定（2）
    c2.moveTo(0.21 * px, 0.11 * py);
    c2.lineTo(0.79 * px, 0.11 * py);
    c2.lineTo(0.79 * px, 0.2 * py);
    c2.lineTo(0.21 * px, 0.2 * py);
    c2.lineTo(0.21 * px, 0.11 * py);
    // 塗りつぶしスタイルを設定
    Style2(area2.PV_R, area2.PV);
    c2.linewidth = 0.5;
    c2.fill()
    // パスに沿って直線を描画（3）
    c2.stroke();


    //領域同士の境界線
    // パスの開始（1）
    c2.beginPath();
    // 始点／終点を設定（2）
    c2.moveTo(0.21 * px, 0.2 * py);
    c2.lineTo(0, 0.45 * py);
    c2.lineTo(0.15 * px, 0.63 * py);
    c2.lineTo(0.36 * px, 0.7 * py);
    c2.lineTo(0.64 * px, 0.7 * py);
    c2.lineTo(0.85 * px, 0.63 * py);
    c2.lineTo(px, 0.45 * py);
    c2.lineTo(0.79 * px, 0.2 * py);
    // 塗りつぶしスタイルを設定
    c2.fillStyle = "#FFFFFF";
    c2.linewidth = 0.5;
    // パスに沿って直線を描画（3）
    c2.stroke();

    // パスの開始（1）
    c2.beginPath();
    // 始点／終点を設定（2）
    c2.moveTo(0.12 * px, 0);
    c2.lineTo(0.21 * px, 0.2 * py);
    c2.lineTo(0.36 * px, 0.27 * py);
    c2.lineTo(0.64 * px, 0.27 * py);
    c2.lineTo(0.79 * px, 0.2 * py);
    c2.lineTo(0.88 * px, 0);
    // 塗りつぶしスタイルを設定
    c2.fillStyle = "#FFFFFF";
    c2.linewidth = 0.5;
    // パスに沿って直線を描画（3）
    c2.stroke();

    // パスの開始（1）
    c2.beginPath();
    // 始点／終点を設定（2）
    c2.moveTo(0.36 * px, 0.27 * py);
    c2.lineTo(0.36 * px, py);
    // 塗りつぶしスタイルを設定
    c2.fillStyle = "#FFFFFF";
    c2.linewidth = 0.5;
    // パスに沿って直線を描画（3）
    c2.stroke();

    // パスの開始（1）
    c2.beginPath();
    // 始点／終点を設定（2）
    c2.moveTo(0.64 * px, 0.27 * py);
    c2.lineTo(0.64 * px, py);
    // 塗りつぶしスタイルを設定
    c2.fillStyle = "#FFFFFF";
    c2.linewidth = 0.5;
    // パスに沿って直線を描画（3）
    c2.stroke();
    //＊＊＊＊＊シュート数・パーセンテージを変数の式で書き換える＊＊＊＊＊
    //左サイド、右サイド、左中央右の上下の順
    //シュート成功回数 / シュート回数
    c2.fillStyle = "White";
    if (isTouchDevice) {
        c2.font = '10px sans-serif';
        c2.fillText(area2.LW_S + ' / ' + area2.LW, 0.05 * px, 0.15 * py);
        c2.fillText(area2.RW_S + ' / ' + area2.RW, 0.88 * px, 0.15 * py);
        c2.fillText('PV ：', 0.25 * px, 0.18 * py);
        c2.fillText('7m ：', 0.25 * px, 0.075 * py);
        c2.fillText(area2.PV_S + ' / ' + area2.PV, 0.43 * px, 0.18 * py);
        c2.fillText(area2.seven_S + ' / ' + area2.seven, 0.43 * px, 0.075 * py);
        c2.font = '12px sans-serif';
        c2.fillText(area2.L6_S + ' / ' + area2.L6, 0.145 * px, 0.4 * py);
        c2.fillText(area2.L9_S + ' / ' + area2.L9, 0.145 * px, 0.8 * py);
        c2.fillText(area2.C6_S + ' / ' + area2.C6, 0.46 * px, 0.46 * py);
        c2.fillText(area2.C9_S + ' / ' + area2.C9, 0.46 * px, 0.8 * py);
        c2.fillText(area2.R6_S + ' / ' + area2.R6, 0.72 * px, 0.4 * py);
        c2.fillText(area2.R9_S + ' / ' + area2.R9, 0.77 * px, 0.8 * py);

        //シュート率　(%)
        c2.font = '10px sans-serif';
        if (area2.LW == 0) {
            c2.fillText('-- %', 0.05 * px, 0.23 * py);
        } else {
            c2.fillText(area2.LW_R + ' %', 0.03 * px, 0.23 * py);
        }
        if (area2.RW == 0) {
            c2.fillText('-- %', 0.86 * px, 0.23 * py);
        } else {
            c2.fillText(area2.RW_R + ' %', 0.86 * px, 0.23 * py);
        }
        if (area2.PV == 0) {
            c2.fillText('-- %', 0.62 * px, 0.18 * py);
        } else {
            c2.fillText(area2.PV_R + ' %', 0.62 * px, 0.18 * py);
        }
        if (area2.seven == 0) {
            c2.fillText('-- %', 0.62 * px, 0.08 * py);
        } else {
            c2.fillText(area2.seven_R + ' %', 0.62 * px, 0.075 * py);
        }
        //サイド以外はフォントサイズを少し大きく
        c2.font = '12px sans-serif';
        if (area2.L6 == 0) {
            c2.fillText('-- %', 0.15 * px, 0.5 * py);
        } else {
            c2.fillText(area2.L6_R + ' %', 0.12 * px, 0.5 * py);
        }
        if (area2.L9 == 0) {
            c2.fillText('-- %', 0.14 * px, 0.9 * py);
        } else {
            c2.fillText(area2.L9_R + ' %', 0.12 * px, 0.9 * py);
        }
        if (area2.C6 == 0) {
            c2.fillText('-- %', 0.46 * px, 0.56 * py);
        } else {
            c2.fillText(area2.C6_R + ' %', 0.44 * px, 0.56 * py);
        }
        if (area2.C9 == 0) {
            c2.fillText('-- %', 0.46 * px, 0.9 * py);
        } else {
            c2.fillText(area2.C9_R + ' %', 0.44 * px, 0.9 * py);
        }
        if (area2.R6 == 0) {
            c2.fillText('-- %', 0.75 * px, 0.5 * py);
        } else {
            c2.fillText(area2.R6_R + ' %', 0.75 * px, 0.5 * py);
        }
        if (area2.R9 == 0) {
            c2.fillText('-- %', 0.77 * px, 0.9 * py);
        } else {
            c2.fillText(area2.R9_R + ' %', 0.75 * px, 0.9 * py);
        }
    } else {
        c2.font = '14px sans-serif';
        c2.fillText(area2.LW_S + ' / ' + area2.LW, 0.05 * px, 0.15 * py);
        c2.fillText(area2.RW_S + ' / ' + area2.W, 0.88 * px, 0.15 * py);
        c2.fillText(area2.PV_S + ' / ' + area2.PV, 0.43 * px, 0.18 * py);
        c2.fillText(area2.seven_S + ' / ' + area2.seven, 0.43 * px, 0.075 * py);
        c2.fillText('PV ：', 0.25 * px, 0.18 * py);
        c2.fillText('7m ：', 0.25 * px, 0.075 * py);
        c2.font = '16px sans-serif';
        c2.fillText(area2.L6_S + ' / ' + area2.L6, 0.145 * px, 0.4 * py);
        c2.fillText(area2.L9_S + ' / ' + area2.L9, 0.145 * px, 0.8 * py);
        c2.fillText(area2.C6_S + ' / ' + area2.C6, 0.46 * px, 0.46 * py);
        c2.fillText(area2.C9_S + ' / ' + area2.C9, 0.46 * px, 0.8 * py);
        c2.fillText(area2.R6_S + ' / ' + area2.R6, 0.72 * px, 0.4 * py);
        c2.fillText(area2.R9_S + ' / ' + area2.R9, 0.77 * px, 0.8 * py);

        //シュート率　(%)
        c2.font = '14px sans-serif';
        if (area2.LW == 0) {
            c2.fillText('-- %', 0.05 * px, 0.23 * py);
        } else {
            c2.fillText(area2.LW_R + ' %', 0.03 * px, 0.23 * py);
        }
        if (area2.RW == 0) {
            c2.fillText('-- %', 0.86 * px, 0.23 * py);
        } else {
            c2.fillText(area2.RW_R + ' %', 0.86 * px, 0.23 * py);
        }
        if (area2.PV == 0) {
            c2.fillText('-- %', 0.62 * px, 0.18 * py);
        } else {
            c2.fillText(area2.PV_R + ' %', 0.62 * px, 0.18 * py);
        }
        if (area2.seven == 0) {
            c2.fillText('-- %', 0.62 * px, 0.08 * py);
        } else {
            c2.fillText(area2.seven_R + ' %', 0.62 * px, 0.075 * py);
        }
        //サイド以外はフォントサイズを少し大きく
        c2.font = '16px sans-serif';
        if (area2.L6 == 0) {
            c2.fillText('-- %', 0.15 * px, 0.5 * py);
        } else {
            c2.fillText(area2.L6_R + ' %', 0.12 * px, 0.5 * py);
        }
        if (area2.L9 == 0) {
            c2.fillText('-- %', 0.14 * px, 0.9 * py);
        } else {
            c2.fillText(area2.L9_R + ' %', 0.12 * px, 0.9 * py);
        }
        if (area2.C6 == 0) {
            c2.fillText('-- %', 0.46 * px, 0.56 * py);
        } else {
            c2.fillText(area2.C6_R + ' %', 0.44 * px, 0.56 * py);
        }
        if (area2.C9 == 0) {
            c2.fillText('-- %', 0.46 * px, 0.9 * py);
        } else {
            c2.fillText(area2.C9_R + ' %', 0.44 * px, 0.9 * py);
        }
        if (area2.R6 == 0) {
            c2.fillText('-- %', 0.75 * px, 0.5 * py);
        } else {
            c2.fillText(area2.R6_R + ' %', 0.75 * px, 0.5 * py);
        }
        if (area2.R9 == 0) {
            c2.fillText('-- %', 0.77 * px, 0.9 * py);
        } else {
            c2.fillText(area2.R9_R + ' %', 0.75 * px, 0.9 * py);
        }
    }
}

function draw(team, LW, PV, RW, L6, C6, R6, L9, C9, R9, seven, LW_S, PV_S, RW_S, L6_S, C6_S, R6_S, L9_S, C9_S, R9_S, seven_S, LW_R, PV_R, RW_R, L6_R, C6_R, R6_R, L9_R, C9_R, R9_R, seven_R) {
    //***px,pyにはcanvasの縦横サイズが入る***
    var px = cv.width;
    var py = cv.height;
    if (team == 1) {
        //左サイド
        c.beginPath();
        c.moveTo(0, 0);
        c.lineTo(0.12 * px, 0);
        c.lineTo(0.21 * px, 0.2 * py);
        c.lineTo(0, 0.45 * py);
        // 塗りつぶしスタイルを設定
        Style(LW_R, LW);
        c.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c.fill();
        c.closePath();

        //右サイド
        c.beginPath();
        c.moveTo(px, 0);
        c.lineTo(0.88 * px, 0);
        c.lineTo(0.79 * px, 0.2 * py);
        c.lineTo(px, 0.45 * py);
        // 塗りつぶしスタイルを設定
        Style(RW_R, RW);
        c.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c.fill();
        c.closePath();

        //左上
        c.beginPath();
        c.moveTo(0, 0.45 * py);
        c.lineTo(0.21 * px, 0.2 * py);
        c.lineTo(0.36 * px, 0.27 * py);
        c.lineTo(0.36 * px, 0.7 * py);
        c.lineTo(0.15 * px, 0.63 * py);
        // 塗りつぶしスタイルを設定
        Style(L6_R, L6);
        c.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c.fill();
        c.closePath();

        //左下
        c.beginPath();
        c.moveTo(0, 0.45 * py);
        c.lineTo(0.15 * px, 0.63 * py);
        c.lineTo(0.36 * px, 0.7 * py);
        c.lineTo(0.36 * px, py);
        c.lineTo(0, py);
        // 塗りつぶしスタイルを設定
        Style(L9_R, L9);
        c.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c.fill();
        c.closePath();

        //中央上
        c.beginPath();
        c.moveTo(0.36 * px, 0.27 * py);
        c.lineTo(0.64 * px, 0.27 * py);
        c.lineTo(0.64 * px, 0.7 * py);
        c.lineTo(0.36 * px, 0.7 * py);
        // 塗りつぶしスタイルを設定
        Style(C6_R, C6);
        c.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c.fill();
        c.closePath();

        //中央下
        c.beginPath();
        c.moveTo(0.36 * px, 0.7 * py);
        c.lineTo(0.64 * px, 0.7 * py);
        c.lineTo(0.64 * px, py);
        c.lineTo(0.36 * px, py);
        // 塗りつぶしスタイルを設定
        Style(C9_R, C9);
        c.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c.fill();
        c.closePath();

        //右上
        c.beginPath();
        c.moveTo(px, 0.45 * py);
        c.lineTo(0.79 * px, 0.2 * py);
        c.lineTo(0.64 * px, 0.27 * py);
        c.lineTo(0.64 * px, 0.7 * py);
        c.lineTo(0.85 * px, 0.63 * py);
        // 塗りつぶしスタイルを設定
        Style(R6_R, R6);
        c.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c.fill();
        c.closePath();

        //右下
        c.beginPath();
        c.moveTo(px, 0.45 * py);
        c.lineTo(0.85 * px, 0.63 * py);
        c.lineTo(0.64 * px, 0.7 * py);
        c.lineTo(0.64 * px, py);
        c.lineTo(px, py);
        // 塗りつぶしスタイルを設定
        Style(R9_R, R9);
        c.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c.fill();
        c.closePath();

        //7mスロー
        // パスの開始（1）
        c.beginPath();
        // 始点／終点を設定（2）
        c.moveTo(0.21 * px, 0 * py);
        c.lineTo(0.79 * px, 0 * py);
        c.lineTo(0.79 * px, 0.09 * py);
        c.lineTo(0.21 * px, 0.09 * py);
        c.lineTo(0.21 * px, 0 * py);
        // 塗りつぶしスタイルを設定
        Style(seven_R, seven);
        c.linewidth = 0.5;
        c.fill();
        // パスに沿って直線を描画（3）
        c.stroke();


        //PTスロー線
        // パスの開始（1）
        c.beginPath();
        // 始点／終点を設定（2）
        c.moveTo(0.21 * px, 0.11 * py);
        c.lineTo(0.79 * px, 0.11 * py);
        c.lineTo(0.79 * px, 0.2 * py);
        c.lineTo(0.21 * px, 0.2 * py);
        c.lineTo(0.21 * px, 0.11 * py);
        // 塗りつぶしスタイルを設定
        Style(PV_R, PV);
        c.linewidth = 0.5;
        c.fill()
        // パスに沿って直線を描画（3）
        c.stroke();


        //領域同士の境界線
        // パスの開始（1）
        c.beginPath();
        // 始点／終点を設定（2）
        c.moveTo(0.21 * px, 0.2 * py);
        c.lineTo(0, 0.45 * py);
        c.lineTo(0.15 * px, 0.63 * py);
        c.lineTo(0.36 * px, 0.7 * py);
        c.lineTo(0.64 * px, 0.7 * py);
        c.lineTo(0.85 * px, 0.63 * py);
        c.lineTo(px, 0.45 * py);
        c.lineTo(0.79 * px, 0.2 * py);
        // 塗りつぶしスタイルを設定
        c.fillStyle = "#FFFFFF";
        c.linewidth = 0.5;
        // パスに沿って直線を描画（3）
        c.stroke();

        // パスの開始（1）
        c.beginPath();
        // 始点／終点を設定（2）
        c.moveTo(0.12 * px, 0);
        c.lineTo(0.21 * px, 0.2 * py);
        c.lineTo(0.36 * px, 0.27 * py);
        c.lineTo(0.64 * px, 0.27 * py);
        c.lineTo(0.79 * px, 0.2 * py);
        c.lineTo(0.88 * px, 0);
        // 塗りつぶしスタイルを設定
        c.fillStyle = "#FFFFFF";
        c.linewidth = 0.5;
        // パスに沿って直線を描画（3）
        c.stroke();

        // パスの開始（1）
        c.beginPath();
        // 始点／終点を設定（2）
        c.moveTo(0.36 * px, 0.27 * py);
        c.lineTo(0.36 * px, py);
        // 塗りつぶしスタイルを設定
        c.fillStyle = "#FFFFFF";
        c.linewidth = 0.5;
        // パスに沿って直線を描画（3）
        c.stroke();

        // パスの開始（1）
        c.beginPath();
        // 始点／終点を設定（2）
        c.moveTo(0.64 * px, 0.27 * py);
        c.lineTo(0.64 * px, py);
        // 塗りつぶしスタイルを設定
        c.fillStyle = "#FFFFFF";
        c.linewidth = 0.5;
        // パスに沿って直線を描画（3）
        c.stroke();
        //＊＊＊＊＊シュート数・パーセンテージを変数の式で書き換える＊＊＊＊＊
        //左サイド、右サイド、左中央右の上下の順
        //シュート成功回数 / シュート回数
        c.fillStyle = "White";
        if (isTouchDevice) {
            c.font = '10px sans-serif';
            c.fillText(LW_S + ' / ' + LW, 0.05 * px, 0.15 * py);
            c.fillText(RW_S + ' / ' + RW, 0.88 * px, 0.15 * py);
            c.fillText(PV_S + ' / ' + PV, 0.43 * px, 0.18 * py);
            c.fillText(seven_S + ' / ' + seven, 0.43 * px, 0.075 * py);
            c.fillText('PV ：', 0.25 * px, 0.18 * py);
            c.fillText('7m ：', 0.25 * px, 0.075 * py);
            c.font = '12px sans-serif';
            c.fillText(L6_S + ' / ' + L6, 0.145 * px, 0.4 * py);
            c.fillText(L9_S + ' / ' + L9, 0.145 * px, 0.8 * py);
            c.fillText(C6_S + ' / ' + C6, 0.46 * px, 0.46 * py);
            c.fillText(C9_S + ' / ' + C9, 0.46 * px, 0.8 * py);
            c.fillText(R6_S + ' / ' + R6, 0.72 * px, 0.4 * py);
            c.fillText(R9_S + ' / ' + R9, 0.77 * px, 0.8 * py);

            //シュート率　(%)
            c.font = '10px sans-serif';
            if (LW == 0) {
                c.fillText('-- %', 0.05 * px, 0.23 * py);
            } else {
                c.fillText(LW_R + ' %', 0.03 * px, 0.23 * py);
            }
            if (RW == 0) {
                c.fillText('-- %', 0.86 * px, 0.23 * py);
            } else {
                c.fillText(RW_R + ' %', 0.86 * px, 0.23 * py);
            }
            if (PV == 0) {
                c.fillText('-- %', 0.62 * px, 0.18 * py);
            } else {
                c.fillText(PV_R + ' %', 0.62 * px, 0.18 * py);
            }
            if (seven == 0) {
                c.fillText('-- %', 0.62 * px, 0.08 * py);
            } else {
                c.fillText(seven_R + ' %', 0.62 * px, 0.08 * py);
            }
            //サイド以外はフォントサイズを少し大きく
            c.font = '12px sans-serif';
            if (L6 == 0) {
                c.fillText('-- %', 0.15 * px, 0.5 * py);
            } else {
                c.fillText(L6_R + ' %', 0.12 * px, 0.5 * py);
            }
            if (L9 == 0) {
                c.fillText('-- %', 0.14 * px, 0.9 * py);
            } else {
                c.fillText(L9_R + ' %', 0.12 * px, 0.9 * py);
            }
            if (C6 == 0) {
                c.fillText('-- %', 0.46 * px, 0.56 * py);
            } else {
                c.fillText(C6_R + ' %', 0.44 * px, 0.56 * py);
            }
            if (C9 == 0) {
                c.fillText('-- %', 0.46 * px, 0.9 * py);
            } else {
                c.fillText(C9_R + ' %', 0.44 * px, 0.9 * py);
            }
            if (R6 == 0) {
                c.fillText('-- %', 0.75 * px, 0.5 * py);
            } else {
                c.fillText(R6_R + ' %', 0.75 * px, 0.5 * py);
            }
            if (R9 == 0) {
                c.fillText('-- %', 0.77 * px, 0.9 * py);
            } else {
                c.fillText(R9_R + ' %', 0.75 * px, 0.9 * py);
            }
        } else {
            c.font = '14px sans-serif';
            c.fillText(LW_S + ' / ' + LW, 0.05 * px, 0.15 * py);
            c.fillText(RW_S + ' / ' + RW, 0.88 * px, 0.15 * py);
            c.fillText(PV_S + ' / ' + PV, 0.43 * px, 0.18 * py);
            c.fillText(seven_S + ' / ' + seven, 0.43 * px, 0.075 * py);
            c.fillText('PV ：', 0.25 * px, 0.18 * py);
            c.fillText('7m ：', 0.25 * px, 0.075 * py);
            c.font = '16px sans-serif';
            c.fillText(L6_S + ' / ' + L6, 0.145 * px, 0.4 * py);
            c.fillText(L9_S + ' / ' + L9, 0.145 * px, 0.8 * py);
            c.fillText(C6_S + ' / ' + C6, 0.46 * px, 0.46 * py);
            c.fillText(C9_S + ' / ' + C9, 0.46 * px, 0.8 * py);
            c.fillText(R6_S + ' / ' + R6, 0.72 * px, 0.4 * py);
            c.fillText(R9_S + ' / ' + R9, 0.77 * px, 0.8 * py);

            //シュート率　(%)
            c.font = '14px sans-serif';
            if (LW == 0) {
                c.fillText('-- %', 0.05 * px, 0.23 * py);
            } else {
                c.fillText(LW_R + ' %', 0.03 * px, 0.23 * py);
            }
            if (RW == 0) {
                c.fillText('-- %', 0.86 * px, 0.23 * py);
            } else {
                c.fillText(RW_R + ' %', 0.86 * px, 0.23 * py);
            }
            if (PV == 0) {
                c.fillText('-- %', 0.62 * px, 0.18 * py);
            } else {
                c.fillText(PV_R + ' %', 0.62 * px, 0.18 * py);
            }
            if (seven == 0) {
                c.fillText('-- %', 0.62 * px, 0.08 * py);
            } else {
                c.fillText(seven_R + ' %', 0.62 * px, 0.08 * py);
            }
            //サイド以外はフォントサイズを少し大きく
            c.font = '16px sans-serif';
            if (L6 == 0) {
                c.fillText('-- %', 0.15 * px, 0.5 * py);
            } else {
                c.fillText(L6_R + ' %', 0.12 * px, 0.5 * py);
            }
            if (L9 == 0) {
                c.fillText('-- %', 0.14 * px, 0.9 * py);
            } else {
                c.fillText(L9_R + ' %', 0.12 * px, 0.9 * py);
            }
            if (C6 == 0) {
                c.fillText('-- %', 0.46 * px, 0.56 * py);
            } else {
                c.fillText(C6_R + ' %', 0.44 * px, 0.56 * py);
            }
            if (C9 == 0) {
                c.fillText('-- %', 0.46 * px, 0.9 * py);
            } else {
                c.fillText(C9_R + ' %', 0.44 * px, 0.9 * py);
            }
            if (R6 == 0) {
                c.fillText('-- %', 0.75 * px, 0.5 * py);
            } else {
                c.fillText(R6_R + ' %', 0.75 * px, 0.5 * py);
            }
            if (R9 == 0) {
                c.fillText('-- %', 0.77 * px, 0.9 * py);
            } else {
                c.fillText(R9_R + ' %', 0.75 * px, 0.9 * py);
            }
        }
    } else {
        //左サイド
        c2.beginPath();
        c2.moveTo(0, 0);
        c2.lineTo(0.12 * px, 0);
        c2.lineTo(0.21 * px, 0.2 * py);
        c2.lineTo(0, 0.45 * py);
        // 塗りつぶしスタイルを設定
        Style2(LW_R, LW);
        c2.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c2.fill();
        c2.closePath();

        //右サイド
        c2.beginPath();
        c2.moveTo(px, 0);
        c2.lineTo(0.88 * px, 0);
        c2.lineTo(0.79 * px, 0.2 * py);
        c2.lineTo(px, 0.45 * py);
        // 塗りつぶしスタイルを設定
        Style2(RW_R, RW);
        c2.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c2.fill();
        c2.closePath();

        //左上
        c2.beginPath();
        c2.moveTo(0, 0.45 * py);
        c2.lineTo(0.21 * px, 0.2 * py);
        c2.lineTo(0.36 * px, 0.27 * py);
        c2.lineTo(0.36 * px, 0.7 * py);
        c2.lineTo(0.15 * px, 0.63 * py);
        // 塗りつぶしスタイルを設定
        Style2(L6_R, L6);
        c2.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c2.fill();
        c2.closePath();

        //左下
        c2.beginPath();
        c2.moveTo(0, 0.45 * py);
        c2.lineTo(0.15 * px, 0.63 * py);
        c2.lineTo(0.36 * px, 0.7 * py);
        c2.lineTo(0.36 * px, py);
        c2.lineTo(0, py);
        // 塗りつぶしスタイルを設定
        Style2(L9_R, L9);
        c2.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c2.fill();
        c2.closePath();

        //中央上
        c2.beginPath();
        c2.moveTo(0.36 * px, 0.27 * py);
        c2.lineTo(0.64 * px, 0.27 * py);
        c2.lineTo(0.64 * px, 0.7 * py);
        c2.lineTo(0.36 * px, 0.7 * py);
        // 塗りつぶしスタイルを設定
        Style2(C6_R, C6);
        c2.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c2.fill();
        c2.closePath();

        //中央下
        c2.beginPath();
        c2.moveTo(0.36 * px, 0.7 * py);
        c2.lineTo(0.64 * px, 0.7 * py);
        c2.lineTo(0.64 * px, py);
        c2.lineTo(0.36 * px, py);
        // 塗りつぶしスタイルを設定
        Style2(C9_R, C9);
        c2.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c2.fill();
        c2.closePath();

        //右上
        c2.beginPath();
        c2.moveTo(px, 0.45 * py);
        c2.lineTo(0.79 * px, 0.2 * py);
        c2.lineTo(0.64 * px, 0.27 * py);
        c2.lineTo(0.64 * px, 0.7 * py);
        c2.lineTo(0.85 * px, 0.63 * py);
        // 塗りつぶしスタイルを設定
        Style2(R6_R, R6);
        c2.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c2.fill();
        c2.closePath();

        //右下
        c2.beginPath();
        c2.moveTo(px, 0.45 * py);
        c2.lineTo(0.85 * px, 0.63 * py);
        c2.lineTo(0.64 * px, 0.7 * py);
        c2.lineTo(0.64 * px, py);
        c2.lineTo(px, py);
        // 塗りつぶしスタイルを設定
        Style2(R9_R, R9);
        c2.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c2.fill();
        c2.closePath();

        //7mスロー
        // パスの開始（1）
        c2.beginPath();
        // 始点／終点を設定（2）
        c2.moveTo(0.21 * px, 0 * py);
        c2.lineTo(0.79 * px, 0 * py);
        c2.lineTo(0.79 * px, 0.09 * py);
        c2.lineTo(0.21 * px, 0.09 * py);
        c2.lineTo(0.21 * px, 0 * py);
        // 塗りつぶしスタイルを設定
        Style2(seven_R, seven);
        c2.linewidth = 0.5;
        c2.fill();
        // パスに沿って直線を描画（3）
        c2.stroke();


        //PTスロー線
        // パスの開始（1）
        c2.beginPath();
        // 始点／終点を設定（2）
        c2.moveTo(0.21 * px, 0.11 * py);
        c2.lineTo(0.79 * px, 0.11 * py);
        c2.lineTo(0.79 * px, 0.2 * py);
        c2.lineTo(0.21 * px, 0.2 * py);
        c2.lineTo(0.21 * px, 0.11 * py);
        // 塗りつぶしスタイルを設定
        Style2(PV_R, PV);
        c2.linewidth = 0.5;
        c2.fill()
        // パスに沿って直線を描画（3）
        c2.stroke();


        //領域同士の境界線
        // パスの開始（1）
        c2.beginPath();
        // 始点／終点を設定（2）
        c2.moveTo(0.21 * px, 0.2 * py);
        c2.lineTo(0, 0.45 * py);
        c2.lineTo(0.15 * px, 0.63 * py);
        c2.lineTo(0.36 * px, 0.7 * py);
        c2.lineTo(0.64 * px, 0.7 * py);
        c2.lineTo(0.85 * px, 0.63 * py);
        c2.lineTo(px, 0.45 * py);
        c2.lineTo(0.79 * px, 0.2 * py);
        // 塗りつぶしスタイルを設定
        c2.fillStyle = "#FFFFFF";
        c2.linewidth = 0.5;
        // パスに沿って直線を描画（3）
        c2.stroke();

        // パスの開始（1）
        c2.beginPath();
        // 始点／終点を設定（2）
        c2.moveTo(0.12 * px, 0);
        c2.lineTo(0.21 * px, 0.2 * py);
        c2.lineTo(0.36 * px, 0.27 * py);
        c2.lineTo(0.64 * px, 0.27 * py);
        c2.lineTo(0.79 * px, 0.2 * py);
        c2.lineTo(0.88 * px, 0);
        // 塗りつぶしスタイルを設定
        c2.fillStyle = "#FFFFFF";
        c2.linewidth = 0.5;
        // パスに沿って直線を描画（3）
        c2.stroke();

        // パスの開始（1）
        c2.beginPath();
        // 始点／終点を設定（2）
        c2.moveTo(0.36 * px, 0.27 * py);
        c2.lineTo(0.36 * px, py);
        // 塗りつぶしスタイルを設定
        c2.fillStyle = "#FFFFFF";
        c2.linewidth = 0.5;
        // パスに沿って直線を描画（3）
        c2.stroke();

        // パスの開始（1）
        c2.beginPath();
        // 始点／終点を設定（2）
        c2.moveTo(0.64 * px, 0.27 * py);
        c2.lineTo(0.64 * px, py);
        // 塗りつぶしスタイルを設定
        c2.fillStyle = "#FFFFFF";
        c2.linewidth = 0.5;
        // パスに沿って直線を描画（3）
        c2.stroke();
        //＊＊＊＊＊シュート数・パーセンテージを変数の式で書き換える＊＊＊＊＊
        //左サイド、右サイド、左中央右の上下の順
        //シュート成功回数 / シュート回数
        c2.fillStyle = "White";
        if (isTouchDevice) {
            c2.font = '10px sans-serif';
            c2.fillText(LW_S + ' / ' + LW, 0.05 * px, 0.15 * py);
            c2.fillText(RW_S + ' / ' + RW, 0.88 * px, 0.15 * py);
            c2.fillText('PV ：', 0.25 * px, 0.18 * py);
            c2.fillText('7m ：', 0.25 * px, 0.075 * py);
            c2.fillText(PV_S + ' / ' + PV, 0.43 * px, 0.18 * py);
            c2.fillText(seven_S + ' / ' + seven, 0.43 * px, 0.075 * py);
            c2.font = '12px sans-serif';
            c2.fillText(L6_S + ' / ' + L6, 0.145 * px, 0.4 * py);
            c2.fillText(L9_S + ' / ' + L9, 0.145 * px, 0.8 * py);
            c2.fillText(C6_S + ' / ' + C6, 0.46 * px, 0.46 * py);
            c2.fillText(C9_S + ' / ' + C9, 0.46 * px, 0.8 * py);
            c2.fillText(R6_S + ' / ' + R6, 0.72 * px, 0.4 * py);
            c2.fillText(R9_S + ' / ' + R9, 0.77 * px, 0.8 * py);

            //シュート率　(%)
            c2.font = '10px sans-serif';
            if (LW == 0) {
                c2.fillText('-- %', 0.05 * px, 0.23 * py);
            } else {
                c2.fillText(LW_R + ' %', 0.03 * px, 0.23 * py);
            }
            if (RW == 0) {
                c2.fillText('-- %', 0.86 * px, 0.23 * py);
            } else {
                c2.fillText(RW_R + ' %', 0.86 * px, 0.23 * py);
            }
            if (PV == 0) {
                c2.fillText('-- %', 0.62 * px, 0.18 * py);
            } else {
                c2.fillText(PV_R + ' %', 0.62 * px, 0.18 * py);
            }
            if (seven == 0) {
                c2.fillText('-- %', 0.62 * px, 0.08 * py);
            } else {
                c2.fillText(seven_R + ' %', 0.62 * px, 0.075 * py);
            }
            //サイド以外はフォントサイズを少し大きく
            c2.font = '12px sans-serif';
            if (L6 == 0) {
                c2.fillText('-- %', 0.15 * px, 0.5 * py);
            } else {
                c2.fillText(L6_R + ' %', 0.12 * px, 0.5 * py);
            }
            if (L9 == 0) {
                c2.fillText('-- %', 0.14 * px, 0.9 * py);
            } else {
                c2.fillText(L9_R + ' %', 0.12 * px, 0.9 * py);
            }
            if (C6 == 0) {
                c2.fillText('-- %', 0.46 * px, 0.56 * py);
            } else {
                c2.fillText(C6_R + ' %', 0.44 * px, 0.56 * py);
            }
            if (C9 == 0) {
                c2.fillText('-- %', 0.46 * px, 0.9 * py);
            } else {
                c2.fillText(C9_R + ' %', 0.44 * px, 0.9 * py);
            }
            if (R6 == 0) {
                c2.fillText('-- %', 0.75 * px, 0.5 * py);
            } else {
                c2.fillText(R6_R + ' %', 0.75 * px, 0.5 * py);
            }
            if (R9 == 0) {
                c2.fillText('-- %', 0.77 * px, 0.9 * py);
            } else {
                c2.fillText(R9_R + ' %', 0.75 * px, 0.9 * py);
            }
        } else {
            c2.font = '14px sans-serif';
            c2.fillText(LW_S + ' / ' + LW, 0.05 * px, 0.15 * py);
            c2.fillText(RW_S + ' / ' + RW, 0.88 * px, 0.15 * py);
            c2.fillText(PV_S + ' / ' + PV, 0.43 * px, 0.18 * py);
            c2.fillText(seven_S + ' / ' + seven, 0.43 * px, 0.075 * py);
            c2.fillText('PV ：', 0.25 * px, 0.18 * py);
            c2.fillText('7m ：', 0.25 * px, 0.075 * py);
            c2.font = '16px sans-serif';
            c2.fillText(L6_S + ' / ' + L6, 0.145 * px, 0.4 * py);
            c2.fillText(L9_S + ' / ' + L9, 0.145 * px, 0.8 * py);
            c2.fillText(C6_S + ' / ' + C6, 0.46 * px, 0.46 * py);
            c2.fillText(C9_S + ' / ' + C9, 0.46 * px, 0.8 * py);
            c2.fillText(R6_S + ' / ' + R6, 0.72 * px, 0.4 * py);
            c2.fillText(R9_S + ' / ' + R9, 0.77 * px, 0.8 * py);

            //シュート率　(%)
            c2.font = '14px sans-serif';
            if (LW == 0) {
                c2.fillText('-- %', 0.05 * px, 0.23 * py);
            } else {
                c2.fillText(LW_R + ' %', 0.03 * px, 0.23 * py);
            }
            if (RW == 0) {
                c2.fillText('-- %', 0.86 * px, 0.23 * py);
            } else {
                c2.fillText(RW_R + ' %', 0.86 * px, 0.23 * py);
            }
            if (PV == 0) {
                c2.fillText('-- %', 0.62 * px, 0.18 * py);
            } else {
                c2.fillText(PV_R + ' %', 0.62 * px, 0.18 * py);
            }
            if (seven == 0) {
                c2.fillText('-- %', 0.62 * px, 0.08 * py);
            } else {
                c2.fillText(seven_R + ' %', 0.62 * px, 0.075 * py);
            }
            //サイド以外はフォントサイズを少し大きく
            c2.font = '16px sans-serif';
            if (L6 == 0) {
                c2.fillText('-- %', 0.15 * px, 0.5 * py);
            } else {
                c2.fillText(L6_R + ' %', 0.12 * px, 0.5 * py);
            }
            if (L9 == 0) {
                c2.fillText('-- %', 0.14 * px, 0.9 * py);
            } else {
                c2.fillText(L9_R + ' %', 0.12 * px, 0.9 * py);
            }
            if (C6 == 0) {
                c2.fillText('-- %', 0.46 * px, 0.56 * py);
            } else {
                c2.fillText(C6_R + ' %', 0.44 * px, 0.56 * py);
            }
            if (C9 == 0) {
                c2.fillText('-- %', 0.46 * px, 0.9 * py);
            } else {
                c2.fillText(C9_R + ' %', 0.44 * px, 0.9 * py);
            }
            if (R6 == 0) {
                c2.fillText('-- %', 0.75 * px, 0.5 * py);
            } else {
                c2.fillText(R6_R + ' %', 0.75 * px, 0.5 * py);
            }
            if (R9 == 0) {
                c2.fillText('-- %', 0.77 * px, 0.9 * py);
            } else {
                c2.fillText(R9_R + ' %', 0.75 * px, 0.9 * py);
            }
        }
    }
}

function drawCanvasPosition2() {
    //***px,pyにはcanvasの縦横サイズが入る***
    var px = cv2.width;
    var py = cv2.height;
    if (HTMLCanvasElement) {
        c2.beginPath();
        c2.moveTo(0, 0);
        c2.lineTo(0.12 * px, 0);
        c2.lineTo(0.21 * px, 0.2 * py);
        c2.lineTo(0, 0.45 * py);
        // 塗りつぶしスタイルを設定
        Style2(LW_r2, LW2);
        c2.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c2.fill();
        c2.closePath();

        //右サイド
        c2.beginPath();
        c2.moveTo(px, 0);
        c2.lineTo(0.88 * px, 0);
        c2.lineTo(0.79 * px, 0.2 * py);
        c2.lineTo(px, 0.45 * py);
        // 塗りつぶしスタイルを設定
        Style2(RW_r2, RW2);
        c2.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c2.fill();
        c2.closePath();

        //左上
        c2.beginPath();
        c2.moveTo(0, 0.45 * py);
        c2.lineTo(0.21 * px, 0.2 * py);
        c2.lineTo(0.36 * px, 0.27 * py);
        c2.lineTo(0.36 * px, 0.7 * py);
        c2.lineTo(0.15 * px, 0.63 * py);
        // 塗りつぶしスタイルを設定
        Style2(L6_r2, L62);
        c2.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c2.fill();
        c2.closePath();

        //左下
        c2.beginPath();
        c2.moveTo(0, 0.45 * py);
        c2.lineTo(0.15 * px, 0.63 * py);
        c2.lineTo(0.36 * px, 0.7 * py);
        c2.lineTo(0.36 * px, py);
        c2.lineTo(0, py);
        // 塗りつぶしスタイルを設定
        Style2(L9_r2, L92);
        c2.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c2.fill();
        c2.closePath();

        //中央上
        c2.beginPath();
        c2.moveTo(0.36 * px, 0.27 * py);
        c2.lineTo(0.64 * px, 0.27 * py);
        c2.lineTo(0.64 * px, 0.7 * py);
        c2.lineTo(0.36 * px, 0.7 * py);
        // 塗りつぶしスタイルを設定
        Style2(C6_r2, C62);
        c2.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c2.fill();
        c2.closePath();

        //中央下
        c2.beginPath();
        c2.moveTo(0.36 * px, 0.7 * py);
        c2.lineTo(0.64 * px, 0.7 * py);
        c2.lineTo(0.64 * px, py);
        c2.lineTo(0.36 * px, py);
        // 塗りつぶしスタイルを設定
        Style2(C9_r2, C92);
        c2.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c2.fill();
        c2.closePath();

        //右上
        c2.beginPath();
        c2.moveTo(px, 0.45 * py);
        c2.lineTo(0.79 * px, 0.2 * py);
        c2.lineTo(0.64 * px, 0.27 * py);
        c2.lineTo(0.64 * px, 0.7 * py);
        c2.lineTo(0.85 * px, 0.63 * py);
        // 塗りつぶしスタイルを設定
        Style2(R6_r2, R62);
        c2.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c2.fill();
        c2.closePath();

        //右下
        c2.beginPath();
        c2.moveTo(px, 0.45 * py);
        c2.lineTo(0.85 * px, 0.63 * py);
        c2.lineTo(0.64 * px, 0.7 * py);
        c2.lineTo(0.64 * px, py);
        c2.lineTo(px, py);
        // 塗りつぶしスタイルを設定
        Style2(R9_r2, R92);
        c2.globalAlpha = 1.0;
        // パスに沿って塗りつぶし
        c2.fill();
        c2.closePath();

        //7mスロー
        // パスの開始（1）
        c2.beginPath();
        // 始点／終点を設定（2）
        c2.moveTo(0.21 * px, 0 * py);
        c2.lineTo(0.79 * px, 0 * py);
        c2.lineTo(0.79 * px, 0.09 * py);
        c2.lineTo(0.21 * px, 0.09 * py);
        c2.lineTo(0.21 * px, 0 * py);
        // 塗りつぶしスタイルを設定
        Style2(seven_r2, seven2);
        c2.linewidth = 0.5;
        c2.fill();
        // パスに沿って直線を描画（3）
        c2.stroke();


        //PTスロー線
        // パスの開始（1）
        c2.beginPath();
        // 始点／終点を設定（2）
        c2.moveTo(0.21 * px, 0.11 * py);
        c2.lineTo(0.79 * px, 0.11 * py);
        c2.lineTo(0.79 * px, 0.2 * py);
        c2.lineTo(0.21 * px, 0.2 * py);
        c2.lineTo(0.21 * px, 0.11 * py);
        // 塗りつぶしスタイルを設定
        Style2(PV_r2, PV2);
        c2.linewidth = 0.5;
        c2.fill()
        // パスに沿って直線を描画（3）
        c2.stroke();


        //領域同士の境界線
        // パスの開始（1）
        c2.beginPath();
        // 始点／終点を設定（2）
        c2.moveTo(0.21 * px, 0.2 * py);
        c2.lineTo(0, 0.45 * py);
        c2.lineTo(0.15 * px, 0.63 * py);
        c2.lineTo(0.36 * px, 0.7 * py);
        c2.lineTo(0.64 * px, 0.7 * py);
        c2.lineTo(0.85 * px, 0.63 * py);
        c2.lineTo(px, 0.45 * py);
        c2.lineTo(0.79 * px, 0.2 * py);
        // 塗りつぶしスタイルを設定
        c2.fillStyle = "#FFFFFF";
        c2.linewidth = 0.5;
        // パスに沿って直線を描画（3）
        c2.stroke();

        // パスの開始（1）
        c2.beginPath();
        // 始点／終点を設定（2）
        c2.moveTo(0.12 * px, 0);
        c2.lineTo(0.21 * px, 0.2 * py);
        c2.lineTo(0.36 * px, 0.27 * py);
        c2.lineTo(0.64 * px, 0.27 * py);
        c2.lineTo(0.79 * px, 0.2 * py);
        c2.lineTo(0.88 * px, 0);
        // 塗りつぶしスタイルを設定
        c2.fillStyle = "#FFFFFF";
        c2.linewidth = 0.5;
        // パスに沿って直線を描画（3）
        c2.stroke();

        // パスの開始（1）
        c2.beginPath();
        // 始点／終点を設定（2）
        c2.moveTo(0.36 * px, 0.27 * py);
        c2.lineTo(0.36 * px, py);
        // 塗りつぶしスタイルを設定
        c2.fillStyle = "#FFFFFF";
        c2.linewidth = 0.5;
        // パスに沿って直線を描画（3）
        c2.stroke();

        // パスの開始（1）
        c2.beginPath();
        // 始点／終点を設定（2）
        c2.moveTo(0.64 * px, 0.27 * py);
        c2.lineTo(0.64 * px, py);
        // 塗りつぶしスタイルを設定
        c2.fillStyle = "#FFFFFF";
        c2.linewidth = 0.5;
        // パスに沿って直線を描画（3）
        c2.stroke();
        //＊＊＊＊＊シュート数・パーセンテージを変数の式で書き換える＊＊＊＊＊
        //左サイド、右サイド、左中央右の上下の順
        //シュート成功回数 / シュート回数
        c2.fillStyle = "White";
        if (isTouchDevice) {
            c2.font = '10px sans-serif';
            c2.fillText(LW_s2 + ' / ' + LW2, 0.05 * px, 0.15 * py);
            c2.fillText(RW_s2 + ' / ' + RW2, 0.88 * px, 0.15 * py);
            c2.fillText(PV_s2 + ' / ' + PV2, 0.43 * px, 0.18 * py);
            c2.fillText(seven_s2 + ' / ' + seven2, 0.43 * px, 0.075 * py);
            c2.fillText('PV ：', 0.25 * px, 0.18 * py);
            c2.fillText('7m ：', 0.25 * px, 0.075 * py);
            c2.font = '12px sans-serif';
            c2.fillText(L6_s2 + ' / ' + L62, 0.145 * px, 0.4 * py);
            c2.fillText(L9_s2 + ' / ' + L92, 0.145 * px, 0.8 * py);
            c2.fillText(C6_s2 + ' / ' + C62, 0.46 * px, 0.46 * py);
            c2.fillText(C9_s2 + ' / ' + C92, 0.46 * px, 0.8 * py);
            c2.fillText(R6_s2 + ' / ' + R62, 0.72 * px, 0.4 * py);
            c2.fillText(R9_s2 + ' / ' + R92, 0.77 * px, 0.8 * py);

            c2.fillStyle = "White";
            //シュート率　(%)
            c2.font = '10px sans-serif';
            if (LW2 == '0') {
                c2.fillText('-- %', 0.05 * px, 0.23 * py);
            } else {
                c2.fillText(LW_r2 + ' %', 0.03 * px, 0.23 * py);
            }
            if (RW2 == '0') {
                c2.fillText('-- %', 0.86 * px, 0.23 * py);
            } else {
                c2.fillText(RW_r2 + ' %', 0.86 * px, 0.23 * py);
            }
            if (PV2 == '0') {
                c2.fillText('-- %', 0.62 * px, 0.18 * py);
            } else {
                c2.fillText(PV_r2 + ' %', 0.62 * px, 0.18 * py);
            }
            if (seven2 == '0') {
                c2.fillText('-- %', 0.62 * px, 0.08 * py);
            } else {
                c2.fillText(seven_r2 + ' %', 0.62 * px, 0.075 * py);
            }
            //サイド以外はフォントサイズを少し大きく
            c2.font = '12px sans-serif';
            if (L62 == '0') {
                c2.fillText('-- %', 0.15 * px, 0.5 * py);
            } else {
                c2.fillText(L6_r2 + ' %', 0.12 * px, 0.5 * py);
            }
            if (L92 == '0') {
                c2.fillText('-- %', 0.14 * px, 0.9 * py);
            } else {
                c2.fillText(L9_r2 + ' %', 0.12 * px, 0.9 * py);
            }
            if (C62 == '0') {
                c2.fillText('-- %', 0.46 * px, 0.56 * py);
            } else {
                c2.fillText(C6_r2 + ' %', 0.44 * px, 0.56 * py);
            }
            if (C92 == '0') {
                c2.fillText('-- %', 0.46 * px, 0.9 * py);
            } else {
                c2.fillText(C9_r2 + ' %', 0.44 * px, 0.9 * py);
            }
            if (R62 == '0') {
                c2.fillText('-- %', 0.75 * px, 0.5 * py);
            } else {
                c2.fillText(R6_r2 + ' %', 0.75 * px, 0.5 * py);
            }
            if (R92 == '0') {
                c2.fillText('-- %', 0.77 * px, 0.9 * py);
            } else {
                c2.fillText(R9_r2 + ' %', 0.75 * px, 0.9 * py);
            }
        } else {
            c2.font = '14px sans-serif';
            c2.fillText(LW_s2 + ' / ' + LW2, 0.05 * px, 0.15 * py);
            c2.fillText(RW_s2 + ' / ' + RW2, 0.88 * px, 0.15 * py);
            c2.fillText(PV_s2 + ' / ' + PV2, 0.43 * px, 0.18 * py);
            c2.fillText(seven_s2 + ' / ' + seven2, 0.43 * px, 0.075 * py);
            c2.fillText('PV ：', 0.25 * px, 0.18 * py);
            c2.fillText('7m ：', 0.25 * px, 0.075 * py);
            c2.font = '16px sans-serif';
            c2.fillText(L6_s2 + ' / ' + L62, 0.145 * px, 0.4 * py);
            c2.fillText(L9_s2 + ' / ' + L92, 0.145 * px, 0.8 * py);
            c2.fillText(C6_s2 + ' / ' + C62, 0.46 * px, 0.46 * py);
            c2.fillText(C9_s2 + ' / ' + C92, 0.46 * px, 0.8 * py);
            c2.fillText(R6_s2 + ' / ' + R62, 0.72 * px, 0.4 * py);
            c2.fillText(R9_s2 + ' / ' + R92, 0.77 * px, 0.8 * py);

            c2.fillStyle = "White";
            //シュート率　(%)
            c2.font = '14px sans-serif';
            if (LW2 == '0') {
                c2.fillText('-- %', 0.05 * px, 0.23 * py);
            } else {
                c2.fillText(LW_r2 + ' %', 0.03 * px, 0.23 * py);
            }
            if (RW2 == '0') {
                c2.fillText('-- %', 0.86 * px, 0.23 * py);
            } else {
                c2.fillText(RW_r2 + ' %', 0.86 * px, 0.23 * py);
            }
            if (PV2 == '0') {
                c2.fillText('-- %', 0.62 * px, 0.18 * py);
            } else {
                c2.fillText(PV_r2 + ' %', 0.62 * px, 0.18 * py);
            }
            if (seven2 == '0') {
                c2.fillText('-- %', 0.62 * px, 0.08 * py);
            } else {
                c2.fillText(seven_r2 + ' %', 0.62 * px, 0.075 * py);
            }
            //サイド以外はフォントサイズを少し大きく
            c2.font = '16px sans-serif';
            if (L62 == '0') {
                c2.fillText('-- %', 0.15 * px, 0.5 * py);
            } else {
                c2.fillText(L6_r2 + ' %', 0.12 * px, 0.5 * py);
            }
            if (L92 == '0') {
                c2.fillText('-- %', 0.14 * px, 0.9 * py);
            } else {
                c2.fillText(L9_r2 + ' %', 0.12 * px, 0.9 * py);
            }
            if (C62 == '0') {
                c2.fillText('-- %', 0.46 * px, 0.56 * py);
            } else {
                c2.fillText(C6_r2 + ' %', 0.44 * px, 0.56 * py);
            }
            if (C92 == '0') {
                c2.fillText('-- %', 0.46 * px, 0.9 * py);
            } else {
                c2.fillText(C9_r2 + ' %', 0.44 * px, 0.9 * py);
            }
            if (R62 == '0') {
                c2.fillText('-- %', 0.75 * px, 0.5 * py);
            } else {
                c2.fillText(R6_r2 + ' %', 0.75 * px, 0.5 * py);
            }
            if (R92 == '0') {
                c2.fillText('-- %', 0.77 * px, 0.9 * py);
            } else {
                c2.fillText(R9_r2 + ' %', 0.75 * px, 0.9 * py);
            }
        }
    }
}

function Style(ratio, total) {
    if (total == '0') {
        c.fillStyle = "White";
        return;
    }
    if (ratio <= 20) {
        c.fillStyle = "RGB(180, 180, 255)";
    } else if (ratio <= 40) {
        c.fillStyle = "RGB(150, 150, 255)";
    } else if (ratio <= 60) {
        c.fillStyle = "RGB(120, 120, 255)";
    } else if (ratio <= 80) {
        c.fillStyle = "RGB(90, 90, 255)";
    } else if (ratio <= 100) {
        c.fillStyle = "RGB(60, 60, 255)";
    }
}

function Style2(ratio, total) {
    if (total == '0') {
        c2.fillStyle = "White";
        return;
    }
    if (ratio <= 20) {
        c2.fillStyle = "RGB(180, 180, 255)";
    } else if (ratio <= 40) {
        c2.fillStyle = "RGB(150, 150, 255)";
    } else if (ratio <= 60) {
        c2.fillStyle = "RGB(120, 120, 255)";
    } else if (ratio <= 80) {
        c2.fillStyle = "RGB(90, 90, 255)";
    } else if (ratio <= 100) {
        c2.fillStyle = "RGB(60, 60, 255)";
    }
}

function drawLine(x1, x2, y1, y2, color) {
    if (isTouchDevice) {
        ctx.lineWidth = line_size;
        ctx.strokeStyle = color;
        ctx.beginPath();
        ctx.moveTo(x1, y1);
        ctx.lineTo(x2, y2);
        ctx.stroke();
    } else {
        ctx.lineWidth = line_size;
        ctx.strokeStyle = color;
        ctx.beginPath();
        ctx.moveTo(x1, y1);
        ctx.lineTo(x2, y2);
        ctx.stroke();
    }
}

function drawLine_select(x1, x2, y1, y2, color) {
    if (isTouchDevice) {
        ctx.lineWidth = 2;
        ctx.strokeStyle = color;
        ctx.beginPath();
        ctx.moveTo(x1, y1);
        ctx.lineTo(x2, y2);
        ctx.stroke();
    } else {
        ctx.lineWidth = 3;
        ctx.strokeStyle = color;
        ctx.beginPath();
        ctx.moveTo(x1, y1);
        ctx.lineTo(x2, y2);
        ctx.stroke();
    }
}

function drawLine2(x1, x2, y1, y2, color) {
    if (isTouchDevice) {
        ctx2.lineWidth = line_size;
        ctx2.strokeStyle = color;
        ctx2.beginPath();
        ctx2.moveTo(x1, y1);
        ctx2.lineTo(x2, y2);
        ctx2.stroke();
    } else {
        ctx2.lineWidth = line_size;
        ctx2.strokeStyle = color;
        ctx2.beginPath();
        ctx2.moveTo(x1, y1);
        ctx2.lineTo(x2, y2);
        ctx2.stroke();
    }
}

function drawLine_select2(x1, x2, y1, y2, color) {
    if (isTouchDevice) {
        ctx2.lineWidth = 2;
        ctx2.strokeStyle = color;
        ctx2.beginPath();
        ctx2.moveTo(x1, y1);
        ctx2.lineTo(x2, y2);
        ctx2.stroke();
    } else {
        ctx2.lineWidth = 3;
        ctx2.strokeStyle = color;
        ctx2.beginPath();
        ctx2.moveTo(x1, y1);
        ctx2.lineTo(x2, y2);
        ctx2.stroke();
    }
}

function drawCircle(x, y) {
    if (isTouchDevice) {
        let radius = 2; // 円の半径
        let startAngle = 0; // 円の開始角度（ラジアン）
        let endAngle = 2 * Math.PI; // 円の終了角度（ラジアン）

        ctx.beginPath();
        ctx.arc(x, y, radius, startAngle, endAngle);
        ctx.fillStyle = `rgba(0, 0, 0, 0.7)`;
        ctx.fill(); // 円を塗りつぶす
    } else {
        let radius = 3; // 円の半径
        let startAngle = 0; // 円の開始角度（ラジアン）
        let endAngle = 2 * Math.PI; // 円の終了角度（ラジアン）

        ctx.beginPath();
        ctx.arc(x, y, radius, startAngle, endAngle);
        ctx.fillStyle = `rgba(0, 0, 0, 0.7)`;
        ctx.fill(); // 円を塗りつぶす
    }
}

function drawCircle2(x, y) {
    if (isTouchDevice) {
        let radius = 2; // 円の半径
        let startAngle = 0; // 円の開始角度（ラジアン）
        let endAngle = 2 * Math.PI; // 円の終了角度（ラジアン）

        ctx2.beginPath();
        ctx2.arc(x, y, radius, startAngle, endAngle);
        ctx2.fillStyle = `rgba(0, 0, 0, 0.7)`;
        ctx2.fill(); // 円を塗りつぶす
    } else {
        let radius = 3; // 円の半径
        let startAngle = 0; // 円の開始角度（ラジアン）
        let endAngle = 2 * Math.PI; // 円の終了角度（ラジアン）

        ctx2.beginPath();
        ctx2.arc(x, y, radius, startAngle, endAngle);
        ctx2.fillStyle = `rgba(0, 0, 0, 0.7)`;
        ctx2.fill(); // 円を塗りつぶす
    }
}

//バツ印を描画
function drawCross(x, y, id) {
    if (isTouchDevice) {
        //線の太さ
        ctx.lineWidth = cross_line_size_i;
        //線の色
        ctx.strokeStyle = cross_color;

        // Stroked Cross
        ctx.beginPath();
        ctx.moveTo(x - 7, y - 7);
        ctx.lineTo(x + 7, y + 7);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(x + 7, y - 7);
        ctx.lineTo(x - 7, y + 7);
        ctx.stroke();
        cross_position1.push({ x: x, y: y, id: id });
    } else {
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
        cross_position1.push({ x: x, y: y, id: id });
    }
}

function drawCross2(x, y, id) {
    if (isTouchDevice) {
        //線の太さ
        ctx2.lineWidth = cross_line_size_i;
        //線の色
        ctx2.strokeStyle = cross_color;

        // Stroked Cross
        ctx2.beginPath();
        ctx2.moveTo(x - 7, y - 7);
        ctx2.lineTo(x + 7, y + 7);
        ctx2.stroke();
        ctx2.beginPath();
        ctx2.moveTo(x + 7, y - 7);
        ctx2.lineTo(x - 7, y + 7);
        ctx2.stroke();
        cross_position2.push({ x: x, y: y, id: id });
    } else {
        //線の太さ
        ctx2.lineWidth = cross_line_size;
        //線の色
        ctx2.strokeStyle = cross_color;

        // Stroked Cross
        ctx2.beginPath();
        ctx2.moveTo(x - 10, y - 10);
        ctx2.lineTo(x + 10, y + 10);
        ctx2.stroke();
        ctx2.beginPath();
        ctx2.moveTo(x + 10, y - 10);
        ctx2.lineTo(x - 10, y + 10);
        ctx2.stroke();
        cross_position2.push({ x: x, y: y, id: id });
    }
}

//バツ印を描画
function drawSelector(x, y) {
    if (isTouchDevice) {
        //線の太さ
        ctx.lineWidth = 3;
        //線の色
        ctx.strokeStyle = cross_color;

        // Stroked Cross
        ctx.beginPath();
        ctx.moveTo(x - 8, y - 8);
        ctx.lineTo(x + 8, y + 8);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(x + 8, y - 8);
        ctx.lineTo(x - 8, y + 8);
        ctx.stroke();
        // cross_position1.push({ x: x, y: y });
    } else {
        //線の太さ
        ctx.lineWidth = select_line_size;
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
        // cross_position1.push({ x: x, y: y });
    }
}

function drawSelector2(x, y) {
    if (isTouchDevice) {
        //線の太さ
        ctx2.lineWidth = 3;
        //線の色
        ctx2.strokeStyle = cross_color;

        // Stroked Cross
        ctx2.beginPath();
        ctx2.moveTo(x - 8, y - 8);
        ctx2.lineTo(x + 8, y + 8);
        ctx2.stroke();
        ctx2.beginPath();
        ctx2.moveTo(x + 8, y - 8);
        ctx2.lineTo(x - 8, y + 8);
        ctx2.stroke();
        // cross_position2.push({ x: x, y: y });
    } else {
        //線の太さ
        ctx2.lineWidth = select_line_size;
        //線の色
        ctx2.strokeStyle = cross_color;

        // Stroked Cross
        ctx2.beginPath();
        ctx2.moveTo(x - 10, y - 10);
        ctx2.lineTo(x + 10, y + 10);
        ctx2.stroke();
        ctx2.beginPath();
        ctx2.moveTo(x + 10, y - 10);
        ctx2.lineTo(x - 10, y + 10);
        ctx2.stroke();
        // cross_position2.push({ x: x, y: y });
    }
}

function draw_only_img(team_id) {
    if (team_id == 1) {
        let width = canvas_path.width;
        let height = canvas_path.height;
        const img = new Image();
        img.src = image_name; // 画像のURLを指定
        img.onload = () => {
            ctx.drawImage(img, 0, 0, width, height);
        };
    } else if (team_id == 2) {
        let width2 = canvas_path2.width;
        let height2 = canvas_path2.height;
        const img2 = new Image();
        img2.src = image_name; // 画像のURLを指定
        img2.onload = () => {
            ctx2.drawImage(img2, 0, 0, width2, height2);
        };
    }
}

function drawCanvasSelector(id) {
    console.log(id);
    //背景画像の設定
    cross_position = [];
    let width = canvas_path.width;
    let height = canvas_path.height;
    const img = new Image();
    img.src = image_name; // 画像のURLを指定
    img.onload = () => {
        ctx.drawImage(img, 0, 0, width, height);
        for (let i = 0; i < xy_parse.position_xy.length; i++) {
            let first = 1;
            let color = "";
            if (xy_parse.pointed_flag[i] == 0) {
                color = "red";
            } else {
                color = "green"
            }
            if (xy_parse.id[i] == id) {
                for (let n = 0; n < goal1.length; n++) {
                    if (goal1[n].id.includes('change_color_cell')) {
                        goal1[n].removeAttribute('id');
                    }
                    if (goal2[n].id.includes('change_color_cell')) {
                        goal2[n].removeAttribute('id');
                    }
                }
                for (let n = 0; n < goal1.length; n++) {
                    if (goal1[n].getAttribute('value') == xy_parse.goal_position[i]) {
                        goal1[n].setAttribute('id', 'change_color_cell');
                    }
                }
                if (!video_flag) {
                    mySeekTo(xy_parse.video_time[i] - 5);
                }
                for (let j = 0; j < xy_parse.position_xy[i].length; j++) {
                    if (xy_parse.position_xy[i][j] <= 0.5) {
                        //配列数が2(=軌跡なし)または、配列の最終要素の場合
                        if (xy_parse.position_xy[i].length == 2 || j == xy_parse.position_xy[i].length - 2) {
                            //バツ印の描画
                            drawSelector(width * (1 - xy_parse.position_xy[i][j + 1]), height * xy_parse.position_xy[i][j] * 2);
                            // console.log(width * (1 - xy_parse2[i][j + 1]), height * xy_parse2[i][j] * 2 + '<-cross');
                        } else { //それ以外の座標
                            //軌跡(一点分)の描画
                            // drawPath(width*(1-xy_parse[i][j+1]), height*xy_parse[i][j]*2);
                            drawLine_select(width * (1 - xy_parse.position_xy[i][j + 1]), width * (1 - xy_parse.position_xy[i][j + 3]), height * xy_parse.position_xy[i][j] * 2, height * xy_parse.position_xy[i][j + 2] * 2, color);
                            // console.log(width*(1-xy_parse[i][j+1]), height*xy_parse[i][j]*2);
                            if (first == 1 && xy_parse.position_xy[i].length >= 6) {
                                drawCircle(width * (1 - xy_parse.position_xy[i][j + 1]), height * xy_parse.position_xy[i][j] * 2);
                                first += 1;
                            }
                        }
                        j++;
                    } else if (xy_parse.position_xy[i][j] >= 0.5) { //チーム２が選択されているとき、チーム２が攻めるコートのデータのみ表示
                        //配列数が2(=軌跡なし)または、配列の最終要素の場合
                        if (xy_parse.position_xy[i].length == 2 || j == xy_parse.position_xy[i].length - 2) {
                            //バツ印の描画
                            drawSelector(width * xy_parse.position_xy[i][j + 1], height * 2 * (1 - xy_parse.position_xy[i][j]));
                            // console.log(width * xy_parse2[i][j + 1], height * 2 * (1 - xy_parse2[i][j]) + '<-cross');
                        } else { //それ以外の座標
                            //軌跡(一点分)の描画
                            //drawPath(width*xy_parse[i][j+1], height*2*(1-xy_parse[i][j]));
                            drawLine_select(width * xy_parse.position_xy[i][j + 1], width * xy_parse.position_xy[i][j + 3], height * 2 * (1 - xy_parse.position_xy[i][j]), height * 2 * (1 - xy_parse.position_xy[i][j + 2]), color);
                            // console.log(width*xy_parse[i][j+1], height*2*(1-xy_parse[i][j]));
                            if (first == 1 && xy_parse.position_xy[i].length >= 6) {
                                drawCircle(width * xy_parse.position_xy[i][j + 1], height * (1 - xy_parse.position_xy[i][j]) * 2);
                                first += 1;
                            }
                        }
                        j++;
                    }
                }
            }
        }
    };
}

function drawCanvasSelector2(id) {
    cross_position2 = [];
    let width = canvas_path2.width;
    let height = canvas_path2.height;
    const img2 = new Image();
    img2.src = image_name; // 画像のURLを指定
    img2.onload = () => {
        ctx2.drawImage(img2, 0, 0, width, height);
        for (let i = 0; i < xy_parse2.position_xy.length; i++) {
            let first = 1;
            let color = "";
            if (xy_parse2.pointed_flag[i] == 0) {
                color = "red";
            } else {
                color = "green"
            }
            if (xy_parse2.id[i] == id) {
                for (let n = 0; n < goal2.length; n++) {
                    if (goal1[n].id.includes('change_color_cell')) {
                        goal1[n].removeAttribute('id');
                    }
                    if (goal2[n].id.includes('change_color_cell')) {
                        goal2[n].removeAttribute('id');
                    }
                }
                for (let n = 0; n < goal2.length; n++) {
                    if (goal2[n].getAttribute('value') == xy_parse2.goal_position[i]) {
                        goal2[n].setAttribute('id', 'change_color_cell');
                    }
                }
                if (!video_flag) {
                    mySeekTo(xy_parse2.video_time[i] - 5);
                }
                for (let j = 0; j < xy_parse2.position_xy[i].length; j++) {
                    if (xy_parse2.position_xy[i][j] <= 0.5) {
                        //配列数が2(=軌跡なし)または、配列の最終要素の場合
                        if (xy_parse2.position_xy[i].length == 2 || j == xy_parse2.position_xy[i].length - 2) {
                            //バツ印の描画
                            drawSelector2(width * (1 - xy_parse2.position_xy[i][j + 1]), height * xy_parse2.position_xy[i][j] * 2);
                            // console.log(width * (1 - xy_parse2[i][j + 1]), height * xy_parse2[i][j] * 2 + '<-cross');
                        } else { //それ以外の座標
                            //軌跡(一点分)の描画
                            // drawPath(width*(1-xy_parse[i][j+1]), height*xy_parse[i][j]*2);
                            drawLine_select2(width * (1 - xy_parse2.position_xy[i][j + 1]), width * (1 - xy_parse2.position_xy[i][j + 3]), height * xy_parse2.position_xy[i][j] * 2, height * xy_parse2.position_xy[i][j + 2] * 2, color);
                            // console.log(width*(1-xy_parse[i][j+1]), height*xy_parse[i][j]*2);
                            if (first == 1 && xy_parse2.position_xy[i].length >= 6) {
                                drawCircle2(width * (1 - xy_parse2.position_xy[i][j + 1]), height * xy_parse2.position_xy[i][j] * 2);
                                first += 1;
                            }
                        }
                        j++;
                    } else if (xy_parse2.position_xy[i][j] >= 0.5) { //チーム２が選択されているとき、チーム２が攻めるコートのデータのみ表示
                        //配列数が2(=軌跡なし)または、配列の最終要素の場合
                        if (xy_parse2.position_xy[i].length == 2 || j == xy_parse2.position_xy[i].length - 2) {
                            //バツ印の描画
                            drawSelector2(width * xy_parse2.position_xy[i][j + 1], height * 2 * (1 - xy_parse2.position_xy[i][j]));
                            // console.log(width * xy_parse2[i][j + 1], height * 2 * (1 - xy_parse2[i][j]) + '<-cross');
                        } else { //それ以外の座標
                            //軌跡(一点分)の描画
                            //drawPath(width*xy_parse[i][j+1], height*2*(1-xy_parse[i][j]));
                            drawLine_select2(width * xy_parse2.position_xy[i][j + 1], width * xy_parse2.position_xy[i][j + 3], height * 2 * (1 - xy_parse2.position_xy[i][j]), height * 2 * (1 - xy_parse2.position_xy[i][j + 2]), color);
                            // console.log(width*xy_parse[i][j+1], height*2*(1-xy_parse[i][j]));
                            if (first == 1 && xy_parse2.position_xy[i].length >= 6) {
                                drawCircle2(width * xy_parse2.position_xy[i][j + 1], height * (1 - xy_parse2.position_xy[i][j]) * 2);
                                first += 1;
                            }
                        }
                        j++;
                    }
                }
            }
        }
    };
}

//各関数の実行
window.onload = () => {
    hidePopup();
    //シュート率表示のcanvasの描画
    drawCanvasPosition();
    drawCanvasPosition2();
    //ドラッグ履歴のcanvasの描画
    drawCanvasPath();
    drawCanvasPath2();
};

function drawCanvasPath() {
    //背景画像の設定
    cross_position1 = [];
    let width = canvas_path.width;
    let height = canvas_path.height;
    // 画像読み込み
    const img = new Image();
    img.src = image_name; // 画像のURLを指定
    img.onload = () => {
        ctx.drawImage(img, 0, 0, width, height);

        for (let i = 0; i < xy_parse.position_xy.length; i++) {
            //最初の要素であるか判定するための変数
            let first = 1;
            color = "";
            if (xy_parse.pointed_flag[i] == 0) {
                color = "red";
            } else {
                color = "green"
            }
            if (xy_parse.empty_shoot[i] == 1) {
                continue;
            }
            if (xy_parse.shooter_kind[i] == 2) {
                if (!pv_click1) {
                    pv_click1 = false;
                    pv_select1 = false;
                    pv_click2 = false;
                    pv_select2 = false;
                    // continue;
                } else {
                    if (pv_select1) {
                        // console.log("pv_no");
                        pv_select1 = false;
                        pv_click2 = false;
                        pv_select2 = false;
                        // continue;
                    } else {
                        // console.log("pv");
                        pv_select1 = true;
                        pv_click2 = false;
                        pv_select2 = false;
                    }
                }
            }
            for (let j = 0; j < xy_parse.position_xy[i].length; j++) {
                if (xy_parse.position_xy[i][j] <= 0.5) {
                    //配列数が2(=軌跡なし)または、配列の最終要素の場合
                    if (xy_parse.position_xy[i].length == 2 || j == xy_parse.position_xy[i].length - 2) {
                        //バツ印の描画
                        drawCross(width * (1 - xy_parse.position_xy[i][j + 1]), height * xy_parse.position_xy[i][j] * 2, xy_parse.id[i]);
                    } else { //それ以外の座標
                        //軌跡(一点分)の描画
                        drawLine(width * (1 - xy_parse.position_xy[i][j + 1]), width * (1 - xy_parse.position_xy[i][j + 3]), height * xy_parse.position_xy[i][j] * 2, height * xy_parse.position_xy[i][j + 2] * 2, color);
                        // console.log(width*(1-xy_parse[i][j+1]), height*xy_parse[i][j]*2);
                        if (first == 1 && xy_parse.position_xy[i].length >= 6) {
                            drawCircle(width * (1 - xy_parse.position_xy[i][j + 1]), height * xy_parse.position_xy[i][j] * 2);
                            first += 1;
                        }
                    }
                    j++;
                } else if (xy_parse.position_xy[i][j] >= 0.5) { //チーム２が選択されているとき、チーム２が攻めるコートのデータのみ表示
                    //配列数が2(=軌跡なし)または、配列の最終要素の場合
                    if (xy_parse.position_xy[i].length == 2 || j == xy_parse.position_xy[i].length - 2) {
                        //バツ印の描画
                        drawCross(width * xy_parse.position_xy[i][j + 1], height * 2 * (1 - xy_parse.position_xy[i][j]), xy_parse.id[i]);
                        // console.log(width * xy_parse[i][j + 1], height * 2 * (1 - xy_parse[i][j]) + '<-cross');
                    } else { //それ以外の座標
                        //軌跡(一点分)の描画
                        //drawPath(width*xy_parse[i][j+1], height*2*(1-xy_parse[i][j]));
                        drawLine(width * xy_parse.position_xy[i][j + 1], width * xy_parse.position_xy[i][j + 3], height * 2 * (1 - xy_parse.position_xy[i][j]), height * 2 * (1 - xy_parse.position_xy[i][j + 2]), color);
                        // console.log(width*xy_parse[i][j+1], height*2*(1-xy_parse[i][j]));
                        if (first == 1 && xy_parse.position_xy[i].length >= 6) {
                            drawCircle(width * xy_parse.position_xy[i][j + 1], height * (1 - xy_parse.position_xy[i][j]) * 2);
                            first += 1;
                        }
                    }
                    j++;
                }
            }
        }
    };
}

function drawCanvasPath2() {
    //背景画像の設定
    cross_position2 = [];
    let width = canvas_path2.width;
    let height = canvas_path2.height;
    // 画像読み込み
    const img2 = new Image();
    img2.src = image_name; // 画像のURLを指定
    img2.onload = () => {
        ctx2.drawImage(img2, 0, 0, width, height);
        for (let i = 0; i < xy_parse2.position_xy.length; i++) {
            let first = 1;
            color = "";
            if (xy_parse2.pointed_flag[i] == 0) {
                color = "red";
            } else {
                color = "green"
            }
            if (xy_parse2.empty_shoot[i] == 1) {
                continue;
            }
            if (xy_parse2.shooter_kind[i] == 2) {
                if (!pv_click2) {
                    pv_click1 = false;
                    pv_select1 = false;
                    pv_click2 = false;
                    pv_select2 = false;
                    // continue;
                } else {
                    if (pv_select2) {
                        // console.log("pv_no");
                        pv_click1 = false;
                        pv_select1 = false;
                        pv_select2 = false;
                        // continue;
                    } else {
                        // console.log("pv");
                        pv_click1 = false;
                        pv_select1 = false;
                        pv_select2 = true;
                    }
                }
            }
            for (let j = 0; j < xy_parse2.position_xy[i].length; j++) {
                if (xy_parse2.position_xy[i][j] <= 0.5) {
                    //配列数が2(=軌跡なし)または、配列の最終要素の場合
                    if (xy_parse2.position_xy[i].length == 2 || j == xy_parse2.position_xy[i].length - 2) {
                        //バツ印の描画
                        drawCross2(width * (1 - xy_parse2.position_xy[i][j + 1]), height * xy_parse2.position_xy[i][j] * 2, xy_parse2.id[i]);
                        // console.log(width * (1 - xy_parse2[i][j + 1]), height * xy_parse2[i][j] * 2 + '<-cross');
                    } else { //それ以外の座標
                        //軌跡(一点分)の描画
                        // drawPath(width*(1-xy_parse[i][j+1]), height*xy_parse[i][j]*2);
                        drawLine2(width * (1 - xy_parse2.position_xy[i][j + 1]), width * (1 - xy_parse2.position_xy[i][j + 3]), height * xy_parse2.position_xy[i][j] * 2, height * xy_parse2.position_xy[i][j + 2] * 2, color);
                        // console.log(width*(1-xy_parse[i][j+1]), height*xy_parse[i][j]*2);
                        if (first == 1 && xy_parse2.position_xy[i].length >= 6) {
                            drawCircle2(width * (1 - xy_parse2.position_xy[i][j + 1]), height * xy_parse2.position_xy[i][j] * 2);
                            first += 1;
                        }
                    }
                    j++;

                } else if (xy_parse2.position_xy[i][j] >= 0.5) { //チーム２が選択されているとき、チーム２が攻めるコートのデータのみ表示
                    //配列数が2(=軌跡なし)または、配列の最終要素の場合
                    if (xy_parse2.position_xy[i].length == 2 || j == xy_parse2.position_xy[i].length - 2) {
                        //バツ印の描画
                        drawCross2(width * xy_parse2.position_xy[i][j + 1], height * 2 * (1 - xy_parse2.position_xy[i][j]), xy_parse2.id[i]);
                        // console.log(width * xy_parse2[i][j + 1], height * 2 * (1 - xy_parse2[i][j]) + '<-cross');
                    } else { //それ以外の座標
                        //軌跡(一点分)の描画
                        //drawPath(width*xy_parse[i][j+1], height*2*(1-xy_parse[i][j]));
                        drawLine2(width * xy_parse2.position_xy[i][j + 1], width * xy_parse2.position_xy[i][j + 3], height * 2 * (1 - xy_parse2.position_xy[i][j]), height * 2 * (1 - xy_parse2.position_xy[i][j + 2]), color);
                        // console.log(width*xy_parse[i][j+1], height*2*(1-xy_parse[i][j]));
                        if (first == 1 && xy_parse2.position_xy[i].length >= 6) {
                            drawCircle2(width * xy_parse2.position_xy[i][j + 1], height * (1 - xy_parse2.position_xy[i][j]) * 2);
                            first += 1;
                        }
                    }
                    j++;

                }
            }
        }
    };
}

function reset_position_both() {
    let selectedValue = $('#swift_flag').val();

    if (selectedValue == 'default') {
        swift_flag = 1;
    } else if (selectedValue == 'no-swift') {
        swift_flag = 2;
    } else {
        swift_flag = 3;
    }

    $.ajax({
        //送信方法
        type: "POST",
        //送信先ファイル名
        url: "./ajax/ajax_SelectSwift.php",
        //受け取りデータの種類
        datatype: "json",
        //送信データ
        data: {
            game_id: game_id,
            first_time: first_time,
            latter_time: latter_time,
            team_name1: team_name1,
            team_name2: team_name2,
            select: select,
            team_id1: team_id1,
            team_id2: team_id2,
            swift_flag: swift_flag,
        },
    }).then(
        //成功時の処理
        function (result) {
            xy_parse = result.parse1.parse;
            xy_parse2 = result.parse2.parse;
            show_table = result.table;
            position_lists = result.parse1.position;
            position_lists2 = result.parse2.position;
            table_filtering();
            resetCanvas();
            resetCanvas2();
            draw(1, position_lists.LW, position_lists.PV, position_lists.RW, position_lists.L6, position_lists.C6, position_lists.R6, position_lists.L9, position_lists.C9, position_lists.R9, position_lists.seven, position_lists.LW_s, position_lists.PV_s, position_lists.RW_s, position_lists.L6_s, position_lists.C6_s, position_lists.R6_s, position_lists.L9_s, position_lists.C9_s, position_lists.R9_s, position_lists.seven_s, position_lists.LW_r, position_lists.PV_r, position_lists.RW_r, position_lists.L6_r, position_lists.C6_r, position_lists.R6_r, position_lists.L9_r, position_lists.C9_r, position_lists.R9_r, position_lists.seven_r);
            draw(2, position_lists2.LW, position_lists2.PV, position_lists2.RW, position_lists2.L6, position_lists2.C6, position_lists2.R6, position_lists2.L9, position_lists2.C9, position_lists2.R9, position_lists2.seven, position_lists2.LW_s, position_lists2.PV_s, position_lists2.RW_s, position_lists2.L6_s, position_lists2.C6_s, position_lists2.R6_s, position_lists2.L9_s, position_lists2.C9_s, position_lists2.R9_s, position_lists2.seven_s, position_lists2.LW_r, position_lists2.PV_r, position_lists2.RW_r, position_lists2.L6_r, position_lists2.C6_r, position_lists2.R6_r, position_lists2.L9_r, position_lists2.C9_r, position_lists2.R9_r, position_lists2.seven_r);
            drawCanvasPath();
            drawCanvasPath2();
            input_goal_position(result.parse1.goal_pos, result.parse1.goal_pos_s);
            input_goal_position2(result.parse2.goal_pos, result.parse2.goal_pos_s);
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

//ポジションがクリックされた時の処理
function reset_position() {
    resetCanvas();
    if (swift_flag == 2 || swift_flag == 3) {
        draw(1, position_lists.LW, position_lists.PV, position_lists.RW, position_lists.L6, position_lists.C6, position_lists.R6, position_lists.L9, position_lists.C9, position_lists.R9, position_lists.seven, position_lists.LW_s, position_lists.PV_s, position_lists.RW_s, position_lists.L6_s, position_lists.C6_s, position_lists.R6_s, position_lists.L9_s, position_lists.C9_s, position_lists.R9_s, position_lists.seven_s, position_lists.LW_r, position_lists.PV_r, position_lists.RW_r, position_lists.L6_r, position_lists.C6_r, position_lists.R6_r, position_lists.L9_r, position_lists.C9_r, position_lists.R9_r, position_lists.seven_r);
        return;
    }
    if (select == 1) {
        drawCanvasPosition();
    } else if (select == 2) {
        draw(1, FLW1, FPV1, FRW1, FL61, FC61, FR61, FL91, FC91, FR91, Fseven1, FLW_s1, FPV_s1, FRW_s1, FL6_s1, FC6_s1, FR6_s1, FL9_s1, FC9_s1, FR9_s1, Fseven_s1, FLW_r1, FPV_r1, FRW_r1, FL6_r1, FC6_r1, FR6_r1, FL9_r1, FC9_r1, FR9_r1, Fseven_r1);
    } else if (select == 3) {
        draw(1, LLW1, LPV1, LRW1, LL61, LC61, LR61, LL91, LC91, LR91, Lseven1, LLW_s1, LPV_s1, LRW_s1, LL6_s1, LC6_s1, LR6_s1, LL9_s1, LC9_s1, LR9_s1, Lseven_s1, LLW_r1, LPV_r1, LRW_r1, LL6_r1, LC6_r1, LR6_r1, LL9_r1, LC9_r1, LR9_r1, Lseven_r1);
    }
}

function reset_position2() {
    resetCanvas2();
    if (swift_flag == 2 || swift_flag == 3) {
        draw(2, position_lists2.LW, position_lists2.PV, position_lists2.RW, position_lists2.L6, position_lists2.C6, position_lists2.R6, position_lists2.L9, position_lists2.C9, position_lists2.R9, position_lists2.seven, position_lists2.LW_s, position_lists2.PV_s, position_lists2.RW_s, position_lists2.L6_s, position_lists2.C6_s, position_lists2.R6_s, position_lists2.L9_s, position_lists2.C9_s, position_lists2.R9_s, position_lists2.seven_s, position_lists2.LW_r, position_lists2.PV_r, position_lists2.RW_r, position_lists2.L6_r, position_lists2.C6_r, position_lists2.R6_r, position_lists2.L9_r, position_lists2.C9_r, position_lists2.R9_r, position_lists2.seven_r);
        return;
    }
    if (select == 1) {
        drawCanvasPosition2();
    } else if (select == 2) {
        draw(2, FLW2, FPV2, FRW2, FL62, FC62, FR62, FL92, FC92, FR92, Fseven2, FLW_s2, FPV_s2, FRW_s2, FL6_s2, FC6_s2, FR6_s2, FL9_s2, FC9_s2, FR9_s2, Fseven_s2, FLW_r2, FPV_r2, FRW_r2, FL6_r2, FC6_r2, FR6_r2, FL9_r2, FC9_r2, FR9_r2, Fseven_r2);
    } else if (select == 3) {
        draw(2, LLW2, LPV2, LRW2, LL62, LC62, LR62, LL92, LC92, LR92, Lseven2, LLW_s2, LPV_s2, LRW_s2, LL6_s2, LC6_s2, LR6_s2, LL9_s2, LC9_s2, LR9_s2, Lseven_s2, LLW_r2, LPV_r2, LRW_r2, LL6_r2, LC6_r2, LR6_r2, LL9_r2, LC9_r2, LR9_r2, Lseven_r2);
    }
}

function table_filtering() {
    html = '';
    let id;
    let time;
    let goal_judge;
    let team_name;
    let player_num;
    for (let i = 0; i < show_table.id.length; i++) {
        id = show_table.id[i];
        time = show_table.time[i];
        goal_judge = show_table.goal_judge[i];
        team_name = show_table.team_name[i];
        player_num = show_table.player_num[i];
        html += '<tr class="shoot_his" id="' + id + '"><th class="shoot_his">' + time + '</th><td class="shoot_his">' + goal_judge + '</td><td class="shoot_his" id="team_abbreviation">' + team_name + '</td><td class="shoot_his">' + player_num + '</td></tr>';
    }
    shoot_body.innerHTML = html;
    addClickEventToRows();
}

function shoot_filtering() {
    //シュートコースのセルの色の初期化
    for (let n = 0; n < goal1.length; n++) {
        if (goal1[n].id.includes('change_color_cell')) {
            goal1[n].removeAttribute('id');
        }
        if (goal2[n].id.includes('change_color_cell')) {
            goal2[n].removeAttribute('id');
        }
    }
    pv_click1 = false;
    pv_select1 = false;
    pv_click2 = false;
    pv_select2 = false;

    //時間帯の取得
    let select_time = $('input[name="time_radio"]:checked').val();
    if (extension == 1) {
        if (select_time == "all") {
            select = 1;
        } else if (select_time == "first") {
            select = 2;
        } else if (select_time == "latter") {
            select = 3;
        } else if (select_time == "third") {
            select = 4;
        } else if (select_time == "fourth") {
            select = 5;
        }
    } else {
        if (select_time == "all") {
            select = 1;
        } else if (select_time == "first") {
            select = 2;
        } else {
            select = 3;
        }
    }

    //攻撃の種類の取得
    let selectedValue = $('#swift_flag').val();
    if (selectedValue == 'default') {
        swift_flag = 1;
    } else if (selectedValue == 'no-swift') {
        swift_flag = 2;
    } else {
        swift_flag = 3;
    }

    //ポジションの取得
    let send_position = position;

    //チーム1の背番号の取得
    select_num1 = parseInt($('#number1').val());

    //チーム2の背番号の取得
    select_num2 = parseInt($('#number2').val());

    $.ajax({
        //送信方法
        type: "POST",
        //送信先ファイル名
        url: "./ajax/ajax_Filtering.php",
        //受け取りデータの種類
        datatype: "json",
        //送信データ
        data: {
            game_id: game_id,
            extension: extension,
            first_time: first_time,
            latter_time: latter_time,
            team_name1: team_name1,
            team_name2: team_name2,
            select: select,
            team_id1: team_id1,
            position: send_position,
            team_id2: team_id2,
            swift_flag: swift_flag,
            number1: select_num1,
            number2: select_num2,
            third_time: third_time,
            fourth_time: fourth_time,
        },
    }).then(
        //成功時の処理
        function (result) {
            show_table = result.shoot_table;
            table_filtering();
            reset_canvas_path();
            draw_only_img(1);
            draw_only_img(2);
            input_goal_position(result.shoot_course, result.shoot_course_s);
        },
        //エラーの時の処理
        function (XMLHttpRequest, textStatus, errorThrown) {
            console.log("通信失敗!!!");
            console.log("XMLHttpRequest : " + XMLHttpRequest.status);
            console.log("textStatus : " + textStatus);
            console.log("errorThrown : " + errorThrown.message);
        }
    );

    resetCanvas();
    resetCanvas2();

    drawCanvasPath();
    drawCanvasPath2();

    table_filtering();

    if (select == 1) {
        drawCanvasPosition();
        drawCanvasPosition2();
    } else if (select == 2) {

    }
}

function shoot_course_input1() {
    document.getElementById("T1").innerHTML = shoot_course1.T;
    document.getElementById("L1").innerHTML = shoot_course1.L;
    document.getElementById("R1").innerHTML = shoot_course1.R;
    document.getElementById("TL1").innerHTML = shoot_course1.TL_s + ' / ' + shoot_course1.TL;
    document.getElementById("TR1").innerHTML = shoot_course1.TR_s + ' / ' + shoot_course1.TR;
    document.getElementById("BL1").innerHTML = shoot_course1.BL_s + ' / ' + shoot_course1.BL;
    document.getElementById("BR1").innerHTML = shoot_course1.BR_s + ' / ' + shoot_course1.BR;
    if (TL1 == '0') {
        document.getElementById("TL_r1").innerHTML = '-- %';
    } else {
        document.getElementById("TL_r1").innerHTML = shoot_course1.TL_r + ' %';
    }
    if (TR1 == '0') {
        document.getElementById("TR_r1").innerHTML = '-- %';
    } else {
        document.getElementById("TR_r1").innerHTML = shoot_course1.TR_r + ' %';
    }
    if (BL1 == '0') {
        document.getElementById("BL_r1").innerHTML = '-- %';
    } else {
        document.getElementById("BL_r1").innerHTML = shoot_course1.BL_r + ' %';
    }
    if (BR1 == '0') {
        document.getElementById("BR_r1").innerHTML = '-- %';
    } else {
        document.getElementById("BR_r1").innerHTML = shoot_course1.BR_r + ' %';
    }
}

function shoot_course_input2() {
    document.getElementById("T2").innerHTML = shoot_course2.T;
    document.getElementById("L2").innerHTML = shoot_course2.L;
    document.getElementById("R2").innerHTML = shoot_course2.R;
    document.getElementById("TL2").innerHTML = shoot_course2.TL_s + ' / ' + shoot_course2.TL;
    document.getElementById("TR2").innerHTML = shoot_course2.TR_s + ' / ' + shoot_course2.TR;
    document.getElementById("BL2").innerHTML = shoot_course2.BL_s + ' / ' + shoot_course2.BL;
    document.getElementById("BR2").innerHTML = shoot_course2.BR_s + ' / ' + shoot_course2.BR;
    if (TL1 == '0') {
        document.getElementById("TL_r2").innerHTML = '-- %';
    } else {
        document.getElementById("TL_r2").innerHTML = shoot_course2.TL_r + ' %';
    }
    if (TR1 == '0') {
        document.getElementById("TR_r2").innerHTML = '-- %';
    } else {
        document.getElementById("TR_r2").innerHTML = shoot_course2.TR_r + ' %';
    }
    if (BL1 == '0') {
        document.getElementById("BL_r2").innerHTML = '-- %';
    } else {
        document.getElementById("BL_r2").innerHTML = shoot_course2.BL_r + ' %';
    }
    if (BR1 == '0') {
        document.getElementById("BR_r2").innerHTML = '-- %';
    } else {
        document.getElementById("BR_r2").innerHTML = shoot_course2.BR_r + ' %';
    }
}

//ラジオボタンでチーム１がクリックされたとき
function All_select() {
    for (let n = 0; n < goal1.length; n++) {
        if (goal1[n].id.includes('change_color_cell')) {
            goal1[n].removeAttribute('id');
        }
        if (goal2[n].id.includes('change_color_cell')) {
            goal2[n].removeAttribute('id');
        }
    }
    select = 1;

    xy_parse = all_shoot1; //軌跡の置き換え

    show_table = shoot_tb;

    table_filtering();

    resetCanvas();

    //canvas（シュート率表示・ドラッグ履歴）の再描画
    drawCanvasPosition();

    document.getElementById("T1").innerHTML = T1;
    document.getElementById("L1").innerHTML = L1;
    document.getElementById("R1").innerHTML = R1;
    document.getElementById("TL1").innerHTML = TL_s1 + ' / ' + TL1;
    document.getElementById("TR1").innerHTML = TR_s1 + ' / ' + TR1;
    document.getElementById("BL1").innerHTML = BL_s1 + ' / ' + BL1;
    document.getElementById("BR1").innerHTML = BR_s1 + ' / ' + BR1;
    if (TL1 == '0') {
        document.getElementById("TL_r1").innerHTML = '-- %';
    } else {
        document.getElementById("TL_r1").innerHTML = TL_r1 + ' %';
    }
    if (TR1 == '0') {
        document.getElementById("TR_r1").innerHTML = '-- %';
    } else {
        document.getElementById("TR_r1").innerHTML = TR_r1 + ' %';
    }
    if (BL1 == '0') {
        document.getElementById("BL_r1").innerHTML = '-- %';
    } else {
        document.getElementById("BL_r1").innerHTML = BL_r1 + ' %';
    }
    if (BR1 == '0') {
        document.getElementById("BR_r1").innerHTML = '-- %';
    } else {
        document.getElementById("BR_r1").innerHTML = BR_r1 + ' %';
    }

    drawCanvasPath();
}

function All_select_both() {
    for (let n = 0; n < goal1.length; n++) {
        if (goal1[n].id.includes('change_color_cell')) {
            goal1[n].removeAttribute('id');
        }
        if (goal2[n].id.includes('change_color_cell')) {
            goal2[n].removeAttribute('id');
        }
    }
    select = 1;

    pv_click1 = false;
    pv_select1 = false;
    pv_click2 = false;
    pv_select2 = false;

    xy_parse = all_shoot1; //軌跡の置き換え

    $('#swift_flag').val('default');

    swift_flag = 1;

    resetCanvas();

    show_table = shoot_tb;

    table_filtering();

    //canvas（シュート率表示・ドラッグ履歴）の再描画
    drawCanvasPosition();

    document.getElementById("T1").innerHTML = T1;
    document.getElementById("L1").innerHTML = L1;
    document.getElementById("R1").innerHTML = R1;
    document.getElementById("TL1").innerHTML = TL_s1 + ' / ' + TL1;
    document.getElementById("TR1").innerHTML = TR_s1 + ' / ' + TR1;
    document.getElementById("BL1").innerHTML = BL_s1 + ' / ' + BL1;
    document.getElementById("BR1").innerHTML = BR_s1 + ' / ' + BR1;
    if (TL1 == '0') {
        document.getElementById("TL_r1").innerHTML = '-- %';
    } else {
        document.getElementById("TL_r1").innerHTML = TL_r1 + ' %';
    }
    if (TR1 == '0') {
        document.getElementById("TR_r1").innerHTML = '-- %';
    } else {
        document.getElementById("TR_r1").innerHTML = TR_r1 + ' %';
    }
    if (BL1 == '0') {
        document.getElementById("BL_r1").innerHTML = '-- %';
    } else {
        document.getElementById("BL_r1").innerHTML = BL_r1 + ' %';
    }
    if (BR1 == '0') {
        document.getElementById("BR_r1").innerHTML = '-- %';
    } else {
        document.getElementById("BR_r1").innerHTML = BR_r1 + ' %';
    }

    drawCanvasPath();

    xy_parse2 = all_shoot2; //軌跡の置き換え

    resetCanvas2();

    //canvas（シュート率表示・ドラッグ履歴）の再描画
    drawCanvasPosition2();

    document.getElementById("T2").innerHTML = T2;
    document.getElementById("L2").innerHTML = L2;
    document.getElementById("R2").innerHTML = R2;
    document.getElementById("TL2").innerHTML = TL_s2 + ' / ' + TL2;
    document.getElementById("TR2").innerHTML = TR_s2 + ' / ' + TR2;
    document.getElementById("BL2").innerHTML = BL_s2 + ' / ' + BL2;
    document.getElementById("BR2").innerHTML = BR_s2 + ' / ' + BR2;
    if (TL2 == '0') {
        document.getElementById("TL_r2").innerHTML = '-- %';
    } else {
        document.getElementById("TL_r2").innerHTML = TL_r2 + ' %';
    }
    if (TR2 == '0') {
        document.getElementById("TR_r2").innerHTML = '-- %';
    } else {
        document.getElementById("TR_r2").innerHTML = TR_r2 + ' %';
    }
    if (BL2 == '0') {
        document.getElementById("BL_r2").innerHTML = '-- %';
    } else {
        document.getElementById("BL_r2").innerHTML = BL_r2 + ' %';
    }
    if (BR2 == '0') {
        document.getElementById("BR_r2").innerHTML = '-- %';
    } else {
        document.getElementById("BR_r2").innerHTML = BR_r2 + ' %';
    }

    drawCanvasPath2();
}

function First_select() {
    for (let n = 0; n < goal1.length; n++) {
        if (goal1[n].id.includes('change_color_cell')) {
            goal1[n].removeAttribute('id');
        }
        if (goal2[n].id.includes('change_color_cell')) {
            goal2[n].removeAttribute('id');
        }
    }
    select = 2;

    xy_parse = first_shoot1; //軌跡の置き換え

    resetCanvas();

    show_table = shoot_tb_first;

    table_filtering();

    //canvas（シュート率表示・ドラッグ履歴）の再描画
    draw(1, FLW1, FPV1, FRW1, FL61, FC61, FR61, FL91, FC91, FR91, Fseven1, FLW_s1, FPV_s1, FRW_s1, FL6_s1, FC6_s1, FR6_s1, FL9_s1, FC9_s1, FR9_s1, Fseven_s1, FLW_r1, FPV_r1, FRW_r1, FL6_r1, FC6_r1, FR6_r1, FL9_r1, FC9_r1, FR9_r1, Fseven_r1);

    document.getElementById("T1").innerHTML = FT1;
    document.getElementById("L1").innerHTML = FL1;
    document.getElementById("R1").innerHTML = FR1;
    document.getElementById("TL1").innerHTML = FTL_s1 + ' / ' + FTL1;
    document.getElementById("TR1").innerHTML = FTR_s1 + ' / ' + FTR1;
    document.getElementById("BL1").innerHTML = FBL_s1 + ' / ' + FBL1;
    document.getElementById("BR1").innerHTML = FBR_s1 + ' / ' + FBR1;
    if (FTL1 == '0') {
        document.getElementById("TL_r1").innerHTML = '-- %';
    } else {
        document.getElementById("TL_r1").innerHTML = FTL_r1 + ' %';
    }
    if (FTR1 == '0') {
        document.getElementById("TR_r1").innerHTML = '-- %';
    } else {
        document.getElementById("TR_r1").innerHTML = FTR_r1 + ' %';
    }
    if (FBL1 == '0') {
        document.getElementById("BL_r1").innerHTML = '-- %';
    } else {
        document.getElementById("BL_r1").innerHTML = FBL_r1 + ' %';
    }
    if (FBR1 == '0') {
        document.getElementById("BR_r1").innerHTML = '-- %';
    } else {
        document.getElementById("BR_r1").innerHTML = FBR_r1 + ' %';
    }

    drawCanvasPath();
}

function First_select_both() {
    for (let n = 0; n < goal1.length; n++) {
        if (goal1[n].id.includes('change_color_cell')) {
            goal1[n].removeAttribute('id');
        }
        if (goal2[n].id.includes('change_color_cell')) {
            goal2[n].removeAttribute('id');
        }
    }
    select = 2;

    pv_click1 = false;
    pv_select1 = false;
    pv_click2 = false;
    pv_select2 = false;

    xy_parse = first_shoot1; //軌跡の置き換え

    resetCanvas();

    $('#swift_flag').val('default');

    swift_flag = 1;

    show_table = shoot_tb_first;

    table_filtering();

    //canvas（シュート率表示・ドラッグ履歴）の再描画
    draw(1, FLW1, FPV1, FRW1, FL61, FC61, FR61, FL91, FC91, FR91, Fseven1, FLW_s1, FPV_s1, FRW_s1, FL6_s1, FC6_s1, FR6_s1, FL9_s1, FC9_s1, FR9_s1, Fseven_s1, FLW_r1, FPV_r1, FRW_r1, FL6_r1, FC6_r1, FR6_r1, FL9_r1, FC9_r1, FR9_r1, Fseven_r1);

    document.getElementById("T1").innerHTML = FT1;
    document.getElementById("L1").innerHTML = FL1;
    document.getElementById("R1").innerHTML = FR1;
    document.getElementById("TL1").innerHTML = FTL_s1 + ' / ' + FTL1;
    document.getElementById("TR1").innerHTML = FTR_s1 + ' / ' + FTR1;
    document.getElementById("BL1").innerHTML = FBL_s1 + ' / ' + FBL1;
    document.getElementById("BR1").innerHTML = FBR_s1 + ' / ' + FBR1;
    if (FTL1 == '0') {
        document.getElementById("TL_r1").innerHTML = '-- %';
    } else {
        document.getElementById("TL_r1").innerHTML = FTL_r1 + ' %';
    }
    if (FTR1 == '0') {
        document.getElementById("TR_r1").innerHTML = '-- %';
    } else {
        document.getElementById("TR_r1").innerHTML = FTR_r1 + ' %';
    }
    if (FBL1 == '0') {
        document.getElementById("BL_r1").innerHTML = '-- %';
    } else {
        document.getElementById("BL_r1").innerHTML = FBL_r1 + ' %';
    }
    if (FBR1 == '0') {
        document.getElementById("BR_r1").innerHTML = '-- %';
    } else {
        document.getElementById("BR_r1").innerHTML = FBR_r1 + ' %';
    }

    drawCanvasPath();

    xy_parse2 = first_shoot2; //軌跡の置き換え

    resetCanvas2();

    //canvas（シュート率表示・ドラッグ履歴）の再描画
    draw(2, FLW2, FPV2, FRW2, FL62, FC62, FR62, FL92, FC92, FR92, Fseven2, FLW_s2, FPV_s2, FRW_s2, FL6_s2, FC6_s2, FR6_s2, FL9_s2, FC9_s2, FR9_s2, Fseven_s2, FLW_r2, FPV_r2, FRW_r2, FL6_r2, FC6_r2, FR6_r2, FL9_r2, FC9_r2, FR9_r2, Fseven_r2);

    document.getElementById("T2").innerHTML = FT2;
    document.getElementById("L2").innerHTML = FL2;
    document.getElementById("R2").innerHTML = FR2;
    document.getElementById("TL2").innerHTML = FTL_s2 + ' / ' + FTL2;
    document.getElementById("TR2").innerHTML = FTR_s2 + ' / ' + FTR2;
    document.getElementById("BL2").innerHTML = FBL_s2 + ' / ' + FBL2;
    document.getElementById("BR2").innerHTML = FBR_s2 + ' / ' + FBR2;
    if (FTL2 == '0') {
        document.getElementById("TL_r2").innerHTML = '-- %';
    } else {
        document.getElementById("TL_r2").innerHTML = FTL_r2 + ' %';
    }
    if (FTR2 == '0') {
        document.getElementById("TR_r2").innerHTML = '-- %';
    } else {
        document.getElementById("TR_r2").innerHTML = FTR_r2 + ' %';
    }
    if (FBL2 == '0') {
        document.getElementById("BL_r2").innerHTML = '-- %';
    } else {
        document.getElementById("BL_r2").innerHTML = FBL_r2 + ' %';
    }
    if (FBR2 == '0') {
        document.getElementById("BR_r2").innerHTML = '-- %';
    } else {
        document.getElementById("BR_r2").innerHTML = FBR_r2 + ' %';
    }

    drawCanvasPath2();
}

function Latter_select() {
    for (let n = 0; n < goal1.length; n++) {
        if (goal1[n].id.includes('change_color_cell')) {
            goal1[n].removeAttribute('id');
        }
        if (goal2[n].id.includes('change_color_cell')) {
            goal2[n].removeAttribute('id');
        }
    }
    select = 3;
    xy_parse = latter_shoot1; //軌跡の置き換え

    resetCanvas();

    show_table = shoot_tb_latter;

    table_filtering();

    //canvas（シュート率表示・ドラッグ履歴）の再描画
    draw(1, LLW1, LPV1, LRW1, LL61, LC61, LR61, LL91, LC91, LR91, Lseven1, LLW_s1, LPV_s1, LRW_s1, LL6_s1, LC6_s1, LR6_s1, LL9_s1, LC9_s1, LR9_s1, Lseven_s1, LLW_r1, LPV_r1, LRW_r1, LL6_r1, LC6_r1, LR6_r1, LL9_r1, LC9_r1, LR9_r1, Lseven_r1);

    document.getElementById("T1").innerHTML = LT1;
    document.getElementById("L1").innerHTML = LL1;
    document.getElementById("R1").innerHTML = LR1;
    document.getElementById("TL1").innerHTML = LTL_s1 + ' / ' + LTL1;
    document.getElementById("TR1").innerHTML = LTR_s1 + ' / ' + LTR1;
    document.getElementById("BL1").innerHTML = LBL_s1 + ' / ' + LBL1;
    document.getElementById("BR1").innerHTML = LBR_s1 + ' / ' + LBR1;
    if (LTL1 == '0') {
        document.getElementById("TL_r1").innerHTML = '-- %';
    } else {
        document.getElementById("TL_r1").innerHTML = LTL_r1 + ' %';
    }
    if (LTR1 == '0') {
        document.getElementById("TR_r1").innerHTML = '-- %';
    } else {
        document.getElementById("TR_r1").innerHTML = LTR_r1 + ' %';
    }
    if (LBL1 == '0') {
        document.getElementById("BL_r1").innerHTML = '-- %';
    } else {
        document.getElementById("BL_r1").innerHTML = LBL_r1 + ' %';
    }
    if (LBR1 == '0') {
        document.getElementById("BR_r1").innerHTML = '-- %';
    } else {
        document.getElementById("BR_r1").innerHTML = LBR_r1 + ' %';
    }

    drawCanvasPath();
}

function Latter_select_both() {
    for (let n = 0; n < goal1.length; n++) {
        if (goal1[n].id.includes('change_color_cell')) {
            goal1[n].removeAttribute('id');
        }
        if (goal2[n].id.includes('change_color_cell')) {
            goal2[n].removeAttribute('id');
        }
    }
    select = 3;
    xy_parse = latter_shoot1; //軌跡の置き換え

    pv_click1 = false;
    pv_select1 = false;
    pv_click2 = false;
    pv_select2 = false;

    resetCanvas();

    $('#swift_flag').val('default');

    swift_flag = 1;

    show_table = shoot_tb_latter;

    table_filtering();

    //canvas（シュート率表示・ドラッグ履歴）の再描画
    draw(1, LLW1, LPV1, LRW1, LL61, LC61, LR61, LL91, LC91, LR91, Lseven1, LLW_s1, LPV_s1, LRW_s1, LL6_s1, LC6_s1, LR6_s1, LL9_s1, LC9_s1, LR9_s1, Lseven_s1, LLW_r1, LPV_r1, LRW_r1, LL6_r1, LC6_r1, LR6_r1, LL9_r1, LC9_r1, LR9_r1, Lseven_r1);

    document.getElementById("T1").innerHTML = LT1;
    document.getElementById("L1").innerHTML = LL1;
    document.getElementById("R1").innerHTML = LR1;
    document.getElementById("TL1").innerHTML = LTL_s1 + ' / ' + LTL1;
    document.getElementById("TR1").innerHTML = LTR_s1 + ' / ' + LTR1;
    document.getElementById("BL1").innerHTML = LBL_s1 + ' / ' + LBL1;
    document.getElementById("BR1").innerHTML = LBR_s1 + ' / ' + LBR1;
    if (LTL1 == '0') {
        document.getElementById("TL_r1").innerHTML = '-- %';
    } else {
        document.getElementById("TL_r1").innerHTML = LTL_r1 + ' %';
    }
    if (LTR1 == '0') {
        document.getElementById("TR_r1").innerHTML = '-- %';
    } else {
        document.getElementById("TR_r1").innerHTML = LTR_r1 + ' %';
    }
    if (LBL1 == '0') {
        document.getElementById("BL_r1").innerHTML = '-- %';
    } else {
        document.getElementById("BL_r1").innerHTML = LBL_r1 + ' %';
    }
    if (LBR1 == '0') {
        document.getElementById("BR_r1").innerHTML = '-- %';
    } else {
        document.getElementById("BR_r1").innerHTML = LBR_r1 + ' %';
    }

    drawCanvasPath();

    xy_parse2 = latter_shoot2; //軌跡の置き換え

    resetCanvas2();

    //canvas（シュート率表示・ドラッグ履歴）の再描画
    draw(2, LLW2, LPV2, LRW2, LL62, LC62, LR62, LL92, LC92, LR92, Lseven2, LLW_s2, LPV_s2, LRW_s2, LL6_s2, LC6_s2, LR6_s2, LL9_s2, LC9_s2, LR9_s2, Lseven_s2, LLW_r2, LPV_r2, LRW_r2, LL6_r2, LC6_r2, LR6_r2, LL9_r2, LC9_r2, LR9_r2, Lseven_r2);

    document.getElementById("T2").innerHTML = LT2;
    document.getElementById("L2").innerHTML = LL2;
    document.getElementById("R2").innerHTML = LR2;
    document.getElementById("TL2").innerHTML = LTL_s2 + ' / ' + LTL2;
    document.getElementById("TR2").innerHTML = LTR_s2 + ' / ' + LTR2;
    document.getElementById("BL2").innerHTML = LBL_s2 + ' / ' + LBL2;
    document.getElementById("BR2").innerHTML = LBR_s2 + ' / ' + LBR2;
    if (LTL2 == '0') {
        document.getElementById("TL_r2").innerHTML = '-- %';
    } else {
        document.getElementById("TL_r2").innerHTML = LTL_r2 + ' %';
    }
    if (LTR2 == '0') {
        document.getElementById("TR_r2").innerHTML = '-- %';
    } else {
        document.getElementById("TR_r2").innerHTML = LTR_r2 + ' %';
    }
    if (LBL2 == '0') {
        document.getElementById("BL_r2").innerHTML = '-- %';
    } else {
        document.getElementById("BL_r2").innerHTML = LBL_r2 + ' %';
    }
    if (LBR2 == '0') {
        document.getElementById("BR_r2").innerHTML = '-- %';
    } else {
        document.getElementById("BR_r2").innerHTML = LBR_r2 + ' %';
    }

    drawCanvasPath2();
}



function All_select2() {
    select = 1;
    xy_parse2 = all_shoot2; //軌跡の置き換え

    resetCanvas2();

    show_table = shoot_tb;

    table_filtering();

    //canvas（シュート率表示・ドラッグ履歴）の再描画
    drawCanvasPosition2();

    document.getElementById("T2").innerHTML = T2;
    document.getElementById("L2").innerHTML = L2;
    document.getElementById("R2").innerHTML = R2;
    document.getElementById("TL2").innerHTML = TL_s2 + ' / ' + TL2;
    document.getElementById("TR2").innerHTML = TR_s2 + ' / ' + TR2;
    document.getElementById("BL2").innerHTML = BL_s2 + ' / ' + BL2;
    document.getElementById("BR2").innerHTML = BR_s2 + ' / ' + BR2;
    if (TL2 == '0') {
        document.getElementById("TL_r2").innerHTML = '-- %';
    } else {
        document.getElementById("TL_r2").innerHTML = TL_r2 + ' %';
    }
    if (TR2 == '0') {
        document.getElementById("TR_r2").innerHTML = '-- %';
    } else {
        document.getElementById("TR_r2").innerHTML = TR_r2 + ' %';
    }
    if (BL2 == '0') {
        document.getElementById("BL_r2").innerHTML = '-- %';
    } else {
        document.getElementById("BL_r2").innerHTML = BL_r2 + ' %';
    }
    if (BR2 == '0') {
        document.getElementById("BR_r2").innerHTML = '-- %';
    } else {
        document.getElementById("BR_r2").innerHTML = BR_r2 + ' %';
    }

    drawCanvasPath2();
}

function First_select2() {
    select = 2;
    xy_parse2 = first_shoot2; //軌跡の置き換え

    resetCanvas2();

    show_table = shoot_tb_first;

    table_filtering();

    //canvas（シュート率表示・ドラッグ履歴）の再描画
    draw(2, FLW2, FPV2, FRW2, FL62, FC62, FR62, FL92, FC92, FR92, Fseven2, FLW_s2, FPV_s2, FRW_s2, FL6_s2, FC6_s2, FR6_s2, FL9_s2, FC9_s2, FR9_s2, Fseven_s2, FLW_r2, FPV_r2, FRW_r2, FL6_r2, FC6_r2, FR6_r2, FL9_r2, FC9_r2, FR9_r2, Fseven_r2);

    document.getElementById("T2").innerHTML = FT2;
    document.getElementById("L2").innerHTML = FL2;
    document.getElementById("R2").innerHTML = FR2;
    document.getElementById("TL2").innerHTML = FTL_s2 + ' / ' + FTL2;
    document.getElementById("TR2").innerHTML = FTR_s2 + ' / ' + FTR2;
    document.getElementById("BL2").innerHTML = FBL_s2 + ' / ' + FBL2;
    document.getElementById("BR2").innerHTML = FBR_s2 + ' / ' + FBR2;
    if (FTL2 == '0') {
        document.getElementById("TL_r2").innerHTML = '-- %';
    } else {
        document.getElementById("TL_r2").innerHTML = FTL_r2 + ' %';
    }
    if (FTR2 == '0') {
        document.getElementById("TR_r2").innerHTML = '-- %';
    } else {
        document.getElementById("TR_r2").innerHTML = FTR_r2 + ' %';
    }
    if (FBL2 == '0') {
        document.getElementById("BL_r2").innerHTML = '-- %';
    } else {
        document.getElementById("BL_r2").innerHTML = FBL_r2 + ' %';
    }
    if (FBR2 == '0') {
        document.getElementById("BR_r2").innerHTML = '-- %';
    } else {
        document.getElementById("BR_r2").innerHTML = FBR_r2 + ' %';
    }

    drawCanvasPath2();
}

function Latter_select2() {
    select = 3;
    xy_parse2 = latter_shoot2; //軌跡の置き換え

    resetCanvas2();

    show_table = shoot_tb_latter;

    table_filtering();

    //canvas（シュート率表示・ドラッグ履歴）の再描画
    draw(2, LLW2, LPV2, LRW2, LL62, LC62, LR62, LL92, LC92, LR92, Lseven2, LLW_s2, LPV_s2, LRW_s2, LL6_s2, LC6_s2, LR6_s2, LL9_s2, LC9_s2, LR9_s2, Lseven_s2, LLW_r2, LPV_r2, LRW_r2, LL6_r2, LC6_r2, LR6_r2, LL9_r2, LC9_r2, LR9_r2, Lseven_r2);

    document.getElementById("T2").innerHTML = LT2;
    document.getElementById("L2").innerHTML = LL2;
    document.getElementById("R2").innerHTML = LR2;
    document.getElementById("TL2").innerHTML = LTL_s2 + ' / ' + LTL2;
    document.getElementById("TR2").innerHTML = LTR_s2 + ' / ' + LTR2;
    document.getElementById("BL2").innerHTML = LBL_s2 + ' / ' + LBL2;
    document.getElementById("BR2").innerHTML = LBR_s2 + ' / ' + LBR2;
    if (LTL2 == '0') {
        document.getElementById("TL_r2").innerHTML = '-- %';
    } else {
        document.getElementById("TL_r2").innerHTML = LTL_r2 + ' %';
    }
    if (LTR2 == '0') {
        document.getElementById("TR_r2").innerHTML = '-- %';
    } else {
        document.getElementById("TR_r2").innerHTML = LTR_r2 + ' %';
    }
    if (LBL2 == '0') {
        document.getElementById("BL_r2").innerHTML = '-- %';
    } else {
        document.getElementById("BL_r2").innerHTML = LBL_r2 + ' %';
    }
    if (LBR2 == '0') {
        document.getElementById("BR_r2").innerHTML = '-- %';
    } else {
        document.getElementById("BR_r2").innerHTML = LBR_r2 + ' %';
    }

    drawCanvasPath2();
}

//canvasのリセット
function resetCanvas() {
    ctx.clearRect(0, 0, canvas_path.width, canvas_path.height);
    c.clearRect(0, 0, cv.width, cv.height);
}

function resetOnlyPath() {
    ctx.clearRect(0, 0, canvas_path.width, canvas_path.height);
}

function resetCanvas2() {
    ctx2.clearRect(0, 0, canvas_path2.width, canvas_path2.height);
    c2.clearRect(0, 0, cv2.width, cv2.height);
}

function resetOnlyPath2() {
    ctx2.clearRect(0, 0, canvas_path2.width, canvas_path2.height);
}

function reset_canvas_path() {
    ctx.clearRect(0, 0, canvas_path.width, canvas_path.height);
    ctx2.clearRect(0, 0, canvas_path2.width, canvas_path2.height);
}

$(".openbtn").click(function () {//ボタンがクリックされたら
    $(this).toggleClass('active');//ボタン自身に activeクラスを付与し
    $("#g-nav").toggleClass('panelactive');//ナビゲーションにpanelactiveクラスを付与
});

$("#g-nav a").click(function () {//ナビゲーションのリンクがクリックされたら
    $(".openbtn").removeClass('active');//ボタンの activeクラスを除去し
    $("#g-nav").removeClass('panelactive');//ナビゲーションのpanelactiveクラスも除去
});

function input_goal_position(goal_position, goal_position_s) {
    const TL_r = (goal_position_s.TL / goal_position.TL * 100).toFixed(1);
    const TR_r = (goal_position_s.TR / goal_position.TR * 100).toFixed(1);
    const BL_r = (goal_position_s.BL / goal_position.BL * 100).toFixed(1);
    const BR_r = (goal_position_s.BR / goal_position.BR * 100).toFixed(1);
    document.getElementById("T1").innerHTML = goal_position.T;
    document.getElementById("L1").innerHTML = goal_position.L;
    document.getElementById("R1").innerHTML = goal_position.R;
    document.getElementById("TL1").innerHTML = goal_position_s.TL + ' / ' + goal_position.TL;
    document.getElementById("TR1").innerHTML = goal_position_s.TR + ' / ' + goal_position.TR;
    document.getElementById("BL1").innerHTML = goal_position_s.BL + ' / ' + goal_position.BL;
    document.getElementById("BR1").innerHTML = goal_position_s.BR + ' / ' + goal_position.BR;
    if (goal_position.TL == '0') {
        document.getElementById("TL_r1").innerHTML = '-- %';
    } else {
        document.getElementById("TL_r1").innerHTML = TL_r + ' %';
    }
    if (goal_position.TR == '0') {
        document.getElementById("TR_r1").innerHTML = '-- %';
    } else {
        document.getElementById("TR_r1").innerHTML = TR_r + ' %';
    }
    if (goal_position.BL == '0') {
        document.getElementById("BL_r1").innerHTML = '-- %';
    } else {
        document.getElementById("BL_r1").innerHTML = BL_r + ' %';
    }
    if (goal_position.BR == '0') {
        document.getElementById("BR_r1").innerHTML = '-- %';
    } else {
        document.getElementById("BR_r1").innerHTML = BR_r + ' %';
    }
}

function input_goal_position2(goal_position, goal_position_s) {
    const TL_r = (goal_position_s.TL / goal_position.TL * 100).toFixed(1);
    const TR_r = (goal_position_s.TR / goal_position.TR * 100).toFixed(1);
    const BL_r = (goal_position_s.BL / goal_position.BL * 100).toFixed(1);
    const BR_r = (goal_position_s.BR / goal_position.BR * 100).toFixed(1);
    document.getElementById("T2").innerHTML = goal_position.T;
    document.getElementById("L2").innerHTML = goal_position.L;
    document.getElementById("R2").innerHTML = goal_position.R;
    document.getElementById("TL2").innerHTML = goal_position_s.TL + ' / ' + goal_position.TL;
    document.getElementById("TR2").innerHTML = goal_position_s.TR + ' / ' + goal_position.TR;
    document.getElementById("BL2").innerHTML = goal_position_s.BL + ' / ' + goal_position.BL;
    document.getElementById("BR2").innerHTML = goal_position_s.BR + ' / ' + goal_position.BR;
    if (goal_position.TL == '0') {
        document.getElementById("TL_r2").innerHTML = '-- %';
    } else {
        document.getElementById("TL_r2").innerHTML = TL_r + ' %';
    }
    if (goal_position.TR == '0') {
        document.getElementById("TR_r2").innerHTML = '-- %';
    } else {
        document.getElementById("TR_r2").innerHTML = TR_r + ' %';
    }
    if (goal_position.BL == '0') {
        document.getElementById("BL_r2").innerHTML = '-- %';
    } else {
        document.getElementById("BL_r2").innerHTML = BL_r + ' %';
    }
    if (goal_position.BR == '0') {
        document.getElementById("BR_r2").innerHTML = '-- %';
    } else {
        document.getElementById("BR_r2").innerHTML = BR_r + ' %';
    }
}

function resizeCanvas() {
    show_popup_count = 0;
    screen_width = window.innerWidth;
    console.log(screen_width);
    if (isTouchDevice) {
        cv.width = window.innerWidth * 0.24;
        cv.height = window.innerHeight * 0.28;
        cv2.width = window.innerWidth * 0.24;
        cv2.height = window.innerHeight * 0.28;
        canvas_path.width = window.innerWidth * 0.4;
        canvas_path.height = window.innerHeight * 0.4;
        canvas_path2.width = window.innerWidth * 0.4;
        canvas_path2.height = window.innerHeight * 0.4;
        console.log("1280未満");
    } else {
        cv.width = window.innerWidth * 0.22;
        cv.height = window.innerHeight * 0.28;
        cv2.width = window.innerWidth * 0.22;
        cv2.height = window.innerHeight * 0.28;
        canvas_path.width = window.innerWidth * 0.39;
        canvas_path.height = window.innerHeight * 0.43;
        canvas_path2.width = window.innerWidth * 0.39;
        canvas_path2.height = window.innerHeight * 0.43;
        console.log("1280以上");
    }
    px = cv.width;
    py = cv.height;

    if (select == 1) {
        All_select_both();
    } else if (select == 2) {
        First_select_both();
    } else if (select == 3) {
        Latter_select_both();
    }
}

window.addEventListener('resize', resizeCanvas);

function seven_show() {
    reset_position();
    c.beginPath();
    // 始点／終点を設定（2）
    c.moveTo(0.21 * px, 0 * py);
    c.lineTo(0.79 * px, 0 * py);
    c.lineTo(0.79 * px, 0.09 * py);
    c.lineTo(0.21 * px, 0.09 * py);
    c.lineTo(0.21 * px, 0 * py);
    c.closePath();
    c.fillStyle = `rgba(255, 0, 0, 0.5)`;
    c.fill();
    $.ajax({
        //送信方法
        type: "POST",
        //送信先ファイル名
        url: "./ajax/ajax_SelectSeven.php",
        //受け取りデータの種類
        datatype: "json",
        //送信データ
        data: {
            game_id: game_id,
            first_time: first_time,
            latter_time: latter_time,
            team_name: team_name1,
            select: select,
            team_id: team_id1,
            swift_flag: swift_flag,
        },
    }).then(
        //成功時の処理
        function (result) {
            show_table = result.shoot_table;
            table_filtering();
            reset_canvas_path();
            draw_only_img(1);
            draw_only_img(2);
            input_goal_position(result.shoot_course, result.shoot_course_s);
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

// 7mがクリックされたとき
function seven_show2() {
    reset_position2();
    c2.beginPath();
    // 始点／終点を設定（2）
    c2.moveTo(0.21 * px, 0 * py);
    c2.lineTo(0.79 * px, 0 * py);
    c2.lineTo(0.79 * px, 0.09 * py);
    c2.lineTo(0.21 * px, 0.09 * py);
    c2.lineTo(0.21 * px, 0 * py);
    c2.closePath();
    c2.fillStyle = `rgba(255, 0, 0, 0.5)`;
    c2.fill();
    $.ajax({
        //送信方法
        type: "POST",
        //送信先ファイル名
        url: "./ajax/ajax_SelectSeven.php",
        //受け取りデータの種類
        datatype: "json",
        //送信データ
        data: {
            game_id: game_id,
            first_time: first_time,
            latter_time: latter_time,
            team_name: team_name2,
            select: select,
            team_id: team_id2,
            swift_flag: swift_flag,
        },
    }).then(
        //成功時の処理
        function (result) {
            show_table = result.shoot_table;
            table_filtering();
            reset_canvas_path();
            draw_only_img(1);
            draw_only_img(2);
            input_goal_position2(result.shoot_course, result.shoot_course_s);
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

//チーム1
function drawPolygon(color) {
    reset_position();
    // console.log(position);
    if (position == 1) {
        c.beginPath();
        c.moveTo(0, 0);
        c.lineTo(0.12 * px, 0);
        c.lineTo(0.21 * px, 0.2 * py);
        c.lineTo(0, 0.45 * py);
        c.closePath();
    } else if (position == 2) {
        c.beginPath();
        c.moveTo(0.21 * px, 0.11 * py);
        c.lineTo(0.79 * px, 0.11 * py);
        c.lineTo(0.79 * px, 0.2 * py);
        c.lineTo(0.21 * px, 0.2 * py);
        c.lineTo(0.21 * px, 0.11 * py);
        c.closePath();
    } else if (position == 3) {
        c.beginPath();
        c.moveTo(px, 0);
        c.lineTo(0.88 * px, 0);
        c.lineTo(0.79 * px, 0.2 * py);
        c.lineTo(px, 0.45 * py);
        c.closePath();
    } else if (position == 4) {
        //左上
        c.beginPath();
        c.moveTo(0, 0.45 * py);
        c.lineTo(0.21 * px, 0.2 * py);
        c.lineTo(0.36 * px, 0.27 * py);
        c.lineTo(0.36 * px, 0.7 * py);
        c.lineTo(0.15 * px, 0.63 * py);
        c.closePath();
    } else if (position == 5) {
        //中央上
        c.beginPath();
        c.moveTo(0.36 * px, 0.27 * py);
        c.lineTo(0.64 * px, 0.27 * py);
        c.lineTo(0.64 * px, 0.7 * py);
        c.lineTo(0.36 * px, 0.7 * py);
        c.closePath();
    } else if (position == 6) {
        //右上
        c.beginPath();
        c.moveTo(px, 0.45 * py);
        c.lineTo(0.79 * px, 0.2 * py);
        c.lineTo(0.64 * px, 0.27 * py);
        c.lineTo(0.64 * px, 0.7 * py);
        c.lineTo(0.85 * px, 0.63 * py);
        c.closePath();
    } else if (position == 7) {
        //左下
        c.beginPath();
        c.moveTo(0, 0.45 * py);
        c.lineTo(0.15 * px, 0.63 * py);
        c.lineTo(0.36 * px, 0.7 * py);
        c.lineTo(0.36 * px, py);
        c.lineTo(0, py);
        c.closePath();
    } else if (position == 8) {
        //中央下
        c.beginPath();
        c.moveTo(0.36 * px, 0.7 * py);
        c.lineTo(0.64 * px, 0.7 * py);
        c.lineTo(0.64 * px, py);
        c.lineTo(0.36 * px, py);
        c.closePath();
    } else if (position == 9) {
        //右下
        c.beginPath();
        c.moveTo(px, 0.45 * py);
        c.lineTo(0.85 * px, 0.63 * py);
        c.lineTo(0.64 * px, 0.7 * py);
        c.lineTo(0.64 * px, py);
        c.lineTo(px, py);
        c.closePath();
    }
    c.fillStyle = `rgba(255, 0, 0, 0.5)`;
    c.fill();
    if (color == "red") {
        $.ajax({
            //送信方法
            type: "POST",
            //送信先ファイル名
            url: "./ajax/ajax_SelectPosition.php",
            //受け取りデータの種類
            datatype: "json",
            //送信データ
            data: {
                game_id: game_id,
                position: position,
                first_time: first_time,
                latter_time: latter_time,
                team_name: team_name1,
                select: select,
                team_id: team_id1,
                swift_flag: swift_flag,
            },
        }).then(
            //成功時の処理
            function (result) {
                xy_parse = result.parse;
                show_table = result.shoot_table;
                table_filtering();
                resetOnlyPath();
                draw_only_img(2);
                reset_canvas_path();
                drawCanvasPath();
                input_goal_position(result.shoot_course, result.shoot_course_s);
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
}

//チーム2のポジションがクリックされたときのデータベース参照と描画
function drawPolygon2(color) {
    reset_position2();
    // console.log(position);
    if (position == 1) {
        c2.beginPath();
        c2.moveTo(0, 0);
        c2.lineTo(0.12 * px, 0);
        c2.lineTo(0.21 * px, 0.2 * py);
        c2.lineTo(0, 0.45 * py);
        c2.closePath();
    } else if (position == 2) {
        c2.beginPath();
        c2.moveTo(0.21 * px, 0.11 * py);
        c2.lineTo(0.79 * px, 0.11 * py);
        c2.lineTo(0.79 * px, 0.2 * py);
        c2.lineTo(0.21 * px, 0.2 * py);
        c2.lineTo(0.21 * px, 0.11 * py);
        c2.closePath();
    } else if (position == 3) {
        c2.beginPath();
        c2.moveTo(px, 0);
        c2.lineTo(0.88 * px, 0);
        c2.lineTo(0.79 * px, 0.2 * py);
        c2.lineTo(px, 0.45 * py);
        c2.closePath();
    } else if (position == 4) {
        //左上
        c2.beginPath();
        c2.moveTo(0, 0.45 * py);
        c2.lineTo(0.21 * px, 0.2 * py);
        c2.lineTo(0.36 * px, 0.27 * py);
        c2.lineTo(0.36 * px, 0.7 * py);
        c2.lineTo(0.15 * px, 0.63 * py);
        c2.closePath();
    } else if (position == 5) {
        //中央上
        c2.beginPath();
        c2.moveTo(0.36 * px, 0.27 * py);
        c2.lineTo(0.64 * px, 0.27 * py);
        c2.lineTo(0.64 * px, 0.7 * py);
        c2.lineTo(0.36 * px, 0.7 * py);
        c2.closePath();
    } else if (position == 6) {
        //右上
        c2.beginPath();
        c2.moveTo(px, 0.45 * py);
        c2.lineTo(0.79 * px, 0.2 * py);
        c2.lineTo(0.64 * px, 0.27 * py);
        c2.lineTo(0.64 * px, 0.7 * py);
        c2.lineTo(0.85 * px, 0.63 * py);
        c2.closePath();
    } else if (position == 7) {
        //左下
        c2.beginPath();
        c2.moveTo(0, 0.45 * py);
        c2.lineTo(0.15 * px, 0.63 * py);
        c2.lineTo(0.36 * px, 0.7 * py);
        c2.lineTo(0.36 * px, py);
        c2.lineTo(0, py);
        c2.closePath();
    } else if (position == 8) {
        //中央下
        c2.beginPath();
        c2.moveTo(0.36 * px, 0.7 * py);
        c2.lineTo(0.64 * px, 0.7 * py);
        c2.lineTo(0.64 * px, py);
        c2.lineTo(0.36 * px, py);
        c2.closePath();
    } else if (position == 9) {
        //右下
        c2.beginPath();
        c2.moveTo(px, 0.45 * py);
        c2.lineTo(0.85 * px, 0.63 * py);
        c2.lineTo(0.64 * px, 0.7 * py);
        c2.lineTo(0.64 * px, py);
        c2.lineTo(px, py);
        c2.closePath();
    }
    c2.fillStyle = `rgba(255, 0, 0, 0.5)`;
    c2.fill();
    if (color == "red") {
        $.ajax({
            //送信方法
            type: "POST",
            //送信先ファイル名
            url: "./ajax/ajax_SelectPosition.php",
            //受け取りデータの種類
            datatype: "json",
            //送信データ
            data: {
                game_id: game_id,
                position: position,
                first_time: first_time,
                latter_time: latter_time,
                team_name: team_name2,
                select: select,
                team_id: team_id2,
                swift_flag: swift_flag,
            },
        }).then(
            //成功時の処理
            function (result) {
                xy_parse2 = result.parse;
                show_table = result.shoot_table;
                table_filtering();
                resetOnlyPath2();
                draw_only_img(1);
                reset_canvas_path();
                drawCanvasPath2();
                input_goal_position2(result.shoot_course, result.shoot_course_s);
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
}


//チーム1のポジションごとのシュート成功率
function isInsidePolygon(x, y) {
    pv_click1 = false;
    const seven = [
        { x: 0.21 * px, y: 0 },
        { x: 0.79 * px, y: 0 },
        { x: 0.79 * px, y: 0.09 * py },
        { x: 0.21 * px, y: 0.09 * py },
        { x: 0.21 * px, y: 0 },
    ];
    const poly = [
        { x: 0, y: 0 },
        { x: 0.12 * px, y: 0 },
        { x: 0.21 * px, y: 0.2 * py },
        { x: 0, y: 0.45 * py },
    ];
    const PV = [
        { x: 0.21 * px, y: 0.11 * py },
        { x: 0.79 * px, y: 0.11 * py },
        { x: 0.79 * px, y: 0.2 * py },
        { x: 0.21 * px, y: 0.2 * py },
        { x: 0.21 * px, y: 0.11 * py },
    ];
    const RW = [
        { x: px, y: 0 },
        { x: 0.88 * px, y: 0 },
        { x: 0.79 * px, y: 0.2 * py },
        { x: px, y: 0.45 * py },
    ];
    const L6 = [
        { x: 0, y: 0.45 * py },
        { x: 0.21 * px, y: 0.2 * py },
        { x: 0.36 * px, y: 0.27 * py },
        { x: 0.36 * px, y: 0.7 * py },
        { x: 0.15 * px, y: 0.63 * py },
    ];
    const C6 = [
        { x: 0.36 * px, y: 0.27 * py },
        { x: 0.64 * px, y: 0.27 * py },
        { x: 0.64 * px, y: 0.7 * py },
        { x: 0.36 * px, y: 0.7 * py },
    ];
    const R6 = [
        { x: px, y: 0.45 * py },
        { x: 0.79 * px, y: 0.2 * py },
        { x: 0.64 * px, y: 0.27 * py },
        { x: 0.64 * px, y: 0.7 * py },
        { x: 0.85 * px, y: 0.63 * py },
    ];
    const L9 = [
        { x: 0, y: 0.45 * py },
        { x: 0.15 * px, y: 0.63 * py },
        { x: 0.36 * px, y: 0.7 * py },
        { x: 0.36 * px, y: py },
        { x: 0, y: py },
    ];
    const C9 = [
        { x: 0.36 * px, y: 0.7 * py },
        { x: 0.64 * px, y: 0.7 * py },
        { x: 0.64 * px, y: py },
        { x: 0.36 * px, y: py },
    ];
    const R9 = [
        { x: px, y: 0.45 * py },
        { x: 0.85 * px, y: 0.63 * py },
        { x: 0.64 * px, y: 0.7 * py },
        { x: 0.64 * px, y: py },
        { x: px, y: py },
    ];

    let inside = false;
    // 左サイド
    for (let i = 0, j = poly.length - 1; i < poly.length; j = i++) {
        const xi = poly[i].x, yi = poly[i].y;
        const xj = poly[j].x, yj = poly[j].y;

        const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
        if (intersect) {
            inside = !inside;
            position = 1;
        }
    }
    // 7m
    if (!inside) {
        for (let i = 0, j = seven.length - 1; i < seven.length; j = i++) {
            const xi = seven[i].x, yi = seven[i].y;
            const xj = seven[j].x, yj = seven[j].y;

            const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) {
                inside = !inside;
                seven_flag = true;
            }
        }
    }
    //PV
    if (!inside) {
        for (let i = 0, j = PV.length - 1; i < PV.length; j = i++) {
            const xi = PV[i].x, yi = PV[i].y;
            const xj = PV[j].x, yj = PV[j].y;

            const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) {
                inside = !inside;
                position = 2;
                pv_click1 = true;
            }
        }
    }
    //右サイド
    if (!inside) {
        for (let i = 0, j = RW.length - 1; i < RW.length; j = i++) {
            const xi = RW[i].x, yi = RW[i].y;
            const xj = RW[j].x, yj = RW[j].y;

            const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) {
                inside = !inside;
                position = 3;
            }
        }
    }
    //左バック6
    if (!inside) {
        for (let i = 0, j = L6.length - 1; i < L6.length; j = i++) {
            const xi = L6[i].x, yi = L6[i].y;
            const xj = L6[j].x, yj = L6[j].y;

            const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) {
                inside = !inside;
                position = 4;
            }
        }
    }
    //センター6
    if (!inside) {
        for (let i = 0, j = C6.length - 1; i < C6.length; j = i++) {
            const xi = C6[i].x, yi = C6[i].y;
            const xj = C6[j].x, yj = C6[j].y;

            const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) {
                inside = !inside;
                position = 5;
            }
        }
    }
    //右バック6
    if (!inside) {
        for (let i = 0, j = R6.length - 1; i < R6.length; j = i++) {
            const xi = R6[i].x, yi = R6[i].y;
            const xj = R6[j].x, yj = R6[j].y;

            const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) {
                inside = !inside;
                position = 6;
            }
        }
    }
    //左バック9
    if (!inside) {
        for (let i = 0, j = L9.length - 1; i < L9.length; j = i++) {
            const xi = L9[i].x, yi = L9[i].y;
            const xj = L9[j].x, yj = L9[j].y;

            const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) {
                inside = !inside;
                position = 7;
            }
        }
    }
    //センター9
    if (!inside) {
        for (let i = 0, j = C9.length - 1; i < C9.length; j = i++) {
            const xi = C9[i].x, yi = C9[i].y;
            const xj = C9[j].x, yj = C9[j].y;

            const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) {
                inside = !inside;
                position = 8;
            }
        }
    }
    //右バック9
    if (!inside) {
        for (let i = 0, j = R9.length - 1; i < R9.length; j = i++) {
            const xi = R9[i].x, yi = R9[i].y;
            const xj = R9[j].x, yj = R9[j].y;

            const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) {
                inside = !inside;
                position = 9;
            }
        }
    }

    return inside;
}

//チーム2のポジションボタンがクリックされたときの処理
function isInsidePolygon2(x, y) {
    pv_click2 = false;
    const seven = [
        { x: 0.21 * px, y: 0 },
        { x: 0.79 * px, y: 0 },
        { x: 0.79 * px, y: 0.09 * py },
        { x: 0.21 * px, y: 0.09 * py },
        { x: 0.21 * px, y: 0 },
    ];
    const poly = [
        { x: 0, y: 0 },
        { x: 0.12 * px, y: 0 },
        { x: 0.21 * px, y: 0.2 * py },
        { x: 0, y: 0.45 * py },
    ];
    const PV = [
        { x: 0.21 * px, y: 0.11 * py },
        { x: 0.79 * px, y: 0.11 * py },
        { x: 0.79 * px, y: 0.2 * py },
        { x: 0.21 * px, y: 0.2 * py },
        { x: 0.21 * px, y: 0.11 * py },
    ];
    const RW = [
        { x: px, y: 0 },
        { x: 0.88 * px, y: 0 },
        { x: 0.79 * px, y: 0.2 * py },
        { x: px, y: 0.45 * py },
    ];
    const L6 = [
        { x: 0, y: 0.45 * py },
        { x: 0.21 * px, y: 0.2 * py },
        { x: 0.36 * px, y: 0.27 * py },
        { x: 0.36 * px, y: 0.7 * py },
        { x: 0.15 * px, y: 0.63 * py },
    ];
    const C6 = [
        { x: 0.36 * px, y: 0.27 * py },
        { x: 0.64 * px, y: 0.27 * py },
        { x: 0.64 * px, y: 0.7 * py },
        { x: 0.36 * px, y: 0.7 * py },
    ];
    const R6 = [
        { x: px, y: 0.45 * py },
        { x: 0.79 * px, y: 0.2 * py },
        { x: 0.64 * px, y: 0.27 * py },
        { x: 0.64 * px, y: 0.7 * py },
        { x: 0.85 * px, y: 0.63 * py },
    ];
    const L9 = [
        { x: 0, y: 0.45 * py },
        { x: 0.15 * px, y: 0.63 * py },
        { x: 0.36 * px, y: 0.7 * py },
        { x: 0.36 * px, y: py },
        { x: 0, y: py },
    ];
    const C9 = [
        { x: 0.36 * px, y: 0.7 * py },
        { x: 0.64 * px, y: 0.7 * py },
        { x: 0.64 * px, y: py },
        { x: 0.36 * px, y: py },
    ];
    const R9 = [
        { x: px, y: 0.45 * py },
        { x: 0.85 * px, y: 0.63 * py },
        { x: 0.64 * px, y: 0.7 * py },
        { x: 0.64 * px, y: py },
        { x: px, y: py },
    ];

    let inside = false;
    // 左サイド
    for (let i = 0, j = poly.length - 1; i < poly.length; j = i++) {
        const xi = poly[i].x, yi = poly[i].y;
        const xj = poly[j].x, yj = poly[j].y;

        const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
        if (intersect) {
            inside = !inside;
            position = 1;
        }
    }
    // 7m
    if (!inside) {
        for (let i = 0, j = seven.length - 1; i < seven.length; j = i++) {
            const xi = seven[i].x, yi = seven[i].y;
            const xj = seven[j].x, yj = seven[j].y;

            const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) {
                inside = !inside;
                seven_flag = true;
            }
        }
    }
    //PV
    if (!inside) {
        for (let i = 0, j = PV.length - 1; i < PV.length; j = i++) {
            const xi = PV[i].x, yi = PV[i].y;
            const xj = PV[j].x, yj = PV[j].y;

            const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) {
                inside = !inside;
                position = 2;
                pv_click2 = true;
            }
        }
    }
    //右サイド
    if (!inside) {
        for (let i = 0, j = RW.length - 1; i < RW.length; j = i++) {
            const xi = RW[i].x, yi = RW[i].y;
            const xj = RW[j].x, yj = RW[j].y;

            const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) {
                inside = !inside;
                position = 3;
            }
        }
    }
    //左バック6
    if (!inside) {
        for (let i = 0, j = L6.length - 1; i < L6.length; j = i++) {
            const xi = L6[i].x, yi = L6[i].y;
            const xj = L6[j].x, yj = L6[j].y;

            const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) {
                inside = !inside;
                position = 4;
            }
        }
    }
    //センター6
    if (!inside) {
        for (let i = 0, j = C6.length - 1; i < C6.length; j = i++) {
            const xi = C6[i].x, yi = C6[i].y;
            const xj = C6[j].x, yj = C6[j].y;

            const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) {
                inside = !inside;
                position = 5;
            }
        }
    }
    //右バック6
    if (!inside) {
        for (let i = 0, j = R6.length - 1; i < R6.length; j = i++) {
            const xi = R6[i].x, yi = R6[i].y;
            const xj = R6[j].x, yj = R6[j].y;

            const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) {
                inside = !inside;
                position = 6;
            }
        }
    }
    //左バック9
    if (!inside) {
        for (let i = 0, j = L9.length - 1; i < L9.length; j = i++) {
            const xi = L9[i].x, yi = L9[i].y;
            const xj = L9[j].x, yj = L9[j].y;

            const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) {
                inside = !inside;
                position = 7;
            }
        }
    }
    //センター9
    if (!inside) {
        for (let i = 0, j = C9.length - 1; i < C9.length; j = i++) {
            const xi = C9[i].x, yi = C9[i].y;
            const xj = C9[j].x, yj = C9[j].y;

            const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) {
                inside = !inside;
                position = 8;
            }
        }
    }
    //右バック9
    if (!inside) {
        for (let i = 0, j = R9.length - 1; i < R9.length; j = i++) {
            const xi = R9[i].x, yi = R9[i].y;
            const xj = R9[j].x, yj = R9[j].y;

            const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) {
                inside = !inside;
                position = 9;
            }
        }
    }

    return inside;
}

// キャンバスパス両方の描画
function drawCV() {
    if (select == 1) {
        All_select_both();
    } else if (select == 2) {
        First_select_both();
    } else if (select == 3) {
        Latter_select_both();
    }
}

// キャンバスパス1の描画
function drawCV1() {
    if (select == 1) {
        All_select();
    } else if (select == 2) {
        First_select();
    } else if (select == 3) {
        Latter_select();
    }
}

// キャンバスパス2の描画
function drawCV2() {
    if (select == 1) {
        All_select2();
    } else if (select == 2) {
        First_select2();
    } else if (select == 3) {
        Latter_select2();
    }
}

// ポジションをクリックしたときの処理
function handleClick(event) {
    const rect = cv.getBoundingClientRect();
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;

    if (isInsidePolygon(x, y)) {
        seven_state_flag2 = false;
        pos2 = 11;
        if (seven_flag) {
            if (seven_state_flag1) {
                seven_state_flag1 = false;
                reset_position_both();
                return;
            }
            seven_state_flag1 = true;
            seven_flag = false;
            reset_position2();
            seven_show();
            return;
        }
        seven_state_flag1 = false;
        if (pos1 == position) {
            reset_position_both();
            pos1 = 11;
            return;
        }
        reset_position2();
        drawPolygon('red');
    } else {
        return;
    }
    pos1 = position;
}

// ポジションをクリックしたときの処理
function handleClick2(event) {
    const rect = cv2.getBoundingClientRect();
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;

    if (isInsidePolygon2(x, y)) {
        pos1 = 11;
        seven_state_flag1 = false;
        if (seven_flag) {
            if (seven_state_flag2) {
                seven_state_flag2 = false;
                reset_position_both();
                return;
            }
            seven_state_flag2 = true;
            seven_flag = false;
            reset_position();
            seven_show2();
            return;
        }
        seven_state_flag2 = false;
        if (pos2 == position) {
            reset_position_both();
            pos2 = 11;
            return;
        }
        reset_position();
        drawPolygon2('red');
    } else {
        return;
    }
    pos2 = position;
}

cv.addEventListener('click', handleClick);
cv2.addEventListener('click', handleClick2);

// キャンバス内のシュートの×ボタンをクリックしたときの処理
function isInsideRectangles(x, y, coords) {
    for (let i = 0; i < coords.length; i++) {
        const rect = {
            x1: coords[i].x - 10,
            y1: coords[i].y - 10,
            x2: coords[i].x + 10,
            y2: coords[i].y + 10
        };

        if (x >= rect.x1 && x <= rect.x2 && y >= rect.y1 && y <= rect.y2) {
            mySeekTo(xy_parse.video_time[i] - 5);
            popup.style.left = '75%';
            click_cross = coords[i].id;
            return true;
        }
    }
    return false;
}

// キャンバス内のシュートの×ボタンをクリックしたときの処理
function isInsideRectangles2(x, y, coords) {
    for (let i = 0; i < coords.length; i++) {
        const rect = {
            x1: coords[i].x - 10,
            y1: coords[i].y - 10,
            x2: coords[i].x + 10,
            y2: coords[i].y + 10
        };

        if (x >= rect.x1 && x <= rect.x2 && y >= rect.y1 && y <= rect.y2) {
            mySeekTo(xy_parse2.video_time[i] - 5);
            popup.style.left = '25%';
            click_cross = coords[i].id;
            return true;
        }
    }
    return false;
}

// キャンバスをクリックしたときに実行するイベントリスナー
canvas_path.addEventListener('click', (event) => {
    const x = event.clientX - canvas_path.getBoundingClientRect().left;
    const y = event.clientY - canvas_path.getBoundingClientRect().top;

    if (isInsideRectangles(x, y, cross_position1)) {
        draw_only_img(2);
        drawCanvasSelector(click_cross);
        if (video_flag) {
            return;
        }
        showPopup();
        player.playVideo();
        event.stopPropagation();
        // console.log('Clicked inside a rectangle');
    } else {
        return;
        // console.log('Clicked outside rectangles');
    }
});

// キャンバス内のシュートの×ボタンをクリックしたときの処理
canvas_path2.addEventListener('click', (event) => {
    const x = event.clientX - canvas_path2.getBoundingClientRect().left;
    const y = event.clientY - canvas_path2.getBoundingClientRect().top;

    if (isInsideRectangles2(x, y, cross_position2)) {
        draw_only_img(1);
        drawCanvasSelector2(click_cross);
        if (video_flag) {
            return;
        }
        showPopup();
        player.playVideo();
        event.stopPropagation();
        // console.log('Clicked inside a rectangle');
    } else {
        return;
        // console.log('Clicked outside rectangles');
    }
});

// ポップアップを非表示にするイベント
// document.addEventListener('click', (event) => {
//   const target = event.target;

//   if (target.id !== 'popup' && target.id !== 'player' && !target.classList.contains('shoot_his') && !popup.classList.contains('hidden')) {
//     table_select = 0;
//     reset_position_both();
//     hidePopup();
//   }
// });

// ポップアップのイベント
// popup.addEventListener('click', (event) => {
//   event.stopPropagation();
// });

// ロード画面
window.addEventListener('load', function () {
    const loadingScreen = document.getElementById('loading-screen');

    // ローディング画面を非表示にし、コンテンツを表示する
    loadingScreen.classList.add('hidden');
});

// 時間から秒へ変換(00:00:00→00000秒)
function hour_to_sec(time) {
    h = parseInt(time.slice(0, 2), 10);
    m = parseInt(time.slice(3, 5), 10);
    s = parseInt(time.slice(6, 8), 10);
    return (h * 60 * 60) + (m * 60) + s;
}

//シュートテーブルの行がクリックされたときの処理
function Click_Sub(obj, id, i) {
    const text = obj.querySelector('#team_abbreviation').textContent;
    if (seven_state_flag1 || seven_state_flag2) {
        if (text == team_name1) {
            popup.style.left = '75%';
        } else if (text == team_name2) {
            popup.style.left = '25%';
        }
        mySeekTo(show_table.video_time[i - 1] - 5);
    } else {
        if (text == team_name1) {
            popup.style.left = '75%';
            reset_canvas_path();
            draw_only_img(2);
            drawCanvasSelector(id);
        } else if (text == team_name2) {
            popup.style.left = '25%';
            reset_canvas_path();
            draw_only_img(1);
            drawCanvasSelector2(id);
        }
        console.log(i);
        console.log(id);
    }
    if (!video_flag) {
        showPopup();
        player.playVideo();
    }
}

//テーブルの行ごとにクリックイベントを付与する関数
function addClickEventToRows() {
    let rows = table.getElementsByTagName('tr');
    for (let i = 0; i < rows.length; i++) {
        rows[i].addEventListener('click', function () {
            const selectedRow = table.querySelector('.selected');
            if (selectedRow) {
                selectedRow.classList.remove('selected');
            }
            this.classList.add('selected');
            Click_Sub(this, parseInt(this.id), i);
        });
    }
}

addClickEventToRows();

// 勝利チームと敗北チームの色分け
$(document).ready(function () {
    // IDを追加する要素を選択し、attr()メソッドを使用してIDを追加します
    const point1 = $("#point1").text();
    const point2 = $("#point2").text();
    if (point1 < point2) {
        $("#point_team1").addClass("loser");
        $("#point_team2").addClass("winner");
        $('#stats_team_name1').addClass("loser");
        $('#stats_team_name2').addClass("winner");
    } else {
        $("#point_team1").addClass("winner");
        $("#point_team2").addClass("loser");
        $('#stats_team_name1').addClass("winner");
        $('#stats_team_name2').addClass("loser");
    }
});

// セレクトボックスの内容が選択されたときの処理
$(document).ready(function () {
    // セレクトボックスの選択が変更されたら
    $('#swift_flag').change(function () {
        // 選択された値を取得
        let selectedValue = $(this).val();

        // 取得した値に基づいて処理を行う
        console.log('選択された値: ' + selectedValue);

        if (selectedValue == 'default') {
            swift_flag = 1;
        } else if (selectedValue == 'no-swift') {
            swift_flag = 2;
        } else {
            swift_flag = 3;
        }

        $.ajax({
            //送信方法
            type: "POST",
            //送信先ファイル名
            url: "./ajax/ajax_SelectSwift.php",
            //受け取りデータの種類
            datatype: "json",
            //送信データ
            data: {
                game_id: game_id,
                first_time: first_time,
                latter_time: latter_time,
                team_name1: team_name1,
                team_name2: team_name2,
                select: select,
                team_id1: team_id1,
                team_id2: team_id2,
                swift_flag: swift_flag,
            },
        }).then(
            //成功時の処理
            function (result) {
                xy_parse = result.parse1.parse;
                xy_parse2 = result.parse2.parse;
                show_table = result.table;
                position_lists = result.parse1.position;
                position_lists2 = result.parse2.position;
                table_filtering();
                resetCanvas();
                resetCanvas2();
                draw(1, position_lists.LW, position_lists.PV, position_lists.RW, position_lists.L6, position_lists.C6, position_lists.R6, position_lists.L9, position_lists.C9, position_lists.R9, position_lists.seven, position_lists.LW_s, position_lists.PV_s, position_lists.RW_s, position_lists.L6_s, position_lists.C6_s, position_lists.R6_s, position_lists.L9_s, position_lists.C9_s, position_lists.R9_s, position_lists.seven_s, position_lists.LW_r, position_lists.PV_r, position_lists.RW_r, position_lists.L6_r, position_lists.C6_r, position_lists.R6_r, position_lists.L9_r, position_lists.C9_r, position_lists.R9_r, position_lists.seven_r);
                draw(2, position_lists2.LW, position_lists2.PV, position_lists2.RW, position_lists2.L6, position_lists2.C6, position_lists2.R6, position_lists2.L9, position_lists2.C9, position_lists2.R9, position_lists2.seven, position_lists2.LW_s, position_lists2.PV_s, position_lists2.RW_s, position_lists2.L6_s, position_lists2.C6_s, position_lists2.R6_s, position_lists2.L9_s, position_lists2.C9_s, position_lists2.R9_s, position_lists2.seven_s, position_lists2.LW_r, position_lists2.PV_r, position_lists2.RW_r, position_lists2.L6_r, position_lists2.C6_r, position_lists2.R6_r, position_lists2.L9_r, position_lists2.C9_r, position_lists2.R9_r, position_lists2.seven_r);
                drawCanvasPath();
                drawCanvasPath2();
                input_goal_position(result.parse1.goal_pos, result.parse1.goal_pos_s);
                input_goal_position2(result.parse2.goal_pos, result.parse2.goal_pos_s);
            },
            //エラーの時の処理
            function (XMLHttpRequest, textStatus, errorThrown) {
                console.log("通信失敗!!!");
                console.log("XMLHttpRequest : " + XMLHttpRequest.status);
                console.log("textStatus : " + textStatus);
                console.log("errorThrown : " + errorThrown.message);
            }
        );
    });
});

window.document.onkeydown = function (evt) {
    if (evt.which == 32) {
        //スペース
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
    } else if (evt.which == 39) {
        //右矢印
        player.seekTo(player.getCurrentTime() + 5, true);
    } else if (evt.which == 37) {
        //左矢印  
        player.seekTo(player.getCurrentTime() - 5, true);
    }
};

let closeBtn = document.getElementById('close-btn');

closeBtn.addEventListener('click', function () {
    hidePopup();
    player.pauseVideo();
    reset_position_both();
});

// メニューを開く関数
function openMenu() {
    document.getElementById("popupMenu").style.display = "block";
}

// メニューを閉じる関数
function closeMenu() {
    document.getElementById("popupMenu").style.display = "none";
}

// メニューコンテンツ内のクリックイベントがメニューを閉じる関数を呼び出さないようにする
document.querySelector('.menu-content').addEventListener('click', function (event) {
    event.stopPropagation();
});

function extension_input_data() {
    xy_parse = input_shoot1;
    xy_parse2 = input_shoot2;
    show_table = input_table;
    area1 = input_area1;
    area2 = input_area2;
    course1 = input_course1;
    course2 = input_course2;
}

function input_data() {
    xy_parse = input_shoot1;
    xy_parse2 = input_shoot2;
    show_table = input_table;
    area1 = input_area1;
    area2 = input_area2;
    course1 = input_course1;
    course2 = input_course2;
}