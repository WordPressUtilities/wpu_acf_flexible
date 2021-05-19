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
        'cola' => 'wpuacf_33p',
        'cta' => array(
            'label' => sprintf(__('Button %s', 'wpu_acf_flexible'),1),
            'type' => 'link'
        ),
        'colb' => 'wpuacf_33p',
        'cta2' => array(
            'label' => sprintf(__('Button %s', 'wpu_acf_flexible'),2),
            'type' => 'link'
        ),
        'colc' => 'wpuacf_33p',
        'cta3' => array(
            'label' => sprintf(__('Button %s', 'wpu_acf_flexible'),3),
            'type' => 'link'
        )
    )
);
