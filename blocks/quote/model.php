<?php
defined('ABSPATH') || die;

$model = array(
    'label' => __('[WPUACF] Quote', 'wpu_acf_flexible'),
    'sub_fields' => array(
        'title' => array(
            'label' => __('Title', 'wpu_acf_flexible')
        ),

        'col1a' => array(
            'type' => 'acfe_column',
            'columns' => '3/6'
        ),
        'author' => array(
            'label' => __('Author', 'wpu_acf_flexible'),
        ),
        'col1b' => array(
            'type' => 'acfe_column',
            'columns' => '3/6'
        ),
        'author_details' => array(
            'label' => __('Author details', 'wpu_acf_flexible'),
        ),
        'col1c' => array(
            'type' => 'acfe_column',
            'columns' => '0/6',
            'endpoint' => 1
        ),
        'quote' => array(
            'label' => __('Quote', 'wpu_acf_flexible'),
            'type' => 'textarea',
            'rows' => 4,
            'required' => 1,
        ),
    )
);
