<?php
/** 60 yeni məhsul: mövcud və yeni bazalarda təkrarsız əlavə olunur. */
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
        ["bustqalterler","Bralette Stella","bralette-stella",31,43,"70B,75B,80B,85C","Qara,Pudra,Bej","x-soft","#e8b8c5","#8e4965"],
        ["bustqalterler","Push-up Opal","push-up-opal",51,66,"70B,75B,80B,85C","Qara,Pudra,Bej","pushup","#e8b8c5","#8e4965"],
        ["bustqalterler","Balkonet Amore","balkonet-amore",57,72,"70B,75B,80B,85C","Qara,Pudra,Bej","rose","#e8b8c5","#8e4965"],
        ["trusikler","Slip Cotton Air","slip-cotton-air",19,27,"S,M,L,XL","Qara,Pudra,Bej","slip3","#e9d0c6","#b57880"],
        ["trusikler","Tanga Satin Kiss","tanga-satin-kiss",22,30,"S,M,L,XL","Qara,Pudra,Bej","silk-tanga","#e9d0c6","#b57880"],
        ["trusikler","Yüksək bel Comfort","yuksek-bel-comfort",26,35,"S,M,L,XL","Qara,Pudra,Bej","highwaist","#e9d0c6","#b57880"],
        ["destler","Dantel dəst Ruby","dantel-dest-ruby",98,126,"S,M,L,XL","Qara,Pudra,Bej","bordo","#a65a76","#51263c"],
        ["destler","İpək dəst Dream","i-pek-dest-dream",104,132,"S,M,L,XL","Qara,Pudra,Bej","pudra","#a65a76","#51263c"],
        ["destler","Qara dəst Moon","qara-dest-moon",87,113,"S,M,L,XL","Qara,Pudra,Bej","x-lace-set","#a65a76","#51263c"],
        ["destler","Zərif dəst Ivory","zerif-dest-ivory",94,121,"S,M,L,XL","Qara,Pudra,Bej","x-gift-set","#a65a76","#51263c"],
        ["gece-geyimleri","Gecəlik Lavender","gecelik-lavender",67,86,"S,M,L,XL","Qara,Pudra,Bej","x-lilac","#d6c3df","#83608e"],
        ["gece-geyimleri","Pijama Silk Touch","pijama-silk-touch",112,145,"S,M,L,XL","Qara,Pudra,Bej","pajama","#d6c3df","#83608e"],
        ["gece-geyimleri","Atlas gecəlik Pearl","atlas-gecelik-pearl",77,101,"S,M,L,XL","Qara,Pudra,Bej","satin","#d6c3df","#83608e"],
        ["gece-geyimleri","Pijama Pink Cloud","pijama-pink-cloud",86,108,"S,M,L,XL","Qara,Pudra,Bej","x-pink-pj","#d6c3df","#83608e"],
        ["bodi","Bodi Nightfall","bodi-nightfall",89,115,"S,M,L,XL","Qara,Pudra,Bej","noir","#725065","#30222d"],
        ["bodi","Bodi Classic Lace","bodi-classic-lace",78,99,"S,M,L,XL","Qara,Pudra,Bej","noir","#725065","#30222d"],
        ["ev-geyimi","Kimono Morning Rose","kimono-morning-rose",82,107,"S,M,L,XL","Qara,Pudra,Bej","kimono","#e6d2be","#a87b6d"],
        ["ev-geyimi","Xalat Soft Cotton","xalat-soft-cotton",64,82,"S,M,L,XL","Qara,Pudra,Bej","x-white-robe","#e6d2be","#a87b6d"],
        ["ev-geyimi","Atlas xalat Golden","atlas-xalat-golden",91,118,"S,M,L,XL","Qara,Pudra,Bej","x-champ-robe","#e6d2be","#a87b6d"],
        ["ev-geyimi","İpək xalat Noir","i-pek-xalat-noir",115,149,"S,M,L,XL","Qara,Pudra,Bej","x-black-robe","#e6d2be","#a87b6d"],
        ["destler","Güllü naxışlı dantel dəst","gullu-naxisli-dantel-dest",69,89,"S,M,L,XL","Qara,Bordo,Pudra","bordo","#a65a76","#51263c"],
        ["destler","Tül detallı qara dəst","tul-detalli-qara-dest",82,105,"S,M,L,XL","Qara,Bordo,Pudra","x-lace-set","#a65a76","#51263c"],
        ["destler","Çiçək tikməli romantik dəst","cicek-tikmeli-romantik-dest",74,96,"S,M,L,XL","Qara,Bordo,Pudra","pudra","#a65a76","#51263c"],
        ["destler","Bordo krujeva premium dəst","bordo-krujeva-premium-dest",97,124,"S,M,L,XL","Qara,Bordo,Pudra","bordo","#a65a76","#51263c"],
        ["bustqalterler","Çiçək naxışlı push-up","cicek-naxisli-push-up",47,61,"75B,75C,80B,80C","Qara,Bordo,Pudra","pushup","#e8b8c5","#8e4965"],
        ["bustqalterler","Tül balkonet zərif model","tul-balkonet-zerif-model",52,68,"75B,75C,80B,80C","Qara,Bordo,Pudra","x-elegance","#e8b8c5","#8e4965"],
        ["bustqalterler","Yumşaq krujeva bralette","yumsaq-krujeva-bralette",33,45,"75B,75C,80B,80C","Qara,Bordo,Pudra","x-soft","#e8b8c5","#8e4965"],
        ["trusikler","Çiçək tikməli tanga","cicek-tikmeli-tanga",19,27,"S,M,L,XL","Qara,Bordo,Pudra","silk-tanga","#e9d0c6","#b57880"],
        ["trusikler","Tül detallı slip","tul-detalli-slip",23,31,"S,M,L,XL","Qara,Bordo,Pudra","slip3","#e9d0c6","#b57880"],
        ["bodi","Çiçək naxışlı tül bodi","cicek-naxisli-tul-bodi",79,103,"S,M,L,XL","Qara,Bordo,Pudra","noir","#725065","#30222d"],
        ["bodi","Qara dantel bodi","qara-dantel-bodi",85,112,"S,M,L,XL","Qara,Bordo,Pudra","noir","#725065","#30222d"],
        ["gece-geyimleri","Saten babydoll Rose","saten-babydoll-rose",63,82,"S,M,L,XL","Qara,Bordo,Pudra","satin","#d6c3df","#83608e"],
        ["gece-geyimleri","Tül gecəlik Midnight","tul-gecelik-midnight",72,93,"S,M,L,XL","Qara,Bordo,Pudra","x-red-long","#d6c3df","#83608e"],
        ["gece-geyimleri","İpək gecəlik Champagne","i-pek-gecelik-champagne",88,113,"S,M,L,XL","Qara,Bordo,Pudra","x-lilac","#d6c3df","#83608e"],
        ["ev-geyimi","Zərif saten kimono","zerif-saten-kimono",77,98,"S,M,L,XL","Qara,Bordo,Pudra","kimono","#e6d2be","#a87b6d"],
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
