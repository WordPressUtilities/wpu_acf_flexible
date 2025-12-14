<?php
defined('ABSPATH') || die;

$btn_classname = apply_filters('wpu_acf_flexible__content__content_classic__buttons_classname', '');

/* ----------------------------------------------------------
  Values
---------------------------------------------------------- */

$_content = get_wpu_acf_title_content();
$_cta_link = get_wpu_acf_cta('cta', $btn_classname);
if (!$_content && !$_cta_link) {
    return;
}

$nb_buttons = apply_filters('wpu_acf_flexible__content__content_classic__buttons_number', 3);
for ($i = 2; $i <= $nb_buttons; $i++) {
    $_cta_link .= get_wpu_acf_cta('cta' . $i, $btn_classname);
}

/* ----------------------------------------------------------
  Extra hooks
---------------------------------------------------------- */

$_content_before = apply_filters('wpu_acf_flexible__content__content_classic__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__content_classic__after', '');
$_content_inside_before = apply_filters('wpu_acf_flexible__content__content_classic__inside_before', '');
$_content_inside_content_cta_link = apply_filters('wpu_acf_flexible__content__content_classic__inside_content_cta_link', '');
$_content_inside_after = apply_filters('wpu_acf_flexible__content__content_classic__inside_after', '');

/* ----------------------------------------------------------
  Content
---------------------------------------------------------- */

echo '<div class="' . get_wpu_acf_wrapper_classname('content-classic') . '">';
echo $_content_before;
echo '<div class="block--content-classic">';
echo $_content_inside_before . $_content . $_content_inside_content_cta_link . $_cta_link . $_content_inside_after;
echo '</div>';
echo $_content_after;
echo '</div>';
