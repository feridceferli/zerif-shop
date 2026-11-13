<?php
require __DIR__ . '/includes/functions.php';

$st = db()->prepare('SELECT p.*, c.name AS cat_name, c.slug AS cat_slug FROM products p
                     LEFT JOIN categories c ON c.id = p.category_id WHERE p.slug = ? AND p.is_active = 1');
$st->execute([$_GET['slug'] ?? '']);
$p = $st->fetch();
if (!$p) {
    http_response_code(404);
    $pageTitle = 'Məhsul tapılmadı';
    require __DIR__ . '/includes/header.php';
    echo '<div class="container empty"><h2>Məhsul tapılmadı</h2><p class="muted">Bu məhsul satışdan çıxarılıb və ya link səhvdir.</p><a class="btn" href="' . url('shop.php') . '">Kataloqa qayıt</a></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$images = array_values(array_filter(array_merge([$p['image']], json_decode($p['gallery'] ?: '[]', true) ?: [])));
if (!$images) $images = [''];
$sizes = csv_list($p['sizes']);
$colors = csv_list($p['colors']);
$sale = $p['old_price'] && $p['old_price'] > $p['price'];
$inStock = (int)$p['stock'] > 0;

$rel = db()->prepare('SELECT * FROM products WHERE is_active = 1 AND category_id = ? AND id != ? ORDER BY RANDOM() LIMIT 4');
$rel->execute([$p['category_id'], $p['id']]);
$related = $rel->fetchAll();

$pageTitle = $p['name'];
$pageDesc = excerpt($p['description'], 155);
require __DIR__ . '/includes/header.php';
?>
<section class="container product">
    <nav class="crumbs">
        <a href="<?= url() ?>">Ana səhifə</a> /
        <?php if ($p['cat_slug']): ?><a href="<?= url('shop.php?cat=' . urlencode($p['cat_slug'])) ?>"><?= e($p['cat_name']) ?></a> /<?php endif; ?>
        <span><?= e($p['name']) ?></span>
    </nav>

    <div class="product-grid">
        <div class="gallery">
            <div class="gallery-main">
                <img src="<?= e(img_url($images[0])) ?>" alt="<?= e($p['name']) ?>" id="mainImg">
                <?php if ($sale): ?><span class="tag tag-sale gallery-tag">-<?= (int)round(100 - $p['price'] / $p['old_price'] * 100) ?>%</span><?php endif; ?>
            </div>
            <?php if (count($images) > 1): ?>
                <div class="thumbs">
                    <?php foreach ($images as $i => $img): ?>
                        <button type="button" class="thumb<?= $i === 0 ? ' active' : '' ?>" data-thumb="<?= e(img_url($img)) ?>">
                            <img src="<?= e(img_url($img)) ?>" alt="">
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="product-info">
            <?php if ($p['is_new']): ?><span class="eyebrow">Yeni kolleksiya</span><?php endif; ?>
            <h1><?= e($p['name']) ?></h1>
            <div class="price price-lg">
                <span class="price-now"><?= money($p['price']) ?></span>
                <?php if ($sale): ?><span class="price-old"><?= money($p['old_price']) ?></span><?php endif; ?>
            </div>
            <p class="stock <?= $inStock ? 'in' : 'out' ?>">
                <?= $inStock ? ((int)$p['stock'] <= 5 ? 'Son ' . (int)$p['stock'] . ' ədəd qalıb' : 'Stokda var') : 'Hazırda stokda yoxdur' ?>
            </p>

            <form method="post" action="<?= url('cart.php') ?>" class="buy-form">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">

                <?php if ($sizes): ?>
                    <div class="opt">
                        <div class="opt-head"><strong>Ölçü</strong><button type="button" class="link small" data-modal-open="sizeModal">Ölçü cədvəli</button></div>
                        <div class="chips">
                            <?php foreach ($sizes as $s): ?>
                                <label class="chip"><input type="radio" name="size" value="<?= e($s) ?>" required><span><?= e($s) ?></span></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($colors): ?>
                    <div class="opt">
                        <div class="opt-head"><strong>Rəng</strong></div>
                        <div class="chips">
                            <?php foreach ($colors as $i => $c): ?>
                                <label class="chip"><input type="radio" name="color" value="<?= e($c) ?>" <?= $i === 0 ? 'checked' : '' ?>><span><?= e($c) ?></span></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="buy-row">
                    <div class="qty" data-qty>
                        <button type="button" data-qty-minus aria-label="Azalt">−</button>
                        <input type="number" name="qty" value="1" min="1" max="<?= max(1, (int)$p['stock']) ?>" aria-label="Say">
                        <button type="button" data-qty-plus aria-label="Artır">+</button>
                    </div>
                    <button class="btn btn-grow" type="submit" <?= $inStock ? '' : 'disabled' ?>><?= icon('bag', 18) ?> Səbətə əlavə et</button>
                </div>
            </form>

            <div class="buy-extra">
                <form method="post" action="<?= url('fav.php') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                    <input type="hidden" name="back" value="<?= e(current_uri()) ?>">
                    <button class="btn btn-ghost btn-sm" type="submit"><?= icon('heart', 16) ?> <?= is_fav((int)$p['id']) ? 'Seçilmişlərdən çıxar' : 'Seçilmişlərə əlavə et' ?></button>
                </form>
                <a class="btn btn-ghost btn-sm" target="_blank" rel="noopener"
                   href="https://wa.me/<?= e(WHATSAPP_NUMBER) ?>?text=<?= rawurlencode('Salam! Bu məhsulla maraqlanıram: ' . $p['name']) ?>"><?= icon('whatsapp', 16) ?> WhatsApp ilə sifariş</a>
            </div>

            <ul class="perks">
                <li><?= icon('box', 18) ?> Gizli, adsız qablaşdırma</li>
                <li><?= icon('truck', 18) ?> <?= money(FREE_DELIVERY_FROM) ?>-dan pulsuz çatdırılma</li>
                <li><?= icon('refresh', 18) ?> 14 gün ərzində ölçü dəyişmə</li>
            </ul>

            <div class="accordion">
                <details open>
                    <summary>Təsvir</summary>
                    <div><?= nl2br(e($p['description'])) ?></div>
                </details>
                <?php if ($p['material']): ?>
                <details>
                    <summary>Material və qulluq</summary>
                    <div><p><strong>Tərkib:</strong> <?= e($p['material']) ?></p><p>30°C-də əl ilə və ya zərif rejimdə yuyun. Ağartıcı istifadə etməyin, ütüləməyin.</p></div>
                </details>
                <?php endif; ?>
                <details>
                    <summary>Çatdırılma və qaytarma</summary>
                    <div><p>Bakı daxili 24 saat ərzində, regionlara 2–3 iş günü. Gigiyena qaydalarına görə yalnız qablaşdırması açılmamış və etiketi üzərində olan məhsullar dəyişdirilir.</p></div>
                </details>
            </div>
        </div>
    </div>
</section>

<?php if ($related): ?>
<section class="section">
    <div class="container">
        <div class="section-head"><h2>Bunlar da xoşunuza gələ bilər</h2></div>
        <div class="grid"><?php foreach ($related as $r) echo product_card($r); ?></div>
    </div>
</section>
<?php endif; ?>

<div class="modal" id="sizeModal" hidden>
    <div class="modal-box">
        <button class="modal-close" type="button" data-modal-close aria-label="Bağla"><?= icon('x') ?></button>
        <h3>Ölçü cədvəli</h3>
        <?php include __DIR__ . '/includes/size-table.php'; ?>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
