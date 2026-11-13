<?php
require __DIR__ . '/_init.php';

$id = (int)($_GET['id'] ?? 0);
$db = db();
$st = $db->prepare('SELECT * FROM orders WHERE id = ?');
$st->execute([$id]);
$o = $st->fetch();
if (!$o) {
    flash('error', 'Sifariş tapılmadı.');
    redirect(url('admin/orders.php'));
}
$it = $db->prepare('SELECT * FROM order_items WHERE order_id = ?');
$it->execute([$id]);
$items = $it->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';
    if ($action === 'status') {
        $new = $_POST['status'] ?? '';
        if (isset(order_statuses()[$new]) && $new !== $o['status']) {
            $db->beginTransaction();
            // Ləğv edildikdə stoku geri qaytar, ləğvdən çıxarıldıqda yenidən çıx
            $sign = 0;
            if ($new === 'cancelled') $sign = 1;
            if ($o['status'] === 'cancelled') $sign = -1;
            if ($sign !== 0) {
                $u = $db->prepare('UPDATE products SET stock = MAX(0, stock + ?), sold_count = MAX(0, sold_count - ?) WHERE id = ?');
                foreach ($items as $i) {
                    if ($i['product_id']) $u->execute([$sign * $i['qty'], $sign * $i['qty'], $i['product_id']]);
                }
            }
            $db->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute([$new, $id]);
            $db->commit();
            flash('success', 'Status dəyişdirildi: ' . status_label($new));
        }
    } elseif ($action === 'note') {
        $db->prepare('UPDATE orders SET admin_note = ? WHERE id = ?')->execute([trim($_POST['admin_note'] ?? ''), $id]);
        flash('success', 'Qeyd yadda saxlanıldı.');
    } elseif ($action === 'delete') {
        $db->prepare('DELETE FROM orders WHERE id = ?')->execute([$id]);
        flash('success', 'Sifariş silindi.');
        redirect(url('admin/orders.php'));
    }
    redirect(url('admin/order.php?id=' . $id));
}

$waPhone = preg_replace('/\D+/', '', $o['phone']);
if (strlen($waPhone) === 9) $waPhone = '994' . $waPhone;
if (strlen($waPhone) === 10 && $waPhone[0] === '0') $waPhone = '994' . substr($waPhone, 1);

admin_header('Sifariş ' . $o['order_no']);
?>
<p><a href="<?= url('admin/orders.php') ?>">← Sifarişlərə qayıt</a></p>
<div class="cols">
    <div>
        <div class="panel">
            <div class="panel-head"><h2>Məhsullar</h2><?= status_badge($o['status']) ?></div>
            <table class="table">
                <thead><tr><th>Məhsul</th><th>Ölçü</th><th>Rəng</th><th>Say</th><th>Qiymət</th><th>Cəm</th></tr></thead>
                <tbody>
                <?php foreach ($items as $i): ?>
                    <tr>
                        <td><?php if ($i['product_id']): ?><a href="<?= url('admin/product-edit.php?id=' . $i['product_id']) ?>"><?= e($i['name']) ?></a><?php else: ?><?= e($i['name']) ?><?php endif; ?></td>
                        <td><?= e($i['size']) ?></td><td><?= e($i['color']) ?></td><td><?= (int)$i['qty'] ?></td>
                        <td><?= money($i['price']) ?></td><td><?= money($i['price'] * $i['qty']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <dl class="totals">
                <div><dt>Məhsullar</dt><dd><?= money($o['subtotal']) ?></dd></div>
                <?php if ($o['discount'] > 0): ?><div><dt>Endirim <?= $o['coupon_code'] ? '(' . e($o['coupon_code']) . ')' : '' ?></dt><dd>−<?= money($o['discount']) ?></dd></div><?php endif; ?>
                <div><dt>Çatdırılma</dt><dd><?= money($o['delivery_fee']) ?></dd></div>
                <div class="grand"><dt>Cəmi</dt><dd><?= money($o['total']) ?></dd></div>
            </dl>
        </div>
        <div class="panel">
            <h2>Daxili qeyd</h2>
            <form method="post">
                <?= csrf_field() ?><input type="hidden" name="action" value="note">
                <textarea name="admin_note" rows="3" placeholder="Yalnız adminlər görür"><?= e($o['admin_note']) ?></textarea>
                <button class="btn btn-sm" type="submit">Yadda saxla</button>
            </form>
        </div>
    </div>
    <div>
        <div class="panel">
            <h2>Status</h2>
            <form method="post" class="inline-form">
                <?= csrf_field() ?><input type="hidden" name="action" value="status">
                <select name="status">
                    <?php foreach (order_statuses() as $k => $l): ?><option value="<?= $k ?>" <?= $o['status'] === $k ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?>
                </select>
                <button class="btn btn-sm" type="submit">Dəyiş</button>
            </form>
            <p class="muted small">«Ləğv edildi» seçildikdə məhsullar avtomatik stoka qaytarılır.</p>
        </div>
        <div class="panel">
            <h2>Müştəri</h2>
            <ul class="kv">
                <li><span>Ad</span><strong><?= e($o['customer_name']) ?></strong></li>
                <li><span>Telefon</span><a href="tel:<?= e($o['phone']) ?>"><?= e($o['phone']) ?></a></li>
                <?php if ($o['email']): ?><li><span>E-poçt</span><a href="mailto:<?= e($o['email']) ?>"><?= e($o['email']) ?></a></li><?php endif; ?>
                <li><span>Ünvan</span><?= e($o['city'] . ', ' . $o['address']) ?></li>
                <li><span>Ödəniş</span><?= e(payment_methods()[$o['payment_method']] ?? $o['payment_method']) ?></li>
                <li><span>Tarix</span><?= e(date('d.m.Y H:i', strtotime($o['created_at']))) ?></li>
                <?php if ($o['note']): ?><li><span>Qeyd</span><em><?= nl2br(e($o['note'])) ?></em></li><?php endif; ?>
            </ul>
            <a class="btn btn-sm btn-wa" target="_blank" rel="noopener" href="https://wa.me/<?= e($waPhone) ?>?text=<?= rawurlencode('Salam, ' . $o['customer_name'] . '! ' . SITE_NAME . ' mağazasından yazırıq. ' . $o['order_no'] . ' nömrəli sifarişinizi təsdiqləmək istəyirik.') ?>">WhatsApp ilə yaz</a>
        </div>
        <div class="panel">
            <form method="post" data-confirm="Sifariş həmişəlik silinsin? Stok avtomatik qaytarılmayacaq.">
                <?= csrf_field() ?><input type="hidden" name="action" value="delete">
                <button class="btn btn-sm btn-danger" type="submit">Sifarişi sil</button>
            </form>
        </div>
    </div>
</div>
<?php admin_footer(); ?>
