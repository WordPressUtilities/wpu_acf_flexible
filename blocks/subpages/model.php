<?php
defined('ABSPATH') || die;

$model = array(
    'key' => 'wpuacf_subpages',
    'label' => __('[WPUACF] Subpages', 'wpu_acf_flexible'),
    'no_save_post' => true,
    'sub_fields' => array(
        'title' => 'wpuacf_title',
        'type' => array(
            'label' => __('Type', 'wpu_acf_flexible'),
            'type' => 'select',
            'default_value' => 'siblings',
            'choices' => array(
                'siblings' => __('Sibling pages', 'wpu_acf_flexible'),
                'children' => __('Child pages', 'wpu_acf_flexible')
            )
        )
    )
);
