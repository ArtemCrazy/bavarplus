<?php
/**
 * Front page — лендинг БАВАР+.
 * Разметка берётся из template-parts/landing.html (1:1 с эталоном).
 */
if (!defined('ABSPATH')) exit;

get_header();
echo bavar_render_landing();
get_footer();
