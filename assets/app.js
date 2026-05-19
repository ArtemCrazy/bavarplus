/* ============================================================
   Бавар+ · скетч-прототип · скрипты
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
    const btn = form.querySelector('.form-submit');
    const old = btn.textContent;
    btn.textContent = '✓ Заявка отправлена';
    btn.disabled = true;
    setTimeout(() => { btn.textContent = old; btn.disabled = false; form.reset(); }, 2400);
  });
}

// ============================================================
// To-top button
// ============================================================
const toTopBtn = document.getElementById('toTop');
if (toTopBtn) {
  window.addEventListener('scroll', () => {
    toTopBtn.classList.toggle('show', window.scrollY > window.innerHeight);
  }, { passive: true });
}
