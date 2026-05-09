/* ============================================================
   MAISON — site script
   - Cart, Wishlist (localStorage)
   - Theme toggle
   - Search overlay
   - Mobile menu
   - Nav dropdowns (hover + keyboard)
   - Hero slider
   - Reveal-on-scroll
   - Countdown
   - Product card add-to-cart / wishlist
   - Header scroll shadow
   - Accordion
============================================================ */

(function () {

  // ── Storage helpers ────────────────────────────────────────
  const Store = {
    get(k, def) { try { return JSON.parse(localStorage.getItem(k)) ?? def; } catch { return def; } },
    set(k, v)   { localStorage.setItem(k, JSON.stringify(v)); },
  };

  function cart()      { return Store.get('ew_cart', []); }
  function wish()      { return Store.get('ew_wish', []); }
  function setCart(v)  { Store.set('ew_cart', v); updateBadges(); }
  function setWish(v)  { Store.set('ew_wish', v); updateBadges(); }

  function updateBadges() {
    const c = cart().reduce((s, i) => s + (i.qty || 1), 0);
    const w = wish().length;
    document.querySelectorAll('[data-cart-count]').forEach(el => {
      el.textContent = c;
      el.style.display = c > 0 ? 'inline-flex' : 'none';
    });
    document.querySelectorAll('[data-wish-count]').forEach(el => {
      el.textContent = w;
      el.style.display = w > 0 ? 'inline-flex' : 'none';
    });
  }

  // ── Toast ──────────────────────────────────────────────────
  function ensureToastStack() {
    let s = document.querySelector('.toast-stack');
    if (!s) { s = document.createElement('div'); s.className = 'toast-stack'; document.body.appendChild(s); }
    return s;
  }
  window.toast = function (msg, icon) {
    const stack = ensureToastStack();
    const t = document.createElement('div');
    t.className = 'toast-item';
    t.setAttribute('data-testid', 'toast-item');
    t.innerHTML = `<i class="bi bi-${icon || 'check2-circle'}"></i><span>${msg}</span>`;
    stack.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; t.style.transform = 'translateX(-12px)'; }, 2400);
    setTimeout(() => t.remove(), 2700);
  };

  // ── Theme ──────────────────────────────────────────────────
  function applyTheme(t) {
    document.documentElement.setAttribute('data-theme', t);
    document.querySelectorAll('.theme-icon').forEach(i => {
      i.className = 'theme-icon bi ' + (t === 'dark' ? 'bi-sun' : 'bi-moon-stars');
    });
  }
  function initTheme() {
    applyTheme(Store.get('ew_theme', 'light'));
    document.addEventListener('click', e => {
      if (!e.target.closest('[data-theme-toggle], [data-testid="theme-toggle-btn"]')) return;
      const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
      applyTheme(next);
      Store.set('ew_theme', next);
    });
  }

  // ── Search overlay ─────────────────────────────────────────
  function initSearch() {
    const overlay = document.getElementById('searchOverlay');
    if (!overlay) return;

    function openSearch() {
      overlay.classList.add('show');
      document.body.style.overflow = 'hidden';
      setTimeout(() => overlay.querySelector('[data-testid="search-input"]')?.focus(), 80);
    }
    function closeSearch() {
      overlay.classList.remove('show');
      document.body.style.overflow = '';
    }

    // ✅ ADD THIS — wire the × close button
    overlay.querySelector('[data-testid="search-close-btn"]')
      ?.addEventListener('click', closeSearch);

    // backdrop click (only when clicking the overlay itself, not the modal)
    overlay.addEventListener('click', e => {
      if (e.target === overlay) closeSearch();
    });

    document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && overlay.classList.contains('show')) closeSearch();
    });

    document.addEventListener('click', e => {
      if (e.target.closest('[data-search-trigger],[data-testid="search-trigger-btn"]'))
        { e.stopPropagation(); openSearch(); }
    });

    overlay.querySelector('form')?.addEventListener('submit', e => {
      e.preventDefault();
      const q = overlay.querySelector('[data-testid="search-input"]')?.value.trim();
      if (q) window.location.href = `/products?search=${encodeURIComponent(q)}`;
      closeSearch();
    });
  }

  // ── Mobile menu ────────────────────────────────────────────
  function initMobileMenu() {
    document.addEventListener('click', e => {
      const trigger = e.target.closest('[data-mobile-menu-trigger], [data-testid="mobile-menu-btn"]');
      if (!trigger) return;
      const el = document.getElementById('mobileMenu');
      if (el && window.bootstrap) {
        window.bootstrap.Offcanvas.getOrCreateInstance(el).show();
      }
    });
  }

  // ── Nav dropdowns (hover + keyboard) ──────────────────────
  function initDropdowns() {
    const dropdowns = document.querySelectorAll('.nav-dropdown');
    if (!dropdowns.length) return;
    const timers = new WeakMap();

    dropdowns.forEach(dd => {
      const trigger = dd.querySelector('.nav-dropdown-trigger');

      function open() {
        dropdowns.forEach(other => { if (other !== dd) other.classList.remove('open'); });
        clearTimeout(timers.get(dd));
        dd.classList.add('open');
      }
      function scheduleClose() {
        timers.set(dd, setTimeout(() => dd.classList.remove('open'), 120));
      }

      dd.addEventListener('mouseenter', open);
      dd.addEventListener('mouseleave', scheduleClose);

      trigger?.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); dd.classList.toggle('open'); }
        if (e.key === 'Escape') dd.classList.remove('open');
      });
    });

    document.addEventListener('click', e => {
      if (!e.target.closest('.nav-dropdown')) {
        dropdowns.forEach(dd => dd.classList.remove('open'));
      }
    });
  }

  // ── Hero slider ────────────────────────────────────────────
  function initHeroSlider() {
    const root = document.querySelector('[data-hero-slider]');
    if (!root) return;
    const slides = root.querySelectorAll('.hero-slide');
    const dots   = root.querySelectorAll('.hero-pagination button');
    if (slides.length < 2) return;
    let i = 0, timer;
    const show = n => {
      slides.forEach((s, idx) => s.style.display = idx === n ? 'flex' : 'none');
      dots.forEach((d, idx) => d.classList.toggle('active', idx === n));
      i = n;
    };
    const next    = () => show((i + 1) % slides.length);
    const start   = () => { timer = setInterval(next, 6500); };
    const restart = () => { clearInterval(timer); start(); };
    dots.forEach((d, idx) => d.addEventListener('click', () => { show(idx); restart(); }));
    show(0);
    start();
  }

  // ── Reveal on scroll ───────────────────────────────────────
  function initReveal() {
    const els = document.querySelectorAll('.reveal');
    if (!els.length) return;
    if (!('IntersectionObserver' in window)) { els.forEach(e => e.classList.add('in')); return; }
    const io = new IntersectionObserver(entries => {
      entries.forEach(en => { if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); } });
    }, { threshold: 0.12 });
    els.forEach(e => io.observe(e));
  }

  // ── Countdown ─────────────────────────────────────────────
  function initCountdown() {
    const root = document.querySelector('[data-countdown]');
    if (!root) return;
    const target = Date.now() + (1000 * 60 * 60 * 56) + 14 * 60 * 1000;
    const pad = n => String(n).padStart(2, '0');
    const tick = () => {
      const diff = target - Date.now();
      if (diff <= 0) return;
      root.querySelector('[data-d]').textContent = pad(Math.floor(diff / 86400000));
      root.querySelector('[data-h]').textContent = pad(Math.floor((diff / 3600000) % 24));
      root.querySelector('[data-m]').textContent = pad(Math.floor((diff / 60000) % 60));
      root.querySelector('[data-s]').textContent = pad(Math.floor((diff / 1000) % 60));
    };
    tick(); setInterval(tick, 1000);
  }

  // ── Add to cart / wishlist ─────────────────────────────────
  function getCardData(el) {
    try { return JSON.parse(el?.closest('[data-product]')?.getAttribute('data-product')); }
    catch { return null; }
  }
  function initProductActions() {
    document.addEventListener('click', e => {
      const cartBtn = e.target.closest('[data-add-to-cart]');
      if (cartBtn) {
        e.preventDefault();
        const data = getCardData(cartBtn);
        if (!data) return;
        const list = cart();
        const ex = list.find(x => x.id === data.id);
        if (ex) ex.qty = (ex.qty || 1) + 1; else list.push({ ...data, qty: 1 });
        setCart(list);
        window.toast(`${data.title} · added to bag`, 'bag-check');
        return;
      }
      const wishBtn = e.target.closest('[data-add-to-wishlist]');
      if (wishBtn) {
        e.preventDefault();
        const data = getCardData(wishBtn);
        if (!data) return;
        const list = wish();
        const idx = list.findIndex(x => x.id === data.id);
        if (idx >= 0) {
          list.splice(idx, 1); setWish(list);
          wishBtn.classList.remove('is-active');
          wishBtn.querySelector('i')?.classList.replace('bi-heart-fill', 'bi-heart');
          window.toast(`${data.title} · removed from wishlist`, 'heart');
        } else {
          list.push(data); setWish(list);
          wishBtn.classList.add('is-active');
          wishBtn.querySelector('i')?.classList.replace('bi-heart', 'bi-heart-fill');
          window.toast(`${data.title} · saved to wishlist`, 'heart-fill');
        }
      }
    });
  }

  // ── Header scroll shadow ───────────────────────────────────
  function initHeaderScroll() {
    const onScroll = () => {
      document.querySelector('.site-header')?.classList.toggle('scrolled', window.scrollY > 8);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // ── Mobile bottom nav active state ─────────────────────────
  function initMobileNavActive() {
    const page = document.body.getAttribute('data-page');
    if (page) document.querySelectorAll(`[data-nav="${page}"]`).forEach(a => a.classList.add('active'));
  }

  // ── Accordion ─────────────────────────────────────────────
  function initAccordion() {
    document.addEventListener('click', e => {
      const head = e.target.closest('.accordion-clean .acc-head');
      if (head) head.parentElement.classList.toggle('open');
    });
  }

  // ── Boot ──────────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initSearch();
    initMobileMenu();
    initDropdowns();
    initHeroSlider();
    initReveal();
    initCountdown();
    initProductActions();
    initHeaderScroll();
    initMobileNavActive();
    initAccordion();
    updateBadges();
  });

})();