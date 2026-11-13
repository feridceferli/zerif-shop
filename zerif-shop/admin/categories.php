<?php
require __DIR__ . '/_init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    try {
        if ($action === 'save') {
            $name = trim($_POST['name'] ?? '');
            if ($name === '') throw new RuntimeException('Kateqoriya adını daxil edin.');
            $desc = trim($_POST['description'] ?? '');
            $sort = (int)($_POST['sort_order'] ?? 0);
            $img = upload_image($_FILES['image'] ?? []);
            $slug = unique_slug('categories', $name, $id);
            if ($id) {
                if ($img) {
                    $old = db()->prepare('SELECT image FROM categories WHERE id = ?');
                    $old->execute([$id]);
                    delete_upload($old->fetchColumn() ?: null);
                    db()->prepare('UPDATE categories SET image = ? WHERE id = ?')->execute([$img, $id]);
                }
                db()->prepare('UPDATE categories SET name = ?, slug = ?, description = ?, sort_order = ? WHERE id = ?')
                    ->execute([$name, $slug, $desc, $sort, $id]);
                flash('success', 'Kateqoriya yeniləndi.');
            } else {
                db()->prepare('INSERT INTO categories (name, slug, description, image, sort_order) VALUES (?,?,?,?,?)')
                    ->execute([$name, $slug, $desc, $img ?? '', $sort]);
                flash('success', 'Kateqoriya əlavə olundu.');
            }
        } elseif ($action === 'delete') {
            $cnt = db()->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
            $cnt->execute([$id]);
            if ((int)$cnt->fetchColumn() > 0) throw new RuntimeException('Bu kateqoriyada məhsul var. Əvvəlcə məhsulları köçürün və ya silin.');
            db()->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);
            flash('success', 'Kateqoriya silindi.');
        }
    } catch (RuntimeException $ex) {
        flash('error', $ex->getMessage());
    }
    redirect(url('admin/categories.php'));
}

$cats = db()->query('SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) cnt FROM categories c ORDER BY sort_order, name')->fetchAll();
$edit = null;
if (!empty($_GET['edit'])) {
    foreach ($cats as $c) if ((int)$c['id'] === (int)$_GET['edit']) $edit = $c;
}

admin_header('Kateqoriyalar');
?>
<div class="cols">
    <div class="panel">
        <table class="table">
            <thead><tr><th></th><th>Ad</th><th>Məhsul</th><th>Sıra</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($cats as $c): ?>
                <tr>
                    <td><img class="thumb" src="<?= e(img_url($c['image'])) ?>" alt=""></td>
                    <td><strong><?= e($c['name']) ?></strong><br><small class="muted"><?= e($c['slug']) ?></small></td>
                    <td><?= (int)$c['cnt'] ?></td>
                    <td><?= (int)$c['sort_order'] ?></td>
                    <td class="actions">
                        <a class="btn btn-sm btn-ghost" href="?edit=<?= $c['id'] ?>">Redaktə</a>
                        <form method="post" data-confirm="Kateqoriya silinsin?"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $c['id'] ?>"><button class="btn btn-sm btn-danger" type="submit">Sil</button></form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <form method="post" enctype="multipart/form-data" class="panel form">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
        <h2><?= $edit ? 'Redaktə: ' . e($edit['name']) : 'Yeni kateqoriya' ?></h2>
        <label>Ad *<input type="text" name="name" value="<?= e($edit['name'] ?? '') ?>" required></label>
        <label>Təsvir<textarea name="description" rows="3"><?= e($edit['description'] ?? '') ?></textarea></label>
        <label>Sıra nömrəsi<input type="number" name="sort_order" value="<?= (int)($edit['sort_order'] ?? count($cats)) ?>"></label>
        <label>Şəkil<input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label>
        <button class="btn btn-block" type="submit">Yadda saxla</button>
        <?php if ($edit): ?><a class="btn btn-ghost btn-block" href="<?= url('admin/categories.php') ?>">Ləğv et</a><?php endif; ?>
    </form>
</div>
<?php admin_footer(); ?>
