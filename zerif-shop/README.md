# Zərif — qadın alt paltarı onlayn mağazası (PHP)

Sadə PHP + SQLite ilə yazılmış tam onlayn mağaza. Framework və ya Composer tələb olunmur.

## İmkanlar

**Mağaza**
- Ana səhifə (hero, kateqoriyalar, bestsellerlər, promo, yeni gələnlər, abunə)
- Kataloq: kateqoriya, ölçü, qiymət aralığı, endirim filtri, axtarış, sıralama, səhifələmə
- Məhsul səhifəsi: qalereya, ölçü/rəng seçimi, ölçü cədvəli, stok, oxşar məhsullar, WhatsApp ilə sifariş
- Səbət: say dəyişmə, promokod, pulsuz çatdırılma göstəricisi
- Sifariş (qeydiyyatsız): ünvan, ödəniş üsulu (nağd / POS / köçürmə), stok yoxlanışı
- Sifarişi izləmə (nömrə + telefon), seçilmişlər, əlaqə forması, info səhifələri
- Mobil uyğun dizayn

**Admin panel** (`/admin`)
- Statistika paneli və 14 günlük satış qrafiki
- Sifarişlər: status dəyişmə (ləğvdə stok avtomatik qayıdır), daxili qeyd, WhatsApp ilə müştəriyə yazmaq
- Məhsullar: əlavə/redaktə/sil, əsas şəkil + qalereya yükləmə, ölçü, rəng, stok, endirim
- Kateqoriyalar, promokodlar, mesajlar, abunəçilər (CSV ixrac), şifrə dəyişmə

**Təhlükəsizlik:** CSRF qoruması, prepared statements, XSS-dən qorunma, `password_hash`, girişdə cəhd limiti,
yüklənən şəkillərin yoxlanılması, `uploads/` qovluğunda PHP icrasının bağlanması.

## Lokal işə salmaq

PHP 8.1+ lazımdır (`pdo_sqlite` genişlənməsi ilə).

```bash
cd zerif-shop
php -S localhost:8000
```

Brauzerdə açın: http://localhost:8000
İlk açılışda baza (`data/shop.sqlite`) və nümunə məhsullar avtomatik yaradılır.

## Admin

- Ünvan: `/admin`
- İstifadəçi: `admin`
- Şifrə: `admin123` — **ilk girişdən sonra Parametrlərdə dəyişin!**

## Hostinqə yükləmək

1. Bütün faylları hostinqin `public_html` (və ya istənilən) qovluğuna yükləyin.
2. `data/` və `uploads/` qovluqlarına yazma icazəsi verin (755 və ya 775).
3. `config.php`-də telefon, WhatsApp, e-poçt, Instagram, çatdırılma qiymətini dəyişin.
4. Hostinqdə PHP 8.1+ və `pdo_sqlite` aktiv olmalıdır (əksər hostinqlərdə standart aktivdir).

## Bazanı sıfırlamaq

`data/shop.sqlite` faylını silin — növbəti açılışda nümunə məlumatlarla yenidən yaradılacaq.

## Şəkillər

Nümunə məhsullarda SVG yer tutucu şəkillər var. Admin paneldən hər məhsula real foto yükləyin
(tövsiyə: 3:4 nisbət, məsələn 900×1200 px, JPG/WEBP).
