<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  Slider
---------------------------------------------------------- */

add_filter('wpu_acf_flexible__field_types', function ($types) {
    $types['wpuacf_slider'] = array(
        'label' => __('Slider', 'wpu_acf_flexible'),
        'type' => 'group',
        'sub_fields' => array(
            'cola' => 'wpuacf_50p',
            'gallery' => array(
                'label' => __('Images', 'wpu_acf_flexible'),
                'type' => 'gallery'
            ),
            'colb' => 'wpuacf_50p',
            'slider_options' => array(
                'label' => __('Options', 'wpu_acf_flexible'),
                'type' => 'group',
                'sub_fields' => array(
                    'autoplay' => array(
                        'label' => __('Autoplay', 'wpu_acf_flexible'),
                        'type' => 'true_false'
                    ),
                    'autoplay_speed' => array(
                        'label' => __('Autoplay speed', 'wpu_acf_flexible'),
                        'type' => 'number',
                        'append' => 'ms',
                        'default_value' => 5000
                    )
                )
            )
        )
    );
    return $types;
}, 10, 1);
