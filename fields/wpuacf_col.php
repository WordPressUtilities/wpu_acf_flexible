<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  Columns
---------------------------------------------------------- */

add_filter('wpu_acf_flexible__field_types', function ($types) {
    $types['wpuacf_100p'] = array(
        'type' => 'acfe_column',
        'columns' => '6/6',
        'endpoint' => true
    );
    $columns = array(
        '25' => '3/12',
        '33' => '4/12',
        '50' => '6/12',
        '66' => '8/12',
        '67' => '8/12',
        '75' => '9/12'
    );
    foreach ($columns as $col_width => $col_value) {
        $types['wpuacf_' . $col_width . 'p'] = array(
            'type' => 'acfe_column',
            'columns' => $col_value
        );
    }
    return $types;
}, 10, 1);
