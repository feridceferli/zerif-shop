<?php
/** 168 yeni məhsul: mövcud və yeni bazalarda təkrarsız əlavə olunur. */
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
        ["destler","Dantel dəst Aylin 001","dantel-dest-aylin-001",49,61,"S,M,L,XL","Qara,Bej,Pudra","bordo","#a65a76","#51263c"],
        ["bustqalterler","Bralette Aylin 002","bralette-aylin-002",41,54,"S,M,L,XL","Qara,Bej,Pudra","x-soft","#e8b8c5","#8e4965"],
        ["trusikler","Slip Aylin 003","slip-aylin-003",29,43,"S,M,L,XL","Qara,Bej,Pudra","slip3","#e9d0c6","#b57880"],
        ["gece-geyimleri","Saten gecəlik Aylin 004","saten-gecelik-aylin-004",88,103,"S,M,L,XL","Qara,Bej,Pudra","satin","#d6c3df","#83608e"],
        ["bodi","Dantel bodi Aylin 005","dantel-bodi-aylin-005",101,117,"S,M,L,XL","Qara,Bej,Pudra","noir","#725065","#30222d"],
        ["ev-geyimi","Kimono xalat Aylin 006","kimono-xalat-aylin-006",114,131,"S,M,L,XL","Qara,Bej,Pudra","kimono","#e6d2be","#a87b6d"],
        ["destler","Dantel dəst Selin 007","dantel-dest-selin-007",50,68,"S,M,L,XL","Qara,Bej,Pudra","bordo","#a65a76","#51263c"],
        ["bustqalterler","Bralette Selin 008","bralette-selin-008",31,50,"S,M,L,XL","Qara,Bej,Pudra","x-soft","#e8b8c5","#8e4965"],
        ["trusikler","Slip Selin 009","slip-selin-009",23,43,"S,M,L,XL","Qara,Bej,Pudra","slip3","#e9d0c6","#b57880"],
        ["gece-geyimleri","Saten gecəlik Selin 010","saten-gecelik-selin-010",89,110,"S,M,L,XL","Qara,Bej,Pudra","satin","#d6c3df","#83608e"],
        ["bodi","Dantel bodi Selin 011","dantel-bodi-selin-011",102,124,"S,M,L,XL","Qara,Bej,Pudra","noir","#725065","#30222d"],
        ["ev-geyimi","Kimono xalat Selin 012","kimono-xalat-selin-012",115,138,"S,M,L,XL","Qara,Bej,Pudra","kimono","#e6d2be","#a87b6d"],
        ["destler","Dantel dəst Narin 013","dantel-dest-narin-013",51,75,"S,M,L,XL","Qara,Bej,Pudra","bordo","#a65a76","#51263c"],
        ["bustqalterler","Bralette Narin 014","bralette-narin-014",59,84,"S,M,L,XL","Qara,Bej,Pudra","x-soft","#e8b8c5","#8e4965"],
        ["trusikler","Slip Narin 015","slip-narin-015",17,43,"S,M,L,XL","Qara,Bej,Pudra","slip3","#e9d0c6","#b57880"],
        ["gece-geyimleri","Saten gecəlik Narin 016","saten-gecelik-narin-016",90,102,"S,M,L,XL","Qara,Bej,Pudra","satin","#d6c3df","#83608e"],
        ["bodi","Dantel bodi Narin 017","dantel-bodi-narin-017",103,116,"S,M,L,XL","Qara,Bej,Pudra","noir","#725065","#30222d"],
        ["ev-geyimi","Kimono xalat Narin 018","kimono-xalat-narin-018",116,130,"S,M,L,XL","Qara,Bej,Pudra","kimono","#e6d2be","#a87b6d"],
        ["destler","Dantel dəst Leyla 019","dantel-dest-leyla-019",52,67,"S,M,L,XL","Qara,Bej,Pudra","bordo","#a65a76","#51263c"],
        ["bustqalterler","Bralette Leyla 020","bralette-leyla-020",49,65,"S,M,L,XL","Qara,Bej,Pudra","x-soft","#e8b8c5","#8e4965"],
        ["trusikler","Slip Leyla 021","slip-leyla-021",35,52,"S,M,L,XL","Qara,Bej,Pudra","slip3","#e9d0c6","#b57880"],
        ["gece-geyimleri","Saten gecəlik Leyla 022","saten-gecelik-leyla-022",91,109,"S,M,L,XL","Qara,Bej,Pudra","satin","#d6c3df","#83608e"],
        ["bodi","Dantel bodi Leyla 023","dantel-bodi-leyla-023",104,123,"S,M,L,XL","Qara,Bej,Pudra","noir","#725065","#30222d"],
        ["ev-geyimi","Kimono xalat Leyla 024","kimono-xalat-leyla-024",117,137,"S,M,L,XL","Qara,Bej,Pudra","kimono","#e6d2be","#a87b6d"],
        ["destler","Dantel dəst Lalə 025","dantel-dest-lale-025",53,74,"S,M,L,XL","Qara,Bej,Pudra","bordo","#a65a76","#51263c"],
        ["bustqalterler","Bralette Lalə 026","bralette-lale-026",39,61,"S,M,L,XL","Qara,Bej,Pudra","x-soft","#e8b8c5","#8e4965"],
        ["trusikler","Slip Lalə 027","slip-lale-027",29,52,"S,M,L,XL","Qara,Bej,Pudra","slip3","#e9d0c6","#b57880"],
        ["gece-geyimleri","Saten gecəlik Lalə 028","saten-gecelik-lale-028",92,116,"S,M,L,XL","Qara,Bej,Pudra","satin","#d6c3df","#83608e"],
        ["bodi","Dantel bodi Lalə 029","dantel-bodi-lale-029",105,130,"S,M,L,XL","Qara,Bej,Pudra","noir","#725065","#30222d"],
        ["ev-geyimi","Kimono xalat Lalə 030","kimono-xalat-lale-030",118,144,"S,M,L,XL","Qara,Bej,Pudra","kimono","#e6d2be","#a87b6d"],
        ["destler","Dantel dəst Mələk 031","dantel-dest-melek-031",54,66,"S,M,L,XL","Qara,Bej,Pudra","bordo","#a65a76","#51263c"],
        ["bustqalterler","Bralette Mələk 032","bralette-melek-032",67,80,"S,M,L,XL","Qara,Bej,Pudra","x-soft","#e8b8c5","#8e4965"],
        ["trusikler","Slip Mələk 033","slip-melek-033",23,37,"S,M,L,XL","Qara,Bej,Pudra","slip3","#e9d0c6","#b57880"],
        ["gece-geyimleri","Saten gecəlik Mələk 034","saten-gecelik-melek-034",93,108,"S,M,L,XL","Qara,Bej,Pudra","satin","#d6c3df","#83608e"],
        ["bodi","Dantel bodi Mələk 035","dantel-bodi-melek-035",106,122,"S,M,L,XL","Qara,Bej,Pudra","noir","#725065","#30222d"],
        ["ev-geyimi","Kimono xalat Mələk 036","kimono-xalat-melek-036",119,136,"S,M,L,XL","Qara,Bej,Pudra","kimono","#e6d2be","#a87b6d"],
        ["destler","Dantel dəst Nigar 037","dantel-dest-nigar-037",55,73,"S,M,L,XL","Qara,Bej,Pudra","bordo","#a65a76","#51263c"],
        ["bustqalterler","Bralette Nigar 038","bralette-nigar-038",57,76,"S,M,L,XL","Qara,Bej,Pudra","x-soft","#e8b8c5","#8e4965"],
        ["trusikler","Slip Nigar 039","slip-nigar-039",17,37,"S,M,L,XL","Qara,Bej,Pudra","slip3","#e9d0c6","#b57880"],
        ["gece-geyimleri","Saten gecəlik Nigar 040","saten-gecelik-nigar-040",94,115,"S,M,L,XL","Qara,Bej,Pudra","satin","#d6c3df","#83608e"],
        ["bodi","Dantel bodi Nigar 041","dantel-bodi-nigar-041",107,129,"S,M,L,XL","Qara,Bej,Pudra","noir","#725065","#30222d"],
        ["ev-geyimi","Kimono xalat Nigar 042","kimono-xalat-nigar-042",120,143,"S,M,L,XL","Qara,Bej,Pudra","kimono","#e6d2be","#a87b6d"],
        ["destler","Dantel dəst İnci 043","dantel-dest-i-nci-043",56,80,"S,M,L,XL","Qara,Bej,Pudra","bordo","#a65a76","#51263c"],
        ["bustqalterler","Bralette İnci 044","bralette-i-nci-044",47,72,"S,M,L,XL","Qara,Bej,Pudra","x-soft","#e8b8c5","#8e4965"],
        ["trusikler","Slip İnci 045","slip-i-nci-045",35,61,"S,M,L,XL","Qara,Bej,Pudra","slip3","#e9d0c6","#b57880"],
        ["gece-geyimleri","Saten gecəlik İnci 046","saten-gecelik-i-nci-046",95,107,"S,M,L,XL","Qara,Bej,Pudra","satin","#d6c3df","#83608e"],
        ["bodi","Dantel bodi İnci 047","dantel-bodi-i-nci-047",108,121,"S,M,L,XL","Qara,Bej,Pudra","noir","#725065","#30222d"],
        ["ev-geyimi","Kimono xalat İnci 048","kimono-xalat-i-nci-048",121,135,"S,M,L,XL","Qara,Bej,Pudra","kimono","#e6d2be","#a87b6d"],
        ["destler","Dantel dəst Dəniz 049","dantel-dest-deniz-049",57,72,"S,M,L,XL","Qara,Bej,Pudra","bordo","#a65a76","#51263c"],
        ["bustqalterler","Bralette Dəniz 050","bralette-deniz-050",37,53,"S,M,L,XL","Qara,Bej,Pudra","x-soft","#e8b8c5","#8e4965"],
        ["trusikler","Slip Dəniz 051","slip-deniz-051",29,46,"S,M,L,XL","Qara,Bej,Pudra","slip3","#e9d0c6","#b57880"],
        ["gece-geyimleri","Saten gecəlik Dəniz 052","saten-gecelik-deniz-052",96,114,"S,M,L,XL","Qara,Bej,Pudra","satin","#d6c3df","#83608e"],
        ["bodi","Dantel bodi Dəniz 053","dantel-bodi-deniz-053",109,128,"S,M,L,XL","Qara,Bej,Pudra","noir","#725065","#30222d"],
        ["ev-geyimi","Kimono xalat Dəniz 054","kimono-xalat-deniz-054",122,142,"S,M,L,XL","Qara,Bej,Pudra","kimono","#e6d2be","#a87b6d"],
        ["destler","Dantel dəst Sevda 055","dantel-dest-sevda-055",58,79,"S,M,L,XL","Qara,Bej,Pudra","bordo","#a65a76","#51263c"],
        ["bustqalterler","Bralette Sevda 056","bralette-sevda-056",65,87,"S,M,L,XL","Qara,Bej,Pudra","x-soft","#e8b8c5","#8e4965"],
        ["trusikler","Slip Sevda 057","slip-sevda-057",23,46,"S,M,L,XL","Qara,Bej,Pudra","slip3","#e9d0c6","#b57880"],
        ["gece-geyimleri","Saten gecəlik Sevda 058","saten-gecelik-sevda-058",97,121,"S,M,L,XL","Qara,Bej,Pudra","satin","#d6c3df","#83608e"],
        ["bodi","Dantel bodi Sevda 059","dantel-bodi-sevda-059",110,135,"S,M,L,XL","Qara,Bej,Pudra","noir","#725065","#30222d"],
        ["ev-geyimi","Kimono xalat Sevda 060","kimono-xalat-sevda-060",123,149,"S,M,L,XL","Qara,Bej,Pudra","kimono","#e6d2be","#a87b6d"],
        ["destler","Dantel dəst Səma 061","dantel-dest-sema-061",59,71,"S,M,L,XL","Qara,Bej,Pudra","bordo","#a65a76","#51263c"],
        ["bustqalterler","Bralette Səma 062","bralette-sema-062",55,68,"S,M,L,XL","Qara,Bej,Pudra","x-soft","#e8b8c5","#8e4965"],
        ["trusikler","Slip Səma 063","slip-sema-063",17,31,"S,M,L,XL","Qara,Bej,Pudra","slip3","#e9d0c6","#b57880"],
        ["gece-geyimleri","Saten gecəlik Səma 064","saten-gecelik-sema-064",98,113,"S,M,L,XL","Qara,Bej,Pudra","satin","#d6c3df","#83608e"],
        ["bodi","Dantel bodi Səma 065","dantel-bodi-sema-065",111,127,"S,M,L,XL","Qara,Bej,Pudra","noir","#725065","#30222d"],
        ["ev-geyimi","Kimono xalat Səma 066","kimono-xalat-sema-066",124,141,"S,M,L,XL","Qara,Bej,Pudra","kimono","#e6d2be","#a87b6d"],
        ["destler","Dantel dəst Arzu 067","dantel-dest-arzu-067",60,78,"S,M,L,XL","Qara,Bej,Pudra","bordo","#a65a76","#51263c"],
        ["bustqalterler","Bralette Arzu 068","bralette-arzu-068",45,64,"S,M,L,XL","Qara,Bej,Pudra","x-soft","#e8b8c5","#8e4965"],
        ["trusikler","Slip Arzu 069","slip-arzu-069",35,55,"S,M,L,XL","Qara,Bej,Pudra","slip3","#e9d0c6","#b57880"],
        ["gece-geyimleri","Saten gecəlik Arzu 070","saten-gecelik-arzu-070",99,120,"S,M,L,XL","Qara,Bej,Pudra","satin","#d6c3df","#83608e"],
        ["bodi","Dantel bodi Arzu 071","dantel-bodi-arzu-071",112,134,"S,M,L,XL","Qara,Bej,Pudra","noir","#725065","#30222d"],
        ["ev-geyimi","Kimono xalat Arzu 072","kimono-xalat-arzu-072",125,148,"S,M,L,XL","Qara,Bej,Pudra","kimono","#e6d2be","#a87b6d"],
        ["destler","Dantel dəst Zümrüd 073","dantel-dest-zumrud-073",61,85,"S,M,L,XL","Qara,Bej,Pudra","bordo","#a65a76","#51263c"],
        ["bustqalterler","Bralette Zümrüd 074","bralette-zumrud-074",35,60,"S,M,L,XL","Qara,Bej,Pudra","x-soft","#e8b8c5","#8e4965"],
        ["trusikler","Slip Zümrüd 075","slip-zumrud-075",29,55,"S,M,L,XL","Qara,Bej,Pudra","slip3","#e9d0c6","#b57880"],
        ["gece-geyimleri","Saten gecəlik Zümrüd 076","saten-gecelik-zumrud-076",100,112,"S,M,L,XL","Qara,Bej,Pudra","satin","#d6c3df","#83608e"],
        ["bodi","Dantel bodi Zümrüd 077","dantel-bodi-zumrud-077",113,126,"S,M,L,XL","Qara,Bej,Pudra","noir","#725065","#30222d"],
        ["ev-geyimi","Kimono xalat Zümrüd 078","kimono-xalat-zumrud-078",49,63,"S,M,L,XL","Qara,Bej,Pudra","kimono","#e6d2be","#a87b6d"],
        ["destler","Dantel dəst Banu 079","dantel-dest-banu-079",62,77,"S,M,L,XL","Qara,Bej,Pudra","bordo","#a65a76","#51263c"],
        ["bustqalterler","Bralette Banu 080","bralette-banu-080",63,79,"S,M,L,XL","Qara,Bej,Pudra","x-soft","#e8b8c5","#8e4965"],
        ["trusikler","Slip Banu 081","slip-banu-081",23,40,"S,M,L,XL","Qara,Bej,Pudra","slip3","#e9d0c6","#b57880"],
        ["gece-geyimleri","Saten gecəlik Banu 082","saten-gecelik-banu-082",101,119,"S,M,L,XL","Qara,Bej,Pudra","satin","#d6c3df","#83608e"],
        ["bodi","Dantel bodi Banu 083","dantel-bodi-banu-083",114,133,"S,M,L,XL","Qara,Bej,Pudra","noir","#725065","#30222d"],
        ["ev-geyimi","Kimono xalat Banu 084","kimono-xalat-banu-084",50,70,"S,M,L,XL","Qara,Bej,Pudra","kimono","#e6d2be","#a87b6d"],
        ["destler","Dantel dəst Nilay 085","dantel-dest-nilay-085",63,84,"S,M,L,XL","Qara,Bej,Pudra","bordo","#a65a76","#51263c"],
        ["bustqalterler","Bralette Nilay 086","bralette-nilay-086",53,75,"S,M,L,XL","Qara,Bej,Pudra","x-soft","#e8b8c5","#8e4965"],
        ["trusikler","Slip Nilay 087","slip-nilay-087",17,40,"S,M,L,XL","Qara,Bej,Pudra","slip3","#e9d0c6","#b57880"],
        ["gece-geyimleri","Saten gecəlik Nilay 088","saten-gecelik-nilay-088",102,126,"S,M,L,XL","Qara,Bej,Pudra","satin","#d6c3df","#83608e"],
        ["bodi","Dantel bodi Nilay 089","dantel-bodi-nilay-089",115,140,"S,M,L,XL","Qara,Bej,Pudra","noir","#725065","#30222d"],
        ["ev-geyimi","Kimono xalat Nilay 090","kimono-xalat-nilay-090",51,77,"S,M,L,XL","Qara,Bej,Pudra","kimono","#e6d2be","#a87b6d"],
        ["destler","Dantel dəst Aysu 091","dantel-dest-aysu-091",64,76,"S,M,L,XL","Qara,Bej,Pudra","bordo","#a65a76","#51263c"],
        ["bustqalterler","Bralette Aysu 092","bralette-aysu-092",43,56,"S,M,L,XL","Qara,Bej,Pudra","x-soft","#e8b8c5","#8e4965"],
        ["trusikler","Slip Aysu 093","slip-aysu-093",35,49,"S,M,L,XL","Qara,Bej,Pudra","slip3","#e9d0c6","#b57880"],
        ["gece-geyimleri","Saten gecəlik Aysu 094","saten-gecelik-aysu-094",103,118,"S,M,L,XL","Qara,Bej,Pudra","satin","#d6c3df","#83608e"],
        ["bodi","Dantel bodi Aysu 095","dantel-bodi-aysu-095",116,132,"S,M,L,XL","Qara,Bej,Pudra","noir","#725065","#30222d"],
        ["ev-geyimi","Kimono xalat Aysu 096","kimono-xalat-aysu-096",52,69,"S,M,L,XL","Qara,Bej,Pudra","kimono","#e6d2be","#a87b6d"],
        ["destler","Dantel dəst Sona 097","dantel-dest-sona-097",65,83,"S,M,L,XL","Qara,Bej,Pudra","bordo","#a65a76","#51263c"],
        ["bustqalterler","Bralette Sona 098","bralette-sona-098",33,52,"S,M,L,XL","Qara,Bej,Pudra","x-soft","#e8b8c5","#8e4965"],
        ["trusikler","Slip Sona 099","slip-sona-099",29,49,"S,M,L,XL","Qara,Bej,Pudra","slip3","#e9d0c6","#b57880"],
        ["gece-geyimleri","Saten gecəlik Sona 100","saten-gecelik-sona-100",104,125,"S,M,L,XL","Qara,Bej,Pudra","satin","#d6c3df","#83608e"],
        ["bodi","Dantel bodi Sona 101","dantel-bodi-sona-101",117,139,"S,M,L,XL","Qara,Bej,Pudra","noir","#725065","#30222d"],
        ["ev-geyimi","Kimono xalat Sona 102","kimono-xalat-sona-102",53,76,"S,M,L,XL","Qara,Bej,Pudra","kimono","#e6d2be","#a87b6d"],
        ["destler","Dantel dəst Nərgiz 103","dantel-dest-nergiz-103",66,90,"S,M,L,XL","Qara,Bej,Pudra","bordo","#a65a76","#51263c"],
        ["bustqalterler","Bralette Nərgiz 104","bralette-nergiz-104",61,86,"S,M,L,XL","Qara,Bej,Pudra","x-soft","#e8b8c5","#8e4965"],
        ["trusikler","Slip Nərgiz 105","slip-nergiz-105",23,49,"S,M,L,XL","Qara,Bej,Pudra","slip3","#e9d0c6","#b57880"],
        ["gece-geyimleri","Saten gecəlik Nərgiz 106","saten-gecelik-nergiz-106",105,117,"S,M,L,XL","Qara,Bej,Pudra","satin","#d6c3df","#83608e"],
        ["bodi","Dantel bodi Nərgiz 107","dantel-bodi-nergiz-107",118,131,"S,M,L,XL","Qara,Bej,Pudra","noir","#725065","#30222d"],
        ["ev-geyimi","Kimono xalat Nərgiz 108","kimono-xalat-nergiz-108",54,68,"S,M,L,XL","Qara,Bej,Pudra","kimono","#e6d2be","#a87b6d"],
    ];
    $categories = $db->query('SELECT slug, id FROM categories')->fetchAll(PDO::FETCH_KEY_PAIR);
    $check = $db->prepare('SELECT 1 FROM products WHERE slug = ? LIMIT 1');
    $insert = $db->prepare('INSERT INTO products (category_id, name, slug, description, material, price, old_price, sizes, colors, stock, image, gallery, is_featured, is_new, is_active, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $realPhotos = [
        'saten-babydoll-rose' => 'https://images.pexels.com/photos/20337356/pexels-photo-20337356.jpeg?auto=compress&cs=tinysrgb&w=800',
        'atlas-pijama-bloom' => 'https://images.pexels.com/photos/20337344/pexels-photo-20337344.jpeg?auto=compress&cs=tinysrgb&w=800',
        'dantel-dest-velvet' => 'https://images.pexels.com/photos/20337341/pexels-photo-20337341.jpeg?auto=compress&cs=tinysrgb&w=800',
        'dantel-gece-koyneyi-rose' => 'https://images.pexels.com/photos/20364779/pexels-photo-20364779.jpeg?auto=compress&cs=tinysrgb&w=800',
        'minimalist-gundelik-bustqalter' => 'https://images.pexels.com/photos/6568233/pexels-photo-6568233.jpeg?auto=compress&cs=tinysrgb&w=800',
        'tul-gecelik-midnight' => 'https://images.pexels.com/photos/19543039/pexels-photo-19543039.jpeg?auto=compress&cs=tinysrgb&w=800',
        'saten-gece-koyneyi-mira' => 'https://images.pexels.com/photos/10285736/pexels-photo-10285736.jpeg?auto=compress&cs=tinysrgb&w=800',
        'dantel-bodi-bella' => 'https://images.pexels.com/photos/26100332/pexels-photo-26100332.jpeg?auto=compress&cs=tinysrgb&w=800',
    ];
    foreach ($products as $i => $p) {
        [$cat, $name, $slug, $price, $old, $sizes, $colors, $photo, $c1, $c2] = $p;
        if (!isset($categories[$cat])) continue;
        $check->execute([$slug]);
        $image = $realPhotos[$slug] ?? unique_collection_image($slug, $name, $cat, $i);
        if ($check->fetchColumn()) {
            $update = $db->prepare('UPDATE products SET image = ? WHERE slug = ? AND (image NOT LIKE ? OR image IS NULL OR ? = 1)');
            $update->execute([$image, $slug, 'https://images.pexels.com/%', isset($realPhotos[$slug]) ? 1 : 0]);
            continue;
        }
        $insert->execute([$categories[$cat], $name, $slug,
            'Yeni kolleksiyadan zərif model. Rahat kəsim və gündəlik istifadə üçün uyğun materiallar.',
            'Yumşaq qarışıq parça', $price, $old, $sizes, $colors, 8 + ($i * 7 % 25),
            $image, '[]', $i % 3 === 0 ? 1 : 0, 1, 1, date('Y-m-d H:i:s')]);
    }
}

/** Each catalog entry receives a distinct, locally generated fashion illustration. */
function unique_collection_image(string $slug, string $name, string $category, int $index): string
{
    $file = 'collection-' . $slug . '.svg';
    $path = UPLOAD_DIR . '/' . $file;
    if (is_file($path)) return 'uploads/' . $file;
    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0775, true);
    $hue = ($index * 137 + 315) % 360;
    $accent = ($hue + 38) % 360;
    $seed = crc32($slug);
    $tilt = ($seed % 13) - 6;
    $pattern = ($seed % 3);
    $body = match ($category) {
        'bustqalterler' => '<path d="M155 300 Q200 225 280 290 L300 345 L320 290 Q400 225 445 300 L422 400 Q360 450 300 395 Q240 450 178 400 Z"/><path d="M185 310 Q155 235 205 205 M415 310 Q445 235 395 205" fill="none" stroke-width="16"/>',
        'trusikler' => '<path d="M150 260 Q300 305 450 260 L415 385 L345 510 Q300 540 255 510 L185 385 Z"/>',
        'destler' => '<path d="M155 245 Q220 175 280 250 L300 300 L320 250 Q380 175 445 245 L420 340 Q360 385 300 330 Q240 385 180 340 Z"/><path d="M185 395 Q300 430 415 395 L380 510 L335 575 L265 575 L220 510 Z"/>',
        'gece-geyimleri' => '<path d="M215 175 L260 215 L340 215 L385 175 L420 300 L455 630 Q300 685 145 630 L180 300 Z"/><path d="M215 175 L260 215 M385 175 L340 215" fill="none" stroke-width="12"/>',
        'bodi' => '<path d="M220 165 L270 205 L330 205 L380 165 L430 295 L385 340 L370 500 L320 600 L280 600 L230 500 L215 340 L170 295 Z"/>',
        default => '<path d="M225 155 L285 195 L315 195 L375 155 L455 300 L410 355 L405 650 L195 650 L190 355 L145 300 Z"/><path d="M300 200 L260 475 L330 475 Z" fill="none" stroke-width="7"/>',
    };
    $safe = htmlspecialchars($name, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    $dots = $pattern === 0 ? '<circle cx="250" cy="355" r="13"/><circle cx="350" cy="355" r="13"/><circle cx="300" cy="435" r="13"/>' : ($pattern === 1 ? '<path d="M205 360 L390 490 M390 360 L205 490" fill="none" stroke-width="5"/>' : '<path d="M200 430 Q300 370 400 430" fill="none" stroke-width="9"/>');
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 800"><defs><linearGradient id="bg" x2="1" y2="1"><stop stop-color="hsl(' . $hue . ',32%,92%)"/><stop offset="1" stop-color="hsl(' . $accent . ',36%,78%)"/></linearGradient><linearGradient id="fabric" x2="1" y2="1"><stop stop-color="hsl(' . $hue . ',42%,48%)"/><stop offset="1" stop-color="hsl(' . $accent . ',48%,29%)"/></linearGradient></defs><rect width="600" height="800" fill="url(#bg)"/><circle cx="300" cy="380" r="250" fill="#fff" opacity=".24"/><g transform="rotate(' . $tilt . ' 300 400)" fill="url(#fabric)" stroke="hsl(' . $accent . ',30%,25%)" stroke-width="4" stroke-linejoin="round">' . $body . '</g><g fill="#fff" opacity=".22">' . $dots . '</g><rect x="20" y="710" width="560" height="70" rx="15" fill="#fff" opacity=".86"/><text x="300" y="751" font-family="Arial,sans-serif" font-size="20" text-anchor="middle" fill="#382a35">' . $safe . '</text></svg>';
    file_put_contents($path, $svg);
    return 'uploads/' . $file;
}
