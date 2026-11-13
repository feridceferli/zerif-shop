<?php
/** @var string|null $pageTitle */
$cats = categories();
$cartCount = cart_count();
$favCount = count(favs());
$fullTitle = !empty($pageTitle) ? $pageTitle . ' — ' . SITE_NAME : SITE_NAME . ' — ' . SITE_TAGLINE;
$activeCat = $_GET['cat'] ?? '';
$script = basename($_SERVER['SCRIPT_NAME']);
?><!doctype html>
<html lang="az">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($fullTitle) ?></title>
    <meta name="description" content="<?= e($pageDesc ?? 'Zərif — qadın alt paltarı, büstqalter, dəst, gecə geyimləri. Bütün Azərbaycan üzrə sürətli çatdırılma, gizli qablaşdırma.') ?>">
    <meta name="theme-color" content="#D6246E">
    <link rel="icon" href="<?= url('assets/img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>?v=10">
</head>
<body>
<a class="skip" href="#main">Məzmuna keç</a>
<div class="topbar">
    <div class="container topbar-inner">
        <span class="topbar-promo"><?= icon('truck', 15) ?> <?= money(FREE_DELIVERY_FROM) ?>-dan yuxarı pulsuz çatdırılma</span>
        <nav class="topbar-links" aria-label="Köməkçi keçidlər">
            <a href="<?= url('track.php') ?>">Sifarişi izlə</a>
            <a href="<?= url('info.php?p=delivery') ?>">Çatdırılma</a>
            <a href="<?= url('contact.php') ?>">Əlaqə</a>
            <a href="tel:<?= e(preg_replace('/\s+/', '', SITE_PHONE)) ?>"><?= e(SITE_PHONE) ?></a>
        </nav>
    </div>
</div>
<header class="site-header" id="siteHeader">
    <div class="container header-row">
        <button class="icon-btn burger" type="button" data-menu-toggle aria-label="Kateqoriyalar" aria-expanded="false" aria-controls="mainNav"><span></span><span></span><span></span></button>
        <a class="logo" href="<?= url() ?>" aria-label="<?= e(SITE_NAME) ?> — ana səhifə"><?= e(mb_strtolower_safe(SITE_NAME)) ?><span>.az</span></a>
        <form class="search" action="<?= url('shop.php') ?>" method="get" role="search">
            <label for="q" class="sr-only">Məhsul axtar</label>
            <input id="q" type="search" name="q" placeholder="Məhsul, kateqoriya axtarın…" value="<?= e($_GET['q'] ?? '') ?>" autocomplete="off">
            <button type="submit" aria-label="Axtar"><?= icon('search', 20) ?></button>
        </form>
        <div class="header-actions">
            <a class="action" href="<?= url('favorites.php') ?>" aria-label="Seçilmişlər (<?= $favCount ?>)">
                <span class="action-ico"><?= icon('heart', 22) ?><?php if ($favCount): ?><span class="count"><?= $favCount ?></span><?php endif; ?></span>
                <span class="action-label">Seçilmişlər</span>
            </a>
            <a class="action" href="<?= url('cart.php') ?>" aria-label="Səbət (<?= $cartCount ?>)">
                <span class="action-ico"><?= icon('bag', 22) ?><?php if ($cartCount): ?><span class="count"><?= $cartCount ?></span><?php endif; ?></span>
                <span class="action-label">Səbətim</span>
            </a>
        </div>
    </div>
    <nav class="cat-nav" id="mainNav" aria-label="Kateqoriyalar">
        <div class="container cat-nav-inner">
            <div class="cat-nav-head"><strong>Kateqoriyalar</strong><button type="button" class="icon-btn" data-menu-close aria-label="Bağla"><?= icon('x') ?></button></div>
            <a href="<?= url('shop.php') ?>" class="<?= ($script === 'shop.php' && !$activeCat && empty($_GET['sale'])) ? 'active' : '' ?>">Hamısı</a>
            <?php foreach ($cats as $c): ?>
                <a href="<?= url('shop.php?cat=' . urlencode($c['slug'])) ?>" class="<?= $activeCat === $c['slug'] ? 'active' : '' ?>"><?= e($c['name']) ?></a>
            <?php endforeach; ?>
            <a href="<?= url('shop.php?sale=1') ?>" class="nav-sale <?= !empty($_GET['sale']) ? 'active' : '' ?>">Endirimlər</a>
            <a href="<?= url('shop.php?sort=new') ?>" class="<?= ($_GET['sort'] ?? '') === 'new' ? 'active' : '' ?>">Yeni gələnlər</a>
        </div>
    </nav>
</header>
<main id="main">
<?php $fl = flashes(); if ($fl): ?>
    <div class="container flashes" role="status" aria-live="polite">
        <?php foreach ($fl as [$type, $msg]): ?>
            <div class="flash flash-<?= e($type) ?>" data-flash><?= e($msg) ?><button type="button" aria-label="Bağla" data-flash-close><?= icon('x', 16) ?></button></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
