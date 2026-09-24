<?php
defined('ABSPATH') || die;

$_logos = get_sub_field('logos');
if (empty($_logos)) {
    return;
}

$_logos_list_classname = apply_filters('wpu_acf_flexible__content__logos__logoslist__classname', 'logos-list');
$_image_size = apply_filters('wpu_acf_flexible__content__logos__image_size', 'medium');
$_add_wrapper = apply_filters('wpu_acf_flexible__content__logos__add_wrapper', true);
$_logos_target = apply_filters('wpu_acf_flexible__content__logos__target', '');

$_logos_html = '';
while (has_sub_field('logos')) {
    $_logo_html = wp_get_attachment_image(get_sub_field('image'), $_image_size);
    $url = get_sub_field('url');
    if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
        $link_attributes = array(
            'class' => 'field-url',
            'href' => $url
        );

        /* Target */
        $target_value = '';
        if ($_logos_target) {
            $target_value = $_logos_target;
        } else {
            $target_value = wpuacfflex_is_external_link($url) ? '_blank' : '';
        }
        if ($target_value) {
            $link_attributes['target'] = $target_value;
        }

        $link_attributes = apply_filters('wpu_acf_flexible__content__logos__link_attributes', $link_attributes, $url);

        $link_attributes_str = '';
        foreach ($link_attributes as $attr => $value) {
            $link_attributes_str .= ' ' . esc_attr($attr) . '="' . esc_attr($value) . '"';
        }

        $_logo_html = '<a' . $link_attributes_str . '>' . $_logo_html . '</a>';
    }
    if ($_add_wrapper) {
        $_logo_html = '<div class="logo-item">' . $_logo_html . '</div>';
    }
    $_logos_html .= '<li>' . $_logo_html . '</li>';
}
$_content_before = apply_filters('wpu_acf_flexible__content__logos__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__logos__after', '');

echo '<section data-count="' . count($_logos) . '" class="' . get_wpu_acf_wrapper_classname('logos') . '">';
echo $_content_before;
echo '<div class="block--logos">' . get_wpu_acf_title_content();
echo '<ul class="' . $_logos_list_classname . '">' . $_logos_html . '</ul>';
echo '</div>';
echo $_content_after;
echo '</section>';
