<?php
/**
 * Zərif — qadın alt paltarı mağazası
 * Əsas sayt parametrləri. Öz məlumatlarınızla dəyişin.
 */

define('SITE_NAME', 'Zərif');
define('SITE_TAGLINE', 'Qadın alt paltarı butiki');
define('SITE_PHONE', '+994 50 000 00 00');
define('SITE_EMAIL', 'info@zerif.az');
define('SITE_ADDRESS', 'Bakı şəhəri, Nizami küçəsi');
define('WHATSAPP_NUMBER', '994500000000');      // yalnız rəqəmlər
define('INSTAGRAM_URL', 'https://instagram.com/');

define('CURRENCY', '₼');
define('DELIVERY_FEE', 4.00);          // çatdırılma haqqı
define('FREE_DELIVERY_FROM', 80.00);   // bu məbləğdən yuxarı pulsuz
define('PER_PAGE', 12);                // kataloqda bir səhifədə məhsul sayı

define('DB_PATH', getenv('DB_PATH') ?: __DIR__ . '/data/shop.sqlite');
define('UPLOAD_DIR', getenv('UPLOAD_DIR') ?: __DIR__ . '/uploads');

date_default_timezone_set('Asia/Baku');
error_reporting(E_ALL);
ini_set('display_errors', '0');
