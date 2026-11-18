<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  Values
---------------------------------------------------------- */

$post_id = wpu_acf_flexible_subpages_get_post_id();
if (!$post_id) {
    return;
}

$siblings = wpu_acf_flexible_get_sibling_pages($post_id);
$position = array_search($post_id, wp_list_pluck($siblings, 'ID'));

$links = array();
if ($position !== false) {
    $links = array_filter(array(
        'prev' => $siblings[$position - 1] ?? false,
        'next' => $siblings[$position + 1] ?? false
    ));
}
if (empty($links)) {
    wpu_acf_flexible_subpages_empty_notice();
    return;
}

$labels = apply_filters('wpu_acf_flexible__content__siblings_nav__labels', array(
    'prev' => __('Previous', 'wpu_acf_flexible'),
    'next' => __('Next', 'wpu_acf_flexible')
));

/* ----------------------------------------------------------
  Layout
---------------------------------------------------------- */

echo '<div class="' . get_wpu_acf_wrapper_classname('siblings-nav') . '">';
echo '<nav class="block--siblings-nav">';
foreach ($links as $direction => $item) {
    echo '<a rel="' . $direction . '" href="' . esc_url(get_permalink($item)) . '" class="block--siblings-nav__link block--siblings-nav__link--' . $direction . '">';
    echo '<span class="block--siblings-nav__label">' . esc_html($labels[$direction]) . '</span>';
    echo '<span class="block--siblings-nav__title">' . esc_html(get_the_title($item)) . '</span>';
    echo '</a>';
}
echo '</nav>';
echo '</div>';
