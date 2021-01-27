<?php

$model = array(
    'key' => 'wpuacf_posts',
    'label' => __('[WPUACF] Posts', 'wpu_acf_flexible'),
    'sub_fields' => array(
        'title' => array(
            'label' => __('Title', 'wpu_acf_flexible')
        ),
        'content' => array(
            'label' => __('Content', 'wpu_acf_flexible'),
            'type' => 'textarea',
            'rows' => 2
        ),
        'type' => array(
            'label' => __('Type', 'wpu_acf_flexible'),
            'type' => 'select',
            'choices' => array(
                'last_posts' => __('Last posts', 'wpu_acf_flexible'),
                'child_posts' => __('Child posts', 'wpu_acf_flexible'),
                'manual_posts' => __('Manual posts', 'wpu_acf_flexible')
            )
        ),
        'p' => array(
            'label' => __('Posts', 'wpu_acf_flexible'),
            'type' => 'relationship',
            'return_format' => 'id',
            'post_type' => array(
                'post',
                'page'
            ),
            'conditional_logic' => array(
                array(
                    array(
                        'field' => 'wpuacf_poststype',
                        'operator' => '==',
                        'value' => 'manual_posts'
                    )
                )
            )
        )
    )
);
