<?php
require __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(url('favorites.php'));
}
csrf_check();
$id = (int)($_POST['id'] ?? 0);
if ($id > 0) {
    $added = fav_toggle($id);
    flash('success', $added ? 'Seçilmişlərə əlavə olundu.' : 'Seçilmişlərdən çıxarıldı.');
}
redirect(safe_back($_POST['back'] ?? null, url('favorites.php')));
