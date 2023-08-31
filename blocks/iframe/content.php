<?php
$_url = get_sub_field('url');
if (!$_url) {
    return;
}
$_height = get_sub_field('height');
if (!is_numeric($_height)) {
    $_height = 500;
}
$_content_before = apply_filters('wpu_acf_flexible__content__iframe__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__iframe__after', '');
echo '<div class="' . get_wpu_acf_wrapper_classname('iframe') . '">';
echo $_content_before;
echo '<div class="block--iframe">';
echo '<iframe src="' . esc_url($_url) . '" width="100%" height="' . $_height . '" frameborder="0" allowTransparency="true" style="border: 0"></iframe>';
echo '</div>';
echo $_content_after;
echo '</div>';
