<?php

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
        'image' => array(
            'label' => __('Image', 'wpu_acf_flexible'),
            'type' => 'image',
            'required' => 1,
        )
    )
);
