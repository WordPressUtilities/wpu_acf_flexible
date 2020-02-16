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
                    'button_label' => 'Add a column',
                    'sub_fields' => array(
                        'tab_content' => array(
                            'label' => __('Content', 'wpu_acf_flexible'),
                            'type' => 'tab',
                            'placement' => 'top',
                            'endpoint' => 0
                        ),
                        'text' => array(
                            'label' => __('Text', 'wpu_acf_flexible'),
                            'type' => 'textarea',
                            'rows' => 3
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
                        'tab_layout' => array(
                            'label' => __('Layout', 'wpu_acf_flexible'),
                            'type' => 'tab',
                            'placement' => 'top',
                            'endpoint' => 0
                        ),
                        'cell_type' => array(
                            'label' => __('Cell type', 'wpu_acf_flexible'),
                            'type' => 'radio',
                            'choices' => array(
                                'empty' => __('Empty', 'wpu_acf_flexible'),
                                'heading' => __('Heading', 'wpu_acf_flexible'),
                                'content' => __('Content', 'wpu_acf_flexible')
                            ),
                            'default_value' => 'content',
                            'layout' => 'vertical',
                            'return_format' => 'value'
                        ),
                        'col1' => array(
                            'type' => 'acfe_column',
                            'columns' => '3/6'
                        ),
                        'nb_cols' => array(
                            'label' => __('Column span', 'wpu_acf_flexible'),
                            'type' => 'number',
                            'default_value' => 1
                        ),
                        'col2' => array(
                            'type' => 'acfe_column',
                            'columns' => '3/6'
                        ),
                        'nb_rows' => array(
                            'label' => __('Row span', 'wpu_acf_flexible'),
                            'type' => 'number',
                            'default_value' => 1
                        )
                    )
                )
            )
        )
    )
);
