<?php
require __DIR__ . '/includes/functions.php';

$no = $_GET['no'] ?? '';
// Sifariş detallarını yalnız onu yaradan sessiyaya göstəririk
if ($no === '' || ($_SESSION['last_order'] ?? '') !== $no) {
    redirect(url('track.php'));
}
$st = db()->prepare('SELECT * FROM orders WHERE order_no = ?');
$st->execute([$no]);
$o = $st->fetch();
if (!$o) {
    redirect(url());
}
$it = db()->prepare('SELECT * FROM order_items WHERE order_id = ?');
$it->execute([$o['id']]);
$items = $it->fetchAll();

$pageTitle = 'Sifariş qəbul olundu';
require __DIR__ . '/includes/header.php';
?>
<section class="container success">
    <div class="success-card">
        <div class="success-icon">✓</div>
        <h1>Təşəkkür edirik, <?= e(explode(' ', $o['customer_name'])[0]) ?>!</h1>
        <p>Sifarişiniz qəbul olundu. Operatorumuz qısa zamanda <strong><?= e($o['phone']) ?></strong> nömrəsi ilə əlaqə saxlayacaq.</p>
        <div class="order-no">Sifariş nömrəsi: <strong><?= e($o['order_no']) ?></strong></div>

        <ul class="mini-items">
            <?php foreach ($items as $i): ?>
                <li><div><strong><?= e($i['name']) ?></strong>
                    <small class="muted"><?= e(trim($i['size'] . ' ' . ($i['color'] ? '· ' . $i['color'] : ''))) ?> × <?= (int)$i['qty'] ?></small></div>
                    <span><?= money($i['price'] * $i['qty']) ?></span></li>
            <?php endforeach; ?>
        </ul>
        <dl class="totals">
            <?php if ($o['discount'] > 0): ?><div class="discount"><dt>Endirim</dt><dd>−<?= money($o['discount']) ?></dd></div><?php endif; ?>
            <div><dt>Çatdırılma</dt><dd><?= $o['delivery_fee'] > 0 ? money($o['delivery_fee']) : 'Pulsuz' ?></dd></div>
            <div class="grand"><dt>Cəmi</dt><dd><?= money($o['total']) ?></dd></div>
        </dl>
        <p class="muted small">Ödəniş: <?= e(payment_methods()[$o['payment_method']] ?? $o['payment_method']) ?> · Ünvan: <?= e($o['city'] . ', ' . $o['address']) ?></p>
        <div class="hero-cta center">
            <a class="btn" href="<?= url('track.php?no=' . urlencode($o['order_no'])) ?>">Sifarişi izlə</a>
            <a class="btn btn-ghost" href="<?= url('shop.php') ?>">Alış-verişə davam et</a>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
