<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

/* ---------- Ümumi köməkçilər ---------- */

function base_url(): string
{
    static $b = null;
    if ($b === null) {
        $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
        $dir = preg_replace('#/admin$#', '', $dir);
        $b = rtrim($dir, '/');
    }
    return $b;
}

function url(string $path = ''): string
{
    return base_url() . '/' . ltrim($path, '/');
}

function e($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function money($v): string
{
    return number_format((float)$v, 2, '.', ' ') . ' ' . CURRENCY;
}

function redirect(string $to): void
{
    header('Location: ' . $to);
    exit;
}

/** Yalnız saytdaxili nisbi yolları qəbul edir (açıq yönləndirmənin qarşısını alır). */
function safe_back(?string $back, string $fallback): string
{
    if ($back && str_starts_with($back, '/') && !str_starts_with($back, '//')) {
        return $back;
    }
    return $fallback;
}

function flash(string $type, string $msg): void
{
    $_SESSION['flash'][] = [$type, $msg];
}

function flashes(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function csrf_check(): void
{
    if (!hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['csrf'] ?? ''))) {
        http_response_code(419);
        exit('Sessiyanın vaxtı bitib. Zəhmət olmasa səhifəni yeniləyib yenidən cəhd edin.');
    }
}

function slugify(string $s): string
{
    $map = ['ə' => 'e', 'Ə' => 'e', 'ı' => 'i', 'İ' => 'i', 'ö' => 'o', 'Ö' => 'o', 'ü' => 'u', 'Ü' => 'u',
            'ğ' => 'g', 'Ğ' => 'g', 'ş' => 's', 'Ş' => 's', 'ç' => 'c', 'Ç' => 'c', '«' => '', '»' => ''];
    $s = strtolower(strtr($s, $map));
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    $s = trim($s, '-');
    return $s !== '' ? $s : 'mehsul';
}

function unique_slug(string $table, string $name, int $ignoreId = 0): string
{
    $base = slugify($name);
    $slug = $base;
    $i = 2;
    $st = db()->prepare("SELECT COUNT(*) FROM $table WHERE slug = ? AND id != ?");
    while (true) {
        $st->execute([$slug, $ignoreId]);
        if ((int)$st->fetchColumn() === 0) {
            return $slug;
        }
        $slug = $base . '-' . $i++;
    }
}

/** UTF-8 təhlükəsiz qısaltma (mbstring olmasa da işləyir). */
function excerpt(?string $s, int $len): string
{
    $s = trim(preg_replace('/\s+/u', ' ', strip_tags((string)$s)));
    if (preg_match('/^.{0,' . $len . '}/us', $s, $m) && strlen($m[0]) < strlen($s)) {
        return rtrim($m[0]) . '…';
    }
    return $s;
}

function csv_list(?string $s): array
{
    return array_values(array_filter(array_map('trim', explode(',', (string)$s)), 'strlen'));
}

function img_url(?string $path): string
{
    if (!$path) {
        return url('assets/img/placeholder.svg');
    }
    if (preg_match('#^https?://#', $path)) {
        return $path;
    }
    return url($path);
}

function product_url(array $p): string
{
    return url('product.php?slug=' . urlencode($p['slug']));
}

function current_uri(): string
{
    return $_SERVER['REQUEST_URI'] ?? url();
}

function categories(): array
{
    static $c = null;
    if ($c === null) {
        $c = db()->query('SELECT * FROM categories ORDER BY sort_order, name')->fetchAll();
    }
    return $c;
}

/* ---------- Səbət ---------- */

function cart(): array
{
    return $_SESSION['cart'] ?? [];
}

function cart_key(int $id, string $size, string $color): string
{
    return $id . '|' . $size . '|' . $color;
}

function cart_add(int $id, string $size, string $color, int $qty): void
{
    $k = cart_key($id, $size, $color);
    $_SESSION['cart'][$k] = ($_SESSION['cart'][$k] ?? 0) + max(1, $qty);
}

function cart_set(string $key, int $qty): void
{
    if ($qty <= 0) {
        unset($_SESSION['cart'][$key]);
    } elseif (isset($_SESSION['cart'][$key])) {
        $_SESSION['cart'][$key] = min($qty, 99);
    }
}

function cart_clear(): void
{
    unset($_SESSION['cart'], $_SESSION['coupon']);
}

function cart_count(): int
{
    return (int)array_sum(cart());
}

/** Səbətdəki məhsulları bazadan çəkir; stoku aşan miqdarları düzəldir. */
function cart_items(): array
{
    $cart = cart();
    if (!$cart) {
        return [];
    }
    $ids = array_values(array_unique(array_map(fn($k) => (int)explode('|', $k)[0], array_keys($cart))));
    $in = implode(',', array_fill(0, count($ids), '?'));
    $st = db()->prepare("SELECT * FROM products WHERE is_active = 1 AND id IN ($in)");
    $st->execute($ids);
    $prods = [];
    foreach ($st as $p) {
        $prods[$p['id']] = $p;
    }
    $items = [];
    foreach ($cart as $k => $qty) {
        [$id, $size, $color] = array_pad(explode('|', $k, 3), 3, '');
        $p = $prods[(int)$id] ?? null;
        if (!$p || (int)$p['stock'] <= 0) {
            unset($_SESSION['cart'][$k]);
            continue;
        }
        if ($qty > (int)$p['stock']) {
            $qty = (int)$p['stock'];
            $_SESSION['cart'][$k] = $qty;
        }
        $items[] = [
            'key' => $k, 'product' => $p, 'size' => $size, 'color' => $color,
            'qty' => $qty, 'line' => round($p['price'] * $qty, 2),
        ];
    }
    return $items;
}

function find_coupon(string $code): ?array
{
    $st = db()->prepare("SELECT * FROM coupons WHERE UPPER(code) = UPPER(?) AND is_active = 1
                         AND (expires_at IS NULL OR expires_at = '' OR expires_at >= date('now','localtime'))");
    $st->execute([trim($code)]);
    return $st->fetch() ?: null;
}

function cart_totals(array $items): array
{
    $sub = round(array_sum(array_column($items, 'line')), 2);
    $discount = 0.0;
    $coupon = null;
    if (!empty($_SESSION['coupon'])) {
        $coupon = find_coupon($_SESSION['coupon']);
        if ($coupon && $sub >= (float)$coupon['min_total']) {
            $discount = $coupon['type'] === 'percent'
                ? round($sub * (float)$coupon['value'] / 100, 2)
                : min($sub, (float)$coupon['value']);
        } else {
            $coupon = null;
        }
    }
    $after = $sub - $discount;
    $delivery = ($sub <= 0 || $after >= FREE_DELIVERY_FROM) ? 0.0 : DELIVERY_FEE;
    return [
        'sub' => $sub, 'discount' => $discount, 'delivery' => $delivery,
        'coupon' => $coupon, 'total' => round($after + $delivery, 2),
    ];
}

/* ---------- Seçilmişlər ---------- */

function favs(): array
{
    return $_SESSION['fav'] ?? [];
}

function is_fav(int $id): bool
{
    return in_array($id, favs(), true);
}

function fav_toggle(int $id): bool
{
    $f = favs();
    if (in_array($id, $f, true)) {
        $_SESSION['fav'] = array_values(array_diff($f, [$id]));
        return false;
    }
    $_SESSION['fav'][] = $id;
    return true;
}

/* ---------- Sifariş statusları ---------- */

function order_statuses(): array
{
    return [
        'new' => 'Yeni',
        'confirmed' => 'Təsdiqləndi',
        'shipped' => 'Kuryerə verildi',
        'delivered' => 'Çatdırıldı',
        'cancelled' => 'Ləğv edildi',
    ];
}

function status_label(string $s): string
{
    return order_statuses()[$s] ?? $s;
}

function payment_methods(): array
{
    return [
        'cash' => 'Qapıda nağd ödəniş',
        'card_courier' => 'Qapıda kartla ödəniş (POS)',
        'transfer' => 'Kart köçürməsi (operator əlaqə saxlayacaq)',
    ];
}

/* ---------- Admin ---------- */

function is_admin(): bool
{
    return !empty($_SESSION['admin_id']);
}

function require_admin(): void
{
    if (!is_admin()) {
        redirect(url('admin/login.php'));
    }
}

/* ---------- Şəkil yükləmə ---------- */

function upload_image(array $f): ?string
{
    if (($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($f['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Şəkil yüklənmədi (xəta kodu ' . (int)$f['error'] . ').');
    }
    if ($f['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('Şəkil 5 MB-dan böyük ola bilməz.');
    }
    $info = @getimagesize($f['tmp_name']);
    $types = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'];
    $ext = $info ? ($types[$info[2]] ?? null) : null;
    if (!$ext) {
        throw new RuntimeException('Yalnız JPG, PNG, WEBP və ya GIF şəkillər qəbul olunur.');
    }
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0775, true);
    }
    $name = 'img_' . date('Ymd') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], UPLOAD_DIR . '/' . $name)) {
        throw new RuntimeException('Şəkil serverə yazıla bilmədi.');
    }
    return 'uploads/' . $name;
}

/** Çoxlu fayl input-unu ($_FILES['x'] name[]) ayrı-ayrı fayllara bölür. */
function normalize_files(array $f): array
{
    $out = [];
    if (!isset($f['name']) || !is_array($f['name'])) {
        return $out;
    }
    foreach ($f['name'] as $i => $n) {
        $out[] = ['name' => $n, 'type' => $f['type'][$i], 'tmp_name' => $f['tmp_name'][$i],
                  'error' => $f['error'][$i], 'size' => $f['size'][$i]];
    }
    return $out;
}

function delete_upload(?string $path): void
{
    if ($path && str_starts_with($path, 'uploads/')) {
        $full = UPLOAD_DIR . '/' . basename($path);
        if (is_file($full)) {
            @unlink($full);
        }
    }
}

/* ---------- İkonlar ---------- */

function icon(string $name, int $size = 20): string
{
    $p = [
        'heart' => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1.1L12 21l7.8-7.5 1-1.1a5.5 5.5 0 0 0 0-7.8z"/>',
        'bag' => '<path d="M6 7h12l1 14H5L6 7z"/><path d="M9 7a3 3 0 0 1 6 0"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'truck' => '<path d="M3 6h11v10H3z"/><path d="M14 9h4l3 3v4h-7"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
        'box' => '<path d="M21 8 12 3 3 8v8l9 5 9-5V8z"/><path d="m3 8 9 5 9-5M12 13v8"/>',
        'refresh' => '<path d="M21 12a9 9 0 1 1-3-6.7L21 8"/><path d="M21 3v5h-5"/>',
        'sparkle' => '<path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8z"/>',
        'phone' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/>',
        'insta' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".5"/>',
        'whatsapp' => '<path d="M3 21l1.6-4.7A9 9 0 1 1 8 19.6L3 21z"/><path d="M9 9.5c0 3 2.5 5.5 5.5 5.5l1.5-1.5-2-1-1 1c-1 0-2.5-1.5-2.5-2.5l1-1-1-2L9 9.5z"/>',
        'x' => '<path d="M18 6 6 18M6 6l12 12"/>',
        'trash' => '<path d="M3 6h18M8 6V4h8v2M6 6l1 15h10l1-15"/>',
        'shield' => '<path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6l-8-3z"/>',
        'pin' => '<path d="M12 21s-7-6.5-7-12a7 7 0 0 1 14 0c0 5.5-7 12-7 12z"/><circle cx="12" cy="9" r="2.5"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'filter' => '<path d="M3 5h18M6 12h12M10 19h4"/>',
        'bolt' => '<path d="M13 2 4 14h7l-1 8 9-12h-7l1-8z"/>',
    ];
    return '<svg class="ico" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
        . ($p[$name] ?? '') . '</svg>';
}

/* ---------- Məhsul kartı ---------- */

function mb_strtolower_safe(string $s): string
{
    return function_exists('mb_strtolower') ? mb_strtolower($s, 'UTF-8') : strtolower($s);
}

function product_card(array $p): string
{
    $sale = !empty($p['old_price']) && $p['old_price'] > $p['price'];
    $pct = $sale ? (int)round(100 - $p['price'] / $p['old_price'] * 100) : 0;
    $fav = is_fav((int)$p['id']);
    $catName = '';
    foreach (categories() as $c) {
        if ((int)$c['id'] === (int)$p['category_id']) $catName = $c['name'];
    }
    $alt = (json_decode($p['gallery'] ?? '[]', true) ?: [])[0] ?? '';
    $out = (int)$p['stock'] <= 0;
    ob_start(); ?>
    <article class="card">
        <a class="card-media" href="<?= e(product_url($p)) ?>">
            <img class="img-main" src="<?= e(img_url($p['image'])) ?>" alt="<?= e($p['name']) ?>" loading="lazy" width="600" height="800">
            <?php if ($alt): ?><img class="img-alt" src="<?= e(img_url($alt)) ?>" alt="" loading="lazy" width="600" height="800" aria-hidden="true"><?php endif; ?>
            <span class="card-badges">
                <?php if ($sale): ?><span class="tag tag-sale">-<?= $pct ?>%</span><?php endif; ?>
                <?php if (!empty($p['is_new'])): ?><span class="tag tag-new">Yeni</span><?php endif; ?>
                <?php if ($out): ?><span class="tag tag-out">Stokda yoxdur</span><?php endif; ?>
            </span>
        </a>
        <form method="post" action="<?= url('fav.php') ?>" class="card-fav">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
            <input type="hidden" name="back" value="<?= e(current_uri()) ?>">
            <button type="submit" class="fav-btn<?= $fav ? ' is-active' : '' ?>" aria-label="<?= $fav ? 'Seçilmişlərdən çıxar' : 'Seçilmişlərə əlavə et' ?>"><?= icon('heart', 18) ?></button>
        </form>
        <div class="card-body">
            <a class="card-title" href="<?= e(product_url($p)) ?>"><b><?= e(SITE_NAME) ?></b> <?= e($p['name']) ?></a>
            <?php if ($catName): ?><span class="card-cat"><?= e($catName) ?></span><?php endif; ?>
            <div class="price">
                <?php if ($sale): ?><span class="price-old"><?= money($p['old_price']) ?></span><?php endif; ?>
                <span class="price-now"><?= money($p['price']) ?></span>
            </div>
            <?php if ($p['price'] >= FREE_DELIVERY_FROM): ?><span class="free-tag"><?= icon('truck', 13) ?> Pulsuz çatdırılma</span><?php endif; ?>
            <a class="card-btn" href="<?= e(product_url($p)) ?>"><?= $out ? 'Ətraflı bax' : 'Səbətə əlavə et' ?></a>
        </div>
    </article>
    <?php
    return ob_get_clean();
}
