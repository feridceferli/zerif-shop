<?php
require __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = trim($_POST['email'] ?? '');
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        db()->prepare('INSERT OR IGNORE INTO subscribers (email) VALUES (?)')->execute([strtolower($email)]);
        flash('success', 'Təşəkkürlər! Abunəliyiniz qeydə alındı.');
    } else {
        flash('error', 'E-poçt ünvanı düzgün deyil.');
    }
}
redirect(url());
