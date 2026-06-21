<?php
/** Рендер секции «Формы сотрудничества». Вёрстка/таблица 1:1, тексты из ACF. */
if (!defined('ABSPATH')) exit;
$d = require __DIR__ . '/defaults.php';
?>
<section class="section section--alt" id="cooperation">
  <div class="wrap">
    <div class="section__head">
      <div class="section__head-text">
        <h2 class="section__title"><?php echo bavar_field('coop_title', $d); ?></h2>
        <p class="section__lede"><?php echo bavar_field('coop_lede', $d); ?></p>
        <p class="section__lede coop__note"><?php echo bavar_field('coop_note', $d); ?></p>
      </div>
    </div>

    <div class="forms">
      <div class="form-card form-card--primary">
        <div class="form-card__name"><?php echo bavar_field('coop_c1_name', $d); ?></div>
        <p class="form-card__tagline"><?php echo bavar_field('coop_c1_tagline', $d); ?></p>
        <p class="form-card__text"><?php echo bavar_field('coop_c1_text', $d); ?></p>
        <ul class="form-card__list">
          <?php for ($i = 1; $i <= 6; $i++): ?><li><?php echo bavar_field("coop_c1_li{$i}", $d); ?></li><?php endfor; ?>
        </ul>
        <div class="form-card__fit"><?php echo bavar_field('coop_c1_fit', $d); ?></div>
      </div>
      <div class="form-card">
        <div class="form-card__name"><?php echo bavar_field('coop_c2_name', $d); ?></div>
        <p class="form-card__tagline"><?php echo bavar_field('coop_c2_tagline', $d); ?></p>
        <p class="form-card__text"><?php echo bavar_field('coop_c2_text', $d); ?></p>
        <ul class="form-card__list">
          <?php for ($i = 1; $i <= 6; $i++): ?><li><?php echo bavar_field("coop_c2_li{$i}", $d); ?></li><?php endfor; ?>
        </ul>
        <div class="form-card__fit"><?php echo bavar_field('coop_c2_fit', $d); ?></div>
      </div>
    </div>

    <h3 class="forms-compare-title"><?php echo bavar_field('coop_compare_title', $d); ?></h3>
    <div class="forms-compare-wrap">
      <table class="forms-compare">
        <thead>
          <tr>
            <th><?php echo bavar_field('coop_th_param', $d); ?></th>
            <th><?php echo bavar_field('coop_th_1', $d); ?></th>
            <th><?php echo bavar_field('coop_th_2', $d); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php for ($r = 1; $r <= 4; $r++):
              $cls = ($r === 4) ? ' class="price"' : ''; ?>
          <tr>
            <th><?php echo bavar_field("coop_row{$r}_label", $d); ?></th>
            <td<?php echo $cls; ?>><?php echo bavar_field("coop_row{$r}_a", $d); ?></td>
            <td<?php echo $cls; ?>><?php echo bavar_field("coop_row{$r}_b", $d); ?></td>
          </tr>
          <?php endfor; ?>
        </tbody>
      </table>
    </div>
    <p class="forms-compare-hint" aria-hidden="true"><?php echo bavar_field('coop_compare_hint', $d); ?></p>
  </div>
</section>
