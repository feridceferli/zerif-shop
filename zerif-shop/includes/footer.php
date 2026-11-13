</main>
<section class="trust">
    <div class="container trust-grid">
        <div><?= icon('truck', 26) ?><div><strong>Sürətli çatdırılma</strong><span>Bakıda 24 saat ərzində</span></div></div>
        <div><?= icon('box', 26) ?><div><strong>Gizli qablaşdırma</strong><span>Bağlamada ad yazılmır</span></div></div>
        <div><?= icon('refresh', 26) ?><div><strong>14 gün dəyişmə</strong><span>Ölçü uyğun gəlmədisə</span></div></div>
        <div><?= icon('shield', 26) ?><div><strong>Təhlükəsiz ödəniş</strong><span>Qapıda nağd və ya kartla</span></div></div>
    </div>
</section>
<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <a class="logo" href="<?= url() ?>"><?= e(mb_strtolower_safe(SITE_NAME)) ?><span>.az</span></a>
            <p class="muted">Qadın alt paltarı, dəstlər və gecə geyimləri onlayn mağazası.</p>
            <div class="socials">
                <a href="<?= e(INSTAGRAM_URL) ?>" target="_blank" rel="noopener" aria-label="Instagram"><?= icon('insta', 18) ?></a>
                <a href="https://wa.me/<?= e(WHATSAPP_NUMBER) ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><?= icon('whatsapp', 18) ?></a>
                <a href="mailto:<?= e(SITE_EMAIL) ?>" aria-label="E-poçt"><?= icon('mail', 18) ?></a>
            </div>
        </div>
        <div>
            <h4>Kateqoriyalar</h4>
            <?php foreach (categories() as $c): ?>
                <a href="<?= url('shop.php?cat=' . urlencode($c['slug'])) ?>"><?= e($c['name']) ?></a>
            <?php endforeach; ?>
        </div>
        <div>
            <h4>Kömək</h4>
            <a href="<?= url('info.php?p=delivery') ?>">Çatdırılma və ödəniş</a>
            <a href="<?= url('info.php?p=returns') ?>">Qaytarma və dəyişmə</a>
            <a href="<?= url('info.php?p=sizes') ?>">Ölçü cədvəli</a>
            <a href="<?= url('track.php') ?>">Sifarişi izlə</a>
            <a href="<?= url('info.php?p=privacy') ?>">Məxfilik siyasəti</a>
        </div>
        <div>
            <h4>Əlaqə</h4>
            <a href="tel:<?= e(preg_replace('/\s+/', '', SITE_PHONE)) ?>"><?= icon('phone', 16) ?> <?= e(SITE_PHONE) ?></a>
            <a href="https://wa.me/<?= e(WHATSAPP_NUMBER) ?>" target="_blank" rel="noopener"><?= icon('whatsapp', 16) ?> WhatsApp</a>
            <a href="mailto:<?= e(SITE_EMAIL) ?>"><?= icon('mail', 16) ?> <?= e(SITE_EMAIL) ?></a>
            <span><?= icon('pin', 16) ?> <?= e(SITE_ADDRESS) ?></span>
        </div>
    </div>
    <div class="container footer-bottom">
        <span>© <?= date('Y') ?> <?= e(SITE_NAME) ?>. Bütün hüquqlar qorunur.</span>
        <span>Nağd · Kart · Kuryer</span>
    </div>
</footer>
<a class="wa-float" href="https://wa.me/<?= e(WHATSAPP_NUMBER) ?>" target="_blank" rel="noopener" aria-label="WhatsApp ilə yazın"><?= icon('whatsapp', 26) ?></a>
<script src="<?= url('assets/js/app.js') ?>?v=10"></script>
</body>
</html>
