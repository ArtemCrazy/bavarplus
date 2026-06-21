<?php
/** Поля ACF — секция «Схема работы». */
if (!defined('ABSPATH')) exit;

$f = [
    'process_title' => ['label' => 'Заголовок секции', 'type' => 'text'],
    'process_lede'  => ['label' => 'Подзаголовок', 'type' => 'textarea', 'rows' => 2],
    'process_intro' => ['label' => 'Вводный абзац', 'type' => 'textarea', 'rows' => 3],
];
for ($i = 1; $i <= 6; $i++) {
    $f["step{$i}_title"]  = ['label' => "Этап $i · заголовок", 'type' => 'text'];
    $f["step{$i}_desc"]   = ['label' => "Этап $i · описание", 'type' => 'textarea', 'rows' => 3];
    $f["step{$i}_result"] = ['label' => "Этап $i · результат (после «Результат:»)", 'type' => 'textarea', 'rows' => 2];
}
return $f;
