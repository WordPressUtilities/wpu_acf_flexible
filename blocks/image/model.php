<?php
defined('ABSPATH') || die;

$model = array(
    'label' => __('[WPUACF] Image', 'wpu_acf_flexible'),
    'sub_fields' => array(
        'title' => array(
            'label' => __('Title', 'wpu_acf_flexible')
        ),
        'content' => array(
            'label' => __('Content', 'wpu_acf_flexible'),
            'type' => 'textarea',
            'rows' => 2
        ),
        'image' => 'wpuacf_image'
    )
);
