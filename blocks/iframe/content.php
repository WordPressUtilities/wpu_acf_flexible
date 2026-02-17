<?php
defined('ABSPATH') || die;
$_url = get_sub_field('url');
if (!$_url) {
    return;
}

# Force https if needed
$_url = is_ssl() ? str_replace('http://', 'https://', $_url) : $_url;

$_height = get_sub_field('height');
if (!is_numeric($_height)) {
    $_height = 500;
}

$mobile_height = get_sub_field('mobile_height');

$row_id = 'iframe_' . wpuacfflex_get_row_id();
$iframe_id = get_sub_field('iframe_id');
if ($iframe_id) {
    $row_id = trim(sanitize_html_class($iframe_id));
}

$iframe_attributes = array(
    'id' => $row_id,
    'src' => esc_url($_url),
    'allowTransparency' => 'true',
    'style' => 'border:0;height:' . $_height . 'px;width:100%;',
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

$_content_before = apply_filters('wpu_acf_flexible__content__iframe__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__iframe__after', '');
echo '<div class="' . get_wpu_acf_wrapper_classname('iframe') . '">';
echo $_content_before;
echo '<div class="block--iframe">';
echo '<iframe' . $iframe_attributes_string . '></iframe>';
echo '</div>';
if ($mobile_height) {
    echo '<style>@media (max-width:768px) {#' . $row_id . '{height:' . $mobile_height . 'px}}</style>';
}
echo $_content_after;
echo '</div>';
