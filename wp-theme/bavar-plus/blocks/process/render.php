<?php
/** Рендер секции «Схема работы». Вёрстка/слайдер/кран 1:1, тексты из ACF. */
if (!defined('ABSPATH')) exit;
$d = require __DIR__ . '/defaults.php';
?>
<section class="section" id="process">
  <div class="wrap">
    <div class="section__head">
      <div class="section__head-text">
        <h2 class="section__title"><?php echo bavar_field('process_title', $d); ?></h2>
        <p class="section__lede">
          <?php echo bavar_field('process_lede', $d); ?>
        </p>
        <p class="section__lede process__intro">
          <?php echo bavar_field('process_intro', $d); ?>
        </p>
      </div>
    </div>

    <div class="steps">
      <?php for ($i = 1; $i <= 6; $i++): ?>
      <div class="step" data-step="<?php echo $i; ?>">
        <h3 class="step__title"><?php echo bavar_field("step{$i}_title", $d); ?></h3>
        <p class="step__desc"><?php echo bavar_field("step{$i}_desc", $d); ?></p>
        <p class="step__result"><strong>Результат:</strong> <?php echo bavar_field("step{$i}_result", $d); ?></p>
      </div>
      <?php endfor; ?>
    </div>

    <div class="svc-nav" aria-hidden="true">
      <button class="svc-arrow svc-arrow--prev" type="button" aria-label="Назад"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></svg></button>
      <div class="svc-dots"></div>
      <button class="svc-arrow svc-arrow--next" type="button" aria-label="Вперёд"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5l7 7-7 7"/></svg></button>
    </div>

    <div class="process-crane" aria-hidden="true">
      <img class="process-crane__kran lz" data-src="assets/kran.png?v=5" alt="">
      <img class="process-crane__box lz" data-src="assets/conteiner.png?v=5" alt="">
    </div>
  </div>
</section>
