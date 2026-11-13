<?php
require __DIR__ . '/_init.php';

if (is_admin()) {
    redirect(url('admin/'));
}
$error = '';
$_SESSION['login_attempts'] = $_SESSION['login_attempts'] ?? 0;
$lockedUntil = $_SESSION['login_locked_until'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (time() < $lockedUntil) {
        $error = 'Çox sayda uğursuz cəhd. ' . ($lockedUntil - time()) . ' saniyə sonra yenidən yoxlayın.';
    } else {
        $st = db()->prepare('SELECT * FROM admins WHERE username = ?');
        $st->execute([trim($_POST['username'] ?? '')]);
        $a = $st->fetch();
        if ($a && password_verify((string)($_POST['password'] ?? ''), $a['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int)$a['id'];
            $_SESSION['admin_name'] = $a['username'];
            $_SESSION['login_attempts'] = 0;
            if (password_verify('admin123', $a['password_hash'])) {
                flash('error', 'Standart şifrədən istifadə edirsiniz. Zəhmət olmasa Parametrlər bölməsində şifrəni dəyişin.');
            }
            redirect(url('admin/'));
        }
        $_SESSION['login_attempts']++;
        if ($_SESSION['login_attempts'] >= 5) {
            $_SESSION['login_locked_until'] = time() + 300;
            $_SESSION['login_attempts'] = 0;
        }
        $error = 'İstifadəçi adı və ya şifrə yanlışdır.';
    }
}
?><!doctype html>
<html lang="az">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin girişi — <?= e(SITE_NAME) ?></title>
    <meta name="robots" content="noindex">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;1,500&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('assets/css/admin.css') ?>">
</head>
<body class="login-page">
<form method="post" class="login-card">
    <?= csrf_field() ?>
    <div class="brand"><?= e(SITE_NAME) ?> <small>admin</small></div>
    <?php if ($error): ?><div class="flash flash-error"><?= e($error) ?></div><?php endif; ?>
    <label>İstifadəçi adı<input type="text" name="username" required autofocus autocomplete="username"></label>
    <label>Şifrə<input type="password" name="password" required autocomplete="current-password"></label>
    <button class="btn btn-block" type="submit">Daxil ol</button>
    <a class="back" href="<?= url() ?>">← Sayta qayıt</a>
</form>
</body>
</html>
