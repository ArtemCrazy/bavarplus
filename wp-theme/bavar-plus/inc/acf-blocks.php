<?php
/**
 * Редактируемые тексты лендинга через ACF (free).
 *
 * Подход: все тексты лендинга — поля одной группы ACF, привязанной к
 * главной странице (front page) и сгруппированной по секциям (вкладки).
 * Клиент правит тексты на одном экране; вёрстка/стиль залочены в шаблонах.
 *
 * Каждая секция — модуль в /blocks/<slug>/:
 *   - render.php   — разметка секции 1:1, тексты через bavar_field()
 *   - fields.php   — поля ACF секции: ['ключ' => ['label'=>..,'type'=>..,'rows'=>..]]
 *   - defaults.php — тексты по умолчанию (default_value полей + фолбэк рендера)
 *
 * front-page.php рендерит статичный лендинг и подменяет конвертированные
 * секции их динамическими версиями (bavar_override_section).
 */

if (!defined('ABSPATH')) exit;

/**
 * Значение текстового поля: из ACF, иначе — дефолт секции.
 * Выводится «как есть» (доверенный ввод редактора, тексты с &nbsp;).
 */
function bavar_field($name, $defaults = []) {
    $v = function_exists('get_field') ? get_field($name) : null;
    if ($v === null || $v === '') {
        return isset($defaults[$name]) ? $defaults[$name] : '';
    }
    return $v;
}

/**
 * Безопасно загрузить PHP-файл, который возвращает массив (return [...]).
 * Выполняется в изолированной области видимости функции, поэтому
 * переменные внутри файла (напр. $fields) НЕ затирают переменные вызывающего.
 */
function bavar_load($file) {
    return require $file;
}

/** Человеко-понятные названия секций для вкладок ACF. */
function bavar_section_label($slug) {
    $map = [
        'hero'        => 'Шапка /Hero',
        'problem'     => 'Проблема и решение',
        'services'    => 'Услуги (зигзаг)',
        'industries'  => 'Отрасли',
        'process'     => 'Схема работы',
        'calc'        => 'Калькулятор',
        'advantages'  => 'Преимущества',
        'cooperation' => 'Формы сотрудничества',
        'cases'       => 'Кейсы',
        'faq'         => 'Частые вопросы',
        'contact'     => 'Контакты',
    ];
    return isset($map[$slug]) ? $map[$slug] : $slug;
}

/**
 * Модули секций (порядок не важен — рендер идёт по id в HTML).
 * Возвращает [slug => dir] для каталогов с полным набором файлов.
 */
function bavar_section_modules() {
    $mods = [];
    foreach (glob(get_template_directory() . '/blocks/*', GLOB_ONLYDIR) as $dir) {
        if (file_exists($dir . '/render.php')
            && file_exists($dir . '/fields.php')
            && file_exists($dir . '/defaults.php')) {
            $mods[basename($dir)] = $dir;
        }
    }
    return $mods;
}

/**
 * Удобный пункт меню «Тексты лендинга» — открывает редактор главной
 * страницы напрямую, чтобы клиент не искал её в разделе «Страницы».
 */
add_action('admin_menu', function () {
    if (!get_option('page_on_front')) return;
    add_menu_page('Тексты лендинга', 'Тексты лендинга', 'edit_pages', 'bavar-texts', '__return_null', 'dashicons-edit', 3);
});
add_action('admin_init', function () {
    if (isset($_GET['page']) && $_GET['page'] === 'bavar-texts') {
        $front = (int) get_option('page_on_front');
        if ($front) {
            wp_safe_redirect(admin_url('post.php?post=' . $front . '&action=edit'));
            exit;
        }
    }
});

/**
 * Группа ACF «Тексты лендинга», привязанная к главной странице.
 * Вкладка на секцию, под ней — её поля. ACF free: без repeater.
 */
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    $fields = [];
    foreach (bavar_section_modules() as $slug => $dir) {
        $meta     = bavar_load($dir . '/fields.php');
        $defaults = bavar_load($dir . '/defaults.php');

        $fields[] = [
            'key'       => 'field_bavar_tab_' . $slug,
            'label'     => bavar_section_label($slug),
            'type'      => 'tab',
            'placement' => 'left',
        ];

        foreach ($meta as $key => $info) {
            $type  = isset($info['type']) ? $info['type'] : 'text';
            $field = [
                'key'           => 'field_bavar_' . $key,
                'label'         => isset($info['label']) ? $info['label'] : $key,
                'name'          => $key,
                'type'          => $type,
                'default_value' => isset($defaults[$key]) ? $defaults[$key] : '',
            ];
            if ($type === 'textarea') {
                $field['rows'] = isset($info['rows']) ? $info['rows'] : 3;
            }
            if (isset($info['instructions'])) {
                $field['instructions'] = $info['instructions'];
            }
            $fields[] = $field;
        }
    }

    if (empty($fields)) return;

    acf_add_local_field_group([
        'key'                   => 'group_bavar_landing',
        'title'                 => 'Тексты лендинга БАВАР+',
        'fields'                => $fields,
        'location'              => [[[
            'param'    => 'page_type',
            'operator' => '==',
            'value'    => 'front_page',
        ]]],
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'hide_on_screen'        => ['the_content'],
        'description'           => 'Тексты главной страницы по секциям. Вёрстка фиксирована.',
    ]);
});
