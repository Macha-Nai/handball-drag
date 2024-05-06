//delete_btnクリックされた時の処理
$(document).ready(function () {
    $('button[name="delete_btn"]').on('click', function () {
        // アラートを表示し、削除の確認を求める
        let id = $(this).val();
        if (confirm("このテーブルを削除しますか？")) {
            deleteGame(id);
        } else {
            return;
        }
    });
});

function deleteGame(id) {
    // ボタンのvalueを取得し、削除処理を呼び出す
    $.ajax({
        url: './ajax/ajaxDeleteGame.php',
        type: 'POST',
        data: {
            'id': id
        },
        dataType: 'text',
    }).done(function (data) {
        // 成功時の処理
        alert('削除しました');
        location.reload();
    }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
        // 失敗時の処理
        alert('削除に失敗しました');
    });
}

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
