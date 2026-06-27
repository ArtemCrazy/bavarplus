<?php
/** Фолбэк: одностраничник — любой запрос отдаёт главную. */
if (!defined('ABSPATH')) {
    exit;
}
echo bavarswiss_render_main();
