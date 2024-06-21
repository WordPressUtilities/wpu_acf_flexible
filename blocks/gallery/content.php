<?php
defined('ABSPATH') || die;

$_images = get_sub_field('images');
if (empty($_images)) {
    return;
}

$gallery_uniqid = 'gallery-' . uniqid();

$_image_size = apply_filters('wpu_acf_flexible__content__gallery__image_size', 'medium');
$_image_size_large = apply_filters('wpu_acf_flexible__content__gallery__image_size_big', 'large');
$_gallery_classname = apply_filters('wpu_acf_flexible__content__gallery__list__classname', 'gallery-list');
$_gallery_use_dialog = apply_filters('wpu_acf_flexible__content__gallery__use_dialog', '__return_false');
$_gallery_use_navigation = apply_filters('wpu_acf_flexible__content__gallery__use_navigation', '__return_false');

$_gallery_html = '';
$_gallery_after = '';
foreach ($_images as $_i => $_image) {
    $_image_html = wp_get_attachment_image($_image, $_image_size);
    $_image_url_large = wp_get_attachment_image_src($_image, $_image_size_large);
    $img_id = $gallery_uniqid . '-' . $_i;
    if ($_image_url_large[0]) {
        $attr = apply_filters('wpu_acf_flexible__content__gallery__link_attributes', 'href="' . $_image_url_large[0] . '"', $_i, $gallery_uniqid);
        if ($_gallery_use_dialog) {
            $attr .= ' data-acf-dialog-target="' . $img_id . '"';
            $_gallery_after .= '<dialog data-acf-dialog-type="image" id="' . $img_id . '">';
            $_gallery_after .= wp_get_attachment_image($_image, $_image_size_large, array('loading' => 'lazy'));
            $_gallery_after .= '<button data-acf-dialog-close><span>' . __('Close', 'wpu_acf_flexible') . '</span></button>';
            if ($_gallery_use_navigation) {
                $_gallery_after .= '<button data-acf-dialog-prev><span>' . __('Previous', 'wpu_acf_flexible') . '</span></button>';
                $_gallery_after .= '<button data-acf-dialog-next><span>' . __('Next', 'wpu_acf_flexible') . '</span></button>';
            }
            $_gallery_after .= '</dialog>';
        }
        $_image_html = '<a ' . $attr . '>' . $_image_html . '</a>';
    }
    $_gallery_html .= '<li><div class="image-item">' . $_image_html . '</div></li>';
}

$_content_before = apply_filters('wpu_acf_flexible__content__gallery__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__gallery__after', '', $gallery_uniqid);

echo '<div class="' . get_wpu_acf_wrapper_classname('gallery') . '">';
echo $_content_before;
echo '<div class="block--gallery" ' . ($_gallery_use_dialog ? 'data-acf-dialog-group=""' : '') . '>';
echo get_wpu_acf_title_content();
echo '<ul class="' . $_gallery_classname . '">' . $_gallery_html . '</ul>';
echo $_gallery_after;
echo '</div>';
echo $_content_after;
echo '</div>';
