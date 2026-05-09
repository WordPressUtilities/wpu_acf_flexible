<?php
defined('ABSPATH') || die;
$_url = get_sub_field('url');
if (!$_url) {
    return;
}

/* ----------------------------------------------------------
  Vars
---------------------------------------------------------- */

/* Hooks
-------------------------- */

$_content_before = apply_filters('wpu_acf_flexible__content__iframe__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__iframe__after', '');
$_mobile_breakpoint = apply_filters('wpu_acf_flexible__content__iframe__mobile_breakpoint', 768);
if (!is_numeric($_mobile_breakpoint)) {
    $_mobile_breakpoint = 768;
}

/* Iframe
-------------------------- */

/* Unique ID for this block instance */
$row_id = 'iframe_' . wpuacfflex_get_row_id();
$iframe_id = get_sub_field('iframe_id');
if ($iframe_id) {
    $row_id = trim(sanitize_html_class($iframe_id));
}
$wrapper_id = 'wrapper_' . $row_id;

/* Force https if needed */
$_url = is_ssl() ? str_replace('http://', 'https://', $_url) : $_url;

/* Height */
$_height = get_sub_field('height');
if (!is_numeric($_height)) {
    $_height = 500;
}

/* Iframe attributes */
$iframe_attributes = array(
    'id' => $row_id,
    'src' => esc_url($_url),
    'allowTransparency' => 'true',
    'style' => 'border:0;height:' . $_height . 'px;width:100%;'
);
if (get_sub_field('iframe_title')) {
    $iframe_attributes['title'] = get_sub_field('iframe_title');
}
if (get_sub_field('enable_lazy_loading')) {
    $iframe_attributes['loading'] = 'lazy';
}

$iframe_attributes_string = '';
foreach ($iframe_attributes as $attr => $value) {
    $iframe_attributes_string .= ' ' . $attr . '="' . esc_attr($value) . '"';
}

/* Extra CSS
-------------------------- */

$iframe_css = '';

/* Mobile height */
$mobile_height = get_sub_field('mobile_height');
if ($mobile_height) {
    $iframe_css .= '@media (max-width:' . $_mobile_breakpoint . 'px) {#' . $row_id . '{height:' . $mobile_height . 'px}}';
}

/* Full width iframe */
$full_width_iframe = get_sub_field('full_width_iframe');
if ($full_width_iframe) {
    $iframe_css .= '#' . $wrapper_id . '{padding:0 !important;}';
    $iframe_css .= '#' . $wrapper_id . ' > * {margin:0 !important;max-width:100%!important;box-sizing:border-box!important;}';
}

/* ----------------------------------------------------------
  Layout
---------------------------------------------------------- */

echo '<div id="' . $wrapper_id . '" class="' . get_wpu_acf_wrapper_classname('iframe') . '">';
echo $_content_before;
echo '<div class="block--iframe">';
echo '<iframe' . $iframe_attributes_string . '></iframe>';
echo '</div>';
if ($iframe_css) {
    echo '<style>' . $iframe_css . '</style>';
}
echo $_content_after;
echo '</div>';
