<?php

$model = array(
    'label' => __('[WPUACF] Hero', 'wpu_acf_flexible'),
    'sub_fields' => array(
        'title' => array(
            'label' => __('Title', 'wpu_acf_flexible'),
            'type' => 'text'
        ),
        'content' => array(
            'label' => __('Subtitle', 'wpu_acf_flexible'),
            'type' => 'text'
        ),
        'cta' => array(
            'label' => __('Button', 'wpu_acf_flexible'),
            'type' => 'link'
        ),
        'image' => 'wpuacf_image',
    )
);
