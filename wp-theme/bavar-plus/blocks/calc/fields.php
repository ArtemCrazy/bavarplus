<?php
/** Поля ACF — секция «Калькулятор». Только подписи; цифры считает скрипт. */
if (!defined('ABSPATH')) exit;

return [
    'calc_title'         => ['label' => 'Заголовок секции', 'type' => 'text'],
    'calc_lede'          => ['label' => 'Подзаголовок', 'type' => 'textarea', 'rows' => 3],
    'calc_panel_tag'     => ['label' => 'Левая панель · тег', 'type' => 'text'],
    'calc_f1_label'      => ['label' => 'Ползунок 1 · подпись', 'type' => 'text'],
    'calc_f1_hint'       => ['label' => 'Ползунок 1 · пояснение', 'type' => 'textarea', 'rows' => 2],
    'calc_f2_label'      => ['label' => 'Ползунок 2 · подпись', 'type' => 'text'],
    'calc_f2_hint'       => ['label' => 'Ползунок 2 · пояснение', 'type' => 'textarea', 'rows' => 2],
    'calc_f3_label'      => ['label' => 'Ползунок 3 · подпись', 'type' => 'text'],
    'calc_f3_hint'       => ['label' => 'Ползунок 3 · пояснение', 'type' => 'textarea', 'rows' => 2],
    'calc_note'          => ['label' => 'Сноска под параметрами', 'type' => 'textarea', 'rows' => 3],
    'calc_result_tag'    => ['label' => 'Правая панель · тег', 'type' => 'text'],
    'calc_breakdown_cap' => ['label' => 'Заголовок разбивки расходов', 'type' => 'text'],
    'calc_br1_name'      => ['label' => 'Строка 1 · название', 'type' => 'text'],
    'calc_br2_name'      => ['label' => 'Строка 2 · название', 'type' => 'text'],
    'calc_br3_name'      => ['label' => 'Строка 3 · название', 'type' => 'text'],
    'calc_br4_name'      => ['label' => 'Строка 4 · название', 'type' => 'text'],
    'calc_br5_name'      => ['label' => 'Строка 5 · название', 'type' => 'text'],
    'calc_pill1_name'    => ['label' => 'Плашка 1 · название', 'type' => 'text'],
    'calc_pill2_name'    => ['label' => 'Плашка 2 · название', 'type' => 'text'],
    'calc_cta'           => ['label' => 'Кнопка', 'type' => 'text'],
    'calc_alert_head'    => ['label' => 'Блок «неочевидные траты» · заголовок', 'type' => 'text'],
    'calc_alert_li1'     => ['label' => 'Неочевидные траты · пункт 1', 'type' => 'text'],
    'calc_alert_li2'     => ['label' => 'Неочевидные траты · пункт 2', 'type' => 'text'],
    'calc_alert_li3'     => ['label' => 'Неочевидные траты · пункт 3', 'type' => 'text'],
    'calc_sticky_lbl'    => ['label' => 'Мобильная плашка экономии · подпись', 'type' => 'text'],
];
