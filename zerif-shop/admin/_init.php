<?php
require_once __DIR__ . '/../includes/functions.php';

if (basename($_SERVER['SCRIPT_NAME']) !== 'login.php') {
    require_admin();
}

function admin_header(string $title): void
{
    $cur = basename($_SERVER['SCRIPT_NAME']);
    $newOrders = (int)db()->query("SELECT COUNT(*) FROM orders WHERE status = 'new'")->fetchColumn();
    $unread = (int)db()->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetchColumn();
    $nav = [
        'index.php' => ['Panel', 0],
        'orders.php' => ['Sifarişlər', $newOrders],
        'products.php' => ['Məhsullar', 0],
        'categories.php' => ['Kateqoriyalar', 0],
        'coupons.php' => ['Promokodlar', 0],
        'messages.php' => ['Mesajlar', $unread],
        'subscribers.php' => ['Abunəçilər', 0],
        'settings.php' => ['Parametrlər', 0],
    ];
    $active = ['order.php' => 'orders.php', 'product-edit.php' => 'products.php'][$cur] ?? $cur;
    ?><!doctype html>
<html lang="az">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> — Admin · <?= e(SITE_NAME) ?></title>
    <meta name="robots" content="noindex">
    <link rel="icon" href="<?= url('assets/img/favicon.svg') ?>" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;1,500&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('assets/css/admin.css') ?>">
</head>
<body class="admin">
<aside class="sidebar" id="sidebar">
    <a class="brand" href="<?= url('admin/') ?>"><?= e(SITE_NAME) ?> <small>admin</small></a>
    <nav>
        <?php foreach ($nav as $file => [$label, $badge]): ?>
            <a href="<?= url('admin/' . $file) ?>" class="<?= $active === $file ? 'active' : '' ?>"><?= e($label) ?><?php if ($badge): ?><span class="pill"><?= $badge ?></span><?php endif; ?></a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-foot">
        <a href="<?= url() ?>" target="_blank">↗ Sayta bax</a>
        <a href="<?= url('admin/logout.php') ?>">Çıxış</a>
    </div>
</aside>
<div class="main">
    <header class="topbar">
        <button class="burger" type="button" onclick="document.getElementById('sidebar').classList.toggle('open')" aria-label="Menyu">☰</button>
        <h1><?= e($title) ?></h1>
        <span class="who"><?= e($_SESSION['admin_name'] ?? '') ?></span>
    </header>
    <div class="content">
    <?php foreach (flashes() as [$type, $msg]): ?>
        <div class="flash flash-<?= e($type) ?>"><?= e($msg) ?></div>
    <?php endforeach;
}

function admin_footer(): void
{
    ?>
    </div>
</div>
<script>
document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('submit', function (e) { if (!confirm(el.dataset.confirm)) e.preventDefault(); });
});
</script>
</body>
</html>
<?php
}

function status_badge(string $s): string
{
    return '<span class="status status-' . e($s) . '">' . e(status_label($s)) . '</span>';
}
