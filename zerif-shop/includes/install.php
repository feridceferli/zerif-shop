<?php
/** Verilənlər bazasının strukturu və nümunə məlumatlar (ilk açılışda avtomatik işləyir). */

function install_schema(PDO $db): void
{
    $db->exec("
    CREATE TABLE IF NOT EXISTS admins (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password_hash TEXT NOT NULL,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    );
    CREATE TABLE IF NOT EXISTS categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        description TEXT DEFAULT '',
        image TEXT DEFAULT '',
        sort_order INTEGER DEFAULT 0
    );
    CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        category_id INTEGER REFERENCES categories(id) ON DELETE SET NULL,
        name TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        description TEXT DEFAULT '',
        material TEXT DEFAULT '',
        price REAL NOT NULL DEFAULT 0,
        old_price REAL DEFAULT NULL,
        sizes TEXT DEFAULT '',
        colors TEXT DEFAULT '',
        stock INTEGER NOT NULL DEFAULT 0,
        image TEXT DEFAULT '',
        gallery TEXT DEFAULT '[]',
        is_featured INTEGER DEFAULT 0,
        is_new INTEGER DEFAULT 0,
        is_active INTEGER DEFAULT 1,
        sold_count INTEGER DEFAULT 0,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_products_cat ON products(category_id);
    CREATE TABLE IF NOT EXISTS coupons (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        code TEXT NOT NULL UNIQUE,
        type TEXT NOT NULL DEFAULT 'percent',
        value REAL NOT NULL DEFAULT 0,
        min_total REAL NOT NULL DEFAULT 0,
        expires_at TEXT DEFAULT NULL,
        is_active INTEGER DEFAULT 1
    );
    CREATE TABLE IF NOT EXISTS orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_no TEXT NOT NULL UNIQUE,
        customer_name TEXT NOT NULL,
        phone TEXT NOT NULL,
        email TEXT DEFAULT '',
        city TEXT DEFAULT '',
        address TEXT DEFAULT '',
        note TEXT DEFAULT '',
        payment_method TEXT DEFAULT 'cash',
        subtotal REAL DEFAULT 0,
        discount REAL DEFAULT 0,
        delivery_fee REAL DEFAULT 0,
        total REAL DEFAULT 0,
        coupon_code TEXT DEFAULT '',
        status TEXT DEFAULT 'new',
        admin_note TEXT DEFAULT '',
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    );
    CREATE TABLE IF NOT EXISTS order_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_id INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
        product_id INTEGER,
        name TEXT NOT NULL,
        price REAL NOT NULL,
        size TEXT DEFAULT '',
        color TEXT DEFAULT '',
        qty INTEGER NOT NULL DEFAULT 1
    );
    CREATE TABLE IF NOT EXISTS messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        phone TEXT DEFAULT '',
        email TEXT DEFAULT '',
        message TEXT NOT NULL,
        is_read INTEGER DEFAULT 0,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    );
    CREATE TABLE IF NOT EXISTS subscribers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT NOT NULL UNIQUE,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    );
    ");
}

/** Pastel qradiyentli SVG şəkil yaradır (real şəkillər əlavə olunana qədər). */
function make_placeholder(string $file, string $title, string $c1, string $c2, string $shape = 'circle'): string
{
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0775, true);
    }
    $t = htmlspecialchars($title, ENT_XML1, 'UTF-8');
    $deco = $shape === 'arch'
        ? '<path d="M150 560 V330 a150 150 0 0 1 300 0 V560 Z" fill="#fff" fill-opacity=".16"/>'
        : '<circle cx="300" cy="360" r="160" fill="#fff" fill-opacity=".16"/>';
    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 800" width="600" height="800">
<defs>
<linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="$c1"/><stop offset="1" stop-color="$c2"/></linearGradient>
<pattern id="lace" width="48" height="48" patternUnits="userSpaceOnUse">
<circle cx="24" cy="24" r="15" fill="none" stroke="#fff" stroke-opacity=".16" stroke-width="1.4"/>
<circle cx="24" cy="24" r="5" fill="none" stroke="#fff" stroke-opacity=".14"/>
<circle cx="0" cy="0" r="7" fill="#fff" fill-opacity=".08"/><circle cx="48" cy="48" r="7" fill="#fff" fill-opacity=".08"/>
</pattern>
</defs>
<rect width="600" height="800" fill="url(#g)"/>
<rect width="600" height="800" fill="url(#lace)"/>
$deco
<text x="300" y="380" text-anchor="middle" font-family="Georgia, 'Times New Roman', serif" font-size="78" font-style="italic" fill="#fff">Zərif</text>
<line x1="230" y1="420" x2="370" y2="420" stroke="#fff" stroke-opacity=".7"/>
<text x="300" y="680" text-anchor="middle" font-family="Georgia, serif" font-size="30" fill="#fff">$t</text>
</svg>
SVG;
    file_put_contents(UPLOAD_DIR . '/' . $file, $svg);
    return 'uploads/' . $file;
}

/** uploads/demo/<ad>.jpg mövcuddursa, onun yolunu qaytarır. */
function demo_image(string $name): ?string
{
    return ($name !== '' && is_file(UPLOAD_DIR . "/demo/$name.jpg")) ? "uploads/demo/$name.jpg" : null;
}

function seed_data(PDO $db): void
{
    $db->beginTransaction();

    $db->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?)')
       ->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT)]);

    $cats = [
        ['Büstqalterlər', 'bustqalterler', 'Push-up, balkonet və yumşaq bralette modelləri', '#d9a5ad', '#a8566d'],
        ['Trusiklər', 'trusikler', 'Slip, tanga və yüksək belli modellər', '#e8c4b8', '#b9787a'],
        ['Dəstlər', 'destler', 'Uyğunlaşdırılmış dantel dəstlər', '#b46a7e', '#5e1f38'],
        ['Gecə geyimləri', 'gece-geyimleri', 'İpək və atlas gecə köynəkləri, pijamalar', '#c9b4d4', '#7d5c8f'],
        ['Bodi', 'bodi', 'Zərif dantelli bodilər', '#3d2b33', '#8c2f50'],
        ['Ev geyimi', 'ev-geyimi', 'Rahat xalat və ev dəstləri', '#e9d8c4', '#b8956b'],
    ];
    $catIds = [];
    $st = $db->prepare('INSERT INTO categories (name, slug, description, image, sort_order) VALUES (?,?,?,?,?)');
    foreach ($cats as $i => $c) {
        $catImg = ['destler' => 'cat-destler', 'bustqalterler' => 'cat-bustqalterler', 'trusikler' => 'cat-trusikler',
                   'bodi' => 'cat-bodi', 'gece-geyimleri' => 'cat-gece', 'ev-geyimi' => 'cat-ev'][$c[1]];
        $img = demo_image($catImg) ?? make_placeholder('cat-' . $c[1] . '.svg', $c[0], $c[3], $c[4], 'arch');
        $st->execute([$c[0], $c[1], $c[2], $img, $i]);
        $catIds[$c[1]] = (int)$db->lastInsertId();
    }

    $braSizes = '70B,75A,75B,75C,80B,80C,85C';
    $std = 'XS,S,M,L,XL';
    $products = [
        // cat, name, price, old, sizes, colors, material, featured, new, c1, c2
        ['bustqalterler', 'Dantelli push-up büstqalter', 45, 59, $braSizes, 'Qara,Pudra,Bordo', '85% poliamid, 15% elastan', 1, 0, '#e3b3bb', '#9c4a62'],
        ['bustqalterler', 'Yumşaq bralette «Bella»', 32, null, $std, 'Ağ,Bej,Qara', 'Pambıq və dantel', 0, 1, '#f1d6cf', '#c48b88'],
        ['bustqalterler', 'Balkonet büstqalter «Rose»', 49, null, $braSizes, 'Qırmızı,Qara', 'Fransız danteli', 1, 0, '#d97c8a', '#7a2135'],
        ['trusikler', 'Dantelli slip — 3-lü dəst', 25, 32, $std, 'Qarışıq', '90% pambıq, 10% elastan', 0, 0, '#f0cfc6', '#b97c78'],
        ['trusikler', 'Yüksək belli trusik', 18, null, $std . ',XXL', 'Bej,Qara', 'Mikrofibra', 0, 1, '#ead8c8', '#b69676'],
        ['trusikler', 'İpək tanga «Silk»', 15, null, $std, 'Pudra,Şampan,Qara', '100% ipək', 0, 0, '#f3e1d6', '#c7a07f'],
        ['destler', 'Dantel dəst «Bordo gecə»', 79, 99, 'S,M,L', 'Bordo', 'Fransız danteli, tül', 1, 1, '#a8445f', '#4a1326'],
        ['destler', 'İpək dəst «Pudra»', 89, null, 'S,M,L,XL', 'Pudra,Ağ', 'İpək və dantel', 1, 0, '#efc9c9', '#b67d86'],
        ['gece-geyimleri', 'Atlas gecə köynəyi', 65, null, $std, 'Şampan,Qara,Zümrüd', 'Atlas (satin)', 1, 1, '#d8c4e0', '#7b5b8e'],
        ['gece-geyimleri', 'İpək pijama dəsti', 95, 120, $std, 'Pudra,Mavi', '100% təbii ipək', 0, 0, '#c7d3e6', '#6a7fa6'],
        ['bodi', 'Dantelli bodi «Noir»', 69, null, 'S,M,L', 'Qara,Bordo', 'Elastik dantel', 1, 1, '#4b3540', '#8c2f50'],
        ['ev-geyimi', 'Kimono xalat «Sakura»', 70, 85, 'S/M,L/XL', 'Pudra,Ağ', 'Viskoz atlas', 0, 1, '#f2d9d3', '#c99a8c'],
        // əlavə elanlar
        ['bustqalterler', 'Pambıq bralette «Soft»', 22, 29, $std, 'Qara,Ağ,Bej', '95% pambıq', 1, 0, '#eee', '#ccc'],
        ['bustqalterler', 'İdman büstqalteri «Active»', 28, null, $std, 'Ağ,Qara,Boz', 'Nəfəs alan mikrofibra', 0, 1, '#eee', '#ccc'],
        ['bustqalterler', 'Ağ dantel büstqalter', 39, 49, $braSizes, 'Ağ,Krem', 'Dantel, yumşaq kasa', 1, 0, '#eee', '#ccc'],
        ['bustqalterler', 'Tikişsiz T-shirt büstqalter', 34, null, $braSizes, 'Bej,Qara,Ağ', 'Tikişsiz mikrofibra', 0, 0, '#eee', '#ccc'],
        ['bustqalterler', 'Qara balkonet «Elegance»', 52, 65, $braSizes, 'Qara', 'Fransız danteli', 1, 1, '#eee', '#ccc'],
        ['bustqalterler', 'Push-up büstqalter «Noir»', 47, null, $braSizes, 'Qara,Bordo', 'Atlas və dantel', 0, 1, '#eee', '#ccc'],
        ['destler', 'Dantel dəst «Midnight»', 74, 92, 'S,M,L', 'Qara', 'Dantel və tül', 1, 0, '#eee', '#ccc'],
        ['destler', 'Hədiyyə qutusunda dəst', 85, null, 'S,M,L', 'Qırmızı,Qara', 'Dantel, hədiyyə qutusu', 0, 1, '#eee', '#ccc'],
        ['gece-geyimleri', 'Zeytun atlas xalat dəsti', 79, 99, $std, 'Zeytun,Şampan', 'Atlas (satin)', 0, 1, '#eee', '#ccc'],
        ['gece-geyimleri', 'Yasəmən atlas gecə köynəyi', 58, null, $std, 'Yasəmən,Pudra', 'Atlas, dantel kənar', 1, 0, '#eee', '#ccc'],
        ['gece-geyimleri', 'Uzun qırmızı ipək köynək', 99, 129, $std, 'Qırmızı,Qara', '100% təbii ipək', 1, 1, '#eee', '#ccc'],
        ['gece-geyimleri', 'Çəhrayı ipək pijama', 89, null, $std, 'Çəhrayı', 'İpək', 0, 0, '#eee', '#ccc'],
        ['gece-geyimleri', 'Pudra pijama dəsti «Rose»', 75, 89, $std, 'Pudra,Ağ', 'Atlas', 0, 1, '#eee', '#ccc'],
        ['ev-geyimi', 'Şampan atlas xalat', 69, null, 'S/M,L/XL', 'Şampan', 'Viskoz atlas', 1, 0, '#eee', '#ccc'],
        ['ev-geyimi', 'Ağ pambıq xalat', 55, 69, 'S/M,L/XL', 'Ağ', 'Pambıq', 0, 0, '#eee', '#ccc'],
        ['ev-geyimi', 'Qara ipək xalat', 82, null, 'S/M,L/XL', 'Qara', 'İpək', 0, 1, '#eee', '#ccc'],
    ];
    $desc = "Gündəlik və xüsusi anlar üçün yaradılmış zərif model. Dəriyə xoş təsir edən yumşaq material, "
          . "bədənə mükəmməl oturan forma və diqqətlə işlənmiş detallar. Gizli qablaşdırma ilə çatdırılır.";

    $st = $db->prepare('INSERT INTO products
        (category_id, name, slug, description, material, price, old_price, sizes, colors, stock, image, gallery, is_featured, is_new, created_at)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    // Mock məhsullar üçün demo fotolar (uploads/demo, Unsplash pulsuz lisenziya)
    $photos = ['pushup', 'bralette', 'rose', 'slip3', 'highwaist', 'silk-tanga', 'bordo', 'pudra', 'satin', 'pajama', 'noir', 'kimono',
               'x-soft', 'x-active', 'x-white-lace', 'x-tshirt', 'x-elegance', 'x-noir-bra', 'x-lace-set', 'x-gift-set',
               'x-olive', 'x-lilac', 'x-red-long', 'x-pink-pj', 'x-rose-pj', 'x-champ-robe', 'x-white-robe', 'x-black-robe'];
    foreach ($products as $i => $p) {
        $slug = slugify($p[1]);
        $key = $photos[$i] ?? '';
        $img = demo_image($key) ?? make_placeholder("p-$slug.svg", $p[1], $p[9], $p[10]);
        $g2 = demo_image($key . '-2');
        $created = date('Y-m-d H:i:s', time() - (count($products) - $i) * 3600);
        $st->execute([
            $catIds[$p[0]], $p[1], $slug, $desc, $p[6], $p[2], $p[3], $p[4], $p[5],
            random_int(5, 30), $img, json_encode($g2 ? [$g2] : []), $p[7], $p[8], $created,
        ]);
    }

    $db->exec("INSERT INTO coupons (code, type, value, min_total) VALUES ('XOS10', 'percent', 10, 30)");
    $db->commit();
}
