<?php
/** Поля ACF — секция «Преимущества». */
if (!defined('ABSPATH')) exit;

$f = [
    'adv_title' => ['label' => 'Заголовок секции', 'type' => 'text'],
    'adv_lede'  => ['label' => 'Подзаголовок', 'type' => 'textarea', 'rows' => 2],
];
for ($i = 1; $i <= 6; $i++) {
    $f["adv{$i}_title"] = ['label' => "Карточка $i · заголовок", 'type' => 'text'];
    $f["adv{$i}_desc"]  = ['label' => "Карточка $i · описание", 'type' => 'textarea', 'rows' => 3];
}
$f['adv_outro'] = ['label' => 'Заключительный абзац', 'type' => 'textarea', 'rows' => 2];
return $f;
