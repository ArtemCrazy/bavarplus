<?php
/**
 * Front page — лендинг БАВАР+.
 *
 * Базовая разметка берётся из template-parts/landing.html (эталон 1:1),
 * а конвертированные секции подменяются динамическими версиями, где
 * тексты редактируются через ACF (см. inc/acf-blocks.php, /blocks/<slug>/).
 */
if (!defined('ABSPATH')) exit;

get_header();

$html = bavar_render_landing();

foreach (array_keys(bavar_section_modules()) as $slug) {
    $section = bavar_render_section($slug);
    if ($section !== '') {
        $html = bavar_override_section($html, $slug, $section);
    }
}

echo $html;

get_footer();
