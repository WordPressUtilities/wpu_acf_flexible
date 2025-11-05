<?php
defined('ABSPATH') || die;

$model = array(
    'label' => __('[WPUACF] Logos', 'wpu_acf_flexible'),
    'sub_fields' => array(
        'title' => array(
            'label' => __('Title', 'wpu_acf_flexible')
        ),
        'content' => array(
            'label' => __('Content', 'wpu_acf_flexible'),
            'type' => 'textarea',
            'rows' => 2
        ),
        'logos' => array(
            'label' => __('Logos', 'wpu_acf_flexible'),
            'type' => 'repeater',
            'sub_fields' => array(
                'image' => array(
                    'label' => __('Image', 'wpu_acf_flexible'),
                    'type' => 'image',
                    'required' => true
                ),
                'url' => array(
                    'label' => __('Link', 'wpu_acf_flexible'),
                    'type' => 'url'
                )
            )
        )
    )
);
