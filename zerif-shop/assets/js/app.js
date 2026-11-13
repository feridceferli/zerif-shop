(function () {
    'use strict';
    var $ = function (s, r) { return (r || document).querySelector(s); };
    var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

    // Başlıq kölgəsi
    var header = $('#siteHeader');
    var onScroll = function () { if (header) header.classList.toggle('scrolled', window.scrollY > 10); };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // Görünmə animasiyası
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var reveal = $$('[data-reveal]');
    if (reduce || !('IntersectionObserver' in window)) {
        reveal.forEach(function (el) { el.classList.add('in'); });
    } else {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); }
            });
        }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
        reveal.forEach(function (el) { io.observe(el); });
    }

    // Mobil menyu
    var burger = $('[data-menu-toggle]');
    if (burger) {
        burger.addEventListener('click', function () {
            var open = document.body.classList.toggle('menu-open');
            burger.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        $$('[data-menu-close]').forEach(function (b) {
            b.addEventListener('click', function () { document.body.classList.remove('menu-open'); burger.setAttribute('aria-expanded', 'false'); });
        });
        document.addEventListener('click', function (e) {
            if (document.body.classList.contains('menu-open') && !e.target.closest('#mainNav') && !e.target.closest('[data-menu-toggle]')) {
                document.body.classList.remove('menu-open');
            }
        });
    }

    // Axtarış paneli
    var st = $('[data-search-toggle]'), sb = $('#searchBar');
    if (st && sb) {
        st.addEventListener('click', function () {
            sb.classList.toggle('open');
            if (sb.classList.contains('open')) setTimeout(function () { $('input', sb).focus(); }, 120);
        });
    }

    // Filtr paneli (mobil)
    var ft = $('[data-filter-toggle]'), fl = $('#filters');
    if (ft && fl) ft.addEventListener('click', function () { fl.classList.toggle('open'); });

    // Sıralama dəyişəndə formu göndər
    $$('[data-autosubmit]').forEach(function (el) {
        el.addEventListener('change', function () { el.form.submit(); });
    });

    // Say +/-
    $$('[data-qty]').forEach(function (box) {
        var input = $('input', box);
        var step = function (d) {
            var min = parseInt(input.min || '0', 10), max = parseInt(input.max || '99', 10);
            var v = Math.min(max, Math.max(min, (parseInt(input.value, 10) || 0) + d));
            input.value = v;
            input.dispatchEvent(new Event('change', { bubbles: true }));
        };
        $('[data-qty-minus]', box).addEventListener('click', function () { step(-1); });
        $('[data-qty-plus]', box).addEventListener('click', function () { step(1); });
    });

    // Səbətdə say dəyişəndə avtomatik yenilə
    var cartForm = $('#cartForm'), timer;
    $$('[data-autoupdate]').forEach(function (el) {
        el.addEventListener('change', function () {
            clearTimeout(timer);
            timer = setTimeout(function () { cartForm.submit(); }, 450);
        });
    });

    // Qalereya
    var main = $('#mainImg');
    $$('[data-thumb]').forEach(function (t) {
        t.addEventListener('click', function () {
            main.style.opacity = '0';
            setTimeout(function () { main.src = t.dataset.thumb; main.style.opacity = '1'; }, 180);
            $$('[data-thumb]').forEach(function (x) { x.classList.remove('active'); });
            t.classList.add('active');
        });
    });

    // Modal
    $$('[data-modal-open]').forEach(function (b) {
        b.addEventListener('click', function () { document.getElementById(b.dataset.modalOpen).hidden = false; });
    });
    $$('.modal').forEach(function (m) {
        m.addEventListener('click', function (e) {
            if (e.target === m || e.target.closest('[data-modal-close]')) m.hidden = true;
        });
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            $$('.modal').forEach(function (m) { m.hidden = true; });
            document.body.classList.remove('menu-open');
        }
    });

    // Bildirişlər
    $$('[data-flash]').forEach(function (f) {
        $('[data-flash-close]', f).addEventListener('click', function () { f.remove(); });
        setTimeout(function () { f.style.transition = 'opacity .4s'; f.style.opacity = '0'; setTimeout(function () { f.remove(); }, 400); }, 5000);
    });
})();
