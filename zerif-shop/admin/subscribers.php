<?php
require __DIR__ . '/_init.php';

if (isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="abuneciler-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['email', 'tarix']);
    foreach (db()->query('SELECT email, created_at FROM subscribers ORDER BY id DESC') as $r) {
        fputcsv($out, [$r['email'], $r['created_at']]);
    }
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    db()->prepare('DELETE FROM subscribers WHERE id = ?')->execute([(int)($_POST['id'] ?? 0)]);
    flash('success', 'Abunəçi silindi.');
    redirect(url('admin/subscribers.php'));
}
$subs = db()->query('SELECT * FROM subscribers ORDER BY id DESC')->fetchAll();
admin_header('Abunəçilər');
?>
<div class="toolbar"><span class="muted"><?= count($subs) ?> abunəçi</span><a class="btn btn-sm push" href="?export=1">CSV yüklə</a></div>
<div class="panel">
    <table class="table">
        <thead><tr><th>E-poçt</th><th>Tarix</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($subs as $s): ?>
            <tr><td><?= e($s['email']) ?></td><td><small><?= e($s['created_at']) ?></small></td>
                <td><form method="post" data-confirm="Silinsin?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $s['id'] ?>"><button class="btn btn-sm btn-danger" type="submit">Sil</button></form></td></tr>
        <?php endforeach; ?>
        <?php if (!$subs): ?><tr><td colspan="3" class="muted">Hələ abunəçi yoxdur.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
<?php admin_footer(); ?>
