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
        'colc' => 'wpuacf_100p',
        'extra_settings' => array(
            'label' => __('Settings', 'wpu_acf_flexible'),
            'type' => 'accordion',
        ),
        'iframe_id' => array(
            'label' => __('Iframe ID', 'wpu_acf_flexible'),
            'type' => 'text',
            'instructions' => __('Optional ID attribute for the iframe', 'wpu_acf_flexible'),
        ),
        'iframe_title' => array(
            'label' => __('Iframe Title', 'wpu_acf_flexible'),
            'type' => 'text',
            'instructions' => __('Optional title attribute for the iframe (for accessibility)', 'wpu_acf_flexible'),
        ),
        'enable_lazy_loading' => array(
            'label' => __('Enable Lazy Loading', 'wpu_acf_flexible'),
            'type' => 'true_false',
            'ui' => 1,
            'default_value' => 0,
            'instructions' => __('If enabled, the iframe will load only when it enters the viewport.', 'wpu_acf_flexible'),
        )
    )
);
