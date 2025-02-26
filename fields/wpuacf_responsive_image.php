<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  Responsive Image
---------------------------------------------------------- */

add_filter('wpu_acf_flexible__field_types', function ($types) {

    $field_settings = array(
        'type' => 'group',
        'label' => __('Responsive image', 'wpu_acf_flexible'),
        'sub_fields' => array(
            'cola' => 'wpuacf_50p',
            'image' => 'wpuacf_image',
            'colb' => 'wpuacf_50p',
            'image_mobile' => array(
                'label' => __('Mobile image', 'wpu_acf_flexible'),
                'required' => false,
                'type' => 'wpuacf_image'
            )
        )
    );

    $types['wpuacf_responsive_image'] = $field_settings;

    $field_settings['sub_fields']['image'] = array(
        'label' => __('Image', 'wpu_acf_flexible'),
        'required' => false,
        'type' => 'wpuacf_image'
    );

    $types['wpuacf_responsive_image__not_required'] = $field_settings;

    return $types;
}, 10, 1);
