<?php
require __DIR__ . '/_init.php';

$db = db();
$today = date('Y-m-d');
$monthStart = date('Y-m-01');
$stats = [
    'today_orders' => (int)$db->query("SELECT COUNT(*) FROM orders WHERE date(created_at) = '$today'")->fetchColumn(),
    'new_orders' => (int)$db->query("SELECT COUNT(*) FROM orders WHERE status = 'new'")->fetchColumn(),
    'month_revenue' => (float)$db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status != 'cancelled' AND date(created_at) >= '$monthStart'")->fetchColumn(),
    'total_revenue' => (float)$db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status = 'delivered'")->fetchColumn(),
    'products' => (int)$db->query('SELECT COUNT(*) FROM products WHERE is_active = 1')->fetchColumn(),
];
$recent = $db->query('SELECT * FROM orders ORDER BY id DESC LIMIT 8')->fetchAll();
$lowStock = $db->query('SELECT id, name, stock FROM products WHERE is_active = 1 AND stock <= 3 ORDER BY stock ASC LIMIT 8')->fetchAll();
$top = $db->query("SELECT oi.name, SUM(oi.qty) q, SUM(oi.qty * oi.price) s FROM order_items oi
                   JOIN orders o ON o.id = oi.order_id WHERE o.status != 'cancelled'
                   GROUP BY oi.name ORDER BY q DESC LIMIT 5")->fetchAll();

// Son 14 günün satış qrafiki
$days = [];
for ($i = 13; $i >= 0; $i--) {
    $days[date('Y-m-d', strtotime("-$i days"))] = 0;
}
$st = $db->prepare("SELECT date(created_at) d, SUM(total) s FROM orders WHERE status != 'cancelled' AND date(created_at) >= ? GROUP BY d");
$st->execute([array_key_first($days)]);
foreach ($st as $r) {
    if (isset($days[$r['d']])) $days[$r['d']] = (float)$r['s'];
}
$maxDay = max(1, max($days));

admin_header('İdarə paneli');
?>
<div class="stats">
    <div class="stat"><span>Bu gün sifariş</span><strong><?= $stats['today_orders'] ?></strong></div>
    <div class="stat"><span>Yeni (baxılmamış)</span><strong><?= $stats['new_orders'] ?></strong></div>
    <div class="stat"><span>Bu ay satış</span><strong><?= money($stats['month_revenue']) ?></strong></div>
    <div class="stat"><span>Çatdırılmış (cəmi)</span><strong><?= money($stats['total_revenue']) ?></strong></div>
    <div class="stat"><span>Aktiv məhsul</span><strong><?= $stats['products'] ?></strong></div>
</div>

<div class="panel">
    <h2>Son 14 gün satış</h2>
    <div class="chart" role="img" aria-label="Son 14 günün satış qrafiki">
        <?php foreach ($days as $d => $v): ?>
            <div class="bar" title="<?= e(date('d.m', strtotime($d))) ?>: <?= e(money($v)) ?>">
                <span style="height: <?= round($v / $maxDay * 100, 1) ?>%"></span>
                <small><?= e(date('d', strtotime($d))) ?></small>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="cols">
    <div class="panel">
        <div class="panel-head"><h2>Son sifarişlər</h2><a href="<?= url('admin/orders.php') ?>">Hamısı →</a></div>
        <?php if ($recent): ?>
        <table class="table">
            <thead><tr><th>№</th><th>Müştəri</th><th>Məbləğ</th><th>Status</th><th>Tarix</th></tr></thead>
            <tbody>
            <?php foreach ($recent as $o): ?>
                <tr onclick="location.href='<?= url('admin/order.php?id=' . $o['id']) ?>'" class="clickable">
                    <td><strong><?= e($o['order_no']) ?></strong></td>
                    <td><?= e($o['customer_name']) ?><br><small><?= e($o['phone']) ?></small></td>
                    <td><?= money($o['total']) ?></td>
                    <td><?= status_badge($o['status']) ?></td>
                    <td><small><?= e(date('d.m H:i', strtotime($o['created_at']))) ?></small></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?><p class="muted">Hələ sifariş yoxdur.</p><?php endif; ?>
    </div>
    <div>
        <div class="panel">
            <h2>Az qalan məhsullar</h2>
            <?php if ($lowStock): ?>
                <ul class="list">
                    <?php foreach ($lowStock as $p): ?>
                        <li><a href="<?= url('admin/product-edit.php?id=' . $p['id']) ?>"><?= e($p['name']) ?></a><span class="<?= $p['stock'] == 0 ? 'danger' : 'warn' ?>"><?= (int)$p['stock'] ?> əd.</span></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?><p class="muted">Hər şey qaydasındadır.</p><?php endif; ?>
        </div>
        <div class="panel">
            <h2>Ən çox satılanlar</h2>
            <?php if ($top): ?>
                <ul class="list">
                    <?php foreach ($top as $t): ?><li><span><?= e($t['name']) ?></span><span><?= (int)$t['q'] ?> əd.</span></li><?php endforeach; ?>
                </ul>
            <?php else: ?><p class="muted">Məlumat yoxdur.</p><?php endif; ?>
        </div>
    </div>
</div>
<?php admin_footer(); ?>
