<?php

$model = array(
    'label' => __('[WPUACF] Team Quote', 'wpu_acf_flexible'),
    'sub_fields' => array(
        'title' => array(
            'label' => __('Title', 'wpu_acf_flexible')
        ),
        'col1a' => array(
            'type' => 'acfe_column',
            'columns' => '2/6'
        ),
        'image' => array(
            'label' => __('Author image', 'wpu_acf_flexible'),
            'type' => 'image',
            'required' => 1
        ),
        'col1b' => array(
            'type' => 'acfe_column',
            'columns' => '2/6'
        ),
        'author' => array(
            'label' => __('Author', 'wpu_acf_flexible'),
            'required' => 1
        ),
        'col1c' => array(
            'type' => 'acfe_column',
            'columns' => '2/6'
        ),
        'author_details' => array(
            'label' => __('Author details', 'wpu_acf_flexible')
        ),
        'col1d' => array(
            'type' => 'acfe_column',
            'columns' => '6/6',
            'endpoint' => 1
        ),
        'quote' => array(
            'label' => __('Quote', 'wpu_acf_flexible'),
            'type' => 'textarea',
            'rows' => 4,
            'required' => 1
        )
    )
);
