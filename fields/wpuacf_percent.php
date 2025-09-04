<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  Percent
---------------------------------------------------------- */

add_filter('wpu_acf_flexible__field_types', function ($types) {
    $types['wpuacf_percent'] = array(
        'label' => 'Percent',
        'append' => '%',
        'type' => 'number',
        'default_value' => 100,
        'min' => 0,
        'max' => 100
    );
    return $types;
}, 10, 1);

function get_wpuacf_percent($field) {
    if (!$field || !ctype_digit($field)) {
        return '';
    }
    return '<span class="wpuacf-percent">' . esc_html($field) . '%</span>';
}
