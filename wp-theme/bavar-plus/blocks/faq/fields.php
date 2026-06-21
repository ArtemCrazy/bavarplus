<?php
/**
 * Описание полей ACF блока FAQ (порядок и подписи в редакторе).
 * Значения по умолчанию берутся из defaults.php по тем же ключам.
 */
if (!defined('ABSPATH')) exit;

$fields = [
    'faq_title' => ['label' => 'Заголовок секции', 'type' => 'text'],
    'faq_lede'  => ['label' => 'Подзаголовок', 'type' => 'textarea', 'rows' => 2],
];
for ($i = 1; $i <= 6; $i++) {
    $fields["faq_q$i"] = ['label' => "Вопрос $i", 'type' => 'text'];
    $fields["faq_a$i"] = ['label' => "Ответ $i", 'type' => 'textarea', 'rows' => 4];
}
return $fields;
