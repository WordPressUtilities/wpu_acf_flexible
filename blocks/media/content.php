<?php
defined('ABSPATH') || die;

$media_type = get_sub_field('media_type');
if (!$media_type) {
    return;
}

$block_content = '';
$image_size = apply_filters('wpu_acf_flexible__content__media__image_size', 'large');
$has_srcset = apply_filters('wpu_acf_flexible__content__media__has_srcset', true);

switch ($media_type) {
case 'image':
    $image = get_sub_field('image');
    if ($image) {
        $block_content .= '<div class="block-media__image"><div class="img">' . get_wpu_acf_image($image, $image_size, array(
            'has_srcset' => $has_srcset
        )) . '</div></div>';
    }
    break;
case 'embed':
    $embed = get_sub_field('embed');
    if ($embed) {
        $block_content .= get_wpu_acf_video_embed_image(array(
            'video_field' => $embed['embed'],
            'use_thumb' => $embed['use_thumb'],
            'image_field' => $embed['cover_image']
        ));
    }
    break;
case 'slider':
    $slider = get_sub_field('slider');
    $block_content .= get_wpu_acf_slider($slider, $image_size);
    break;
}

/* ----------------------------------------------------------
  Content
---------------------------------------------------------- */

echo '<section class="centered-container cc-block--media ' . get_wpu_acf_wrapper_classname('media') . '" data-media-type="' . esc_attr($media_type) . '"><div class="block--media">';
echo apply_filters('wpu_acf_flexible__content__media__before', '');
echo get_wpu_acf_title_content();
echo apply_filters('wpu_acf_flexible__content__media__between', '');
echo $block_content;
echo apply_filters('wpu_acf_flexible__content__media__after', '');
echo '</div></section>';
