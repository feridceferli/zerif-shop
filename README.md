# Zərif Shop — saytın yayımlanması

Bu repodakı `zerif-shop/` qovluğu tam PHP 8.1+ / SQLite mağaza layihəsidir. Kataloq, məhsul detalları, səbət, sifariş, status izləmə, promo-kod və admin paneli bu qovluqdadır.

## Lokal işə salma

```bash
cd zerif-shop
php -S localhost:8000
```

PHP-də `pdo_sqlite` genişlənməsi aktiv olmalıdır. Sayt: http://localhost:8000/ ; admin: http://localhost:8000/admin/ .

## Render-də yayımlama

1. GitHub-a bu dəyişiklikləri `main` budağına birləşdirin.
2. Render-də **New → Blueprint** seçin və `feridceferli/zerif-shop` repozitoriyasını göstərin.
3. `render.yaml` faylına əsaslanan xidmətin yaradılmasını təsdiqləyin.
4. Yayımlama bitəndə Render-in verdiyi `onrender.com` domenini açın.

Dockerfile Apache/PHP 8.3 və `pdo_sqlite` qurur, `zerif-shop/` məzmununu saytın kökünə köçürür. Buna görə domenin kökü mağazanı açır.

**Məlumat saxlanması:** `render.yaml` bir persistent disk yaradır və SQLite məlumat bazasını və yüklənən məhsul şəkillərini birlikdə `/var/lib/zerif-data` persistent diskində saxlayır. Render-də persistent disk ödənişli plan tələb edə bilər. Tətbiq ilk açılışda nümunə şəkilləri diskə köçürür; sonrakı yayımlamalarda məlumatlar qorunur.

**Admin təhlükəsizliyi:** layihənin ilkin giriş məlumatları nümunə məqsədlidir: `admin` / `admin123`. Saytı yayımlayan kimi **Admin → Parametrlər** bölməsindən şifrəni dəyişin. Bu standart şifrə ilə mağazanı ictimaiyyətə açmayın.

**Mağaza parametrləri:** `zerif-shop/config.php` faylında telefon, WhatsApp, e-poçt, Instagram və çatdırılma dəyərlərini real məlumatlarla əvəz edin. Ödəniş növləri sifariş məlumatını toplayır, onlayn kart ödənişi inteqrasiyası yoxdur.

**Qeyd:** GitHub Pages statik hostinqdir, PHP/SQLite işlətmir; Render, PHP dəstəkləyən VPS və ya PHP hostinq istifadə edin.
