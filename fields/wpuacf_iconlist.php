<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  Iconlist
---------------------------------------------------------- */

add_filter('wpu_acf_flexible__field_types', function ($types) {
    $types['wpuacf_iconlist'] = array(
        'label' => __('Icon list', 'wpu_acf_flexible'),
        'type' => 'repeater',
        'layout' => 'block',
        'button_label' => __('Add a line', 'wpu_acf_flexible'),
        'sub_fields' => array(
            'cola' => 'wpuacf_25p',
            'icon' => array(
                'type' => 'wpuacf_icon',
                'required' => true
            ),
            'colb' => 'wpuacf_75p',
            'title' => 'wpuacf_title',
            'content' => array(
                'label' => __('Content', 'wpu_acf_flexible'),
                'required' => true,
                'type' => 'wpuacf_minieditor'
            )
        ),
        'field_vars_callback' => function ($id, $sub_field, $level) {
            return '';
        },
        'field_html_callback' => function ($id, $sub_field, $level) {
            return '<?php echo wpuacf_iconlist(get_sub_field(\'' . $id . '\')); ?>' . "\n";
        }
    );
    return $types;
}, 10, 1);

function wpuacf_iconlist($list) {
    $html = '';

    if (!is_array($list)) {
        return '';
    }

    $html .= '<ul class="iconlist">';
    foreach ($list as $line) {
        $html .= '<li class="iconlist-item">';
        $html .= '<div class="iconlist-item__icon">' . get_wpu_acf_icon($line['icon']) . '</div>';
        $html .= '<h3 class="iconlist-item__title">' . esc_html($line['title']) . '</h3>';
        $html .= get_wpu_acf_minieditor($line['content'], array(
            'add_wrapper' => true
        ));
        $html .= '</li>';
    }
    $html .= '</ul>';
    return $html;

}
