<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  CTA
---------------------------------------------------------- */

function get_wpu_acf_link($link, $classname = '', $attributes = '', $args = array()) {
    if ($link && is_string($link) && substr($link, 0, 1) == '{') {
        $link = json_decode($link, true);
    }
    if (is_array($link)) {
        if (!isset($link['url']) && isset($link['href'])) {
            $link['url'] = $link['href'];
        }
        if (!isset($link['title']) && isset($link['text'])) {
            $link['title'] = $link['text'];
        }
        if (!isset($link['title_visible'])) {
            $link['title_visible'] = $link['title'];
        }
        $link = array_merge(array(
            'target' => '',
            'before_span' => '',
            'after_span' => ''
        ), $link);
    }
    if (!$link || !is_array($link) || !isset($link['url'])) {
        return '';
    }
    if (is_array($args) && $args) {
        $link = array_merge($link, $args);
    }

    $link = apply_filters('get_wpu_acf_link__link', $link);
    $link['title_visible'] = strip_tags($link['title_visible'], '<u><i><strong><em><span><img>');
    $classname = apply_filters('get_wpu_acf_link_classname', $classname);
    return '<a title="' . esc_attr(strip_tags($link['title'])) . '"' .
    ' class="acfflex-link ' . esc_attr($classname) . '"' .
        ' ' . $attributes .
        ' rel="noopener" ' .
        ($link['target'] ? ' target="' . $link['target'] . '"' : '') .
        ' href="' . $link['url'] . '">' .
        $link['before_span'] .
        '<span>' . $link['title_visible'] . '</span>' .
        $link['after_span'] .
        '</a>';
}

/**
 * Get a link with a wrapper
 * @return string
 */
function get_wpu_acf_cta($link_item = 'cta', $classname = '', $attributes = '') {
    if (is_string($link_item) && substr($link_item, 0, 2) == '{"') {
        $_cta_link = json_decode($link_item, true);
    } elseif (is_array($link_item)) {
        $_cta_link = $link_item;
    } else {
        $_cta_link = get_sub_field($link_item);
    }
    $_return = '';
    if (is_array($_cta_link)) {
        $_return .= '<div class="field-cta">' . get_wpu_acf_link($_cta_link, $classname, $attributes) . '</div>';
    }
    return $_return;
}

/**
 * Get a CTA with an image
 * @param  array  $field
 * @param  string $classname
 * @param  string $attributes
 * @param  array  $args
 * @return string
 */
function get_wpu_acf_imagecta($field, $classname = '', $attributes = '', $args = array()) {
    if (!is_array($field) || !isset($field['image'], $field['cta'])) {
        return '';
    }
    $image_size = is_array($args) && isset($args['image_size']) ? $args['image_size'] : 'thumbnail';
    $html = get_wpu_acf_image($field['image'], $image_size);
    if ($field['cta'] && $html) {
        $field['cta']['title_visible'] = $html;
        $html = get_wpu_acf_link($field['cta'], $classname, $attributes);
    }
    return $html;
}
