<?php
require __DIR__ . '/_init.php';

$id = (int)($_GET['id'] ?? 0);
$p = [
    'id' => 0, 'category_id' => '', 'name' => '', 'description' => '', 'material' => '', 'price' => '',
    'old_price' => '', 'sizes' => 'XS,S,M,L,XL', 'colors' => '', 'stock' => 10, 'image' => '', 'gallery' => '[]',
    'is_featured' => 0, 'is_new' => 1, 'is_active' => 1,
];
if ($id) {
    $st = db()->prepare('SELECT * FROM products WHERE id = ?');
    $st->execute([$id]);
    $p = $st->fetch();
    if (!$p) {
        flash('error', 'Məhsul tapılmadı.');
        redirect(url('admin/products.php'));
    }
}
$gallery = json_decode($p['gallery'] ?: '[]', true) ?: [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $d = [
        'category_id' => (int)($_POST['category_id'] ?? 0) ?: null,
        'name' => trim($_POST['name'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'material' => trim($_POST['material'] ?? ''),
        'price' => (float)str_replace(',', '.', $_POST['price'] ?? 0),
        'old_price' => trim($_POST['old_price'] ?? '') === '' ? null : (float)str_replace(',', '.', $_POST['old_price']),
        'sizes' => implode(',', csv_list($_POST['sizes'] ?? '')),
        'colors' => implode(',', csv_list($_POST['colors'] ?? '')),
        'stock' => max(0, (int)($_POST['stock'] ?? 0)),
        'is_featured' => empty($_POST['is_featured']) ? 0 : 1,
        'is_new' => empty($_POST['is_new']) ? 0 : 1,
        'is_active' => empty($_POST['is_active']) ? 0 : 1,
    ];
    if ($d['name'] === '') $errors[] = 'Məhsulun adını daxil edin.';
    if ($d['price'] <= 0) $errors[] = 'Qiyməti düzgün daxil edin.';
    if ($d['old_price'] !== null && $d['old_price'] <= $d['price']) $d['old_price'] = null;

    $newImage = null;
    $newGallery = [];
    if (!$errors) {
        try {
            $newImage = upload_image($_FILES['image'] ?? []);
            foreach (normalize_files($_FILES['gallery'] ?? []) as $gf) {
                if ($g = upload_image($gf)) $newGallery[] = $g;
            }
        } catch (RuntimeException $ex) {
            $errors[] = $ex->getMessage();
        }
    }

    if (!$errors) {
        $image = $p['image'];
        if ($newImage) {
            if ($image) delete_upload($image);
            $image = $newImage;
        }
        $remove = (array)($_POST['remove_gallery'] ?? []);
        $keep = [];
        foreach ($gallery as $g) {
            if (in_array($g, $remove, true)) delete_upload($g); else $keep[] = $g;
        }
        $d['gallery'] = json_encode(array_values(array_merge($keep, $newGallery)));
        $d['image'] = $image;
        $d['slug'] = unique_slug('products', $d['name'], (int)$p['id']);

        $cols = array_keys($d);
        if ($p['id']) {
            $set = implode(', ', array_map(fn($c) => "$c = ?", $cols));
            db()->prepare("UPDATE products SET $set WHERE id = ?")->execute([...array_values($d), $p['id']]);
            $id = (int)$p['id'];
            flash('success', 'Məhsul yeniləndi.');
        } else {
            $d['created_at'] = date('Y-m-d H:i:s');
            $cols = array_keys($d);
            db()->prepare('INSERT INTO products (' . implode(',', $cols) . ') VALUES (' . implode(',', array_fill(0, count($cols), '?')) . ')')
                ->execute(array_values($d));
            $id = (int)db()->lastInsertId();
            flash('success', 'Məhsul əlavə olundu.');
        }
        redirect(url('admin/product-edit.php?id=' . $id));
    }
    $p = array_merge($p, $d, ['old_price' => $d['old_price'] ?? '']);
}

admin_header($p['id'] ? 'Məhsulu redaktə et' : 'Yeni məhsul');
?>
<p><a href="<?= url('admin/products.php') ?>">← Məhsullara qayıt</a>
<?php if ($p['id']): ?> · <a href="<?= e(product_url($p)) ?>" target="_blank">Saytda bax ↗</a><?php endif; ?></p>
<?php foreach ($errors as $er): ?><div class="flash flash-error"><?= e($er) ?></div><?php endforeach; ?>

<form method="post" enctype="multipart/form-data" class="cols">
    <?= csrf_field() ?>
    <div>
        <div class="panel form">
            <label>Ad *<input type="text" name="name" value="<?= e($p['name']) ?>" required></label>
            <label>Təsvir<textarea name="description" rows="6"><?= e($p['description']) ?></textarea></label>
            <label>Material / tərkib<input type="text" name="material" value="<?= e($p['material']) ?>" placeholder="Məs: 85% poliamid, 15% elastan"></label>
            <div class="row">
                <label>Qiymət (<?= CURRENCY ?>) *<input type="number" step="0.01" min="0" name="price" value="<?= e($p['price']) ?>" required></label>
                <label>Köhnə qiymət (endirim üçün)<input type="number" step="0.01" min="0" name="old_price" value="<?= e($p['old_price']) ?>"></label>
                <label>Stok (ədəd)<input type="number" min="0" name="stock" value="<?= e($p['stock']) ?>"></label>
            </div>
            <label>Ölçülər (vergüllə)<input type="text" name="sizes" value="<?= e($p['sizes']) ?>" placeholder="XS,S,M,L,XL və ya 70B,75B,80C"></label>
            <label>Rənglər (vergüllə)<input type="text" name="colors" value="<?= e($p['colors']) ?>" placeholder="Qara,Ağ,Pudra"></label>
        </div>
        <div class="panel form">
            <h2>Əlavə şəkillər (qalereya)</h2>
            <?php if ($gallery): ?>
                <div class="gallery-edit">
                    <?php foreach ($gallery as $g): ?>
                        <label class="g-item"><img src="<?= e(img_url($g)) ?>" alt=""><span><input type="checkbox" name="remove_gallery[]" value="<?= e($g) ?>"> sil</span></label>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <label>Şəkil əlavə et (bir neçə seçmək olar)<input type="file" name="gallery[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple></label>
        </div>
    </div>
    <div>
        <div class="panel form">
            <h2>Əsas şəkil</h2>
            <img class="preview" src="<?= e(img_url($p['image'])) ?>" alt="" id="imgPreview">
            <label>Yeni şəkil yüklə<input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif"
                onchange="if(this.files[0]){document.getElementById('imgPreview').src=URL.createObjectURL(this.files[0])}"></label>
            <small class="muted">JPG/PNG/WEBP, maks. 5 MB. Tövsiyə: 3:4 nisbət (məs. 900×1200).</small>
        </div>
        <div class="panel form">
            <label>Kateqoriya
                <select name="category_id">
                    <option value="">— seçin —</option>
                    <?php foreach (categories() as $c): ?><option value="<?= $c['id'] ?>" <?= (int)$p['category_id'] === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
                </select>
            </label>
            <label class="check"><input type="checkbox" name="is_active" value="1" <?= $p['is_active'] ? 'checked' : '' ?>> Saytda göstər</label>
            <label class="check"><input type="checkbox" name="is_featured" value="1" <?= $p['is_featured'] ? 'checked' : '' ?>> Bestseller (ana səhifədə)</label>
            <label class="check"><input type="checkbox" name="is_new" value="1" <?= $p['is_new'] ? 'checked' : '' ?>> «Yeni» nişanı</label>
            <button class="btn btn-block" type="submit">Yadda saxla</button>
        </div>
    </div>
</form>
<?php admin_footer(); ?>
