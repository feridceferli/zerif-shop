<?php
require __DIR__ . '/includes/functions.php';

$ids = favs();
$products = [];
if ($ids) {
    $in = implode(',', array_fill(0, count($ids), '?'));
    $st = db()->prepare("SELECT * FROM products WHERE is_active = 1 AND id IN ($in)");
    $st->execute($ids);
    $products = $st->fetchAll();
}
$pageTitle = 'Seçilmişlər';
require __DIR__ . '/includes/header.php';
?>
<section class="page-head">
    <div class="container"><nav class="crumbs"><a href="<?= url() ?>">Ana səhifə</a> / <span>Seçilmişlər</span></nav><h1>Seçilmişlər</h1></div>
</section>
<section class="container">
    <?php if ($products): ?>
        <div class="grid"><?php foreach ($products as $p) echo product_card($p); ?></div>
    <?php else: ?>
        <div class="empty">
            <?= icon('heart', 44) ?>
            <h3>Hələ heç nə seçməmisiniz</h3>
            <p class="muted">Bəyəndiyiniz məhsulların üzərindəki ürək ikonuna basın.</p>
            <a class="btn" href="<?= url('shop.php') ?>">Kataloqa keç</a>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
