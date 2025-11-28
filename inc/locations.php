<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  Custom location rule : value of a true/false meta field
---------------------------------------------------------- */

/*
    Example :
'location' => array(
    array(
        array(
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'page'
        ),
        array(
            'param' => 'wpuacfflex_meta_value_truefalse',
            'operator' => '==',
            'value' => 'myproject_enable_feature'
        )
    )
),
*/

add_filter('acf/location/rule_match/wpuacfflex_meta_value_truefalse', function ($match, $rule, $screen) {
    if (!isset($screen['post_id']) || !$screen['post_id']) {
        return $match;
    }
    $meta = intval(get_post_meta($screen['post_id'], $rule['value'], true), 10);
    if ($rule['operator'] === '==') {
        return ($meta === 1);
    } elseif ($rule['operator'] === '!=') {
        return ($meta !== 1);
    }
    return $match;
}, 10, 3);
