<?php
/**
 * BavarSwiss (главный сайт) — bootstrap темы.
 *
 * Этап 1: тема отдаёт перенесённую с NetHouse страницу 1:1 из main.html.
 * Этап 2: ключевые тексты выводятся в админку через ACF (группа «Тексты главной
 * BavarSwiss» на главной странице). Механизм: в main.html ищем исходный текст и
 * подменяем его значением поля, если оно изменено (дефолт = исходный текст).
 * Поля и дефолты генерируются из main.html скриптом build-fields.py.
 * Форма «Отправить сообщение» собрана своя (модалка + AJAX), т.к. оригинал был
 * на бэкенде NetHouse.
 */
if (!defined('ABSPATH')) {
    exit;
}

define('BAVARSWISS_VERSION', '1.0.0');

/** Карта полей: name => ['label'=>..., 'default'=>...] (из inc/main-fields.php). */
function bavarswiss_fields() {
    static $f = null;
    if ($f === null) {
        $file = get_template_directory() . '/inc/main-fields.php';
        $f = file_exists($file) ? include $file : [];
    }
    return $f;
}

/** ACF-группа «Тексты главной BavarSwiss», привязанная к главной странице. */
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    $fields = [];
    foreach (bavarswiss_fields() as $name => $info) {
        $def = isset($info['default']) ? $info['default'] : '';
        $fields[] = [
            'key'           => 'field_' . $name,
            'label'         => isset($info['label']) ? $info['label'] : $name,
            'name'          => $name,
            'type'          => (mb_strlen($def) > 50 ? 'textarea' : 'text'),
            'default_value' => $def,
            'rows'          => 3,
        ];
    }
    // Телефон и e-mail (правят и текст, и ссылку tel:/mailto: — спец-обработка в рендере).
    $fields[] = [
        'key' => 'field_main_phone', 'label' => 'Контакты · телефон',
        'name' => 'main_phone', 'type' => 'text', 'default_value' => '+7 495 2220706',
    ];
    $fields[] = [
        'key' => 'field_main_email', 'label' => 'Контакты · e-mail',
        'name' => 'main_email', 'type' => 'text', 'default_value' => 'info@bavarswiss.ru',
    ];
    // Служебное поле: получатель заявок с формы.
    $fields[] = [
        'key'           => 'field_main_form_recipient',
        'label'         => 'Куда отправлять сообщения с формы (email)',
        'name'          => 'main_form_recipient',
        'type'          => 'text',
        'default_value' => 'artem@crazy.studio',
        'instructions'  => 'Получатель писем с формы «Отправить сообщение». Можно несколько через запятую.',
    ];
    if (!$fields) {
        return;
    }
    acf_add_local_field_group([
        'key'            => 'group_bavarswiss_main',
        'title'          => 'Тексты главной BavarSwiss',
        'fields'         => $fields,
        'location'       => [[['param' => 'page_type', 'operator' => '==', 'value' => 'front_page']]],
        'hide_on_screen' => ['the_content'],
        'menu_order'     => 0,
        'description'    => 'Тексты главной страницы. Вёрстка фиксирована.',
    ]);
});

/** Классический редактор для страниц — чтобы поля ACF были на виду. */
add_filter('use_block_editor_for_post_type', function ($use, $post_type) {
    return $post_type === 'page' ? false : $use;
}, 10, 2);

/** Быстрый пункт меню «Тексты главной». */
add_action('admin_menu', function () {
    $front = (int) get_option('page_on_front');
    if ($front) {
        add_menu_page('Тексты главной', 'Тексты главной', 'edit_pages',
            'post.php?post=' . $front . '&action=edit', '', 'dashicons-edit', 3);
    }
});

/**
 * Обработка формы «Отправить сообщение» → письмо на адрес из поля
 * main_form_recipient (иначе — запасной адрес).
 */
add_action('wp_ajax_bavarmain_lead', 'bavarmain_handle_lead');
add_action('wp_ajax_nopriv_bavarmain_lead', 'bavarmain_handle_lead');
function bavarmain_handle_lead() {
    check_ajax_referer('bavarmain_lead', 'nonce');

    $name    = sanitize_text_field($_POST['name'] ?? '');
    $contact = sanitize_text_field($_POST['contact'] ?? '');
    $message = sanitize_textarea_field($_POST['message'] ?? '');

    if ($name === '' || $contact === '') {
        wp_send_json_error(['msg' => 'Укажите имя и контакт.']);
    }

    $to    = '';
    $front = (int) get_option('page_on_front');
    if ($front && function_exists('get_field')) {
        $to = trim((string) get_field('main_form_recipient', $front));
    }
    if ($to === '') {
        $to = 'artem@crazy.studio';
    }
    $recipients = array_filter(array_map('trim', explode(',', $to)));

    $host    = wp_parse_url(home_url(), PHP_URL_HOST);
    $subject = 'Сообщение с сайта ' . $host;
    $body    = implode("\n", [
        'Имя: ' . $name,
        'Контакт: ' . $contact,
        'Сообщение: ' . ($message !== '' ? $message : '—'),
        'Дата: ' . current_time('d.m.Y H:i'),
    ]);
    $headers = ['Content-Type: text/plain; charset=UTF-8'];
    if (is_email($contact)) {
        $headers[] = 'Reply-To: ' . $contact;
    }

    if (wp_mail($recipients, $subject, $body, $headers)) {
        wp_send_json_success(['msg' => 'Спасибо! Сообщение отправлено.']);
    }
    wp_send_json_error(['msg' => 'Не удалось отправить. Попробуйте позже или позвоните нам.']);
}

/** Разметка+скрипт формы (модалка). Внедряется при рендере, т.к. wp_footer не вызывается. */
function bavarswiss_form_html() {
    $ajax  = esc_url(admin_url('admin-ajax.php'));
    $nonce = esc_js(wp_create_nonce('bavarmain_lead'));
    return <<<HTML
<div id="bm-modal" aria-hidden="true">
  <div class="bm-box">
    <button class="bm-x" type="button" aria-label="Закрыть">&times;</button>
    <div class="bm-title">Отправить сообщение</div>
    <form id="bm-form">
      <input type="text" name="name" placeholder="Ваше имя" required>
      <input type="text" name="contact" placeholder="Телефон или e-mail" required>
      <textarea name="message" rows="4" placeholder="Сообщение"></textarea>
      <button type="submit" class="bm-submit">Отправить</button>
      <div class="bm-note" role="status"></div>
    </form>
  </div>
</div>
<style>
#bm-modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:100000;align-items:center;justify-content:center;padding:16px}
#bm-modal .bm-box{position:relative;background:#fff;border-radius:10px;max-width:460px;width:100%;padding:28px 26px;box-shadow:0 20px 60px rgba(0,0,0,.3);font-family:inherit}
#bm-modal .bm-x{position:absolute;top:10px;right:14px;border:0;background:none;font-size:30px;line-height:1;color:#888;cursor:pointer}
#bm-modal .bm-title{font-size:22px;font-weight:700;margin:0 0 16px;color:#111}
#bm-modal input,#bm-modal textarea{width:100%;box-sizing:border-box;margin:0 0 12px;padding:12px 14px;border:1px solid #d4d4d4;border-radius:7px;font:inherit;font-size:15px}
#bm-modal .bm-submit{width:100%;padding:13px;border:0;border-radius:7px;background:#e30613;color:#fff;font:inherit;font-size:16px;font-weight:600;cursor:pointer}
#bm-modal .bm-submit:hover{background:#c40410}
#bm-modal .bm-note{margin-top:12px;font-size:14px;color:#333;min-height:18px;text-align:center}
</style>
<script>
(function(){
  var AJAX="{$ajax}", NONCE="{$nonce}";
  function open(){var m=document.getElementById('bm-modal');if(m){m.style.display='flex';}}
  function close(){var m=document.getElementById('bm-modal');if(m){m.style.display='none';}}
  document.addEventListener('click',function(e){
    var fb=e.target.closest&&e.target.closest('[class*="feedback"]');
    if(!fb){var a=e.target.closest&&e.target.closest('a,button');if(a&&/Отправить сообщение/i.test(a.textContent||'')){fb=a;}}
    if(fb&&!fb.closest('#bm-modal')){e.preventDefault();open();return;}
    if(e.target.id==='bm-modal'||(e.target.classList&&e.target.classList.contains('bm-x'))){close();}
  });
  document.addEventListener('keydown',function(e){if(e.key==='Escape'){close();}});
  var f=document.getElementById('bm-form');
  if(f){f.addEventListener('submit',function(e){
    e.preventDefault();
    var note=f.querySelector('.bm-note');
    var fd=new FormData();
    fd.append('action','bavarmain_lead');fd.append('nonce',NONCE);
    fd.append('name',f.querySelector('[name=name]').value);
    fd.append('contact',f.querySelector('[name=contact]').value);
    fd.append('message',f.querySelector('[name=message]').value);
    note.textContent='Отправляем…';
    fetch(AJAX,{method:'POST',body:fd,credentials:'same-origin'}).then(function(r){return r.json();}).then(function(d){
      note.textContent=(d&&d.data&&d.data.msg)?d.data.msg:(d&&d.success?'Отправлено':'Ошибка');
      if(d&&d.success){f.reset();}
    }).catch(function(){note.textContent='Ошибка отправки. Позвоните нам, пожалуйста.';});
  });}
})();
</script>
HTML;
}

/**
 * Рендер главной из main.html: переписываем пути assets/ на URL темы,
 * подменяем исходные тексты значениями ACF-полей (если изменены) и внедряем форму.
 */
function bavarswiss_render_main() {
    $file = get_template_directory() . '/main.html';
    if (!file_exists($file)) {
        return '<!-- main.html not found -->';
    }
    $html = file_get_contents($file);
    $uri  = trailingslashit(get_template_directory_uri());
    $html = str_replace('assets/', $uri . 'assets/', $html);

    $front = (int) get_option('page_on_front');
    if ($front && function_exists('get_field')) {
        $map = [];
        foreach (bavarswiss_fields() as $name => $info) {
            $def = isset($info['default']) ? $info['default'] : '';
            $val = get_field($name, $front);
            $val = ($val !== '' && $val !== null) ? (string) $val : $def;
            if ($def !== '' && $val !== $def) {
                $map[$def] = esc_html($val);
            }
        }
        uksort($map, function ($a, $b) { return mb_strlen($b) - mb_strlen($a); });
        foreach ($map as $def => $val) {
            $html = str_replace($def, $val, $html);
        }

        // Телефон: подменяем оба варианта отображения и ссылку tel:.
        $ph = trim((string) get_field('main_phone', $front));
        if ($ph !== '' && $ph !== '+7 495 2220706') {
            $html = str_replace(['+7 495 2220706', '+7(495)2220706'], esc_html($ph), $html);
            $digits = preg_replace('/[^0-9+]/', '', $ph);
            if ($digits !== '') {
                $html = str_replace('tel:+74952220706', 'tel:' . $digits, $html);
            }
        }
        // E-mail: одна замена чинит и текст, и mailto: (адрес — подстрока ссылки).
        $em = trim((string) get_field('main_email', $front));
        if ($em !== '' && $em !== 'info@bavarswiss.ru' && is_email($em)) {
            $html = str_replace('info@bavarswiss.ru', esc_html($em), $html);
        }
    }

    // Внедряем форму перед </body>.
    $html = str_replace('</body>', bavarswiss_form_html() . "\n</body>", $html);

    return $html;
}
