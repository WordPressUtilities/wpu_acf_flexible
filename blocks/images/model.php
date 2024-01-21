<?php
defined('ABSPATH') || die;

$model = array(
    'label' => __('[WPUACF] Images', 'wpu_acf_flexible'),
    'sub_fields' => array(
        'images' => array(
            'min' => apply_filters('wpu_acf_flexible__model__images__max', 2),
            'max' => apply_filters('wpu_acf_flexible__model__images__max', 3),
            'required' => true,
            'type' => 'repeater',
            'label' => __('Images', 'wpu_acf_flexible'),
            'sub_fields' => array(
                'image' => 'wpuacf_image'
            )
        )
    )
);
