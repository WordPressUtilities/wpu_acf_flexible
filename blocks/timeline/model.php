<?php

defined('ABSPATH') || die;

$model = array(
    'label' => __('[WPUACF] Timeline', 'wpu_acf_flexible'),
    'sub_fields' => array(
        'events' => array(
            'required' => true,
            'min' => 1,
            'label' => __('Events', 'wpu_acf_flexible'),
            'type' => 'repeater',
            'layout' => 'block',
            'button_label' => __('Add an event', 'wpu_acf_flexible'),
            'sub_fields' => array(
                'cola' => 'wpuacf_33p',
                'date' => array(
                    'label' => __('Date', 'wpu_acf_flexible'),
                    'wpuacf_example_values' => array(
                        2024,
                        'March 15',
                        '15/03/2024',
                    )
                ),
                'image' => array(
                    'required' => false,
                    'type' => 'wpuacf_image'
                ),
                'colb' => 'wpuacf_66p',
                'title' => 'wpuacf_title',
                'text' => array(
                    'label' => __('Content', 'wpu_acf_flexible'),
                    'type' => 'wpuacf_minieditor',
                    'editor_height' => 100
                ),
                'cta' => 'wpuacf_cta'
            )
        )
    )
);
