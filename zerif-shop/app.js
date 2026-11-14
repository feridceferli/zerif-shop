window.addEventListener('DOMContentLoaded', () => {
    const tg = window.Telegram.WebApp;
    tg.ready();
    tg.expand();

    if (tg.initDataUnsafe && tg.initDataUnsafe.user) {
        const userName = tg.initDataUnsafe.user.first_name || "İstifadəçi";
        document.getElementById('user-greeting').innerText = `Xoş gəlmisiniz, ${userName}!`;
    }

    document.getElementById('topup-btn').addEventListener('click', () => {
        tg.showAlert("Balans artırma sorğusu göndərildi.");
    });
});
