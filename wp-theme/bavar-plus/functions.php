<?php
/**
 * BAVAR+ Landing — theme bootstrap.
 *
 * Дизайн лендинга залочен на уровне темы. Тексты редактируются через
 * блоки/поля в админке (см. inc/). Стили и скрипты переиспользуют
 * существующие assets/styles.css и assets/app.js из статического прототипа.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('BAVAR_VERSION', '1.0.0');

require_once get_template_directory() . '/inc/acf-blocks.php';

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('html5', ['style', 'script']);
    add_theme_support('editor-styles');
});

/**
 * Подключаем шрифт Inter, боевые стили и скрипт прототипа.
 * Версия файлов берётся из filemtime — кэш сбрасывается автоматически
 * при каждой правке (заменяет ручной ?v=NNN из статической версии).
 */
add_action('wp_enqueue_scripts', function () {
    $uri = get_template_directory_uri();
    $dir = get_template_directory();

    wp_enqueue_style(
        'bavar-inter',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap',
        [],
        null
    );

    wp_enqueue_style(
        'bavar-styles',
        $uri . '/assets/styles.css',
        ['bavar-inter'],
        file_exists($dir . '/assets/styles.css') ? filemtime($dir . '/assets/styles.css') : BAVAR_VERSION
    );

    wp_enqueue_script(
        'bavar-app',
        $uri . '/assets/app.js',
        [],
        file_exists($dir . '/assets/app.js') ? filemtime($dir . '/assets/app.js') : BAVAR_VERSION,
        true
    );

    // Передаём в скрипт адрес обработчика заявок и nonce.
    wp_localize_script('bavar-app', 'BAVAR_AJAX', [
        'url'   => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('bavar_lead'),
    ]);
});

/**
 * Фавиконка (набор иконок перенесён с основного сайта bavarswiss.ru).
 * Выводится в <head> через wp_head с ранним приоритетом.
 */
add_action('wp_head', function () {
    $f = trailingslashit(get_template_directory_uri()) . 'assets/favicons/';
    $links = [
        '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url($f . 'favicon-32x32.png') . '">',
        '<link rel="icon" type="image/png" sizes="16x16" href="' . esc_url($f . 'favicon-16x16.png') . '">',
        '<link rel="icon" type="image/png" sizes="96x96" href="' . esc_url($f . 'favicon-96x96.png') . '">',
        '<link rel="icon" type="image/png" sizes="192x192" href="' . esc_url($f . 'favicon-192x192.png') . '">',
        '<link rel="apple-touch-icon" sizes="180x180" href="' . esc_url($f . 'favicon-180x180.png') . '">',
        '<link rel="shortcut icon" href="' . esc_url($f . 'favicon.ico') . '">',
    ];
    echo "\n" . implode("\n", $links) . "\n";
}, 5);

/**
 * Мета-описание сайта в <head> из «Краткого описания» (Настройки → Общие).
 * Без SEO-плагина — хардкод в теме.
 */
add_action('wp_head', function () {
    $desc = trim((string) get_bloginfo('description'));
    if ($desc !== '') {
        echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
    }
}, 5);

/**
 * На главной не дублируем «Краткое описание» в <title>: заголовок вкладки
 * остаётся кратким (имя сайта), а описание используется как meta description.
 */
add_filter('document_title_parts', function ($parts) {
    if (is_front_page()) {
        unset($parts['tagline']);
    }
    return $parts;
});

/**
 * Яндекс.Метрика (счётчик 111745055) — установлен по запросу клиента.
 * Выводится в <head> через wp_head.
 */
add_action('wp_head', function () {
    echo <<<'YM'

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();
   for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
   k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js?id=111745055", "ym");
   ym(111745055, "init", {ssr:true, webvisor:true, clickmap:true, ecommerce:"dataLayer", accurateTrackBounce:true, trackLinks:true});
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/111745055" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

YM;
});

/**
 * Обработка заявок с форм сайта (#contact-form, #modal-form).
 * Отправляет письмо на адрес из поля «Куда отправлять заявки» (ACF, блок Контакты),
 * иначе — на e-mail администратора сайта.
 */
add_action('wp_ajax_bavar_lead', 'bavar_handle_lead');
add_action('wp_ajax_nopriv_bavar_lead', 'bavar_handle_lead');
function bavar_handle_lead() {
    check_ajax_referer('bavar_lead', 'nonce');

    $name  = sanitize_text_field($_POST['name']  ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $source = sanitize_text_field($_POST['source'] ?? 'форма');

    if ($name === '' || ($phone === '' && $email === '')) {
        wp_send_json_error(['msg' => 'Укажите имя и телефон.']);
    }

    // Получатель: ACF-поле form_recipient на главной странице, иначе админ сайта.
    $to    = '';
    $front = (int) get_option('page_on_front');
    if ($front && function_exists('get_field')) {
        $to = trim((string) get_field('form_recipient', $front));
    }
    if ($to === '') {
        // Поле не заполнено — берём адрес по умолчанию из defaults.php блока Контакты.
        $cdef = get_template_directory() . '/blocks/contact/defaults.php';
        if (file_exists($cdef)) {
            $d = include $cdef;
            if (is_array($d) && !empty($d['form_recipient'])) {
                $to = trim($d['form_recipient']);
            }
        }
    }
    if ($to === '') {
        $to = get_option('admin_email');
    }
    $recipients = array_filter(array_map('trim', explode(',', $to)));

    $host    = wp_parse_url(home_url(), PHP_URL_HOST);
    $subject = 'Заявка с сайта ' . $host;
    $lines   = [
        'Имя: ' . $name,
        'Телефон: ' . ($phone !== '' ? $phone : '—'),
        'Email: ' . ($email !== '' ? $email : '—'),
        'Источник: ' . $source,
        'Дата: ' . current_time('d.m.Y H:i'),
    ];
    $body    = implode("\n", $lines);

    $headers = ['Content-Type: text/plain; charset=UTF-8'];
    if ($email !== '' && is_email($email)) {
        $headers[] = 'Reply-To: ' . $email;
    }

    $ok = wp_mail($recipients, $subject, $body, $headers);

    if ($ok) {
        wp_send_json_success(['msg' => 'Заявка отправлена']);
    }
    wp_send_json_error(['msg' => 'Не удалось отправить. Попробуйте позже или позвоните нам.']);
}

/**
 * Рендер статической разметки лендинга из template-parts/landing.html
 * с переписыванием относительных путей assets/ на URL темы.
 *
 * Единая точка трансформации: разметка хранится 1:1 с эталоном,
 * а пути к ассетам подставляются на лету.
 */
function bavar_render_landing() {
    $dir  = get_template_directory();
    $uri  = trailingslashit(get_template_directory_uri());
    $file = $dir . '/template-parts/landing.html';

    if (!file_exists($file)) {
        return '<!-- landing.html not found -->';
    }

    $html = file_get_contents($file);
    return str_replace('assets/', $uri . 'assets/', $html);
}

/**
 * Рендер одной секции из модуля /blocks/<slug>/render.php
 * (тексты из ACF с откатом на дефолты) + переписывание путей assets/.
 */
function bavar_render_section($slug) {
    $file = get_template_directory() . '/blocks/' . $slug . '/render.php';
    if (!file_exists($file)) return '';
    ob_start();
    include $file;
    $out = ob_get_clean();
    $uri = trailingslashit(get_template_directory_uri());
    return str_replace('assets/', $uri . 'assets/', $out);
}

/**
 * Заменяет в HTML секцию <section id="$id">…</section> на $new.
 * Секции не вложены, поэтому первый </section> после открытия — её закрытие.
 * preg_replace_callback, чтобы спецсимволы ($, \) в $new не интерпретировались.
 */
function bavar_override_section($html, $id, $new) {
    $pattern = '/<section\b[^>]*\bid="' . preg_quote($id, '/') . '"[^>]*>.*?<\/section>/s';
    return preg_replace_callback($pattern, function () use ($new) {
        return $new;
    }, $html, 1);
}
