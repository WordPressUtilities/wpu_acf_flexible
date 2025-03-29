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

$_content_before = apply_filters('wpu_acf_flexible__content__iframe__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__iframe__after', '');
echo '<div class="' . get_wpu_acf_wrapper_classname('iframe') . '">';
echo $_content_before;
echo '<div class="block--iframe">';
echo '<iframe id="' . $row_id . '" src="' . esc_url($_url) . '" width="100%" height="' . $_height . '" frameborder="0" allowTransparency="true" style="border: 0"></iframe>';
echo '</div>';
if ($mobile_height) {
    echo '<style>@media (max-width:768px) {#' . $row_id . '{height:' . $mobile_height . 'px}}</style>';
}
echo $_content_after;
echo '</div>';
