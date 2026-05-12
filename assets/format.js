// Shared helpers across all calculator prototypes.

const RUB = new Intl.NumberFormat('ru-RU', {
  style: 'currency',
  currency: 'RUB',
  maximumFractionDigits: 0,
});

const NUM = new Intl.NumberFormat('ru-RU', { maximumFractionDigits: 0 });

function fmtRub(n) {
  if (!isFinite(n)) return '—';
  return RUB.format(Math.round(n));
}

function fmtNum(n) {
  if (!isFinite(n)) return '—';
  return NUM.format(Math.round(n));
}

// Pluralization for Russian numbers (e.g. 1 человек / 2 человека / 5 человек).
function plural(n, forms) {
  const abs = Math.abs(n) % 100;
  const n1 = abs % 10;
  if (abs > 10 && abs < 20) return forms[2];
  if (n1 > 1 && n1 < 5) return forms[1];
  if (n1 === 1) return forms[0];
  return forms[2];
}

// Бавар+ tariffs — placeholders for prototype demo only.
const BAVAR_TARIFFS = {
  light:   { name: 'Бавар+ Лайт',    price: 300000, desc: 'Точечные сделки, базовая поддержка' },
  standard:{ name: 'Бавар+ Стандарт',price: 500000, desc: 'Постоянные поставки, выделенный менеджер' },
  premium: { name: 'Бавар+ Максимум',price: 800000, desc: 'Полный аутсорсинг ВЭД под ключ' },
};

// Typical in-house ВЭД cost line items (gross monthly, ₽).
const INHOUSE_LINES = [
  { id: 'head',     name: 'Руководитель отдела ВЭД',     monthly: 280000, default: true },
  { id: 'manager',  name: 'Менеджеры ВЭД (×2)',          monthly: 320000, default: true },
  { id: 'logist',   name: 'Логист',                      monthly: 130000, default: true },
  { id: 'declarant',name: 'Декларант / таможенник',      monthly: 150000, default: true },
  { id: 'lawyer',   name: 'Юрист по ВЭД',                monthly: 120000, default: true },
  { id: 'translator',name:'Переводчик (китайский)',      monthly: 140000, default: true },
  { id: 'taxes',    name: 'Страховые взносы (≈30% ФОТ)', monthly: 348000, default: true },
  { id: 'office',   name: 'Рабочие места, оборудование', monthly: 60000,  default: true },
  { id: 'software', name: 'ПО, связь, ВЭД-сервисы',      monthly: 35000,  default: true },
  { id: 'travel',   name: 'Командировки в Китай',        monthly: 180000, default: true },
  { id: 'risk',     name: 'Риски: штрафы за ошибки',     monthly: 80000,  default: false },
];
