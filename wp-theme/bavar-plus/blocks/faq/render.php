<?php
/**
 * Рендер блока FAQ. Разметка 1:1 с эталоном; тексты — из полей ACF
 * (с откатом на значения по умолчанию). Вёрстка/классы залочены.
 */
if (!defined('ABSPATH')) exit;

$d = require __DIR__ . '/defaults.php';
?>
<section class="section section--alt" id="faq">
  <div class="wrap">
    <div class="section__head">
      <div class="section__head-text">
        <h2 class="section__title"><?php echo bavar_field('faq_title', $d); ?></h2>
        <p class="section__lede"><?php echo bavar_field('faq_lede', $d); ?></p>
      </div>
    </div>

    <div class="faq-list">
      <?php for ($i = 1; $i <= 6; $i++):
          $q = bavar_field("faq_q$i", $d);
          $a = bavar_field("faq_a$i", $d);
          if ($q === '') continue; ?>
      <details class="faq-item">
        <summary><?php echo $q; ?></summary>
        <div class="faq-item__body"><div class="faq-item__inner">
          <?php echo $a; ?>
        </div></div>
      </details>
      <?php endfor; ?>
    </div>
  </div>
</section>
