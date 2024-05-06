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

window.onload = function () {
  document.getElementById('password-change-form').onsubmit = function (e) {
    let newPassword = document.getElementById('new-password').value;
    let confirmPassword = document.getElementById('confirm-new-password').value;

    // 新しいパスワードと確認用のパスワードが一致しない場合
    if (newPassword !== confirmPassword) {
      e.preventDefault(); // フォームの送信をキャンセル
      alert('新しいパスワードと確認用の新しいパスワードが一致しません。');
    }
  };
};