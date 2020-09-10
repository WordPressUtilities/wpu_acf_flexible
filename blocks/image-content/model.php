<?php

$model = array(
    'label' => __('[WPUACF] Image - Content', 'wpu_acf_flexible'),
    'sub_fields' => array(
        'col1a' => array(
            'type' => 'acfe_column',
            'columns' => '3/6'
        ),
        'image' => 'wpuacf_image',
        'col1b' => array(
            'type' => 'acfe_column',
            'columns' => '3/6'
        ),
        'image_position' => 'wpuacf_image_position',
        'col1c' => array(
            'type' => 'acfe_column',
            'columns' => '6/6',
            'endpoint' => true
        ),
        'title' => array(
            'label' => __('Title', 'wpu_acf_flexible')
        ),
        'content' => array(
            'label' => __('Content', 'wpu_acf_flexible'),
            'type' => 'textarea',
            'rows' => 2
        ),
        'cta' => array(
            'label' => __('Button', 'wpu_acf_flexible'),
            'type' => 'link'
        )
    )
);
