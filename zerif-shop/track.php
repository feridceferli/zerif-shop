<?php
require __DIR__ . '/includes/functions.php';

$no = trim($_REQUEST['no'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$order = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $st = db()->prepare('SELECT * FROM orders WHERE UPPER(order_no) = UPPER(?)');
    $st->execute([$no]);
    $o = $st->fetch();
    $digits = fn($s) => substr(preg_replace('/\D+/', '', $s), -9);
    if ($o && $digits($o['phone']) === $digits($phone) && strlen($digits($phone)) === 9) {
        $order = $o;
        $it = db()->prepare('SELECT * FROM order_items WHERE order_id = ?');
        $it->execute([$o['id']]);
        $items = $it->fetchAll();
    } else {
        $error = 'Sifariş tapılmadı. Nömrəni və telefonu yoxlayın.';
    }
}

$pageTitle = 'Sifarişi izlə';
require __DIR__ . '/includes/header.php';
$flow = ['new', 'confirmed', 'shipped', 'delivered'];
?>
<section class="page-head">
    <div class="container"><h1>Sifarişi izlə</h1><p class="muted">Sifariş nömrəsini və sifarişdə göstərdiyiniz telefon nömrəsini daxil edin.</p></div>
</section>
<section class="container narrow">
    <form method="post" class="form-card track-form">
        <?= csrf_field() ?>
        <label class="field">Sifariş nömrəsi<input type="text" name="no" value="<?= e($no) ?>" placeholder="ZR..." required></label>
        <label class="field">Telefon<input type="tel" name="phone" value="<?= e($phone) ?>" placeholder="+994 50 ..." required></label>
        <button class="btn" type="submit">Yoxla</button>
    </form>
    <?php if ($error): ?><div class="flash flash-error"><?= e($error) ?></div><?php endif; ?>

    <?php if ($order): ?>
        <div class="form-card">
            <h3>Sifariş <?= e($order['order_no']) ?></h3>
            <p class="muted small"><?= e(date('d.m.Y H:i', strtotime($order['created_at']))) ?></p>
            <?php if ($order['status'] === 'cancelled'): ?>
                <div class="flash flash-error">Bu sifariş ləğv edilib.</div>
            <?php else: $cur = array_search($order['status'], $flow, true); ?>
                <ol class="timeline">
                    <?php foreach ($flow as $i => $s): ?>
                        <li class="<?= $i <= $cur ? 'done' : '' ?>"><span></span><?= e(status_label($s)) ?></li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>
            <ul class="mini-items">
                <?php foreach ($items as $i): ?>
                    <li><div><strong><?= e($i['name']) ?></strong><small class="muted"><?= e($i['size']) ?> <?= $i['color'] ? '· ' . e($i['color']) : '' ?> × <?= (int)$i['qty'] ?></small></div><span><?= money($i['price'] * $i['qty']) ?></span></li>
                <?php endforeach; ?>
            </ul>
            <dl class="totals"><div class="grand"><dt>Cəmi</dt><dd><?= money($order['total']) ?></dd></div></dl>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
