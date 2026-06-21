<?php
/** Поля ACF — секция «Услуги». */
if (!defined('ABSPATH')) exit;

$fields = [
    'services_title' => ['label' => 'Заголовок секции', 'type' => 'text'],
    'services_lede'  => ['label' => 'Подзаголовок', 'type' => 'textarea', 'rows' => 3],
];
for ($i = 1; $i <= 6; $i++) {
    $fields["svc{$i}_title"] = ['label' => "Услуга $i · заголовок", 'type' => 'text'];
    $fields["svc{$i}_desc"]  = ['label' => "Услуга $i · описание", 'type' => 'textarea', 'rows' => 3];
}
return $fields;
