<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  Values
---------------------------------------------------------- */

$similar_type = get_sub_field('similar_type');
$default_values = wpu_acf_flexible_similar_get_default_values($similar_type);
if (!$default_values) {
    return;
}

/* CMS
-------------------------- */

$block_content = wpu_acf_flexible_similar_get_title_and_cta($similar_type);
$items = wpu_acf_flexible_similar_get_items($similar_type, get_sub_field('similar_' . $similar_type));

/* ----------------------------------------------------------
  Layout
---------------------------------------------------------- */

echo '<section data-per-page="' . $default_values['posts_per_page'] . '" data-type="' . $similar_type . '" class="' . get_wpu_acf_wrapper_classname('similar') . '">';
echo '<div class="block--similar">';
echo '<h2 class="field-title">' . esc_html($block_content['title']) . '</h2>';
echo '<ul class="block--similar__list">';
foreach ($items as $item) {
    echo '<li class="block--similar__item">';
    echo '<a href="' . get_permalink($item) . '" class="block--similar__link">';
    echo get_the_title($item);
    echo '</a>';
    echo '</li>';
}
echo '</ul>';
echo get_wpu_acf_cta($block_content['button']);
echo '</div>';
echo '</section>';
