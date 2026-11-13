<?php
require __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $st = db()->prepare('SELECT * FROM products WHERE id = ? AND is_active = 1');
        $st->execute([(int)($_POST['id'] ?? 0)]);
        $p = $st->fetch();
        $back = $p ? product_url($p) : url('shop.php');
        if (!$p) {
            flash('error', 'Məhsul tapılmadı.');
            redirect(url('shop.php'));
        }
        $sizes = csv_list($p['sizes']);
        $colors = csv_list($p['colors']);
        $size = trim($_POST['size'] ?? '');
        $color = trim($_POST['color'] ?? '');
        $qty = max(1, (int)($_POST['qty'] ?? 1));

        if ($sizes && !in_array($size, $sizes, true)) {
            flash('error', 'Zəhmət olmasa ölçü seçin.');
            redirect($back);
        }
        if ($colors && !in_array($color, $colors, true)) {
            $color = $colors[0];
        }
        if (!$sizes) $size = '';
        if (!$colors) $color = '';

        $inCart = cart()[cart_key((int)$p['id'], $size, $color)] ?? 0;
        if ($inCart + $qty > (int)$p['stock']) {
            flash('error', 'Stokda yalnız ' . (int)$p['stock'] . ' ədəd var.');
            redirect($back);
        }
        cart_add((int)$p['id'], $size, $color, $qty);
        flash('success', '«' . $p['name'] . '» səbətə əlavə olundu.');
        redirect(url('cart.php'));
    }

    if ($action === 'update') {
        foreach ((array)($_POST['qty'] ?? []) as $key => $q) {
            cart_set((string)$key, (int)$q);
        }
        flash('success', 'Səbət yeniləndi.');
    } elseif ($action === 'remove') {
        cart_set((string)($_POST['key'] ?? ''), 0);
        flash('success', 'Məhsul səbətdən silindi.');
    } elseif ($action === 'coupon') {
        $code = trim($_POST['code'] ?? '');
        $c = $code !== '' ? find_coupon($code) : null;
        if ($c) {
            $_SESSION['coupon'] = $c['code'];
            $sub = array_sum(array_column(cart_items(), 'line'));
            if ($sub < (float)$c['min_total']) {
                flash('error', 'Bu promokod ' . money($c['min_total']) . '-dan yuxarı sifarişlər üçün keçərlidir.');
            } else {
                flash('success', 'Promokod tətbiq olundu.');
            }
        } else {
            flash('error', 'Promokod yanlışdır və ya müddəti bitib.');
        }
    } elseif ($action === 'uncoupon') {
        unset($_SESSION['coupon']);
    }
    redirect(url('cart.php'));
}

$items = cart_items();
$t = cart_totals($items);
$pageTitle = 'Səbət';
require __DIR__ . '/includes/header.php';
?>
<section class="page-head">
    <div class="container">
        <nav class="crumbs"><a href="<?= url() ?>">Ana səhifə</a> / <span>Səbət</span></nav>
        <h1>Səbət</h1>
    </div>
</section>

<section class="container">
<?php if (!$items): ?>
    <div class="empty">
        <?= icon('bag', 44) ?>
        <h3>Səbətiniz boşdur</h3>
        <p class="muted">Kataloqa baxın — sizə uyğun nəsə mütləq tapılacaq.</p>
        <a class="btn" href="<?= url('shop.php') ?>">Kataloqa keç</a>
    </div>
<?php else: ?>
    <div class="cart-layout">
        <form method="post" class="cart-list" id="cartForm">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="update">
            <?php foreach ($items as $it): $p = $it['product']; ?>
                <div class="cart-row">
                    <a href="<?= e(product_url($p)) ?>" class="cart-img"><img src="<?= e(img_url($p['image'])) ?>" alt=""></a>
                    <div class="cart-info">
                        <a href="<?= e(product_url($p)) ?>" class="cart-name"><?= e($p['name']) ?></a>
                        <div class="muted small">
                            <?php if ($it['size']): ?>Ölçü: <strong><?= e($it['size']) ?></strong><?php endif; ?>
                            <?php if ($it['color']): ?> · Rəng: <strong><?= e($it['color']) ?></strong><?php endif; ?>
                        </div>
                        <div class="small"><?= money($p['price']) ?> / əd.</div>
                    </div>
                    <div class="qty" data-qty>
                        <button type="button" data-qty-minus aria-label="Azalt">−</button>
                        <input type="number" name="qty[<?= e($it['key']) ?>]" value="<?= (int)$it['qty'] ?>" min="0" max="<?= (int)$p['stock'] ?>" data-autoupdate aria-label="Say">
                        <button type="button" data-qty-plus aria-label="Artır">+</button>
                    </div>
                    <div class="cart-line"><?= money($it['line']) ?></div>
                    <button class="icon-btn" type="submit" form="rm-<?= md5($it['key']) ?>" aria-label="Sil"><?= icon('trash', 18) ?></button>
                </div>
            <?php endforeach; ?>
            <div class="cart-actions">
                <a href="<?= url('shop.php') ?>" class="link">← Alış-verişə davam et</a>
                <button class="btn btn-ghost btn-sm" type="submit">Səbəti yenilə</button>
            </div>
        </form>
        <?php foreach ($items as $it): ?>
            <form method="post" id="rm-<?= md5($it['key']) ?>" hidden>
                <?= csrf_field() ?><input type="hidden" name="action" value="remove"><input type="hidden" name="key" value="<?= e($it['key']) ?>">
            </form>
        <?php endforeach; ?>

        <aside class="summary">
            <h3>Sifarişin xülasəsi</h3>
            <?php if ($t['coupon']): ?>
                <form method="post" class="coupon-applied">
                    <?= csrf_field() ?><input type="hidden" name="action" value="uncoupon">
                    <span>Promokod: <strong><?= e($t['coupon']['code']) ?></strong></span>
                    <button type="submit" class="link small">Ləğv et</button>
                </form>
            <?php else: ?>
                <form method="post" class="coupon">
                    <?= csrf_field() ?><input type="hidden" name="action" value="coupon">
                    <input type="text" name="code" placeholder="Promokod" aria-label="Promokod">
                    <button class="btn btn-ghost btn-sm" type="submit">Tətbiq et</button>
                </form>
            <?php endif; ?>
            <dl class="totals">
                <div><dt>Məhsullar</dt><dd><?= money($t['sub']) ?></dd></div>
                <?php if ($t['discount'] > 0): ?><div class="discount"><dt>Endirim</dt><dd>−<?= money($t['discount']) ?></dd></div><?php endif; ?>
                <div><dt>Çatdırılma</dt><dd><?= $t['delivery'] > 0 ? money($t['delivery']) : 'Pulsuz' ?></dd></div>
                <div class="grand"><dt>Cəmi</dt><dd><?= money($t['total']) ?></dd></div>
            </dl>
            <?php $left = FREE_DELIVERY_FROM - ($t['sub'] - $t['discount']); if ($left > 0): ?>
                <div class="free-ship">
                    <div class="bar"><span style="width: <?= (int)min(100, ($t['sub'] - $t['discount']) / FREE_DELIVERY_FROM * 100) ?>%"></span></div>
                    <small>Pulsuz çatdırılma üçün daha <strong><?= money($left) ?></strong></small>
                </div>
            <?php endif; ?>
            <a class="btn btn-block" href="<?= url('checkout.php') ?>">Sifarişi rəsmiləşdir</a>
            <p class="muted small center"><?= icon('box', 14) ?> Gizli qablaşdırma ilə göndərilir</p>
        </aside>
    </div>
<?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
