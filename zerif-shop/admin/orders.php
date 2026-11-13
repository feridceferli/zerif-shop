<?php
require __DIR__ . '/_init.php';

$status = $_GET['status'] ?? '';
$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$per = 25;

$where = ['1=1'];
$params = [];
if ($status !== '' && isset(order_statuses()[$status])) {
    $where[] = 'status = ?';
    $params[] = $status;
}
if ($q !== '') {
    $where[] = '(order_no LIKE ? OR customer_name LIKE ? OR phone LIKE ?)';
    array_push($params, "%$q%", "%$q%", "%$q%");
}
$w = implode(' AND ', $where);
$st = db()->prepare("SELECT COUNT(*) FROM orders WHERE $w");
$st->execute($params);
$total = (int)$st->fetchColumn();
$pages = max(1, (int)ceil($total / $per));
$st = db()->prepare("SELECT * FROM orders WHERE $w ORDER BY id DESC LIMIT $per OFFSET " . (($page - 1) * $per));
$st->execute($params);
$orders = $st->fetchAll();
$counts = db()->query('SELECT status, COUNT(*) FROM orders GROUP BY status')->fetchAll(PDO::FETCH_KEY_PAIR);

admin_header('Sifarişlər');
?>
<div class="tabs">
    <a href="<?= url('admin/orders.php') ?>" class="<?= $status === '' ? 'active' : '' ?>">Hamısı (<?= array_sum($counts) ?>)</a>
    <?php foreach (order_statuses() as $k => $l): ?>
        <a href="<?= url('admin/orders.php?status=' . $k) ?>" class="<?= $status === $k ? 'active' : '' ?>"><?= e($l) ?> (<?= (int)($counts[$k] ?? 0) ?>)</a>
    <?php endforeach; ?>
</div>
<form class="toolbar" method="get">
    <?php if ($status): ?><input type="hidden" name="status" value="<?= e($status) ?>"><?php endif; ?>
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="Nömrə, ad və ya telefon">
    <button class="btn btn-sm" type="submit">Axtar</button>
</form>
<div class="panel">
    <?php if ($orders): ?>
    <div class="table-wrap">
    <table class="table">
        <thead><tr><th>№</th><th>Müştəri</th><th>Şəhər</th><th>Ödəniş</th><th>Məbləğ</th><th>Status</th><th>Tarix</th></tr></thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
            <tr class="clickable" onclick="location.href='<?= url('admin/order.php?id=' . $o['id']) ?>'">
                <td><a href="<?= url('admin/order.php?id=' . $o['id']) ?>"><strong><?= e($o['order_no']) ?></strong></a></td>
                <td><?= e($o['customer_name']) ?><br><small><?= e($o['phone']) ?></small></td>
                <td><?= e($o['city']) ?></td>
                <td><small><?= e(payment_methods()[$o['payment_method']] ?? $o['payment_method']) ?></small></td>
                <td><strong><?= money($o['total']) ?></strong></td>
                <td><?= status_badge($o['status']) ?></td>
                <td><small><?= e(date('d.m.Y H:i', strtotime($o['created_at']))) ?></small></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php if ($pages > 1): ?>
        <nav class="pagination"><?php for ($i = 1; $i <= $pages; $i++): ?>
            <a class="<?= $i === $page ? 'active' : '' ?>" href="?<?= e(http_build_query(array_merge($_GET, ['page' => $i]))) ?>"><?= $i ?></a>
        <?php endfor; ?></nav>
    <?php endif; ?>
    <?php else: ?><p class="muted">Sifariş tapılmadı.</p><?php endif; ?>
</div>
<?php admin_footer(); ?>
