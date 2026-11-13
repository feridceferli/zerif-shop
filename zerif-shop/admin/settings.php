<?php
require __DIR__ . '/_init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $st = db()->prepare('SELECT * FROM admins WHERE id = ?');
    $st->execute([$_SESSION['admin_id']]);
    $a = $st->fetch();
    $cur = (string)($_POST['current'] ?? '');
    $new = (string)($_POST['new'] ?? '');
    $rep = (string)($_POST['repeat'] ?? '');
    $username = trim($_POST['username'] ?? $a['username']);

    if (!$a || !password_verify($cur, $a['password_hash'])) {
        flash('error', 'Hazırkı şifrə yanlışdır.');
    } elseif ($new !== '' && strlen($new) < 8) {
        flash('error', 'Yeni şifrə ən azı 8 simvol olmalıdır.');
    } elseif ($new !== $rep) {
        flash('error', 'Yeni şifrələr üst-üstə düşmür.');
    } elseif (strlen($username) < 3) {
        flash('error', 'İstifadəçi adı ən azı 3 simvol olmalıdır.');
    } else {
        $hash = $new !== '' ? password_hash($new, PASSWORD_DEFAULT) : $a['password_hash'];
        try {
            db()->prepare('UPDATE admins SET username = ?, password_hash = ? WHERE id = ?')->execute([$username, $hash, $a['id']]);
            $_SESSION['admin_name'] = $username;
            flash('success', 'Məlumatlar yeniləndi.');
        } catch (PDOException $ex) {
            flash('error', 'Bu istifadəçi adı artıq mövcuddur.');
        }
    }
    redirect(url('admin/settings.php'));
}
admin_header('Parametrlər');
?>
<div class="cols">
    <form method="post" class="panel form">
        <?= csrf_field() ?>
        <h2>Hesab və şifrə</h2>
        <label>İstifadəçi adı<input type="text" name="username" value="<?= e($_SESSION['admin_name'] ?? '') ?>" required></label>
        <label>Hazırkı şifrə *<input type="password" name="current" required autocomplete="current-password"></label>
        <label>Yeni şifrə (dəyişmək istəmirsinizsə boş saxlayın)<input type="password" name="new" autocomplete="new-password"></label>
        <label>Yeni şifrə təkrar<input type="password" name="repeat" autocomplete="new-password"></label>
        <button class="btn" type="submit">Yadda saxla</button>
    </form>
    <div class="panel">
        <h2>Sayt parametrləri</h2>
        <p class="muted">Mağaza adı, telefon, WhatsApp, çatdırılma haqqı və s. <code>config.php</code> faylında dəyişdirilir.</p>
        <ul class="kv">
            <li><span>Telefon</span><?= e(SITE_PHONE) ?></li>
            <li><span>WhatsApp</span><?= e(WHATSAPP_NUMBER) ?></li>
            <li><span>Çatdırılma</span><?= money(DELIVERY_FEE) ?></li>
            <li><span>Pulsuz çatdırılma</span><?= money(FREE_DELIVERY_FROM) ?>-dan</li>
        </ul>
    </div>
</div>
<?php admin_footer(); ?>
