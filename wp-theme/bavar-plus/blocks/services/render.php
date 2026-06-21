<?php
/** Рендер секции «Услуги» (зигзаг). Вёрстка/SVG/слайдер 1:1, тексты из ACF. */
if (!defined('ABSPATH')) exit;
$d = require __DIR__ . '/defaults.php';

$cards = [
    ['n' => '01', 'dir' => 'down', 'left' => '8.333%',  'img' => 'svc-01.png?v=1', 'alt' => 'Аудит по всему Китаю'],
    ['n' => '02', 'dir' => 'up',   'left' => '25%',      'img' => 'svc-02.png?v=1', 'alt' => 'Выкуп и контроль качества'],
    ['n' => '03', 'dir' => 'down', 'left' => '41.667%',  'img' => 'svc-03.png?v=1', 'alt' => 'Операционное сопровождение'],
    ['n' => '04', 'dir' => 'up',   'left' => '58.333%',  'img' => 'svc-04.png?v=1', 'alt' => 'Логистика и консолидация'],
    ['n' => '05', 'dir' => 'down', 'left' => '75%',      'img' => 'svc-05.png?v=1', 'alt' => 'Таможенное оформление'],
    ['n' => '06', 'dir' => 'up',   'left' => '91.667%',  'img' => 'svc-06.png?v=1', 'alt' => 'Сертификация и документы'],
];
?>
<section class="section" id="services">
  <div class="wrap">
    <div class="section__head">
      <div class="section__head-text">
        <h2 class="section__title"><?php echo bavar_field('services_title', $d); ?></h2>
        <p class="section__lede">
          <?php echo bavar_field('services_lede', $d); ?>
        </p>
      </div>
    </div>

    <div class="process-flow">
      <svg class="pf-svg" viewBox="0 0 1200 600" preserveAspectRatio="none" aria-hidden="true">
        <path class="pf-line" d="M100 360 L300 240 L500 360 L700 240 L900 360 L1100 240" vector-effect="non-scaling-stroke"/>
      </svg>
      <span class="pf-dot" style="left:8.333%;top:60%"></span>
      <span class="pf-dot" style="left:25%;top:40%"></span>
      <span class="pf-dot" style="left:41.667%;top:60%"></span>
      <span class="pf-dot" style="left:58.333%;top:40%"></span>
      <span class="pf-dot" style="left:75%;top:60%"></span>
      <span class="pf-dot" style="left:91.667%;top:40%"></span>

      <?php foreach ($cards as $i => $c): $k = $i + 1; ?>
      <div class="pf-stage pf-stage--<?php echo $c['dir']; ?>" style="left:<?php echo $c['left']; ?>">
        <div class="pf-card">
          <div class="pf-card__num"><?php echo $c['n']; ?></div>
          <h3 class="pf-card__title"><?php echo bavar_field("svc{$k}_title", $d); ?></h3>
          <p class="pf-card__desc"><?php echo bavar_field("svc{$k}_desc", $d); ?></p>
        </div>
        <div class="pf-img"><img src="assets/<?php echo $c['img']; ?>" alt="<?php echo $c['alt']; ?>"></div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="svc-nav" aria-hidden="true">
      <button class="svc-arrow svc-arrow--prev" type="button" aria-label="Назад"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></svg></button>
      <div class="svc-dots"></div>
      <button class="svc-arrow svc-arrow--next" type="button" aria-label="Вперёд"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5l7 7-7 7"/></svg></button>
    </div>

  </div>
</section>
