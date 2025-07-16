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

/* ----------------------------------------------------------
  Align vertical
---------------------------------------------------------- */

add_filter('wpu_acf_flexible__field_types', function ($types) {
    $types['wpuacf_align_vertical'] = array(
        'label' => __('Vertical Alignment', 'wpu_acf_flexible'),
        'type' => 'select',
        'choices' => array(
            'top' => __('Top', 'wpu_acf_flexible'),
            'middle' => __('Middle', 'wpu_acf_flexible'),
            'bottom' => __('Bottom', 'wpu_acf_flexible')
        ),
        'default_value' => 'top'
    );
    return $types;
}, 10, 1);
