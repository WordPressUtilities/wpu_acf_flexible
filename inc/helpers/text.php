<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  Text
---------------------------------------------------------- */

function get_wpu_acf_text($field_value, $args = array()) {
    if (!is_array($args)) {
        $args = array();
    }

    /* Null field */
    if (!$field_value) {
        return '';
    }

    $args = array_merge(array(
        'classname' => 'field-text cssc-content',
        'allowed_tags' => ''
    ), $args);

    /* Handle an edge case with single brackets */
    if (strpos($field_value, '<') !== false XOR strpos($field_value, '>') !== false) {
        $field_value = str_replace('<', '&lt;', $field_value);
        $field_value = str_replace('>', '&gt;', $field_value);
    }

    $field_value = trim(strip_tags($field_value, $args['allowed_tags']));
    if (!$field_value) {
        return '';
    }
    return '<div class="' . esc_attr($args['classname']) . '">' . wpautop($field_value) . '</div>';
}

/* Basic field
-------------------------- */

function get_wpu_acf_field_html($field_value, $args = array()) {
    if (!$field_value) {
        return '';
    }

    $args = array_merge(array(
        'tag' => 'div',
        'classname' => 'field',
        'allowed_tags' => ''
    ), $args);

    $field_value = trim(strip_tags($field_value, $args['allowed_tags']));
    if (!$field_value) {
        return '';
    }

    return '<' . esc_attr($args['tag']) . ' class="' . esc_attr($args['classname']) . '">' . $field_value . '</' . esc_attr($args['tag']) . '>';
}

/* ----------------------------------------------------------
  Mini editor
---------------------------------------------------------- */

function get_wpu_acf_minieditor($field, $args = array()) {
    if (!is_array($args)) {
        $args = array();
    }
    if (!isset($args['allowed_tags'])) {
        $args['allowed_tags'] = '<u><a><strong><span><em><p>';
    }
    if (isset($args['extra_allowed_tags'])) {
        $args['allowed_tags'] .= $args['extra_allowed_tags'];
    }
    if (!isset($args['add_wrapper'])) {
        $args['add_wrapper'] = false;
    }
    if (!isset($args['wrapper_classname'])) {
        $args['wrapper_classname'] = 'field-minieditor cssc-content';
    }
    $args['allowed_tags'] = apply_filters('wpu_acf_flexible__get_wpu_acf_minieditor__allowed_tags', $args['allowed_tags'], $field, $args);
    if ($field) {
        $field = strip_tags($field, $args['allowed_tags']);
    }
    $field = apply_filters('wpu_acf_flexible__get_wpu_acf_minieditor__before_wpautop', $field, $args['allowed_tags']);
    $field_content = wpautop($field);
    if (!$field_content) {
        return '';
    }
    if ($args['add_wrapper']) {
        return '<div class="' . esc_attr($args['wrapper_classname']) . '">' . $field_content . '</div>';
    }
    return $field_content;
}
