<?php

$_logos = get_sub_field('logos');
if (empty($_logos)) {
    return;
}

$_logos_list_classname = apply_filters('wpu_acf_flexible__content__logos__logoslist__classname', 'logos-list');
$_image_size = apply_filters('wpu_acf_flexible__content__logos__image_size', 'medium');
$_add_wrapper = apply_filters('wpu_acf_flexible__content__logos__add_wrapper', true);

$_logos = '';
while (has_sub_field('logos')) {
    $_logo_html = wp_get_attachment_image(get_sub_field('image'), $_image_size);
    $url = get_sub_field('url');
    if ($url) {
        $_logo_html = '<a class="field-url" href="' . $url . '">' . $_logo_html . '</a>';
    }
    if ($_add_wrapper) {
        $_logo_html = '<div class="logo-item">' . $_logo_html . '</div>';
    }
    $_logos .= '<li>' . $_logo_html . '</li>';
}
$_content_before = apply_filters('wpu_acf_flexible__content__logos__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__logos__after', '');

echo '<div class="' . get_wpu_acf_wrapper_classname('logos') . '">';
echo $_content_before;
echo '<div class="block--logos">' . get_wpu_acf_title_content();
echo '<ul class="' . $_logos_list_classname . '">' . $_logos . '</ul>';
echo '</div>';
echo $_content_after;
echo '</div>';
