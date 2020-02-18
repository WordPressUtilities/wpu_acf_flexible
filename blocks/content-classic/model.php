<?php

$model = array(
    'label' => __('[WPUACF] Content', 'wpu_acf_flexible'),
    'sub_fields' => array(
        'title' => array(
            'label' => __('Title', 'wpu_acf_flexible'),
            'type' => 'text'
        ),
        'content' => array(
            'label' => __('Content', 'wpu_acf_flexible'),
            'type' => 'editor',
            'toolbar' => 'full'
        ),
        'cta' => array(
            'label' => __('Button', 'wpu_acf_flexible'),
            'type' => 'link'
        )
    )
);
