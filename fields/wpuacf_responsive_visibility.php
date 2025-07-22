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


/**
 * Get the CSS class names for responsive visibility.
 *
 * @param array $field_value The field value.
 * @return array The CSS class names.
 */
function get_wpuacf_responsive_visibility_classnames($field_value = array()) {
    if (!is_array($field_value) || empty($field_value)) {
        return array();
    }

    $classnames = array();
    if (in_array('desktop', $field_value)) {
        $classnames[] = 'hidden-on-full';
    }
    if (in_array('tablet', $field_value)) {
        $classnames[] = 'hidden-on-tablet';
    }
    if (in_array('phone', $field_value) || in_array('mobile', $field_value)) {
        $classnames[] = 'hidden-on-phone';
    }
    return $classnames;
}
