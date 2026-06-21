<?php
/** Рендер секции «Калькулятор». Вся логика/ID/ползунки 1:1 (app.js),
 *  редактируются только статичные подписи. */
if (!defined('ABSPATH')) exit;
$d = require __DIR__ . '/defaults.php';
?>
<section class="section section--alt" id="calc">
  <div class="wrap">
    <div class="section__head">
      <div class="section__head-text">
        <h2 class="section__title"><?php echo bavar_field('calc_title', $d); ?></h2>
        <p class="section__lede">
          <?php echo bavar_field('calc_lede', $d); ?>
        </p>
      </div>
    </div>

    <div class="calc">
      <div class="calc__panel">
        <div class="calc__panel-tag"><?php echo bavar_field('calc_panel_tag', $d); ?></div>
        <div class="calc__field">
          <div class="calc__lbl">
            <span><?php echo bavar_field('calc_f1_label', $d); ?></span>
            <span class="lbl-val" id="cl-staff">7 человек</span>
          </div>
          <input type="range" class="calc__range" id="cf-staff" min="6" max="14" value="7" step="1">
          <div class="calc__hint"><?php echo bavar_field('calc_f1_hint', $d); ?></div>
        </div>
        <div class="calc__field">
          <div class="calc__lbl">
            <span><?php echo bavar_field('calc_f2_label', $d); ?></span>
            <span class="lbl-val" id="cl-salary">140 000 ₽</span>
          </div>
          <input type="range" class="calc__range" id="cf-salary" min="80000" max="250000" value="140000" step="5000">
          <div class="calc__hint"><?php echo bavar_field('calc_f2_hint', $d); ?></div>
        </div>
        <div class="calc__field">
          <div class="calc__lbl">
            <span><?php echo bavar_field('calc_f3_label', $d); ?></span>
            <span class="lbl-val" id="cl-deals">8 сделок</span>
          </div>
          <input type="range" class="calc__range" id="cf-deals" min="1" max="30" value="8" step="1">
          <div class="calc__hint"><?php echo bavar_field('calc_f3_hint', $d); ?></div>
        </div>

        <p class="calc__note">
          <?php echo bavar_field('calc_note', $d); ?>
        </p>
      </div>

      <div class="calc__panel calc__panel--result">
        <div class="calc__panel-tag"><?php echo bavar_field('calc_result_tag', $d); ?></div>
        <div class="calc__hero" id="cr-hero">—</div>
        <div class="calc__hero-sub" id="cr-hero-sub">—</div>
        <div class="calc__breakdown-cap"><?php echo bavar_field('calc_breakdown_cap', $d); ?></div>
        <ul class="calc__breakdown">
          <li>
            <span class="br-ic"><img class="lz" data-src="assets/calc-fot.png?v=1" alt=""></span>
            <span class="nm"><?php echo bavar_field('calc_br1_name', $d); ?></span><span class="vl" id="br-fot">—</span>
          </li>
          <li>
            <span class="br-ic"><img class="lz" data-src="assets/calc-taxes.png?v=1" alt=""></span>
            <span class="nm"><?php echo bavar_field('calc_br2_name', $d); ?></span><span class="vl" id="br-taxes">—</span>
          </li>
          <li>
            <span class="br-ic"><img class="lz" data-src="assets/calc-office.png?v=1" alt=""></span>
            <span class="nm"><?php echo bavar_field('calc_br3_name', $d); ?></span><span class="vl" id="br-office">—</span>
          </li>
          <li>
            <span class="br-ic"><img class="lz" data-src="assets/calc-travel.png?v=1" alt=""></span>
            <span class="nm"><?php echo bavar_field('calc_br4_name', $d); ?></span><span class="vl" id="br-shanghai">—</span>
          </li>
          <li>
            <span class="br-ic"><img class="lz" data-src="assets/calc-risk.png?v=1" alt=""></span>
            <span class="nm"><?php echo bavar_field('calc_br5_name', $d); ?></span><span class="vl" id="br-risk">—</span>
          </li>
        </ul>
        <div class="calc__pills">
          <div class="calc__pill"><span class="nm"><?php echo bavar_field('calc_pill1_name', $d); ?></span><span class="vl" id="br-total">—</span></div>
          <div class="calc__pill calc__pill--bavar"><span class="nm"><?php echo bavar_field('calc_pill2_name', $d); ?></span><span class="vl" id="br-bavar">—</span></div>
        </div>
        <div class="calc__cta">
          <a href="#contact" class="btn btn--primary"><?php echo bavar_field('calc_cta', $d); ?> <span class="btn__arrow">→</span></a>
        </div>
      </div>
    </div>

    <div class="calc-alert">
      <div class="calc-alert__head">
        <span class="calc-alert__ic">!</span>
        <?php echo bavar_field('calc_alert_head', $d); ?>
      </div>
      <ul class="calc-alert__list">
        <li><?php echo bavar_field('calc_alert_li1', $d); ?></li>
        <li><?php echo bavar_field('calc_alert_li2', $d); ?></li>
        <li><?php echo bavar_field('calc_alert_li3', $d); ?></li>
      </ul>
    </div>

    <div class="calc-sticky" aria-hidden="true">
      <span class="calc-sticky__lbl"><?php echo bavar_field('calc_sticky_lbl', $d); ?></span>
      <span class="calc-sticky__val" id="cr-sticky">—</span>
    </div>
  </div>
</section>
