<?php
/** Поля ACF — секция «Формы сотрудничества». */
if (!defined('ABSPATH')) exit;

$f = [
    'coop_title' => ['label' => 'Заголовок секции', 'type' => 'text'],
    'coop_lede'  => ['label' => 'Подзаголовок', 'type' => 'text'],
    'coop_note'  => ['label' => 'Примечание', 'type' => 'text'],
];
foreach ([1, 2] as $c) {
    $f["coop_c{$c}_name"]    = ['label' => "Карточка $c · название", 'type' => 'text'];
    $f["coop_c{$c}_tagline"] = ['label' => "Карточка $c · подзаголовок", 'type' => 'text'];
    $f["coop_c{$c}_text"]    = ['label' => "Карточка $c · описание", 'type' => 'textarea', 'rows' => 3];
    for ($i = 1; $i <= 6; $i++) {
        $f["coop_c{$c}_li{$i}"] = ['label' => "Карточка $c · пункт $i", 'type' => 'text'];
    }
    $f["coop_c{$c}_fit"] = ['label' => "Карточка $c · «подходит, если»", 'type' => 'text'];
}
$f['coop_compare_title'] = ['label' => 'Таблица · заголовок', 'type' => 'text'];
$f['coop_th_param']      = ['label' => 'Таблица · шапка «Параметр»', 'type' => 'text'];
$f['coop_th_1']          = ['label' => 'Таблица · шапка колонки 1', 'type' => 'text'];
$f['coop_th_2']          = ['label' => 'Таблица · шапка колонки 2', 'type' => 'text'];
for ($r = 1; $r <= 4; $r++) {
    $f["coop_row{$r}_label"] = ['label' => "Таблица · строка $r · параметр", 'type' => 'text'];
    $f["coop_row{$r}_a"]     = ['label' => "Таблица · строка $r · колонка 1", 'type' => 'text'];
    $f["coop_row{$r}_b"]     = ['label' => "Таблица · строка $r · колонка 2", 'type' => 'text'];
}
$f['coop_compare_hint'] = ['label' => 'Подсказка прокрутки таблицы', 'type' => 'text'];
return $f;
