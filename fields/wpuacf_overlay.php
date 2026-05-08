<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  Overlay
---------------------------------------------------------- */

add_filter('wpu_acf_flexible__field_types', function ($types) {
    $types['wpuacf_overlay'] = array(
        'label' => __('Overlay', 'wpu_acf_flexible'),
        'type' => 'group',
        'sub_fields' => array(
            'cola' => 'wpuacf_50p',
            'overlay' => array(
                'label' => __('Overlay on background', 'wpu_acf_flexible'),
                'type' => 'true_false',
                'default_value' => 1,
                'ui' => true
            ),
            'colb' => 'wpuacf_50p',
            'overlay_percent' => array(
                'label' => __('Overlay percent', 'wpu_acf_flexible'),
                'type' => 'wpuacf_percent',
                'default_value' => 50,
                'wpuacf_condition' => array(
                    'overlay' => 1
                )
            ),
            'colc' => 'wpuacf_100p'
        )
    );
    return $types;
}, 10, 1);

function wpuacfflex_get_overlay_style($overlay) {
    if (!is_array($overlay)) {
        $overlay = array();
    }
    $overlay = array_merge(array(
        'overlay' => false,
        'overlay_percent' => 50
    ), $overlay);

    if (!is_numeric($overlay['overlay_percent'])) {
        $overlay['overlay_percent'] = 50;
    }

    if (!$overlay['overlay']) {
        $overlay['overlay_percent'] = 0;
    }

    return '--overlay-opacity:' . (intval($overlay['overlay_percent']) / 100) . ';';
}
