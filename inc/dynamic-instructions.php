<?php
defined('ABSPATH') || die;

add_filter('acf/prepare_field', function ($field) {
    if (!isset($field['wpuacf_dynamic_instructions']) || empty($field['wpuacf_dynamic_instructions'])) {
        return $field;
    }
    $field['instructions'] = 'default_value';
    $field['wrapper']['data-dynamic-instructions'] = wp_json_encode(
        $field['wpuacf_dynamic_instructions']
    );

    return $field;
},1);
