<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  Responsive Visibility
---------------------------------------------------------- */

add_filter('wpu_acf_flexible__field_types', function ($types) {
    $types['wpuacf_responsive_visibility'] = array(
        'label' => __('Responsive visibility', 'wpu_acf_flexible'),
        'type' => 'checkbox',
        'choices' => array(
            'desktop' => __('Hide on desktop', 'wpu_acf_flexible'),
            'tablet' => __('Hide on tablet', 'wpu_acf_flexible'),
            'mobile' => __('Hide on mobile', 'wpu_acf_flexible')
        )
    );
    return $types;
}, 10, 1);
