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
    '.bio__intro', '.bio-card', '.bio-notes',
    '.step', '.adv', '.calc__panel',
    '.case', '.faq-item', '.contact2', '.shanghai'
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
        e.target.classList.add('is-visible');
        io.unobserve(e.target);
      }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
  els.forEach(el => io.observe(el));
})();

// ============================================================
// Process-flow (блок 04): показываем плейсхолдер, пока нет PNG
// ============================================================
document.querySelectorAll('.pf-img img').forEach(img => {
  const wrap = img.closest('.pf-img');
  if (!wrap) return;
  const markLoaded = () => wrap.classList.add('is-loaded');
  const markFailed = () => { img.style.display = 'none'; };
  if (img.complete) {
    if (img.naturalWidth > 0) markLoaded(); else markFailed();
  }
  img.addEventListener('load', markLoaded);
  img.addEventListener('error', markFailed);
});

// ============================================================
// Calculator
// ============================================================
const BAVAR_FIXED = 300_000;
const TAX_RATE = 0.30;
const OFFICE_PER_PERSON = 8_000;
const SOFTWARE_BASE = 35_000;
const TRAVEL_PER_DEAL = 22_000;
const RISK_PER_DEAL = 10_000;

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
  brkTravel: document.getElementById('br-travel'),
  brkRisk: document.getElementById('br-risk'),
  brkTotal: document.getElementById('br-total'),
  brkBavar: document.getElementById('br-bavar'),
  barOwn: document.getElementById('bar-own'),
  barBavar: document.getElementById('bar-bavar'),
};

function recalc() {
  if (!calcEls.staff) return;
  const staff = +calcEls.staff.value;
  const salary = +calcEls.salary.value;
  const deals = +calcEls.deals.value;

  const fot = staff * salary;
  const taxes = fot * TAX_RATE;
  const office = staff * OFFICE_PER_PERSON + SOFTWARE_BASE;
  const travel = deals * TRAVEL_PER_DEAL;
  const risk = deals * RISK_PER_DEAL;
  const inhouse = fot + taxes + office + travel + risk;
  const savings = Math.max(0, inhouse - BAVAR_FIXED);

  calcEls.lblStaff.textContent = staff + ' ' + plural(staff, ['человек','человека','человек']);
  calcEls.lblSalary.textContent = fmtRub(salary);
  calcEls.lblDeals.textContent = deals + ' ' + plural(deals, ['сделка','сделки','сделок']);

  calcEls.brkFot.textContent = fmtRub(fot);
  calcEls.brkTaxes.textContent = fmtRub(taxes);
  calcEls.brkOffice.textContent = fmtRub(office);
  calcEls.brkTravel.textContent = fmtRub(travel);
  calcEls.brkRisk.textContent = fmtRub(risk);
  calcEls.brkTotal.textContent = fmtRub(inhouse);
  calcEls.brkBavar.textContent = fmtRub(BAVAR_FIXED);

  // Полосы-бары: ширина пропорциональна сумме (макс = свой отдел)
  const maxVal = Math.max(inhouse, BAVAR_FIXED, 1);
  if (calcEls.barOwn)   calcEls.barOwn.style.width   = (inhouse / maxVal * 100) + '%';
  if (calcEls.barBavar) calcEls.barBavar.style.width = (BAVAR_FIXED / maxVal * 100) + '%';

  calcEls.heroSavings.textContent = fmtShort(savings);
  calcEls.heroSub.textContent = `в месяц · ${fmtShort(savings * 12)} в год`;
}

['staff','salary','deals'].forEach(k => {
  if (calcEls[k]) calcEls[k].addEventListener('input', recalc);
});
recalc();

// ============================================================
// Form (prototype only)
// ============================================================
const form = document.getElementById('contact-form');
if (form) {
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const btn = form.querySelector('button[type=submit]');
    const old = btn.textContent;
    btn.textContent = '✓ Заявка отправлена';
    btn.disabled = true;
    setTimeout(() => { btn.textContent = old; btn.disabled = false; form.reset(); }, 2400);
  });
}
