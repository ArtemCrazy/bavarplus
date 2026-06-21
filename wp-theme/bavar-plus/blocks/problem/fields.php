<?php
/** Поля ACF — секция «Проблема / Решение». */
if (!defined('ABSPATH')) exit;

return [
    'problem_title'   => ['label' => 'Заголовок секции', 'type' => 'text'],
    'problem_lede'    => ['label' => 'Подзаголовок', 'type' => 'textarea', 'rows' => 4],

    'problem_p_label' => ['label' => 'Левая карточка · метка', 'type' => 'text'],
    'problem_p_title' => ['label' => 'Левая карточка · заголовок', 'type' => 'text'],
    'problem_p_li1'   => ['label' => 'Левая · пункт 1', 'type' => 'textarea', 'rows' => 2],
    'problem_p_li2'   => ['label' => 'Левая · пункт 2', 'type' => 'textarea', 'rows' => 2],
    'problem_p_li3'   => ['label' => 'Левая · пункт 3', 'type' => 'textarea', 'rows' => 2],
    'problem_p_li4'   => ['label' => 'Левая · пункт 4', 'type' => 'textarea', 'rows' => 2],
    'problem_p_li5'   => ['label' => 'Левая · пункт 5', 'type' => 'textarea', 'rows' => 2],
    'problem_p_li6'   => ['label' => 'Левая · пункт 6', 'type' => 'textarea', 'rows' => 2],

    'problem_s_title' => ['label' => 'Правая карточка · заголовок', 'type' => 'text'],
    'problem_s_li1'   => ['label' => 'Правая · пункт 1', 'type' => 'textarea', 'rows' => 2],
    'problem_s_li2'   => ['label' => 'Правая · пункт 2', 'type' => 'textarea', 'rows' => 2],
    'problem_s_li3'   => ['label' => 'Правая · пункт 3', 'type' => 'textarea', 'rows' => 2],
    'problem_s_li4'   => ['label' => 'Правая · пункт 4', 'type' => 'textarea', 'rows' => 2],
    'problem_s_li5'   => ['label' => 'Правая · пункт 5', 'type' => 'textarea', 'rows' => 2],
    'problem_s_li6'   => ['label' => 'Правая · пункт 6', 'type' => 'textarea', 'rows' => 2],
];
