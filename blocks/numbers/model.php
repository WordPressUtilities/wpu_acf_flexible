<?php
defined('ABSPATH') || die;

$model = array(
    'label' => __('[WPUACF] Numbers', 'wpu_acf_flexible'),
    'sub_fields' => array(
        'title' => 'wpuacf_title',
        'content' => 'wpuacf_text',
        'numbers' => array(
            'required' => true,
            'label' => __('Numbers', 'wpu_acf_flexible'),
            'type' => 'repeater',
            'sub_fields' => array(
                'number' => array(
                    'label' => __('Number', 'wpu_acf_flexible'),
                    'type' => 'text',
                    'required' => true,
                    'maxlength' => 10
                ),
                'label' => array(
                    'label' => __('Label', 'wpu_acf_flexible')
                )
            )
        )
    )
);
