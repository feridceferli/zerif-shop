<?php
require __DIR__ . '/includes/functions.php';

$pages = [
    'delivery' => ['Çatdırılma və ödəniş', '
        <h3>Çatdırılma</h3>
        <ul>
            <li><strong>Bakı, Sumqayıt, Xırdalan</strong> — 24 saat ərzində kuryer ilə.</li>
            <li><strong>Regionlar</strong> — 2–3 iş günü ərzində poçt və ya kuryer xidməti ilə.</li>
            <li>Çatdırılma haqqı: <strong>' . money(DELIVERY_FEE) . '</strong>. ' . money(FREE_DELIVERY_FROM) . '-dan yuxarı sifarişlərdə <strong>pulsuz</strong>.</li>
            <li>Bütün bağlamalar <strong>gizli, adsız qablaşdırmada</strong> göndərilir — üzərində mağaza və ya məhsul adı yazılmır.</li>
        </ul>
        <h3>Ödəniş</h3>
        <ul>
            <li>Qapıda nağd ödəniş</li>
            <li>Qapıda kartla ödəniş (kuryerdə POS terminal)</li>
            <li>Kart köçürməsi — operator sizinlə əlaqə saxlayıb rekvizitləri göndərəcək</li>
        </ul>'],
    'returns' => ['Qaytarma və dəyişmə', '
        <p>Məhsul ölçüyə uyğun gəlmədikdə onu <strong>14 gün</strong> ərzində dəyişə bilərsiniz.</p>
        <ul>
            <li>Gigiyena qaydalarına əsasən alt paltarı yalnız <strong>istifadə olunmamış</strong>, etiketləri üzərində və orijinal qablaşdırmada olduqda qəbul edilir.</li>
            <li>Zavod qüsuru aşkar edildikdə məhsul pulsuz dəyişdirilir və ya pul geri qaytarılır.</li>
            <li>Dəyişmə üçün bizə telefon və ya WhatsApp ilə müraciət edin: ' . e(SITE_PHONE) . '</li>
        </ul>'],
    'privacy' => ['Məxfilik siyasəti', '
        <p>Sifariş zamanı təqdim etdiyiniz ad, telefon və ünvan yalnız sifarişin çatdırılması üçün istifadə olunur və üçüncü şəxslərə ötürülmür.</p>
        <p>Saytda yalnız səbət və seçilmişləri yadda saxlamaq üçün zəruri sessiya kukisi istifadə olunur.</p>
        <p>Məlumatlarınızın silinməsini istəyirsinizsə, ' . e(SITE_EMAIL) . ' ünvanına yazın.</p>'],
    'sizes' => ['Ölçü cədvəli', null],
    'about' => ['Haqqımızda', '
        <p><strong>' . e(SITE_NAME) . '</strong> — hər qadının özünü gözəl və rahat hiss etməsi üçün yaradılmış alt paltarı butikidir.</p>
        <p>Kolleksiyalarımızı keyfiyyətli dantel, təbii ipək və yumşaq pambıqdan seçirik. Hər məhsul əl ilə yoxlanılır və diqqətlə qablaşdırılır.</p>'],
];
$key = $_GET['p'] ?? 'about';
if (!isset($pages[$key])) $key = 'about';
[$title, $html] = $pages[$key];

$pageTitle = $title;
require __DIR__ . '/includes/header.php';
?>
<section class="page-head">
    <div class="container"><nav class="crumbs"><a href="<?= url() ?>">Ana səhifə</a> / <span><?= e($title) ?></span></nav><h1><?= e($title) ?></h1></div>
</section>
<section class="container narrow prose">
    <?php if ($html === null) { include __DIR__ . '/includes/size-table.php'; } else { echo $html; } ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
