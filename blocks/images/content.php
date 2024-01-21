<?php
defined('ABSPATH') || die;
$_image_size = apply_filters('wpu_acf_flexible__content__images__image_size', 'medium');
$_images = get_sub_field('images');
if (empty($_images)) {
    return;
}

$_content_before = apply_filters('wpu_acf_flexible__content__images__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__images__after', '');
echo '<div class="' . get_wpu_acf_wrapper_classname('images') . '">';
echo $_content_before;
echo '<div class="block--images acfflex-grid">';
foreach ($_images as $_image) {
    echo '<div class="block--images__item">';
    echo get_wpu_acf_figure($_image['image'], $_image_size);
    echo '</div>';
}
echo '</div>';
echo $_content_after;
echo '</div>';
