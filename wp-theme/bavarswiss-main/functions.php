<?php
/**
 * BavarSwiss (главный сайт) — bootstrap темы.
 *
 * Этап 1: тема отдаёт перенесённую с NetHouse страницу 1:1 из main.html.
 * Этап 2: ключевые тексты выводятся в админку через ACF (группа «Тексты главной
 * BavarSwiss» на главной странице). Механизм: в main.html ищем исходный текст и
 * подменяем его значением поля, если оно изменено (дефолт = исходный текст).
 * Поля и дефолты генерируются из main.html скриптом build-fields.py.
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
 * Рендер главной из main.html: переписываем пути assets/ на URL темы и
 * подменяем исходные тексты значениями ACF-полей (если изменены).
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
        // длинные исходные строки заменяем первыми (защита от вложенных подстрок)
        uksort($map, function ($a, $b) { return mb_strlen($b) - mb_strlen($a); });
        foreach ($map as $def => $val) {
            $html = str_replace($def, $val, $html);
        }
    }
    return $html;
}
