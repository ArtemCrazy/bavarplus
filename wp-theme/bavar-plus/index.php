<?php
/**
 * Fallback-шаблон. Сайт одностраничный — показываем лендинг
 * в любом контексте, чтобы избежать пустых экранов.
 */
if (!defined('ABSPATH')) exit;

get_header();
echo bavar_render_landing();
get_footer();
