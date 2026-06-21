/* ============================================================
   БАВАР+ · полированный прототип v2 · скрипты
   ============================================================ */

// ---------- formatters ----------
const RUB = new Intl.NumberFormat('ru-RU', { style:'currency', currency:'RUB', maximumFractionDigits:0 });
const fmtRub = n => isFinite(n) ? RUB.format(Math.round(n)) : '—';
const fmtShort = n => {
  if (!isFinite(n)) return '—';
  if (Math.abs(n) >= 1_000_000) {
    const v = n / 1_000_000;
    const s = (v >= 10 ? v.toFixed(1) : v.toFixed(2)).replace(/\.?0+$/, '').replace('.', ',');
    return s + ' млн ₽';
  }
  if (Math.abs(n) >= 1000) return Math.round(n / 1000) + ' тыс ₽';
  return fmtRub(n);
};
const plural = (n, forms) => {
  const abs = Math.abs(n) % 100;
  const n1 = abs % 10;
  if (abs > 10 && abs < 20) return forms[2];
  if (n1 > 1 && n1 < 5) return forms[1];
  if (n1 === 1) return forms[0];
  return forms[2];
};

// ============================================================
// Hero variant switcher
// ============================================================
const heroVariants = document.querySelectorAll('.hero[data-variant]');
const switcherLinks = document.querySelectorAll('#v-switcher a');

function showVariant(v) {
  heroVariants.forEach(h => {
    const match = h.dataset.variant === v;
    h.hidden = !match;
  });
  switcherLinks.forEach(a => {
    a.classList.toggle('active', a.dataset.v === v);
  });
  // Update logo color in nav based on hero v1 (red) vs v2/v3 (red on white default)
  // No change needed — logo is consistent red.
}

function getVariantFromHash() {
  const h = (location.hash || '').replace('#', '').trim();
  return ['v1', 'v2'].includes(h) ? h : 'v1';
}

showVariant(getVariantFromHash());

window.addEventListener('hashchange', () => {
  const v = getVariantFromHash();
  showVariant(v);
  // smooth scroll to top so the new hero is in view
  if (location.hash.match(/^#v[12]$/)) {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
});

switcherLinks.forEach(a => {
  a.addEventListener('click', (e) => {
    // hashchange will fire and handle the rest
  });
});

// ============================================================
// Scroll-reveal: мягкое появление блоков при прокрутке
// ============================================================
(function () {
  // Элементы, которые проявляем. Заголовки/лиды + крупные блоки.
  // НЕ трогаем .pf-stage / .pf-card — они позиционируются через transform.
  const SEL = [
    '.section__title', '.section__lede',
    '.cmp-card', '.process-flow', '.guarantees',
    '.bio__intro', '.bio-card', '.bio-notes', '.bio-note',
    '.step', '.adv', '.adv-outro', '.calc__panel', '.calc-alert', '.form-card', '.forms-compare-wrap',
    '.faq-item', '.contact2', '.office2__left', '.office2__visual'
  ].join(', ');

  const els = Array.from(document.querySelectorAll(SEL));
  if (!els.length) return;

  // Направление: левая карточка сравнения «влетает» слева, правая — справа.
  els.forEach(el => {
    el.classList.add('reveal');
    if (el.classList.contains('cmp-card--problem')) el.dataset.reveal = 'left';
    if (el.classList.contains('cmp-card--solution')) el.dataset.reveal = 'right';
  });

  // Стаггер внутри одной группы (соседи по родителю появляются по очереди)
  const byParent = new Map();
  els.forEach(el => {
    const p = el.parentElement;
    const arr = byParent.get(p) || [];
    arr.push(el);
    byParent.set(p, arr);
  });
  byParent.forEach(arr => arr.forEach((el, i) => {
    el.style.transitionDelay = Math.min(i * 90, 540) + 'ms';
  }));

  if (!('IntersectionObserver' in window)) {
    els.forEach(el => el.classList.add('is-visible'));
    return;
  }
  const io = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        const el = e.target;
        el.classList.add('is-visible');
        io.unobserve(el);
        // снимаем инлайновую задержку после проявления, иначе она тормозит
        // последующие hover-переходы (например, красную рамку в FAQ)
        setTimeout(() => { el.style.transitionDelay = ''; }, 1300);
      }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
  els.forEach(el => io.observe(el));
})();

// ============================================================
// Lazy Load — подгрузка картинок по мере приближения к вьюпорту
// (data-src → src за 300px до появления, плавный fade-in)
// ============================================================
(function () {
  const imgs = Array.from(document.querySelectorAll('img[data-src]'));
  if (!imgs.length) return;

  const reveal = (img) => {
    img.classList.add('lz-loaded');
    // блок 04: скрыть плейсхолдер, когда сцена реально загрузилась
    const pf = img.closest('.pf-img');
    if (pf) pf.classList.add('is-loaded');
  };
  const load = (img) => {
    if (!img.dataset.src) return;
    img.addEventListener('load', () => reveal(img), { once: true });
    img.addEventListener('error', () => { if (img.closest('.pf-img')) img.style.display = 'none'; }, { once: true });
    img.src = img.dataset.src;
    img.removeAttribute('data-src');
    if (img.complete && img.naturalWidth > 0) reveal(img); // из кэша — мгновенно
  };

  if (!('IntersectionObserver' in window)) { imgs.forEach(load); return; }
  const io = new IntersectionObserver((entries) => {
    entries.forEach(e => { if (e.isIntersecting) { load(e.target); io.unobserve(e.target); } });
  }, { rootMargin: '300px 0px' });
  imgs.forEach(img => io.observe(img));
})();

// ============================================================
// FAQ — плавное раскрытие/сворачивание (нативный <details> рывком)
// ============================================================
(function () {
  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  document.querySelectorAll('.faq-item').forEach(item => {
    const summary = item.querySelector('summary');
    const body = item.querySelector('.faq-item__body');
    if (!summary || !body) return;
    summary.addEventListener('click', (e) => {
      e.preventDefault();
      if (reduce) { item.open = !item.open; item.classList.toggle('is-expanded', item.open); return; }
      if (item.dataset.anim) return;
      if (item.open) {
        // закрытие: убираем класс (grid-rows 1fr→0fr), скрываем по завершении
        item.dataset.anim = '1';
        item.classList.remove('is-expanded');
        const done = (ev) => {
          if (ev.propertyName !== 'grid-template-rows') return;
          item.open = false;
          delete item.dataset.anim;
          body.removeEventListener('transitionend', done);
        };
        body.addEventListener('transitionend', done);
      } else {
        // открытие: показываем контент, на след. кадрах включаем grid-rows 0fr→1fr
        item.open = true;
        requestAnimationFrame(() => requestAnimationFrame(() => item.classList.add('is-expanded')));
      }
    });
  });
})();

// ============================================================
// Calculator
// ============================================================
const BAVAR_FIXED = 300_000;
const TAX_RATE = 0.43;          // налоги + страховые взносы (на ФОТ «на руки»)
const OFFICE_MSK = 100_000;     // офис в Москве, ₽/мес
const OFFICE_SHANGHAI = 500_000; // офис в Шанхае + штат, ₽/мес
const HIDDEN_COSTS = 200_000;   // скрытые расходы и риски, ₽/мес

const calcEls = {
  staff: document.getElementById('cf-staff'),
  salary: document.getElementById('cf-salary'),
  deals: document.getElementById('cf-deals'),
  lblStaff: document.getElementById('cl-staff'),
  lblSalary: document.getElementById('cl-salary'),
  lblDeals: document.getElementById('cl-deals'),
  heroSavings: document.getElementById('cr-hero'),
  heroSub: document.getElementById('cr-hero-sub'),
  brkFot: document.getElementById('br-fot'),
  brkTaxes: document.getElementById('br-taxes'),
  brkOffice: document.getElementById('br-office'),
  brkShanghai: document.getElementById('br-shanghai'),
  brkRisk: document.getElementById('br-risk'),
  brkTotal: document.getElementById('br-total'),
  brkBavar: document.getElementById('br-bavar'),
  barOwn: document.getElementById('bar-own'),
  barBavar: document.getElementById('bar-bavar'),
  sticky: document.getElementById('cr-sticky'),
};

function recalc() {
  if (!calcEls.staff) return;
  const staff = +calcEls.staff.value;
  const salary = +calcEls.salary.value;
  const deals = +calcEls.deals.value;

  const fot = staff * salary;
  const taxes = Math.round(fot * TAX_RATE);
  const inhouse = fot + taxes + OFFICE_MSK + OFFICE_SHANGHAI + HIDDEN_COSTS;
  const savings = Math.max(0, inhouse - BAVAR_FIXED);

  calcEls.lblStaff.textContent = staff + ' ' + plural(staff, ['человек','человека','человек']);
  calcEls.lblSalary.textContent = fmtRub(salary);
  calcEls.lblDeals.textContent = deals + ' ' + plural(deals, ['сделка','сделки','сделок']);

  calcEls.brkFot.textContent = fmtRub(fot);
  calcEls.brkTaxes.textContent = fmtRub(taxes);
  calcEls.brkOffice.textContent = fmtRub(OFFICE_MSK);
  if (calcEls.brkShanghai) calcEls.brkShanghai.textContent = fmtRub(OFFICE_SHANGHAI);
  calcEls.brkRisk.textContent = fmtRub(HIDDEN_COSTS);
  calcEls.brkTotal.textContent = fmtRub(inhouse);
  calcEls.brkBavar.textContent = fmtRub(BAVAR_FIXED);

  // Полосы-бары: ширина пропорциональна сумме (макс = свой отдел)
  const maxVal = Math.max(inhouse, BAVAR_FIXED, 1);
  if (calcEls.barOwn)   calcEls.barOwn.style.width   = (inhouse / maxVal * 100) + '%';
  if (calcEls.barBavar) calcEls.barBavar.style.width = (BAVAR_FIXED / maxVal * 100) + '%';

  calcEls.heroSavings.textContent = fmtShort(savings);
  calcEls.heroSub.textContent = `в месяц · ${fmtShort(savings * 12)} в год`;
  if (calcEls.sticky) calcEls.sticky.textContent = fmtShort(savings);
}

['staff','salary','deals'].forEach(k => {
  if (calcEls[k]) calcEls[k].addEventListener('input', recalc);
});
recalc();

// Липкая плашка с экономией показывается, пока блок калькулятора в зоне видимости (мобильные)
(function () {
  const calc = document.getElementById('calc');
  const sticky = document.querySelector('.calc-sticky');
  if (!calc || !sticky || !('IntersectionObserver' in window)) return;
  const io = new IntersectionObserver((entries) => {
    entries.forEach(e => sticky.classList.toggle('is-shown', e.isIntersecting));
  }, { rootMargin: '-10% 0px -45% 0px' });
  io.observe(calc);
})();

// ============================================================
// Form (prototype only) — успех на любую форму с .js-lead / id contact-form / modal-form
// ============================================================
function wireForm(form) {
  if (!form) return;
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const btn = form.querySelector('button[type=submit]');
    if (!btn) return;
    const old = btn.textContent;
    btn.textContent = '✓ Заявка отправлена';
    btn.disabled = true;
    setTimeout(() => { btn.textContent = old; btn.disabled = false; form.reset(); }, 2400);
  });
}
wireForm(document.getElementById('contact-form'));
wireForm(document.getElementById('modal-form'));

// ============================================================
// Кейсы — карусель (стрелки + точки)
// ============================================================
(function () {
  const root = document.querySelector('.cases');
  if (!root) return;
  const track = root.querySelector('.cases__track');
  const cards = Array.from(track.children);
  const dotsWrap = root.querySelector('.cases__dots');
  const prev = root.querySelector('.cases__arrow--prev');
  const next = root.querySelector('.cases__arrow--next');
  if (!track || !cards.length) return;

  // точки — по числу карточек
  const dots = cards.map((_, i) => {
    const b = document.createElement('button');
    b.className = 'cases__dot' + (i === 0 ? ' is-active' : '');
    b.type = 'button';
    b.setAttribute('aria-label', 'Кейс ' + (i + 1));
    b.addEventListener('click', () => scrollToDot(i));
    dotsWrap.appendChild(b);
    return b;
  });

  // шаг = расстояние между соседними карточками (ширина + gap)
  const step = () => (cards.length > 1 ? cards[1].offsetLeft - cards[0].offsetLeft : cards[0].offsetWidth);
  const maxScroll = () => track.scrollWidth - track.clientWidth;
  // активная точка — по доле прокрутки (конец трека = последняя точка)
  const activeIndex = () => {
    const m = maxScroll();
    if (m <= 1) return 0;
    return Math.round((track.scrollLeft / m) * (cards.length - 1));
  };
  const scrollToDot = (i) => {
    const n = Math.max(0, Math.min(i, cards.length - 1));
    track.scrollTo({ left: maxScroll() * (n / (cards.length - 1)), behavior: 'smooth' });
  };
  const sync = () => {
    const a = activeIndex();
    dots.forEach((d, i) => d.classList.toggle('is-active', i === a));
  };

  // стрелки — строго по центру фото карточки
  const placeArrows = () => {
    const photo = root.querySelector('.case__photo');
    if (!photo) return;
    const y = photo.offsetTop + photo.offsetHeight / 2;
    [prev, next].forEach(a => { if (a) a.style.top = y + 'px'; });
  };
  placeArrows();
  window.addEventListener('resize', placeArrows);
  window.addEventListener('load', placeArrows);

  prev && prev.addEventListener('click', () => track.scrollBy({ left: -step(), behavior: 'smooth' }));
  next && next.addEventListener('click', () => track.scrollBy({ left:  step(), behavior: 'smooth' }));
  let raf;
  track.addEventListener('scroll', () => {
    cancelAnimationFrame(raf);
    raf = requestAnimationFrame(sync);
  });
})();

// ============================================================
// Модальное окно «Связаться»
// ============================================================
(function () {
  const modal = document.getElementById('lead-modal');
  if (!modal) return;
  let lastFocus = null;
  const open = () => {
    lastFocus = document.activeElement;
    modal.hidden = false;
    document.body.classList.add('modal-open');
    requestAnimationFrame(() => modal.classList.add('is-open'));
    const f = modal.querySelector('input');
    if (f) setTimeout(() => f.focus(), 60);
  };
  const close = () => {
    modal.classList.remove('is-open');
    document.body.classList.remove('modal-open');
    setTimeout(() => { modal.hidden = true; if (lastFocus) lastFocus.focus(); }, 280);
  };
  document.querySelectorAll('[data-modal-open]').forEach(el => {
    el.addEventListener('click', (e) => { e.preventDefault(); open(); });
  });
  modal.querySelectorAll('[data-modal-close]').forEach(el => {
    el.addEventListener('click', close);
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !modal.hidden) close();
  });
})();

// ============================================================
// Мобильные слайдеры (стрелки, точки, центрирование активного слайда)
// ============================================================
(function () {
  const mq = window.matchMedia('(max-width: 768px)');

  function initCarousel(track, nav, slideSelector) {
    if (!track || !nav) return;
    const prevBtn = nav.querySelector('.svc-arrow--prev');
    const nextBtn = nav.querySelector('.svc-arrow--next');
    const dotsWrap = nav.querySelector('.svc-dots');
    const slides = Array.prototype.slice.call(track.querySelectorAll(slideSelector));
    if (!slides.length) return;

    const dots = slides.map((_, i) => {
      const d = document.createElement('button');
      d.type = 'button';
      d.className = 'svc-dot';
      d.setAttribute('aria-label', 'Слайд ' + (i + 1));
      d.addEventListener('click', () => centerSlide(i));
      dotsWrap.appendChild(d);
      return d;
    });

    function activeIndex() {
      const max = track.scrollWidth - track.clientWidth;
      if (max <= 1) return 0;
      return Math.round((track.scrollLeft / max) * (slides.length - 1));
    }
    function centerSlide(i, behavior) {
      i = Math.max(0, Math.min(slides.length - 1, i));
      const s = slides[i];
      const sr = s.getBoundingClientRect();
      const tr = track.getBoundingClientRect();
      const delta = (sr.left - tr.left) - (track.clientWidth - sr.width) / 2;
      track.scrollTo({ left: track.scrollLeft + delta, behavior: behavior || 'smooth' });
    }
    function update() {
      const idx = activeIndex();
      dots.forEach((d, i) => d.classList.toggle('is-active', i === idx));
    }
    if (prevBtn) prevBtn.addEventListener('click', () => centerSlide(activeIndex() - 1));
    if (nextBtn) nextBtn.addEventListener('click', () => centerSlide(activeIndex() + 1));
    let raf;
    track.addEventListener('scroll', () => {
      if (raf) cancelAnimationFrame(raf);
      raf = requestAnimationFrame(update);
    }, { passive: true });

    // На мобильных задаём ширину слайда и боковые поля в пикселях,
    // чтобы активный слайд (включая первый) был ровно по центру. На десктопе — сбрасываем.
    function layoutSlider() {
      if (!mq.matches) {
        track.style.paddingLeft = track.style.paddingRight = '';
        slides.forEach(s => { s.style.flex = ''; });
        return;
      }
      // слайд во всю ширину контента (как у остальных карточек) — без увеличенных боковых полей
      track.style.paddingLeft = track.style.paddingRight = '0px';
      const slideW = track.clientWidth;
      slides.forEach(s => { s.style.flex = '0 0 ' + slideW + 'px'; });
      centerSlide(activeIndex(), 'auto');
    }
    layoutSlider();
    window.addEventListener('load', layoutSlider);
    let rt;
    window.addEventListener('resize', () => {
      clearTimeout(rt);
      rt = setTimeout(layoutSlider, 150);
    });
    update();
  }

  initCarousel(
    document.querySelector('#services .process-flow'),
    document.querySelector('#services .svc-nav'),
    '.pf-stage'
  );
  initCarousel(
    document.querySelector('#process .steps'),
    document.querySelector('#process .svc-nav'),
    '.step'
  );
  initCarousel(
    document.querySelector('#advantages .steps'),
    document.querySelector('#advantages .svc-nav'),
    '.step'
  );
})();
