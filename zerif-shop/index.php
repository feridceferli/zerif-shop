<?php
require __DIR__ . '/includes/functions.php';

$q = fn(string $sql) => db()->query($sql)->fetchAll();
$deals = $q('SELECT * FROM products WHERE is_active = 1 AND old_price > price ORDER BY (old_price - price) / old_price DESC LIMIT 10');
$best = $q('SELECT * FROM products WHERE is_active = 1 AND is_featured = 1 ORDER BY sold_count DESC, id DESC LIMIT 10');
$newest = $q('SELECT * FROM products WHERE is_active = 1 ORDER BY created_at DESC, id DESC LIMIT 10');
$catCounts = db()->query('SELECT category_id, COUNT(*) c FROM products WHERE is_active = 1 GROUP BY category_id')->fetchAll(PDO::FETCH_KEY_PAIR);
$bannerImg = db()->query("SELECT image FROM products WHERE is_active = 1 AND image LIKE 'uploads/demo/%' ORDER BY is_featured DESC, price DESC LIMIT 3")->fetchAll(PDO::FETCH_COLUMN);

function product_row(string $title, array $items, string $more, string $icon = ''): void
{
    if (!$items) return; ?>
    <section class="block">
        <div class="block-head">
            <h2><?= $icon ?><?= e($title) ?></h2>
            <a class="more" href="<?= e($more) ?>">Hamısına bax ›</a>
        </div>
        <div class="grid grid-5"><?php foreach ($items as $p) echo product_card($p); ?></div>
    </section>
<?php }

$pageTitle = '';
require __DIR__ . '/includes/header.php';
?>
<div class="container home">
    <section class="banners">
        <a class="banner banner-main" href="<?= url('shop.php?sale=1') ?>">
            <div class="banner-text">
                <span class="banner-tag">Mövsüm endirimi</span>
                <h1>Alt paltarlarında <br><strong>40%-dək endirim</strong></h1>
                <p>Dəstlər, büstqalterlər və gecə geyimlərində xüsusi qiymətlər</p>
                <span class="btn btn-white">İndi al</span>
            </div>
            <?php if (!empty($bannerImg[0])): ?><img src="<?= e(img_url($bannerImg[0])) ?>" alt="" class="banner-img"><?php endif; ?>
        </a>
        <div class="banner-side">
            <a class="banner banner-small banner-dark" href="<?= url('shop.php?cat=gece-geyimleri') ?>">
                <div class="banner-text"><span class="banner-tag">Yeni</span><h3>Gecə geyimləri</h3><span class="link-arrow">Kəşf et ›</span></div>
                <?php if (!empty($bannerImg[1])): ?><img src="<?= e(img_url($bannerImg[1])) ?>" alt="" class="banner-img"><?php endif; ?>
            </a>
            <a class="banner banner-small banner-light" href="<?= url('cart.php') ?>">
                <div class="banner-text"><span class="banner-tag">Promokod</span><h3>İlk sifarişə 10%</h3><span class="code">XOS10</span></div>
                <?php if (!empty($bannerImg[2])): ?><img src="<?= e(img_url($bannerImg[2])) ?>" alt="" class="banner-img"><?php endif; ?>
            </a>
        </div>
    </section>

    <section class="cat-circles" aria-label="Kateqoriyalar">
        <?php foreach (categories() as $c): ?>
            <a href="<?= url('shop.php?cat=' . urlencode($c['slug'])) ?>" class="cat-circle">
                <span class="cat-circle-img"><img src="<?= e(img_url($c['image'])) ?>" alt="" loading="lazy"></span>
                <strong><?= e($c['name']) ?></strong>
                <small><?= (int)($catCounts[$c['id']] ?? 0) ?> məhsul</small>
            </a>
        <?php endforeach; ?>
        <a href="<?= url('shop.php?sale=1') ?>" class="cat-circle">
            <span class="cat-circle-img cat-circle-sale">%</span>
            <strong>Endirimlər</strong>
            <small>Ən sərfəli</small>
        </a>
    </section>

    <?php product_row('Flaş endirimlər', $deals, url('shop.php?sale=1'), '<span class="flash-ico">' . icon('bolt', 18) . '</span>'); ?>
    <?php product_row('Çox satılanlar', $best, url('shop.php?sort=popular')); ?>

    <a class="strip" href="<?= url('info.php?p=delivery') ?>">
        <?= icon('box', 22) ?> <span><strong>Gizli qablaşdırma</strong> — bağlamanın üzərində mağaza və məhsul adı yazılmır</span> <em>Ətraflı ›</em>
    </a>

    <?php product_row('Yeni gələnlər', $newest, url('shop.php?sort=new')); ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
