// script.js
window.addEventListener('load', function () {
    const loadingScreen = document.getElementById('loading-screen');
    const content = document.getElementById('content');

    // ローディング画面を非表示にし、コンテンツを表示する
    loadingScreen.classList.add('hidden');
    content.classList.remove('hidden');
});
