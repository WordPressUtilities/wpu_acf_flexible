<?php
$model = array(
    'label' => __('[WPUACF] Video', 'wpu_acf_flexible'),
    'sub_fields' => array(
        'title' => array(
            'label' => __('Title', 'wpu_acf_flexible')
        ),
        'content' => array(
            'label' => __('Content', 'wpu_acf_flexible'),
            'type' => 'textarea',
            'rows' => 2
        ),
        'video' => array(
            'label' => __('Video', 'wpu_acf_flexible'),
            'type' => 'oembed',
            'required' => 1,
        )
    )
);
