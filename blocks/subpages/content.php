<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  Values
---------------------------------------------------------- */

$post_id = wpu_acf_flexible_subpages_get_post_id();
if (!$post_id) {
    return;
}

$items = get_sub_field('type') == 'children' ? wpu_acf_flexible_get_child_pages($post_id) : wpu_acf_flexible_get_sibling_pages($post_id);
if (empty($items)) {
    wpu_acf_flexible_subpages_empty_notice();
    return;
}

$title = get_sub_field('title');

/* ----------------------------------------------------------
  Layout
---------------------------------------------------------- */

echo '<nav' . ($title ? ' aria-label="' . esc_attr($title) . '"' : '') . ' class="' . get_wpu_acf_wrapper_classname('subpages') . '">';
echo '<div class="block--subpages">';
if ($title) {
    echo '<h2 class="field-title">' . esc_html($title) . '</h2>';
}
echo '<ul class="block--subpages__list">';
foreach ($items as $item) {
    $is_current = $item->ID == $post_id;
    echo '<li class="block--subpages__item' . ($is_current ? ' is-current' : '') . '">';
    echo '<a href="' . esc_url(get_permalink($item)) . '" class="block--subpages__link"' . ($is_current ? ' aria-current="page"' : '') . '>';
    echo esc_html(get_the_title($item));
    echo '</a>';
    echo '</li>';
}
echo '</ul>';
echo '</div>';
echo '</nav>';
