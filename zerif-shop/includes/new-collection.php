<?php
/** 25 yeni məhsul: mövcud və yeni bazalarda təkrarsız əlavə olunur. */
function add_new_collection(PDO $db): void
{
    require_once __DIR__ . '/install.php';
    $products = [
        ["bustqalterler", "Dantel bralette Luna", "dantel-bralette-luna", 29, 39, "S,M,L,XL", "Qara,Bej", "x-soft", "#e8b8c5", "#8e4965"],
        ["bustqalterler", "Atlas push-up Aurora", "atlas-push-up-aurora", 48, 62, "75B,75C,80B,80C", "Pudra,Qara", "pushup", "#e8b8c5", "#8e4965"],
        ["bustqalterler", "Minimalist gündəlik büstqalter", "minimalist-gundelik-bustqalter", 35, 45, "70B,75B,80B,85C", "Ağ,Bej", "x-tshirt", "#e8b8c5", "#8e4965"],
        ["bustqalterler", "Zərif krujeva balkonet", "zerif-krujeva-balkonet", 54, 69, "75B,75C,80C", "Bordo,Qara", "x-elegance", "#e8b8c5", "#8e4965"],
        ["trusikler", "Yumşaq pambıq slip dəsti", "yumsaq-pambiq-slip-desti", 24, 32, "S,M,L,XL", "Ağ,Bej,Qara", "slip3", "#e9d0c6", "#b57880"],
        ["trusikler", "Tikişsiz yüksək belli model", "tikissiz-yuksek-belli-model", 21, 29, "S,M,L,XL,XXL", "Bej,Qara", "highwaist", "#e9d0c6", "#b57880"],
        ["trusikler", "Dantel tanga Flora", "dantel-tanga-flora", 17, 24, "XS,S,M,L", "Pudra,Qara", "silk-tanga", "#e9d0c6", "#b57880"],
        ["trusikler", "Rahat gündəlik slip", "rahat-gundelik-slip", 14, 20, "S,M,L,XL", "Ağ,Bej", "slip3", "#e9d0c6", "#b57880"],
        ["destler", "Dantel dəst Velvet", "dantel-dest-velvet", 88, 112, "S,M,L", "Bordo,Qara", "bordo", "#a65a76", "#51263c"],
        ["destler", "Romantik dəst Lily", "romantik-dest-lily", 72, 95, "S,M,L,XL", "Pudra,Ağ", "pudra", "#a65a76", "#51263c"],
        ["destler", "Klassik qara dəst", "klassik-qara-dest", 79, 99, "S,M,L", "Qara", "x-lace-set", "#a65a76", "#51263c"],
        ["destler", "Atlas dəst Pearl", "atlas-dest-pearl", 93, 119, "S,M,L,XL", "Şampan,Bej", "x-gift-set", "#a65a76", "#51263c"],
        ["gece-geyimleri", "Saten gecə köynəyi Mira", "saten-gece-koyneyi-mira", 62, 79, "S,M,L,XL", "Pudra,Qara", "satin", "#d6c3df", "#83608e"],
        ["gece-geyimleri", "İpək pijama Cloud", "i-pek-pijama-cloud", 109, 139, "S,M,L,XL", "Mavi,Ağ", "pajama", "#d6c3df", "#83608e"],
        ["gece-geyimleri", "Dantel gecə köynəyi Rose", "dantel-gece-koyneyi-rose", 68, 89, "S,M,L", "Bordo,Qara", "x-red-long", "#d6c3df", "#83608e"],
        ["gece-geyimleri", "Atlas pijama Bloom", "atlas-pijama-bloom", 84, 109, "S,M,L,XL", "Çəhrayı,Pudra", "x-pink-pj", "#d6c3df", "#83608e"],
        ["gece-geyimleri", "Uzun saten gecəlik", "uzun-saten-gecelik", 91, 119, "S,M,L,XL", "Şampan,Qara", "x-lilac", "#d6c3df", "#83608e"],
        ["bodi", "Dantel bodi Bella", "dantel-bodi-bella", 73, 94, "S,M,L", "Qara,Bordo", "noir", "#725065", "#30222d"],
        ["bodi", "Tikişsiz bodi Shape", "tikissiz-bodi-shape", 58, 75, "S,M,L,XL", "Bej,Qara", "noir", "#725065", "#30222d"],
        ["bodi", "Romantik bodi Pearl", "romantik-bodi-pearl", 81, 105, "S,M,L", "Ağ,Pudra", "noir", "#725065", "#30222d"],
        ["ev-geyimi", "Kimono xalat Blossom", "kimono-xalat-blossom", 76, 99, "S/M,L/XL", "Pudra,Ağ", "kimono", "#e6d2be", "#a87b6d"],
        ["ev-geyimi", "Atlas ev dəsti Cozy", "atlas-ev-desti-cozy", 83, 109, "S,M,L,XL", "Şampan,Qara", "x-champ-robe", "#e6d2be", "#a87b6d"],
        ["ev-geyimi", "Pambıq ev xalatı Soft", "pambiq-ev-xalati-soft", 59, 79, "S/M,L/XL", "Ağ,Bej", "x-white-robe", "#e6d2be", "#a87b6d"],
        ["ev-geyimi", "İpək xalat Midnight", "i-pek-xalat-midnight", 96, 125, "S/M,L/XL", "Qara,Bordo", "x-black-robe", "#e6d2be", "#a87b6d"],
        ["ev-geyimi", "Yüngül kimono Sakura", "yungul-kimono-sakura", 71, 92, "S/M,L/XL", "Çəhrayı,Pudra", "kimono", "#e6d2be", "#a87b6d"],
    ];
    $categories = $db->query('SELECT id, slug FROM categories')->fetchAll(PDO::FETCH_KEY_PAIR);
    $check = $db->prepare('SELECT 1 FROM products WHERE slug = ? LIMIT 1');
    $insert = $db->prepare('INSERT INTO products (category_id, name, slug, description, material, price, old_price, sizes, colors, stock, image, gallery, is_featured, is_new, is_active, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    foreach ($products as $i => $p) {
        [$cat, $name, $slug, $price, $old, $sizes, $colors, $photo, $c1, $c2] = $p;
        if (!isset($categories[$cat])) continue;
        $check->execute([$slug]);
        if ($check->fetchColumn()) continue;
        $image = demo_image($photo) ?? make_placeholder('new-' . $slug . '.svg', $name, $c1, $c2);
        $insert->execute([$categories[$cat], $name, $slug,
            'Yeni kolleksiyadan zərif model. Rahat kəsim və gündəlik istifadə üçün uyğun materiallar.',
            'Yumşaq qarışıq parça', $price, $old, $sizes, $colors, 8 + ($i * 7 % 25),
            $image, '[]', $i % 3 === 0 ? 1 : 0, 1, 1, date('Y-m-d H:i:s')]);
    }
}
