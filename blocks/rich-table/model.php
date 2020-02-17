<?php

$model = array(
    'label' => __('[WPUACF] Table', 'wpu_acf_flexible'),
    'sub_fields' => array(
        'lines' => array(
            'label' => __('Table lines', 'wpu_acf_flexible'),
            'type' => 'repeater',
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => ''
            ),
            'acfe_repeater_stylised_button' => 0,
            'layout' => 'table',
            'button_label' => __('Add a line', 'wpu_acf_flexible'),
            'sub_fields' => array(
                'columns' => array(
                    'label' => __('Columns', 'wpu_acf_flexible'),
                    'type' => 'repeater',
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => ''
                    ),
                    'acfe_repeater_stylised_button' => 0,
                    'layout' => 'block',
                    'button_label' => __('Add a column', 'wpu_acf_flexible'),
                    'sub_fields' => array(
                        'tab_content' => array(
                            'label' => __('Content', 'wpu_acf_flexible'),
                            'type' => 'tab',
                            'placement' => 'top',
                            'endpoint' => 0
                        ),
                        'col1a' => array(
                            'type' => 'acfe_column',
                            'columns' => '4/6'
                        ),
                        'text' => array(
                            'label' => __('Text', 'wpu_acf_flexible'),
                            'type' => 'textarea',
                            'rows' => 3
                        ),
                        'col1b' => array(
                            'type' => 'acfe_column',
                            'columns' => '2/6'
                        ),
                        'image' => array(
                            'label' => __('Image', 'wpu_acf_flexible'),
                            'type' => 'image',
                            'acfe_uploader' => 'wp',
                            'acfe_thumbnail' => 0,
                            'return_format' => 'id',
                            'preview_size' => 'thumbnail',
                            'library' => 'all'
                        ),
                        'col1c' => array(
                            'type' => 'acfe_column',
                            'columns' => '1/6',
                            'endpoint' => 1
                        ),
                        'tab_layout' => array(
                            'label' => __('Layout', 'wpu_acf_flexible'),
                            'type' => 'tab',
                            'placement' => 'top',
                            'endpoint' => 0
                        ),
                        'col2a' => array(
                            'type' => 'acfe_column',
                            'columns' => '2/6'
                        ),
                        'cell_type' => array(
                            'label' => __('Cell type', 'wpu_acf_flexible'),
                            'type' => 'select',
                            'choices' => array(
                                'empty' => __('Empty', 'wpu_acf_flexible'),
                                'heading' => __('Heading', 'wpu_acf_flexible'),
                                'content' => __('Content', 'wpu_acf_flexible')
                            ),
                            'default_value' => 'content',
                            'layout' => 'vertical',
                            'return_format' => 'value'
                        ),
                        'col2b' => array(
                            'type' => 'acfe_column',
                            'columns' => '2/6'
                        ),
                        'nb_cols' => array(
                            'label' => __('Number of columns', 'wpu_acf_flexible'),
                            'type' => 'number',
                            'default_value' => 1
                        ),
                        'col2c' => array(
                            'type' => 'acfe_column',
                            'columns' => '2/6'
                        ),
                        'nb_rows' => array(
                            'label' => __('Number of rows', 'wpu_acf_flexible'),
                            'type' => 'number',
                            'default_value' => 1
                        ),
                        'col2d' => array(
                            'type' => 'acfe_column',
                            'columns' => '2/6',
                            'endpoint' => 1
                        )
                    )
                )
            )
        )
    )
);
