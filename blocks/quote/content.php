<?php
defined('ABSPATH') || die;
$_author = get_sub_field('author');
$_author_details = get_sub_field('author_details');
$_quote = apply_filters('the_content', get_sub_field('quote'));
if (!$_quote) {
    return;
}
$_content_before = apply_filters('wpu_acf_flexible__content__quote__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__quote__after', '');
$_display_comma = apply_filters('wpu_acf_flexible__content__quote__comma', ', ');
$_footer_classname = apply_filters('wpu_acf_flexible__content__quote__footer_classname', '');
echo '<div class="' . get_wpu_acf_wrapper_classname('quote') . '">';
echo $_content_before;
echo '<div class="block--quote">';
echo get_wpu_acf_title_content();
echo '<blockquote>';
echo '<div class="field-quote">' . $_quote . '</div>';
if ($_author || $_author_details):
    echo '<footer class="' . esc_attr($_footer_classname) . '">';
    echo ($_author ? '<span class="author-name">' . $_author . '</span>' : '');
    echo ($_author && $_author_details ? $_display_comma : '');
    echo ($_author_details ? '<span class="author-details">' . $_author_details . '</span>' : '');
    echo '</footer>';
endif;
echo '</blockquote>';
echo '</div>';
echo $_content_after;
echo '</div>';
