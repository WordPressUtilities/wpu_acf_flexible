<?php
defined('ABSPATH') || die;

$model = array(
    'label' => __('[WPUACF] Gallery', 'wpu_acf_flexible'),
    'sub_fields' => array(
        'title' => array(
            'label' => __('Title', 'wpu_acf_flexible')
        ),
        'content' => array(
            'label' => __('Content', 'wpu_acf_flexible'),
            'type' => 'textarea',
            'rows' => 2
        ),
        'images' => array(
            'required' => 1,
            'label' => __('Images', 'wpu_acf_flexible'),
            'type' => 'gallery',
            'return_format' => 'id',
        )
    )
);
