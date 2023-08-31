<?php

$model = array(
    'label' => __('[WPUACF] Iframe', 'wpu_acf_flexible'),
    'sub_fields' => array(
        'url' => array(
            'label' => __('URL', 'wpu_acf_flexible'),
            'type' => 'url',
            'required' => true
        ),
        'height' => array(
            'label' => __('Iframe Height', 'wpu_acf_flexible'),
            'type' => 'number',
            'default_value' => 500
        )
    )
);
