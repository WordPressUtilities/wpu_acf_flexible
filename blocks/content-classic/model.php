<?php
defined('ABSPATH') || die;

$nb_buttons = apply_filters('wpu_acf_flexible__content__content_classic__buttons_number', 3);

$model = array(
    'label' => __('[WPUACF] Content', 'wpu_acf_flexible'),
    'sub_fields' => array(
        'title' => array(
            'label' => __('Title', 'wpu_acf_flexible'),
            'type' => 'text'
        ),
        'content' => array(
            'label' => __('Content', 'wpu_acf_flexible'),
            'type' => 'editor',
            'toolbar' => 'full'
        ),
        'cta' => array(
            'label' => $nb_buttons == 1 ? __('Button', 'wpu_acf_flexible') : sprintf(__('Button %s', 'wpu_acf_flexible'), 1),
            'type' => 'link'
        )
    )
);

for ($i = 2; $i <= $nb_buttons; $i++) {
    $model['sub_fields']['cta' . $i] = array(
        'label' => sprintf(__('Button %s', 'wpu_acf_flexible'), $i),
        'type' => 'link'
    );
}
