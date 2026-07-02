<?php
/** Рендер секции «Отрасли». Вёрстка/иллюстрации 1:1, тексты из ACF. */
if (!defined('ABSPATH')) exit;
$d = require __DIR__ . '/defaults.php';

$cards = [
    ['n' => '01', 'icon' => 'ind-eng.png?v=2'],
    ['n' => '02', 'icon' => 'ind-mount.png?v=2'],
    ['n' => '03', 'icon' => 'ind-launch.png?v=2'],
    ['n' => '04', 'icon' => 'ind-service.png?v=2'],
];
?>
<section class="section section--alt" id="industries">
  <div class="wrap">
    <div class="bio">
      <div class="bio__intro">
        <h2 class="bio__title"><?php echo bavar_field('ind_title', $d); ?></h2>
        <p class="bio__lede">
          <?php echo bavar_field('ind_lede', $d); ?>
        </p>
        <img class="bio__illo lz" data-src="assets/bio-line.png?v=3" alt="Производственная биотех-линия">
      </div>

      <div class="bio__cards">
        <?php foreach ($cards as $i => $c): $k = $i + 1; ?>
        <div class="bio-card">
          <div class="bio-card__head">
            <div class="bio-card__icon"><img class="lz" data-src="assets/<?php echo $c['icon']; ?>" alt=""></div>
            <div class="bio-card__heading">
              <div class="bio-card__num"><?php echo $c['n']; ?></div>
              <h4 class="bio-card__title"><?php echo bavar_field("ind{$k}_title", $d); ?></h4>
            </div>
          </div>
          <p class="bio-card__desc"><?php echo bavar_field("ind{$k}_desc", $d); ?></p>
          <span class="bio-card__arrow" aria-hidden="true">→</span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <p class="bio-note">
      <?php echo bavar_field('ind_note', $d); ?>
    </p>
  </div>
</section>
