<?php
$model = array(
    'label' => __('[WPUACF] Anchor', 'wpu_acf_flexible'),
    'save_post' => false,
    'sub_fields' => array(
        'slug' => array(
            'label' => __('Slug', 'wpu_acf_flexible'),
            'instructions' => __('Please insert only lowercase letters without accents, numbers, and hyphens.', 'wpu_acf_flexible')
        ),
    )
);
