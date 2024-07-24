<?php
defined('ABSPATH') || die;
$columns_max = apply_filters('wpu_acf_flexible__model__columns__columns_max', 3);
$model = array(
    'label' => __('[WPUACF] Columns', 'wpu_acf_flexible'),
    'save_post' => false,
    'sub_fields' => array(
        'columns' => array(
            'label' => __('Columns', 'wpu_acf_flexible'),
            'type' => 'repeater',
            'min' => 1,
            'max' => $columns_max,
            'layout' => 'block',
            'sub_fields' => array(
                /* Text */
                'tab_text' => array(
                    'label' => __('Content', 'wpu_acf_flexible'),
                    'type' => 'tab'
                ),
                'surtitle' => array(
                    'label' => __('Surtitle', 'wpu_acf_flexible'),
                    'type' => 'text'
                ),
                'title' => array(
                    'label' => __('Title', 'wpu_acf_flexible'),
                    'type' => 'text'
                ),
                'content' => array(
                    'label' => __('Content', 'wpu_acf_flexible'),
                    'type' => 'wpuacf_minieditor',
                    'media_upload' => 0
                ),
                /* Image */
                'tab_image' => array(
                    'label' => __('Image', 'wpu_acf_flexible'),
                    'type' => 'tab'
                ),
                'image' => array(
                    'label' => __('Image', 'wpu_acf_flexible'),
                    'type' => 'image',
                    'preview_size' => 'thumbnail'
                ),
                /* Slider */
                'tab_slider' => array(
                    'label' => __('Slider', 'wpu_acf_flexible'),
                    'type' => 'tab'
                ),
                'slider' => 'wpuacf_slider',
                /* Embed */
                'tab_embed' => array(
                    'label' => __('Video', 'wpu_acf_flexible'),
                    'type' => 'tab'
                ),
                'embed' => 'wpuacf_embed',
                /* Action */
                'tab_action' => array(
                    'label' => __('Call to action', 'wpu_acf_flexible'),
                    'type' => 'tab'
                ),
                'tabcta_cola' => 'wpuacf_50p',
                'cta_main' => array(
                    'label' => __('Main action', 'wpu_acf_flexible'),
                    'type' => 'wpuacf_cta'
                ),
                'tabcta_colb' => 'wpuacf_50p',
                'cta_secondary' => array(
                    'label' => __('Secondary action', 'wpu_acf_flexible'),
                    'type' => 'wpuacf_cta'
                ),
                'tabcta_colc' => 'wpuacf_100p'
            )
        )
    )
);
