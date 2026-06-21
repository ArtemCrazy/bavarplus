<?php
/**
 * Шаблон страницы — рендерит блоки Gutenberg из контента (the_content).
 * Используется для страниц, собранных из ACF-блоков БАВАР+.
 */
if (!defined('ABSPATH')) exit;

get_header();
while (have_posts()) {
    the_post();
    the_content();
}
get_footer();
