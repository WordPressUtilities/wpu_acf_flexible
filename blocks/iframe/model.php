<?php
defined('ABSPATH') || die;

$model = array(
    'label' => __('[WPUACF] Iframe', 'wpu_acf_flexible'),
    'sub_fields' => array(
        'url' => array(
            'label' => __('URL', 'wpu_acf_flexible'),
            'type' => 'url',
            'required' => true
        ),
        'cola' => 'wpuacf_50p',
        'height' => array(
            'label' => __('Iframe Height', 'wpu_acf_flexible'),
            'type' => 'number',
            'default_value' => 500
        ),
        'colb' => 'wpuacf_50p',
        'mobile_height' => array(
            'label' => __('Iframe Height (Mobile)', 'wpu_acf_flexible'),
            'type' => 'number'
        ),
        'colc' => 'wpuacf_100p'
    )
);
