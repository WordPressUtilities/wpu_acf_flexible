<?php

$model = array(
    'label' => __('[WPUACF] Features', 'wpu_acf_flexible'),
    'sub_fields' => array(
        'title' => array(
            'label' => __('Title', 'wpu_acf_flexible')
        ),
        'content' => array(
            'label' => __('Content', 'wpu_acf_flexible'),
            'type' => 'textarea',
            'rows' => 2
        ),
        'features' => array(
            'label' => __('Features', 'wpu_acf_flexible'),
            'type' => 'repeater',
            'required' => 1,
            'min' => 1,
            'max' => 3,
            'sub_fields' => array(
                'image' => array(
                    'label' => __('Image', 'wpu_acf_flexible'),
                    'type' => 'image'
                ),
                'title' => array(
                    'label' => __('Title', 'wpu_acf_flexible')
                ),
                'content' => array(
                    'label' => __('Content', 'wpu_acf_flexible'),
                    'type' => 'textarea',
                    'rows' => 2
                ),
                'link' => array(
                    'label' => __('Button', 'wpu_acf_flexible'),
                    'type' => 'link'
                )
            )
        )
    )
);
