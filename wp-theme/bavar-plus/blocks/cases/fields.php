<?php
/** Поля ACF — секция «Кейсы». */
if (!defined('ABSPATH')) exit;

$f = [
    'cases_title' => ['label' => 'Заголовок секции', 'type' => 'text'],
    'cases_lede'  => ['label' => 'Подзаголовок', 'type' => 'textarea', 'rows' => 2],
];
for ($c = 1; $c <= 5; $c++) {
    $f["case{$c}_tag"]  = ['label' => "Кейс $c · отрасль (метка)", 'type' => 'text'];
    $f["case{$c}_text"] = ['label' => "Кейс $c · описание", 'type' => 'textarea', 'rows' => 3];
    for ($m = 1; $m <= 3; $m++) {
        $f["case{$c}_m{$m}_val"] = ['label' => "Кейс $c · метрика $m · значение", 'type' => 'text'];
        $f["case{$c}_m{$m}_lbl"] = ['label' => "Кейс $c · метрика $m · подпись", 'type' => 'text'];
    }
}
return $f;
