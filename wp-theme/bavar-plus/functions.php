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
});

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
