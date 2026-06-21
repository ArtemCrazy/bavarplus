<?php
/** Поля ACF — секция «Отрасли». */
if (!defined('ABSPATH')) exit;

$f = [
    'ind_title' => ['label' => 'Заголовок (можно &lt;br&gt; для переноса)', 'type' => 'text'],
    'ind_lede'  => ['label' => 'Лид (допускается &lt;strong&gt;)', 'type' => 'textarea', 'rows' => 4],
];
for ($i = 1; $i <= 4; $i++) {
    $f["ind{$i}_title"] = ['label' => "Карточка $i · заголовок", 'type' => 'text'];
    $f["ind{$i}_desc"]  = ['label' => "Карточка $i · описание", 'type' => 'textarea', 'rows' => 3];
}
$f['ind_note'] = ['label' => 'Примечание под карточками', 'type' => 'textarea', 'rows' => 3];
return $f;
