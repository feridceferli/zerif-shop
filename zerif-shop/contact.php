<?php
require __DIR__ . '/includes/functions.php';

$f = ['name' => '', 'phone' => '', 'email' => '', 'message' => ''];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach ($f as $k => $_) $f[$k] = trim((string)($_POST[$k] ?? ''));
    if (!empty($_POST['website'])) redirect(url('contact.php')); // spam botlar üçün tələ
    if (strlen($f['name']) < 2) $errors[] = 'Adınızı daxil edin.';
    if ($f['phone'] === '' && $f['email'] === '') $errors[] = 'Telefon və ya e-poçt daxil edin.';
    if ($f['email'] !== '' && !filter_var($f['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'E-poçt düzgün deyil.';
    if (strlen($f['message']) < 5) $errors[] = 'Mesajınızı yazın.';
    if (!$errors) {
        db()->prepare('INSERT INTO messages (name, phone, email, message, created_at) VALUES (?,?,?,?,?)')
            ->execute([$f['name'], $f['phone'], $f['email'], $f['message'], date('Y-m-d H:i:s')]);
        flash('success', 'Mesajınız göndərildi. Tezliklə cavab verəcəyik.');
        redirect(url('contact.php'));
    }
}
$pageTitle = 'Əlaqə';
require __DIR__ . '/includes/header.php';
?>
<section class="page-head">
    <div class="container"><nav class="crumbs"><a href="<?= url() ?>">Ana səhifə</a> / <span>Əlaqə</span></nav><h1>Bizimlə əlaqə</h1></div>
</section>
<section class="container contact-grid">
    <div class="form-card">
        <h3>Əlaqə məlumatları</h3>
        <ul class="contact-list">
            <li><?= icon('phone') ?> <a href="tel:<?= e(preg_replace('/\s+/', '', SITE_PHONE)) ?>"><?= e(SITE_PHONE) ?></a></li>
            <li><?= icon('whatsapp') ?> <a href="https://wa.me/<?= e(WHATSAPP_NUMBER) ?>" target="_blank" rel="noopener">WhatsApp ilə yazın</a></li>
            <li><?= icon('mail') ?> <a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a></li>
            <li><?= icon('insta') ?> <a href="<?= e(INSTAGRAM_URL) ?>" target="_blank" rel="noopener">Instagram</a></li>
            <li><?= icon('pin') ?> <?= e(SITE_ADDRESS) ?></li>
        </ul>
        <p class="muted">İş saatları: hər gün 10:00 – 21:00</p>
    </div>
    <form method="post" class="form-card">
        <?= csrf_field() ?>
        <h3>Mesaj yazın</h3>
        <?php foreach ($errors as $er): ?><div class="flash flash-error"><?= e($er) ?></div><?php endforeach; ?>
        <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
        <div class="form-grid">
            <label class="field">Adınız *<input type="text" name="name" value="<?= e($f['name']) ?>" required></label>
            <label class="field">Telefon<input type="tel" name="phone" value="<?= e($f['phone']) ?>"></label>
            <label class="field field-full">E-poçt<input type="email" name="email" value="<?= e($f['email']) ?>"></label>
            <label class="field field-full">Mesaj *<textarea name="message" rows="5" required><?= e($f['message']) ?></textarea></label>
        </div>
        <button class="btn" type="submit">Göndər</button>
    </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
