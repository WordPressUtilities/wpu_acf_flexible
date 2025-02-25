<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  Align
---------------------------------------------------------- */

add_filter('wpu_acf_flexible__field_types', function ($types) {
    $types['wpuacf_align'] = array(
        'label' => __('Alignment', 'wpu_acf_flexible'),
        'type' => 'select',
        'choices' => array(
            'left' => __('Left', 'wpu_acf_flexible'),
            'center' => __('Center', 'wpu_acf_flexible'),
            'right' => __('Right', 'wpu_acf_flexible')
        ),
        'default_value' => 'center'
    );
    return $types;
}, 10, 1);
