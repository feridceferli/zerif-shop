<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Telegram Test</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a1a; color: #0f0; }
        .box { background: #000; border: 1px solid #0f0; padding: 20px; margin: 10px 0; border-radius: 4px; }
        button { background: #0f0; color: #000; border: none; padding: 10px 20px; font-weight: bold; cursor: pointer; }
        button:hover { background: #0a0; }
        pre { background: #111; padding: 10px; overflow-x: auto; }
        .success { color: #0f0; }
        .error { color: #f00; }
    </style>
</head>
<body>

<h1>🔍 TELEGRAM DEBUG TESTI</h1>

<div class="box">
    <h3>📋 Konfigürasyon</h3>
    <pre><?php
define('TELEGRAM_BOT_TOKEN', '8802880944:AAG52H11b0zSYypWUrdEXiLZJpDb84CHo18');
define('TELEGRAM_CHAT_ID', '-1003935725037');
echo "Bot Token: " . TELEGRAM_BOT_TOKEN . "\n";
echo "Chat ID: " . TELEGRAM_CHAT_ID . "\n";
?></pre>
</div>

<?php
if (isset($_POST['test'])) {
    echo '<div class="box">';
    echo '<h3>🚀 Test Sonucu</h3>';
    
    $message = "🧪 <b>TEST MESAJI</b>\n";
    $message .= "━━━━━━━━━━━━━━━━━━━\n";
    $message .= "⏰ Zaman: <b>" . date('Y-m-d H:i:s') . "</b>\n";
    $message .= "📍 IP: <code>" . ($_SERVER['REMOTE_ADDR'] ?? 'localhost') . "</code>\n";
    $message .= "━━━━━━━━━━━━━━━━━━━\n";
    $message .= "✅ PHP Telegram entegrasyonu çalışıyor!";
    
    // DNS bypass: try multiple Telegram IPs if hostname resolution fails
    $telegram_endpoints = [
        'https://api.telegram.org/bot' . TELEGRAM_BOT_TOKEN . '/sendMessage',
        'https://149.154.167.220/bot' . TELEGRAM_BOT_TOKEN . '/sendMessage', // Primary IP
        'https://149.154.167.197/bot' . TELEGRAM_BOT_TOKEN . '/sendMessage', // Fallback IP
    ];
    
    $data = [
        'chat_id' => TELEGRAM_CHAT_ID,
        'text' => $message,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true
    ];
    
    echo '<pre>';
    echo "📤 Gönderilen Data:\n";
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    
    $response = null;
    $httpCode = 0;
    $error = null;
    $used_endpoint = null;
    
    foreach ($telegram_endpoints as $url) {
        echo "🔗 Deneniyor: $url\n";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Host: api.telegram.org' // Force correct Host header for IP endpoints
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verify for IP endpoints
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if (!$error && $httpCode === 200) {
            $used_endpoint = $url;
            echo "✅ Bağlantı başarılı!\n\n";
            break;
        } else {
            echo "❌ Başarısız: " . ($error ?: "HTTP $httpCode") . "\n\n";
        }
    }
    
    if ($used_endpoint) {
        echo "📥 Kullanılan Endpoint: $used_endpoint\n";
        echo "📥 HTTP Code: $httpCode\n\n";
    } else {
        echo '<span class="error">❌ Tüm endpoint\'ler başarısız oldu!</span>' . "\n\n";
        if ($error) {
            echo '<span class="error">Son CURL Hatası: ' . $error . "</span>\n\n";
        }
    }
    
    echo "📨 Telegram Yanıtı:\n";
    $responseData = json_decode($response, true);
    echo json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    
    if ($httpCode === 200 && isset($responseData['ok']) && $responseData['ok']) {
        echo "\n" . '<span class="success">✅ BAŞARILI! Telegram kanalını kontrol et.</span>';
    } else {
        echo "\n" . '<span class="error">❌ BAŞARISIZ! Yukarıdaki hatayı kontrol et.</span>';
    }
    
    echo '</pre>';
    echo '</div>';
}
?>

<div class="box">
    <h3>🧪 Test Et</h3>
    <form method="POST">
        <button type="submit" name="test">Telegram'a Test Mesajı Gönder</button>
    </form>
</div>

<div class="box">
    <h3>📝 Sorun Giderme</h3>
    <ul>
        <li><strong>401 Unauthorized:</strong> Bot token yanlış</li>
        <li><strong>400 Bad Request (chat not found):</strong> Chat ID yanlış</li>
        <li><strong>403 Forbidden:</strong> Bot kanala eklenmemiş veya admin değil</li>
        <li><strong>CURL Error:</strong> Sunucu internet bağlantısı yok veya CURL kapalı</li>
        <li><strong>Timeout:</strong> Telegram API'ye erişilemiyor (firewall/proxy)</li>
    </ul>
</div>

<div class="box">
    <h3>🔧 Kurulum Kontrolü</h3>
    <pre><?php
echo "PHP Version: " . PHP_VERSION . "\n";
echo "CURL Extension: " . (function_exists('curl_version') ? '✅ Yüklü' : '❌ Yüklü değil') . "\n";
echo "allow_url_fopen: " . (ini_get('allow_url_fopen') ? '✅ Açık' : '❌ Kapalı') . "\n";
echo "Server IP: " . ($_SERVER['SERVER_ADDR'] ?? 'N/A') . "\n";
echo "Client IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'N/A') . "\n";
?></pre>
</div>

</body>
</html>
