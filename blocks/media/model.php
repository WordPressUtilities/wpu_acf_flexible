<?php
defined('ABSPATH') || die;

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
        'media_type' => array(
            'type' => 'select',
            'label' => __('Media type', 'wpu_acf_flexible'),
            'choices' => array(
                'image' => __('Image', 'wpu_acf_flexible'),
                'embed' => __('Embed', 'wpu_acf_flexible'),
                'slider' => __('Slider', 'wpu_acf_flexible')
            )
        ),
        'image' => array(
            'type' => 'wpuacf_image',
            'wpuacf_condition' => array(
                'media_type' => 'image'
            )
        ),
        'embed' => array(
            'label' => __('Embed', 'wpu_acf_flexible'),
            'type' => 'wpuacf_embed',
            'wpuacf_condition' => array(
                'media_type' => 'embed'
            )
        ),
        'slider' => array(
            'label' => __('Slider', 'wpu_acf_flexible'),
            'type' => 'wpuacf_slider',
            'wpuacf_condition' => array(
                'media_type' => 'slider'
            )
        )
    )
);
