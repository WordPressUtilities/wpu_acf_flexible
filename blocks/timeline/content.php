<?php
defined('ABSPATH') || die;

$_timeline_events_image_size = apply_filters('wpu_acf_flexible__content__timeline__events_image_size', 'medium');
$_content_before = apply_filters('wpu_acf_flexible__content__timeline__before', '');
$_content_before_inner = apply_filters('wpu_acf_flexible__content__timeline__before_inner', '');
$_content_after = apply_filters('wpu_acf_flexible__content__timeline__after', '');
$_content_after_inner = apply_filters('wpu_acf_flexible__content__timeline__after_inner', '');

$_events = get_sub_field('events');
if (!$_events) {
    return;
}

$_events_html = '';
while (has_sub_field('events')) {
    $title = get_sub_field('title');
    $_events_html .= '<li><div class="events-list__item">';
    $_events_html .= '<div class="col-image"><div class="events-list__item__image">' . get_wpu_acf_image(get_sub_field('image'), $_timeline_events_image_size) . '</div></div>';
    $_events_html .= '<div class="col-content"><div class="events-list__item__content">';
    $_events_html .= '<div class="field-date events-list__item__date">' . wp_strip_all_tags(get_sub_field('date')) . '</div>';
    $_events_html .= apply_filters('wpu_acf_flexible__content__timeline__events__title', '<h3 class="field-title events-list__item__title">' . wp_strip_all_tags($title) . '</h3>', $title);
    $_events_html .= get_wpu_acf_minieditor(get_sub_field('text'), array(
        'add_wrapper' => true
    ));
    $_events_html .= get_wpu_acf_cta(get_sub_field('cta'));
    $_events_html .= '</div></div>';
    $_events_html .= '</div></li>';
}

echo '<section data-nb="' . count($_events) . '" class="' . get_wpu_acf_wrapper_classname('timeline') . '">';
echo $_content_before;
echo '<div class="block--timeline">';
echo $_content_before_inner;
echo '<ul class="events-list">' . $_events_html . '</ul>';
echo $_content_after_inner;
echo '</div>';
echo $_content_after;
echo '</section>';
