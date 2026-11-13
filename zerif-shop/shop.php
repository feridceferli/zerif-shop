<?php
require __DIR__ . '/includes/functions.php';

$cat = trim($_GET['cat'] ?? '');
$q = trim($_GET['q'] ?? '');
$size = trim($_GET['size'] ?? '');
$sale = !empty($_GET['sale']);
$min = $_GET['min'] ?? '';
$max = $_GET['max'] ?? '';
$sort = $_GET['sort'] ?? 'new';
$page = max(1, (int)($_GET['page'] ?? 1));

$where = ['p.is_active = 1'];
$params = [];
$category = null;
if ($cat !== '') {
    foreach (categories() as $c) {
        if ($c['slug'] === $cat) {
            $category = $c;
        }
    }
    $where[] = 'c.slug = ?';
    $params[] = $cat;
}
if ($q !== '') {
    $where[] = '(p.name LIKE ? OR p.description LIKE ? OR p.material LIKE ? OR c.name LIKE ?)';
    array_push($params, "%$q%", "%$q%", "%$q%", "%$q%");
}
if ($size !== '') {
    $where[] = "(',' || REPLACE(p.sizes, ' ', '') || ',') LIKE ?";
    $params[] = '%,' . $size . ',%';
}
if ($sale) {
    $where[] = 'p.old_price IS NOT NULL AND p.old_price > p.price';
}
if ($min !== '' && is_numeric($min)) {
    $where[] = 'p.price >= ?';
    $params[] = (float)$min;
}
if ($max !== '' && is_numeric($max)) {
    $where[] = 'p.price <= ?';
    $params[] = (float)$max;
}
$orderBy = match ($sort) {
    'price_asc' => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'popular' => 'p.sold_count DESC, p.is_featured DESC',
    'name' => 'p.name ASC',
    default => 'p.created_at DESC, p.id DESC',
};

$sqlWhere = implode(' AND ', $where);
$st = db()->prepare("SELECT COUNT(*) FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE $sqlWhere");
$st->execute($params);
$total = (int)$st->fetchColumn();
$pages = max(1, (int)ceil($total / PER_PAGE));
$page = min($page, $pages);
$offset = ($page - 1) * PER_PAGE;

$st = db()->prepare("SELECT p.* FROM products p LEFT JOIN categories c ON c.id = p.category_id
                     WHERE $sqlWhere ORDER BY $orderBy LIMIT " . PER_PAGE . " OFFSET $offset");
$st->execute($params);
$products = $st->fetchAll();

// Mövcud ölçülər (filtr üçün)
$allSizes = [];
foreach (db()->query('SELECT sizes FROM products WHERE is_active = 1') as $r) {
    foreach (csv_list($r['sizes']) as $s) {
        $allSizes[$s] = true;
    }
}
$allSizes = array_keys($allSizes);
usort($allSizes, function ($a, $b) {
    $order = ['XS', 'S', 'S/M', 'M', 'L', 'L/XL', 'XL', 'XXL'];
    $ia = array_search($a, $order, true);
    $ib = array_search($b, $order, true);
    if ($ia !== false && $ib !== false) return $ia <=> $ib;
    if ($ia !== false) return -1;
    if ($ib !== false) return 1;
    return strnatcmp($a, $b);
});

function qs(array $over): string
{
    $q = array_merge($_GET, $over);
    $q = array_filter($q, fn($v) => $v !== '' && $v !== null);
    return url('shop.php' . ($q ? '?' . http_build_query($q) : ''));
}

$heading = $category['name'] ?? ($sale ? 'Endirimli məhsullar' : ($q !== '' ? '«' . $q . '» üzrə nəticələr' : 'Bütün məhsullar'));
$pageTitle = $heading;
require __DIR__ . '/includes/header.php';
?>
<section class="page-head">
    <div class="container">
        <nav class="crumbs"><a href="<?= url() ?>">Ana səhifə</a> / <span><?= e($heading) ?></span></nav>
        <h1><?= e($heading) ?></h1>
        <?php if ($category && $category['description']): ?><p class="muted"><?= e($category['description']) ?></p><?php endif; ?>
    </div>
</section>

<section class="container shop">
    <aside class="filters" id="filters">
        <form method="get" action="<?= url('shop.php') ?>">
            <?php if ($q !== ''): ?><input type="hidden" name="q" value="<?= e($q) ?>"><?php endif; ?>
            <input type="hidden" name="sort" value="<?= e($sort) ?>">
            <div class="filter-block">
                <h4>Kateqoriya</h4>
                <label class="radio"><input type="radio" name="cat" value="" <?= $cat === '' ? 'checked' : '' ?>> Hamısı</label>
                <?php foreach (categories() as $c): ?>
                    <label class="radio"><input type="radio" name="cat" value="<?= e($c['slug']) ?>" <?= $cat === $c['slug'] ? 'checked' : '' ?>> <?= e($c['name']) ?></label>
                <?php endforeach; ?>
            </div>
            <div class="filter-block">
                <h4>Ölçü</h4>
                <div class="chips">
                    <?php foreach ($allSizes as $s): ?>
                        <label class="chip"><input type="radio" name="size" value="<?= e($s) ?>" <?= $size === $s ? 'checked' : '' ?>><span><?= e($s) ?></span></label>
                    <?php endforeach; ?>
                </div>
                <?php if ($size !== ''): ?><a class="link small" href="<?= e(qs(['size' => '', 'page' => ''])) ?>">Ölçünü sıfırla</a><?php endif; ?>
            </div>
            <div class="filter-block">
                <h4>Qiymət, <?= CURRENCY ?></h4>
                <div class="price-range">
                    <input type="number" name="min" min="0" step="1" placeholder="min" value="<?= e($min) ?>">
                    <span>—</span>
                    <input type="number" name="max" min="0" step="1" placeholder="max" value="<?= e($max) ?>">
                </div>
            </div>
            <div class="filter-block">
                <label class="check"><input type="checkbox" name="sale" value="1" <?= $sale ? 'checked' : '' ?>> Yalnız endirimdə olanlar</label>
            </div>
            <button class="btn btn-block" type="submit">Tətbiq et</button>
            <a class="btn btn-ghost btn-block" href="<?= url('shop.php') ?>">Sıfırla</a>
        </form>
    </aside>

    <div class="shop-main">
        <div class="toolbar">
            <button class="btn btn-ghost btn-sm filter-toggle" type="button" data-filter-toggle><?= icon('filter', 16) ?> Filtr</button>
            <span class="muted"><?= $total ?> məhsul</span>
            <form method="get" action="<?= url('shop.php') ?>" class="sort-form">
                <?php foreach ($_GET as $k => $v): if ($k === 'sort' || $k === 'page' || is_array($v)) continue; ?>
                    <input type="hidden" name="<?= e($k) ?>" value="<?= e($v) ?>">
                <?php endforeach; ?>
                <select name="sort" data-autosubmit aria-label="Sıralama">
                    <option value="new" <?= $sort === 'new' ? 'selected' : '' ?>>Ən yenilər</option>
                    <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>Populyar</option>
                    <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Ucuzdan bahaya</option>
                    <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Bahadan ucuza</option>
                    <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Ada görə</option>
                </select>
            </form>
        </div>

        <?php if ($products): ?>
            <div class="grid grid-3">
                <?php foreach ($products as $p) echo product_card($p); ?>
            </div>
            <?php if ($pages > 1): ?>
                <nav class="pagination">
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                        <a href="<?= e(qs(['page' => $i])) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty">
                <?= icon('search', 40) ?>
                <h3>Heç nə tapılmadı</h3>
                <p class="muted">Filtrləri dəyişin və ya başqa söz ilə axtarın.</p>
                <a class="btn" href="<?= url('shop.php') ?>">Bütün məhsullar</a>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
