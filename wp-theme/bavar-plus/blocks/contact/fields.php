<?php
/** Поля ACF — секция «Контакты». */
if (!defined('ABSPATH')) exit;

return [
    'contact_title' => ['label' => 'Заголовок секции', 'type' => 'text'],
    'contact_lede'  => ['label' => 'Подзаголовок', 'type' => 'text'],

    'contact_phone_lbl'  => ['label' => 'Телефон · подпись', 'type' => 'text'],
    'contact_phone_val'  => ['label' => 'Телефон · отображение', 'type' => 'text'],
    'contact_phone_href' => ['label' => 'Телефон · ссылка (tel:)', 'type' => 'text'],
    'contact_email_lbl'  => ['label' => 'Email · подпись', 'type' => 'text'],
    'contact_email_val'  => ['label' => 'Email · отображение', 'type' => 'text'],
    'contact_email_href' => ['label' => 'Email · ссылка (mailto:)', 'type' => 'text'],
    'contact_tg_lbl'     => ['label' => 'Telegram · подпись', 'type' => 'text'],
    'contact_tg_val'     => ['label' => 'Telegram · отображение', 'type' => 'text'],
    'contact_tg_href'    => ['label' => 'Telegram · ссылка', 'type' => 'text'],

    'cform_title'    => ['label' => 'Форма · заголовок', 'type' => 'text'],
    'cform_sub'      => ['label' => 'Форма · подзаголовок', 'type' => 'text'],
    'cform_name_ph'  => ['label' => 'Форма · плейсхолдер «Имя»', 'type' => 'text'],
    'cform_phone_ph' => ['label' => 'Форма · плейсхолдер «Телефон»', 'type' => 'text'],
    'cform_agree'    => ['label' => 'Форма · согласие (можно ссылку &lt;a&gt;)', 'type' => 'textarea', 'rows' => 3],
    'cform_btn'      => ['label' => 'Форма · кнопка', 'type' => 'text'],
];
