<?php
/** Рендер секции «Проблема / Решение». Вёрстка 1:1, тексты из ACF. */
if (!defined('ABSPATH')) exit;
$d = require __DIR__ . '/defaults.php';
?>
<section class="section section--alt" id="problem">
  <div class="wrap">
    <div class="section__head">
      <div class="section__head-text">
        <h2 class="section__title"><?php echo bavar_field('problem_title', $d); ?></h2>
        <p class="section__lede">
          <?php echo bavar_field('problem_lede', $d); ?>
        </p>
      </div>
    </div>

    <div class="compare-grid">
      <div class="cmp-card cmp-card--problem">
        <div class="cmp-card__head">
          <div class="cmp-card__icon">!</div>
          <div class="cmp-card__label"><?php echo bavar_field('problem_p_label', $d); ?></div>
        </div>
        <h3 class="cmp-card__title"><?php echo bavar_field('problem_p_title', $d); ?></h3>
        <div class="cmp-card__visual-frame">
          <img class="cmp-card__visual cmp-card__visual--keyed lz" data-src="assets/minus.png?v=1" alt="Шесть отдельных договоров с разными подрядчиками — каждая зона ответственности своя">
        </div>
        <ul class="cmp-card__list">
          <li><?php echo bavar_field('problem_p_li1', $d); ?></li>
          <li><?php echo bavar_field('problem_p_li2', $d); ?></li>
          <li><?php echo bavar_field('problem_p_li3', $d); ?></li>
          <li><?php echo bavar_field('problem_p_li4', $d); ?></li>
          <li><?php echo bavar_field('problem_p_li5', $d); ?></li>
          <li><?php echo bavar_field('problem_p_li6', $d); ?></li>
        </ul>
      </div>
      <div class="cmp-card cmp-card--solution">
        <div class="cmp-card__head">
          <div class="cmp-card__icon">✓</div>
          <div class="cmp-card__label">Когда ВЭД ведёт <span class="brand-inline">Бавар<span class="brand-inline__plus">+</span></span></div>
        </div>
        <h3 class="cmp-card__title"><?php echo bavar_field('problem_s_title', $d); ?></h3>
        <img class="cmp-card__visual lz" data-src="assets/plus.png?v=2" alt="Один договор BAVAR+ — все услуги от поиска поставщика до передачи груза">
        <ul class="cmp-card__list">
          <li><?php echo bavar_field('problem_s_li1', $d); ?></li>
          <li><?php echo bavar_field('problem_s_li2', $d); ?></li>
          <li><?php echo bavar_field('problem_s_li3', $d); ?></li>
          <li><?php echo bavar_field('problem_s_li4', $d); ?></li>
          <li><?php echo bavar_field('problem_s_li5', $d); ?></li>
          <li><?php echo bavar_field('problem_s_li6', $d); ?></li>
        </ul>
      </div>
    </div>
  </div>
</section>
