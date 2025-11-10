<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  Media
---------------------------------------------------------- */

function get_wpu_acf_video($video_id, $args = array()) {
    if (!is_numeric($video_id)) {
        return '';
    }
    $attachment_url = wp_get_attachment_url($video_id);
    if (!$attachment_url) {
        return '';
    }
    if (!is_array($args)) {
        $args = array();
    }
    $args['data-wpu-acf-video'] = '1';

    $src_attr = 'src';
    if (isset($args['data-intersect-only']) || isset($args['data-mobile-only']) || isset($args['data-desktop-only'])) {
        $src_attr = 'data-src';
    }

    $item_src = '<video';
    foreach ($args as $k => $v) {
        $item_src .= ' ' . $k . '="' . esc_attr($v) . '"';
    }
    $item_src .= ' autoplay loop muted playsinline><source ' . $src_attr . '="' . $attachment_url . '" type="video/mp4" /></video>';
    return $item_src;
}

function get_wpu_acf_image_src($image, $size = 'thumbnail') {
    if (is_array($image) && isset($image['ID']) && is_numeric($image['ID'])) {
        $image = $image['ID'];
    }
    if (!is_numeric($image)) {
        return '';
    }
    $image = wp_get_attachment_image_src($image, $size);
    return is_array($image) ? $image[0] : '';
}

function get_wpu_acf_image($image, $size = 'thumbnail', $attr = array()) {
    if (is_array($image) && isset($image['ID']) && is_numeric($image['ID'])) {
        $image = $image['ID'];
    }
    if (!is_numeric($image)) {
        return '';
    }

    if (!is_array($attr)) {
        $attr = array();
    }
    if (!isset($attr['loading'])) {
        $attr['loading'] = 'lazy';
    }
    $attr = apply_filters('get_wpu_acf_image__image_attr', $attr, $image, $size);

    $has_srcset = apply_filters('get_wpu_acf_image__has_srcset', false);
    if (isset($attr['has_srcset'])) {
        $has_srcset = $attr['has_srcset'];
        unset($attr['has_srcset']);
        $attr['srcset'] = '';
    }
    if (!isset($attr['srcset'])) {
        $has_srcset = true;
    }

    /* Force default alt text if needed */
    if (apply_filters('get_wpu_acf_image__force_alt', false)) {
        $alt_text = get_post_meta($image, '_wp_attachment_image_alt', true);
        if (empty($alt_text)) {
            $attr['alt'] = apply_filters('get_wpu_acf_image__default_alt', get_the_title($image));
        }
    }

    /* Retrieve image HTML without srcset */
    if (!$has_srcset) {
        add_filter('wp_calculate_image_srcset_meta', '__return_null');
    }
    $html = wp_get_attachment_image($image, $size, false, $attr);
    if (!$has_srcset) {
        remove_filter('wp_calculate_image_srcset_meta', '__return_null');
    }
    return $html;
}

function get_wpu_acf_figure($image, $size = 'thumbnail', $attr = array()) {
    if (!is_numeric($image)) {
        return '';
    }

    /* Load default attributes */
    if (!is_array($attr)) {
        $attr = array();
    }
    $default_attr = array(
        'figcaption' => true,
        'figcaption_content' => '',
        'img_wrapper' => false
    );
    foreach ($default_attr as $k => $v) {
        if (!isset($attr[$k])) {
            $attr[$k] = $v;
        }
    }

    /* Keep only valid attributes in image */
    $image_attr = $attr;
    foreach ($default_attr as $k => $v) {
        if (isset($image_attr[$k])) {
            unset($image_attr[$k]);
        }
    }

    $html = get_wpu_acf_image($image, $size, $image_attr);
    if ($attr['img_wrapper']) {
        $html = '<div class="figure-img-wrapper">' . $html . '</div>';
    }

    if (apply_filters('get_wpu_acf_figure__display_figcaption', $attr['figcaption'])) {
        $thumb_details = get_post($image);
        $_figure_content = '';
        if ($attr['figcaption_content']) {
            $_figure_content .= '<p class="figure-title">' . trim($attr['figcaption_content']) . '</p>';
        } else {
            if (isset($thumb_details->post_title) && $thumb_details->post_title) {
                $_figure_content .= '<p class="figure-title">' . trim($thumb_details->post_title) . '</p>';
            }
            if (isset($thumb_details->post_excerpt) && $thumb_details->post_excerpt) {
                $_figure_content .= '<p class="figure-excerpt">' . trim($thumb_details->post_excerpt) . '</p>';
            }
        }
        if (!empty($_figure_content)) {
            $html .= '<figcaption>' . $_figure_content . '</figcaption>';
        }
    }

    return '<figure class="acfflex-figure">' . $html . '</figure>';
}

function get_wpu_acf_responsive_image($field_value, $classname = '', $args = array()) {
    if (!is_array($field_value) || !isset($field_value['image'])) {
        return '';
    }
    if (!is_array($args)) {
        $args = array();
    }
    $args = array_merge(array(
        'mobile_max' => 767,
        'mobile_size' => 'large',
        'desktop_size' => 'large'
    ), $args);

    $mobile_max = apply_filters('get_wpu_acf_responsive_image__mobile_max', $args['mobile_max']);
    $classname = apply_filters('get_wpu_acf_responsive_image__classname', 'wpu-acf-responsive-image ' . $classname);
    $html = '<picture class="' . trim(esc_attr($classname)) . '">';
    if (isset($field_value['image_mobile']) && $field_value['image_mobile']):
        $html .= '<source media="(max-width: ' . $mobile_max . 'px)" srcset="' . get_wpu_acf_image_src($field_value['image_mobile'], $args['mobile_size']) . '">';
    endif;
    $html .= get_wpu_acf_image($field_value['image'], $args['desktop_size'], array(
        'loading' => 'lazy',
        'has_srcset' => apply_filters('get_wpu_acf_responsive_image__has_srcset', false)
    ));
    $html .= '</picture>';
    return $html;
}

function get_wpu_acf_slider($slider, $image_size = 'large') {
    if (!$slider || !$slider['gallery']) {
        return '';
    }
    $slider_html = '';
    $slider_attributes = '';
    if ($slider['slider_options']['autoplay']) {
        $slider_attributes .= ' data-slider-autoplay="' . $slider['slider_options']['autoplay'] . '"';
    }
    if ($slider['slider_options']['autoplay_speed']) {
        $slider_attributes .= ' data-slider-autoplay-speed="' . $slider['slider_options']['autoplay_speed'] . '"';
    }
    $slider_html .= '<div class="wpuacf-slider " ' . $slider_attributes . '>';
    foreach ($slider['gallery'] as $img):
        $slider_html .= '<div><div class="img">' . get_wpu_acf_image($img['ID'], $image_size) . '</div></div>';
    endforeach;
    $slider_html .= '</div>';
    return $slider_html;
}

/* ----------------------------------------------------------
  Gallery
---------------------------------------------------------- */

function get_wpu_acf_gallery($gallery, $args = array()) {
    if (!is_array($gallery) || empty($gallery)) {
        return '';
    }
    $args = !is_array($args) ? array() : $args;
    $default_args = array(
        'format' => 'medium',
        'bigimage_format' => 'large',
        'wrapper_classname' => '',
        'link_classname' => '',
        'list_classname' => '',
        'item_classname' => '',
        'link_bigimage' => true
    );
    $args = array_merge($default_args, $args);
    $args['wrapper_classname'] .= ' wpuacf-gallery__wrapper';
    $args['list_classname'] .= ' wpuacf-gallery';
    $args['item_classname'] .= ' wpuacf-gallery__item';
    $args['link_classname'] .= ' wpuacf-gallery__item-link';

    $html = '<div class="' . trim(esc_attr($args['wrapper_classname'])) . '">';
    $html .= '<ul class="' . trim(esc_attr($args['list_classname'])) . '">';
    foreach ($gallery as $image) {
        $bigimage = $args['link_bigimage'] ? wp_get_attachment_image_url($image['ID'], $args['bigimage_format']) : false;
        $html .= '<li>';
        $html .= '<div class="' . trim(esc_attr($args['item_classname'])) . '">';
        $html .= $bigimage ? '<a class="' . trim(esc_attr($args['link_classname'])) . '" href="' . $bigimage . '">' : '';
        $html .= wp_get_attachment_image($image['ID'], $args['format']);
        $html .= $bigimage ? '</a>' : '';
        $html .= '</div>';
        $html .= '</li>';
    }
    $html .= '</ul>';
    $html .= '</div>';

    return $html;
}
