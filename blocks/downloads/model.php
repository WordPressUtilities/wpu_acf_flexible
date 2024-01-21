<?php
defined('ABSPATH') || die;

$model = array(
    'label' => __('[WPUACF] Downloads', 'wpu_acf_flexible'),
    'sub_fields' => array(
        'title' => array(
            'label' => __('Title', 'wpu_acf_flexible')
        ),
        'content' => array(
            'label' => __('Content', 'wpu_acf_flexible'),
            'type' => 'textarea',
            'rows' => 2
        ),
        'files' => array(
            'label' => __('Files', 'wpu_acf_flexible'),
            'type' => 'repeater',
            'sub_fields' => array(
                'file' => array(
                    'label' => __('File', 'wpu_acf_flexible'),
                    'type' => 'file'
                ),
                'filename' => array(
                    'label' => __('File name', 'wpu_acf_flexible'),
                    'instructions' => __('Uses file name by default', 'wpu_acf_flexible')
                ),
                'url' => array(
                    'label' => __('External file link', 'wpu_acf_flexible'),
                    'instructions' => __('Not used if a file exists', 'wpu_acf_flexible')
                )
            )
        )
    )
);
