<?php
require __DIR__ . '/_init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($action === 'toggle') {
        db()->prepare('UPDATE products SET is_active = 1 - is_active WHERE id = ?')->execute([$id]);
        flash('success', 'Məhsulun görünürlüyü dəyişdirildi.');
    } elseif ($action === 'delete') {
        $st = db()->prepare('SELECT image, gallery FROM products WHERE id = ?');
        $st->execute([$id]);
        if ($p = $st->fetch()) {
            delete_upload($p['image']);
            foreach (json_decode($p['gallery'] ?: '[]', true) ?: [] as $g) delete_upload($g);
            db()->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
            flash('success', 'Məhsul silindi.');
        }
    }
    redirect(url('admin/products.php?' . http_build_query(array_intersect_key($_GET, ['q' => 1, 'cat' => 1]))));
}

$q = trim($_GET['q'] ?? '');
$cat = (int)($_GET['cat'] ?? 0);
$where = ['1=1'];
$params = [];
if ($q !== '') { $where[] = 'p.name LIKE ?'; $params[] = "%$q%"; }
if ($cat) { $where[] = 'p.category_id = ?'; $params[] = $cat; }
$st = db()->prepare('SELECT p.*, c.name cat_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE '
    . implode(' AND ', $where) . ' ORDER BY p.id DESC');
$st->execute($params);
$products = $st->fetchAll();

admin_header('Məhsullar');
?>
<form class="toolbar" method="get">
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="Məhsul adı">
    <select name="cat">
        <option value="0">Bütün kateqoriyalar</option>
        <?php foreach (categories() as $c): ?><option value="<?= $c['id'] ?>" <?= $cat === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
    </select>
    <button class="btn btn-sm btn-ghost" type="submit">Filtr</button>
    <a class="btn btn-sm push" href="<?= url('admin/product-edit.php') ?>">+ Yeni məhsul</a>
</form>
<div class="panel">
    <div class="table-wrap">
    <table class="table">
        <thead><tr><th></th><th>Ad</th><th>Kateqoriya</th><th>Qiymət</th><th>Stok</th><th>Satılıb</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($products as $p): ?>
            <tr class="<?= $p['is_active'] ? '' : 'dim' ?>">
                <td><img class="thumb" src="<?= e(img_url($p['image'])) ?>" alt=""></td>
                <td><a href="<?= url('admin/product-edit.php?id=' . $p['id']) ?>"><strong><?= e($p['name']) ?></strong></a>
                    <?php if ($p['is_featured']): ?><span class="chip">Bestseller</span><?php endif; ?>
                    <?php if ($p['is_new']): ?><span class="chip">Yeni</span><?php endif; ?></td>
                <td><?= e($p['cat_name'] ?? '—') ?></td>
                <td><?= money($p['price']) ?><?php if ($p['old_price'] > $p['price']): ?><br><small class="strike"><?= money($p['old_price']) ?></small><?php endif; ?></td>
                <td><span class="<?= $p['stock'] == 0 ? 'danger' : ($p['stock'] <= 3 ? 'warn' : '') ?>"><?= (int)$p['stock'] ?></span></td>
                <td><?= (int)$p['sold_count'] ?></td>
                <td>
                    <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <button class="link-btn" type="submit"><?= $p['is_active'] ? '● Aktiv' : '○ Gizli' ?></button></form>
                </td>
                <td class="actions">
                    <a class="btn btn-sm btn-ghost" href="<?= url('admin/product-edit.php?id=' . $p['id']) ?>">Redaktə</a>
                    <form method="post" data-confirm="«<?= e($p['name']) ?>» silinsin?"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <button class="btn btn-sm btn-danger" type="submit">Sil</button></form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$products): ?><tr><td colspan="8" class="muted">Məhsul tapılmadı.</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
<?php admin_footer(); ?>
