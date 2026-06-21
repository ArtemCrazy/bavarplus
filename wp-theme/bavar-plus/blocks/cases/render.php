<?php
/** Рендер секции «Кейсы». Карусель/SVG-иконки 1:1, тексты из ACF. */
if (!defined('ABSPATH')) exit;
$d = require __DIR__ . '/defaults.php';

// Контуры иконок метрик (внутренности SVG); обёртка одинаковая.
$ic = [
    'clock'       => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
    'check'       => '<path d="M20 7L9 18l-5-5"/>',
    'trend'       => '<path d="M3 17l6-6 4 4 8-8"/><path d="M17 7h4v4"/>',
    'shield'      => '<path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/>',
    'ruble'       => '<path d="M12 2v20M17 6H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
    'checkcircle' => '<circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/>',
];
$cases = [
    ['img' => 'case-dairy.jpg?v=1',      'm' => ['clock', 'check', 'trend']],
    ['img' => 'case-industrial.jpg?v=1', 'm' => ['clock', 'shield', 'trend']],
    ['img' => 'case-cosmetics.jpg?v=1',  'm' => ['clock', 'check', 'trend']],
    ['img' => 'case-pharma.jpg?v=1',     'm' => ['clock', 'shield', 'ruble']],
    ['img' => 'case-beverages.jpg?v=1',  'm' => ['clock', 'checkcircle', 'ruble']],
];
$svg = function ($paths) {
    return '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">' . $paths . '</svg>';
};
?>
<section class="section" id="cases">
  <div class="wrap">
    <div class="section__head">
      <div class="section__head-text">
        <h2 class="section__title"><?php echo bavar_field('cases_title', $d); ?></h2>
        <p class="section__lede">
          <?php echo bavar_field('cases_lede', $d); ?>
        </p>
      </div>
    </div>

    <div class="cases">
      <button class="cases__arrow cases__arrow--prev" type="button" aria-label="Назад"><svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></svg></button>
      <button class="cases__arrow cases__arrow--next" type="button" aria-label="Вперёд"><svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5l7 7-7 7"/></svg></button>

      <div class="cases__track">
        <?php foreach ($cases as $i => $c): $k = $i + 1; ?>
        <article class="case">
          <div class="case__photo">
            <img class="case__img lz" data-src="assets/<?php echo $c['img']; ?>" alt="">
            <span class="case__tag"><?php echo bavar_field("case{$k}_tag", $d); ?></span>
          </div>
          <div class="case__body">
            <p class="case__text"><?php echo bavar_field("case{$k}_text", $d); ?></p>
            <div class="case__metrics">
              <?php for ($m = 1; $m <= 3; $m++): ?>
              <div class="case__metric">
                <span class="case__metric-ic"><?php echo $svg($ic[$c['m'][$m - 1]]); ?></span>
                <span class="case__metric-val"><?php echo bavar_field("case{$k}_m{$m}_val", $d); ?></span>
                <span class="case__metric-lbl"><?php echo bavar_field("case{$k}_m{$m}_lbl", $d); ?></span>
              </div>
              <?php endfor; ?>
            </div>
          </div>
        </article>
        <?php endforeach; ?>
      </div>

      <div class="cases__dots" aria-hidden="true"></div>
    </div>
  </div>
</section>
