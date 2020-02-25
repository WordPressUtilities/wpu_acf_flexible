<?php

$model = array(
    'label' => __('[WPUACF] Image - Content', 'wpu_acf_flexible'),
    'sub_fields' => array(
        'col1a' => array(
            'type' => 'acfe_column',
            'columns' => '3/6'
        ),
        'image' => array(
            'label' => __('Image', 'wpu_acf_flexible'),
            'type' => 'image',
            'required' => 1,
        ),
        'col1b' => array(
            'type' => 'acfe_column',
            'columns' => '3/6'
        ),
        'image_position' => array(
            'label' => __('Image position', 'wpu_acf_flexible'),
            'type' => 'select',
            'choices' => array(
                'left' => 'Left',
                'right' => 'Right',
            )
        ),
        'col1c' => array(
            'type' => 'acfe_column',
            'columns' => '6/6',
            'endpoint' => true,
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
        ),
    )
);
