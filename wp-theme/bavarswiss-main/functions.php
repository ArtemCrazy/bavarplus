<?php
/**
 * BavarSwiss (главный сайт) — bootstrap темы.
 *
 * Этап 1: тема отдаёт перенесённую с NetHouse страницу 1:1 из main.html.
 * Вёрстка самодостаточна (инлайн-CSS + локальные ассеты), поэтому
 * отдельные стили/скрипты не подключаем — только переписываем относительные
 * пути assets/ на URL темы. Тексты выводятся в админку поэтапно (этап 2, ACF).
 */
if (!defined('ABSPATH')) {
    exit;
}

define('BAVARSWISS_VERSION', '1.0.0');

/**
 * Рендер главной страницы из main.html с переписыванием путей assets/
 * на URL темы (файлы лежат в /assets внутри темы).
 */
function bavarswiss_render_main() {
    $file = get_template_directory() . '/main.html';
    if (!file_exists($file)) {
        return '<!-- main.html not found -->';
    }
    $html = file_get_contents($file);
    $uri  = trailingslashit(get_template_directory_uri());
    return str_replace('assets/', $uri . 'assets/', $html);
}
