<?php
require __DIR__ . '/includes/functions.php';

$items = cart_items();
if (!$items) {
    redirect(url('cart.php'));
}
$t = cart_totals($items);
$errors = [];
$f = [
    'name' => '', 'phone' => '', 'email' => '', 'city' => 'Bakı',
    'address' => '', 'note' => '', 'payment' => 'transfer',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach ($f as $k => $_) {
        $f[$k] = trim((string)($_POST[$k] ?? ''));
    }
    $f['payment'] = 'transfer';
    $phoneDigits = preg_replace('/\D+/', '', $f['phone']);

    if (strlen($f['name']) < 3) $errors['name'] = 'Ad və soyadınızı daxil edin.';
    if (strlen($phoneDigits) < 9 || strlen($phoneDigits) > 15) $errors['phone'] = 'Düzgün telefon nömrəsi daxil edin.';
    if ($f['email'] !== '' && !filter_var($f['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'E-poçt düzgün deyil.';
    if ($f['city'] === '') $errors['city'] = 'Şəhəri daxil edin.';
    if (strlen($f['address']) < 5) $errors['address'] = 'Çatdırılma ünvanını daxil edin.';
    if (!isset(payment_methods()[$f['payment']])) $errors['payment'] = 'Ödəniş üsulunu seçin.';
    if (empty($_POST['agree'])) $errors['agree'] = 'Şərtlərlə razılaşmalısınız.';

    if (!$errors) {
        $db = db();
        try {
            $db->beginTransaction();
            $dec = $db->prepare('UPDATE products SET stock = stock - ?, sold_count = sold_count + ? WHERE id = ? AND stock >= ?');
            foreach ($items as $it) {
                $dec->execute([$it['qty'], $it['qty'], $it['product']['id'], $it['qty']]);
                if ($dec->rowCount() === 0) {
                    throw new RuntimeException('«' . $it['product']['name'] . '» məhsulundan stokda kifayət qədər yoxdur.');
                }
            }
            $orderNo = 'ZR' . date('ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
            $db->prepare('INSERT INTO orders (order_no, customer_name, phone, email, city, address, note, payment_method,
                          subtotal, discount, delivery_fee, total, coupon_code, status, created_at)
                          VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')
               ->execute([$orderNo, $f['name'], $f['phone'], $f['email'], $f['city'], $f['address'], $f['note'],
                          $f['payment'], $t['sub'], $t['discount'], $t['delivery'], $t['total'],
                          $t['coupon']['code'] ?? '', 'new', date('Y-m-d H:i:s')]);
            $orderId = (int)$db->lastInsertId();
            $ins = $db->prepare('INSERT INTO order_items (order_id, product_id, name, price, size, color, qty) VALUES (?,?,?,?,?,?,?)');
            foreach ($items as $it) {
                $ins->execute([$orderId, $it['product']['id'], $it['product']['name'], $it['product']['price'],
                               $it['size'], $it['color'], $it['qty']]);
            }
            $db->commit();
            
            // Optional Telegram order notification. Configure TELEGRAM_BOT_TOKEN and
            // TELEGRAM_CHAT_ID as private Render environment variables.
            $telegramToken = trim((string)(getenv('TELEGRAM_BOT_TOKEN') ?: ''));
            $telegramChatId = trim((string)(getenv('TELEGRAM_CHAT_ID') ?: ''));
            if ($telegramToken !== '' && $telegramChatId !== '' && function_exists('curl_init')) {
                $messageLines = ["🛍 Zərif Shop — yeni sifariş", "Sifariş: " . $orderNo, ""];
                $totalQty = 0;
                foreach ($items as $it) {
                    $qty = (int)$it['qty'];
                    $totalQty += $qty;
                    $messageLines[] = $it['product']['name'];
                    $messageLines[] = 'Məhsul linki: https://zerif-shop.onrender.com/product.php?slug=' . rawurlencode((string)$it['product']['slug']);
                    $messageLines[] = "Say: " . $qty . " | Birinin qiyməti: " . money($it['product']['price']) . " | Cəmi: " . money($it['line']);
                }
                $messageLines[] = "";
                $messageLines[] = "Ümumi məhsul sayı: " . $totalQty;
                $messageLines[] = "Məhsulların məbləği: " . money($t['sub']);
                $messageLines[] = "Sifarişin yekunu: " . money($t['total']);
                $ch = curl_init('https://api.telegram.org/bot' . $telegramToken . '/sendMessage');
                curl_setopt_array($ch, [
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => http_build_query([
                        'chat_id' => $telegramChatId,
                        'text' => implode("\n", $messageLines),
                    ]),
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CONNECTTIMEOUT => 2,
                    CURLOPT_TIMEOUT => 4,
                ]);
                // Notification failure must never invalidate an already committed order.
                try {
                    $telegramResponse = curl_exec($ch);
                    $telegramHttp = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $telegramResult = is_string($telegramResponse) ? json_decode($telegramResponse, true) : null;
                    if ($telegramHttp !== 200 || !is_array($telegramResult) || empty($telegramResult['ok'])) {
                        error_log('Telegram order notification failed for ' . $orderNo . ' (HTTP ' . $telegramHttp . ')');
                    }
                } catch (Throwable $ex) {
                    error_log('Telegram order notification error for ' . $orderNo . ': ' . $ex->getMessage());
                }
                curl_close($ch);
            }
            cart_clear();
            $_SESSION['last_order'] = $orderNo;
            redirect(url('order-success.php?no=' . urlencode($orderNo)));
        } catch (RuntimeException $ex) {
            $db->rollBack();
            $errors['general'] = $ex->getMessage();
        }
    }
}

$pageTitle = 'Sifarişin rəsmiləşdirilməsi';
require __DIR__ . '/includes/header.php';

function field_err(array $errors, string $k): string
{
    return isset($errors[$k]) ? '<small class="err">' . e($errors[$k]) . '</small>' : '';
}
?>
<section class="page-head">
    <div class="container">
        <nav class="crumbs"><a href="<?= url() ?>">Ana səhifə</a> / <a href="<?= url('cart.php') ?>">Səbət</a> / <span>Rəsmiləşdirmə</span></nav>
        <h1>Sifarişin rəsmiləşdirilməsi</h1>
    </div>
</section>

<section class="container">
    <?php if (isset($errors['general'])): ?><div class="flash flash-error"><?= e($errors['general']) ?></div><?php endif; ?>
    <form method="post" class="cart-layout" novalidate>
        <?= csrf_field() ?>
        <div class="form-card">
            <h3>1. Əlaqə məlumatları</h3>
            <div class="form-grid">
                <label class="field">Ad, soyad *
                    <input type="text" name="name" value="<?= e($f['name']) ?>" required autocomplete="name">
                    <?= field_err($errors, 'name') ?>
                </label>
                <label class="field">Telefon *
                    <input type="tel" name="phone" value="<?= e($f['phone']) ?>" placeholder="+994 50 123 45 67" required autocomplete="tel">
                    <?= field_err($errors, 'phone') ?>
                </label>
                <label class="field field-full">E-poçt (istəyə görə)
                    <input type="email" name="email" value="<?= e($f['email']) ?>" autocomplete="email">
                    <?= field_err($errors, 'email') ?>
                </label>
            </div>

            <h3>2. Çatdırılma</h3>
            <div class="form-grid">
                <label class="field">Şəhər / rayon *
                    <input type="text" name="city" value="<?= e($f['city']) ?>" list="cities" required>
                    <datalist id="cities"><option>Bakı</option><option>Sumqayıt</option><option>Gəncə</option><option>Xırdalan</option><option>Mingəçevir</option><option>Lənkəran</option><option>Şəki</option><option>Quba</option></datalist>
                    <?= field_err($errors, 'city') ?>
                </label>
                <label class="field">Ünvan *
                    <input type="text" name="address" value="<?= e($f['address']) ?>" placeholder="Küçə, bina, mənzil" required autocomplete="street-address">
                    <?= field_err($errors, 'address') ?>
                </label>
                <label class="field field-full">Qeyd (istəyə görə)
                    <textarea name="note" rows="3" placeholder="Məs: zəng etmədən gəlməyin, çatdırılma vaxtı..."><?= e($f['note']) ?></textarea>
                </label>
            </div>

            <h3>3. Ödəniş üsulu</h3>
            <div class="pay-options">
                <?php foreach (payment_methods() as $k => $label): ?>
                    <label class="pay-option"><input type="radio" name="payment" value="<?= e($k) ?>" <?= $f['payment'] === $k ? 'checked' : '' ?>><span><?= e($label) ?></span></label>
                <?php endforeach; ?>
                <?= field_err($errors, 'payment') ?>
            </div>
            <label class="check"><input type="checkbox" name="agree" value="1" <?= !empty($_POST['agree']) ? 'checked' : '' ?>>
                <span><a href="<?= url('info.php?p=returns') ?>" target="_blank">Qaytarma şərtləri</a> və <a href="<?= url('info.php?p=privacy') ?>" target="_blank">məxfilik siyasəti</a> ilə tanış oldum</span></label>
            <?= field_err($errors, 'agree') ?>
        </div>

        <aside class="summary">
            <h3>Sifarişiniz</h3>
            <ul class="mini-items">
                <?php foreach ($items as $it): ?>
                    <li>
                        <img src="<?= e(img_url($it['product']['image'])) ?>" alt="">
                        <div><strong><?= e($it['product']['name']) ?></strong>
                            <small class="muted"><?= e(trim($it['size'] . ' ' . ($it['color'] ? '· ' . $it['color'] : ''))) ?> × <?= (int)$it['qty'] ?></small></div>
                        <span><?= money($it['line']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <dl class="totals">
                <div><dt>Məhsullar</dt><dd><?= money($t['sub']) ?></dd></div>
                <?php if ($t['discount'] > 0): ?><div class="discount"><dt>Endirim (<?= e($t['coupon']['code']) ?>)</dt><dd>−<?= money($t['discount']) ?></dd></div><?php endif; ?>
                <div><dt>Çatdırılma</dt><dd><?= $t['delivery'] > 0 ? money($t['delivery']) : 'Pulsuz' ?></dd></div>
                <div class="grand"><dt>Ödəniləcək</dt><dd><?= money($t['total']) ?></dd></div>
            </dl>
            <button class="btn btn-block" type="submit">Sifarişi təsdiqlə</button>
        </aside>
    </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
