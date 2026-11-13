<?php
require __DIR__ . '/_init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    if ($action === 'add') {
        $code = strtoupper(preg_replace('/[^A-Za-z0-9_-]/', '', $_POST['code'] ?? ''));
        $type = ($_POST['type'] ?? '') === 'fixed' ? 'fixed' : 'percent';
        $value = (float)($_POST['value'] ?? 0);
        $min = (float)($_POST['min_total'] ?? 0);
        $exp = trim($_POST['expires_at'] ?? '') ?: null;
        if ($code === '' || $value <= 0 || ($type === 'percent' && $value > 90)) {
            flash('error', 'Kodu və düzgün dəyəri daxil edin (faiz maks. 90).');
        } else {
            try {
                db()->prepare('INSERT INTO coupons (code, type, value, min_total, expires_at) VALUES (?,?,?,?,?)')
                    ->execute([$code, $type, $value, $min, $exp]);
                flash('success', 'Promokod yaradıldı.');
            } catch (PDOException $ex) {
                flash('error', 'Bu kod artıq mövcuddur.');
            }
        }
    } elseif ($action === 'toggle') {
        db()->prepare('UPDATE coupons SET is_active = 1 - is_active WHERE id = ?')->execute([$id]);
    } elseif ($action === 'delete') {
        db()->prepare('DELETE FROM coupons WHERE id = ?')->execute([$id]);
        flash('success', 'Promokod silindi.');
    }
    redirect(url('admin/coupons.php'));
}

$coupons = db()->query("SELECT c.*, (SELECT COUNT(*) FROM orders o WHERE o.coupon_code = c.code) used FROM coupons c ORDER BY id DESC")->fetchAll();
admin_header('Promokodlar');
?>
<div class="cols">
    <div class="panel">
        <table class="table">
            <thead><tr><th>Kod</th><th>Endirim</th><th>Min. məbləğ</th><th>Bitmə</th><th>İstifadə</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($coupons as $c): ?>
                <tr class="<?= $c['is_active'] ? '' : 'dim' ?>">
                    <td><strong><?= e($c['code']) ?></strong></td>
                    <td><?= $c['type'] === 'percent' ? (float)$c['value'] . '%' : money($c['value']) ?></td>
                    <td><?= money($c['min_total']) ?></td>
                    <td><?= $c['expires_at'] ? e(date('d.m.Y', strtotime($c['expires_at']))) : '—' ?></td>
                    <td><?= (int)$c['used'] ?></td>
                    <td><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= $c['id'] ?>"><button class="link-btn" type="submit"><?= $c['is_active'] ? '● Aktiv' : '○ Deaktiv' ?></button></form></td>
                    <td><form method="post" data-confirm="Promokod silinsin?"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $c['id'] ?>"><button class="btn btn-sm btn-danger" type="submit">Sil</button></form></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$coupons): ?><tr><td colspan="7" class="muted">Promokod yoxdur.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
    <form method="post" class="panel form">
        <?= csrf_field() ?><input type="hidden" name="action" value="add">
        <h2>Yeni promokod</h2>
        <label>Kod *<input type="text" name="code" placeholder="YAY20" required></label>
        <div class="row">
            <label>Növ<select name="type"><option value="percent">Faiz (%)</option><option value="fixed">Sabit məbləğ (<?= CURRENCY ?>)</option></select></label>
            <label>Dəyər *<input type="number" step="0.01" min="0" name="value" required></label>
        </div>
        <label>Minimum sifariş (<?= CURRENCY ?>)<input type="number" step="0.01" min="0" name="min_total" value="0"></label>
        <label>Bitmə tarixi (istəyə görə)<input type="date" name="expires_at"></label>
        <button class="btn btn-block" type="submit">Yarat</button>
    </form>
</div>
<?php admin_footer(); ?>
