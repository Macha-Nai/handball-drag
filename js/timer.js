let timer;
let seconds = 0;
let timerRunning = false;
let timerElement;

// 自動停止する時間のリスト
let stopTimes = [30 * 60, 60 * 60, 65 * 60, 70 * 60];

// <div id="timer_area">要素を取得
let timerArea = document.getElementById("timer_area");

// タイマー要素を生成
timerElement = document.createElement("p");
timerElement.id = "timer";
timerElement.textContent = "00:00"; // mm:ss 形式の初期値
timerArea.appendChild(timerElement);

// 時間表示部分をクリックして時間を入力
timerElement.addEventListener("click", function () {
  let inputTime = prompt("時間を入力してください (mm:ss)", "00:00");
  if (inputTime !== null) {
    setTime(inputTime);
  }
});

// 開始ボタンを生成
let startButton = document.createElement("button");
startButton.textContent = "開始";
startButton.onclick = startTimer;
timerArea.appendChild(startButton);

// 停止ボタンを生成
let stopButton = document.createElement("button");
stopButton.textContent = "停止";
stopButton.onclick = stopTimer;
timerArea.appendChild(stopButton);

// リセットボタンを生成
let resetButton = document.createElement("button");
resetButton.textContent = "リセット";
resetButton.onclick = resetTimer;
timerArea.appendChild(resetButton);

// 1秒後に飛ばせるボタンを生成
let skipButton = document.createElement("button");
skipButton.textContent = "1秒進める";
skipButton.onclick = skipTimer;
timerArea.appendChild(skipButton);

// 1秒戻すボタンを生成
let rewindButton = document.createElement("button");
rewindButton.textContent = "1秒戻す";
rewindButton.onclick = rewindTimer;
timerArea.appendChild(rewindButton);

function startTimer() {
  if (!timerRunning) {
    timer = setInterval(updateTimer, 1000);
    timerRunning = true;
  }
}

function stopTimer() {
  clearInterval(timer);
  timerRunning = false;
}

function resetTimer() {
  stopTimer();
  seconds = 0;
  updateTimerDisplay();
}

function skipTimer() {
  seconds += 1;
  checkStopTime(); // 時間の経過を確認
  updateTimerDisplay();
}

function rewindTimer() {
  if (seconds >= 1) {
    seconds -= 1;
    checkStopTime(); // 時間の経過を確認
    updateTimerDisplay();
  }
}

function updateTimer() {
  seconds += 1;
  checkStopTime(); // 時間の経過を確認
  updateTimerDisplay();
}

function updateTimerDisplay() {
  let minutes = Math.floor(seconds / 60);
  let remainingSeconds = seconds % 60;
  timerElement.textContent = formatTime(minutes) + ":" + formatTime(remainingSeconds);
}

function formatTime(time) {
  return time < 10 ? "0" + time : time;
}

function setTime(inputTime) {
  let parts = inputTime.split(":");
  if (parts.length === 2) {
    let newSeconds = parseInt(parts[0]) * 60 + parseInt(parts[1]);
    if (!isNaN(newSeconds)) {
      seconds = newSeconds;
      updateTimerDisplay();
    }
  }
}

function checkStopTime() {
  if (stopTimes.includes(seconds)) {
    stopTimer();
  }
}