<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  Icon + CTA
---------------------------------------------------------- */

add_filter('wpu_acf_flexible__field_types', function ($types) {
    $types['wpuacf_iconcta'] = array(
        'label' => __('Icon + CTA', 'wpu_acf_flexible'),
        'type' => 'group',
        'layout' => 'block',
        'sub_fields' => array(
            'link' => 'wpuacf_cta',
            'cola' => 'wpuacf_50p',
            'load_icon_before' => array(
                'label' => __('Show an Icon before text', 'wpu_acf_flexible'),
                'type' => 'true_false'
            ),
            'icon_before' => array(
                'type' => 'wpuacf_icon',
                'wpuacf_condition' => array(
                    'load_icon_before' => '1'
                )
            ),
            'colb' => 'wpuacf_50p',
            'load_icon_after' => array(
                'label' => __('Show an Icon after text', 'wpu_acf_flexible'),
                'type' => 'true_false'
            ),
            'icon_after' => array(
                'type' => 'wpuacf_icon',
                'wpuacf_condition' => array(
                    'load_icon_after' => '1'
                )
            )

        ),
        'field_vars_callback' => function ($id, $sub_field, $level) {
            return '';
        },
        'field_html_callback' => function ($id, $sub_field, $level) {
            return '<?php echo wpuacf_get_iconcta(get_sub_field(\'' . $id . '\')); ?>' . "\n";
        }
    );
    return $types;
}, 10, 1);

function wpuacfflex_get_iconcta($field, $classname = '', $attributes = '', $args = array()) {
    if (!is_array($field) || !isset($field['link']) || !is_array($field['link'])) {
        return '';
    }
    if (isset($field['load_icon_before'], $field['icon_before']) && $field['load_icon_before'] && $field['icon_before']) {
        $field['link']['before_span'] = get_wpu_acf_icon($field['icon_before']);
    }
    if (isset($field['load_icon_after'], $field['icon_after']) && $field['load_icon_after'] && $field['icon_after']) {
        $field['link']['after_span'] = get_wpu_acf_icon($field['icon_after']);
    }
    return get_wpu_acf_cta($field['link'], $classname, $attributes, $args);
}
