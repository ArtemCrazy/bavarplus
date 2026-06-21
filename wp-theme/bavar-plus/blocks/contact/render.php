<?php
/** Рендер секции «Контакты». Вёрстка/иконки/форма 1:1, тексты из ACF.
 *  ID формы и полей сохранены (app.js обрабатывает отправку). */
if (!defined('ABSPATH')) exit;
$d = require __DIR__ . '/defaults.php';
?>
<section class="section" id="contact">
  <div class="wrap">
    <div class="contact2">
      <div class="contact2__left">
        <h2 class="contact2__title"><?php echo bavar_field('contact_title', $d); ?></h2>
        <p class="contact2__lede"><?php echo bavar_field('contact_lede', $d); ?></p>

        <ul class="contact2__list">
          <li class="contact2__item">
            <span class="contact2__ic">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.6 2.7a2 2 0 0 1-.5 2.1L8 9.6a16 16 0 0 0 6 6l1.1-1.1a2 2 0 0 1 2.1-.5c.9.3 1.8.5 2.7.6a2 2 0 0 1 1.7 2z"/></svg>
            </span>
            <span class="contact2__meta">
              <span class="contact2__lbl"><?php echo bavar_field('contact_phone_lbl', $d); ?></span>
              <a class="contact2__val" href="<?php echo bavar_field('contact_phone_href', $d); ?>"><?php echo bavar_field('contact_phone_val', $d); ?></a>
            </span>
          </li>
          <li class="contact2__item">
            <span class="contact2__ic">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
            </span>
            <span class="contact2__meta">
              <span class="contact2__lbl"><?php echo bavar_field('contact_email_lbl', $d); ?></span>
              <a class="contact2__val" href="<?php echo bavar_field('contact_email_href', $d); ?>"><?php echo bavar_field('contact_email_val', $d); ?></a>
            </span>
          </li>
          <li class="contact2__item">
            <span class="contact2__ic">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3 2 10.5l6 2.2L18 6l-7.5 9.2.3 4.3 3-3.6 4.2 3z"/></svg>
            </span>
            <span class="contact2__meta">
              <span class="contact2__lbl"><?php echo bavar_field('contact_tg_lbl', $d); ?></span>
              <a class="contact2__val" href="<?php echo bavar_field('contact_tg_href', $d); ?>" target="_blank" rel="noopener"><?php echo bavar_field('contact_tg_val', $d); ?></a>
            </span>
          </li>
        </ul>

      </div>

      <form class="cform" id="contact-form" novalidate>
        <div class="cform__deco" aria-hidden="true"></div>
        <h3 class="cform__title"><?php echo bavar_field('cform_title', $d); ?></h3>
        <p class="cform__sub"><?php echo bavar_field('cform_sub', $d); ?></p>
        <input class="cform__input" type="text" id="f-name" name="name" placeholder="<?php echo esc_attr(bavar_field('cform_name_ph', $d)); ?>" required>
        <input class="cform__input" type="tel" id="f-phone" name="phone" placeholder="<?php echo esc_attr(bavar_field('cform_phone_ph', $d)); ?>" required>
        <label class="cform__check">
          <input type="checkbox" id="f-agree" required>
          <span><?php echo bavar_field('cform_agree', $d); ?></span>
        </label>
        <button type="submit" class="cform__btn"><?php echo bavar_field('cform_btn', $d); ?></button>
      </form>
    </div>
  </div>
</section>
