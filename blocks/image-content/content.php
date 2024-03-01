<?php
defined('ABSPATH') || die;
$_image_id = get_sub_field('image');
if (!$_image_id) {
    return;
}
$_image_size = apply_filters('wpu_acf_flexible__content__image_content__image_size', 'medium', $_image_id);
$_image_type = apply_filters('wpu_acf_flexible__content__image_content__image_type', 'figure', $_image_id);
$_image = $_image_type == 'figure' ? get_wpu_acf_figure($_image_id, $_image_size) : get_wpu_acf_image($_image_id, $_image_size);
$_image_position = get_sub_field('image_position');
if (!$_image_position) {
    $_image_position = 'left';
}

$_image_container = '<div class="main-grid__image">' . $_image . '</div>';

$_title_position = apply_filters('wpu_acf_flexible__content__image_content__title_position', 'inner');
$_content_before = apply_filters('wpu_acf_flexible__content__image_content__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__image_content__after', '');
$_content_before_main = apply_filters('wpu_acf_flexible__content__image_content__main__before', '');
$_content_after_main = apply_filters('wpu_acf_flexible__content__image_content__main__after', '');

echo '<div data-image-position="' . esc_attr($_image_position) . '" class="' . get_wpu_acf_wrapper_classname('image-content') . '">';
echo $_content_before;
echo '<div class="block--image-content">';

echo $_title_position == 'over' ? get_wpu_acf__title() : '';

echo '<div class="acfflex-grid">';
echo $_image_position == 'left' ? $_image_container : '';
echo '<div class="main-grid__content">';
echo $_content_before_main;
echo $_title_position == 'inner' ? get_wpu_acf__title() : '';
echo get_wpu_acf__content();
echo get_wpu_acf_cta();
echo $_content_after_main;
echo '</div>';
echo $_image_position == 'right' ? $_image_container : '';
echo '</div>';

echo '</div>';
echo $_content_after;
echo '</div>';
