<?php
defined('ABSPATH') || die;
$_image_size = apply_filters('wpu_acf_flexible__content__team-quote__image_size', 'thumbnail');
$_image = get_wpu_acf_image_src(get_sub_field('image'), $_image_size);
if (!$_image) {
    return;
}
$_author = get_sub_field('author');
$_author_details = get_sub_field('author_details');
$_quote = apply_filters('the_content', get_sub_field('quote'));
$_display_details_as_string = apply_filters('wpu_acf_flexible__content__team_quote__display_details_as_string', true);
if (!$_quote) {
    return;
}
$_content_before = apply_filters('wpu_acf_flexible__content__team_quote__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__team_quote__after', '');
echo '<div class="' . get_wpu_acf_wrapper_classname('team-quote') . '">';
echo $_content_before;
echo '<div class="block--team-quote">';
echo get_wpu_acf_title_content();
echo '<blockquote>';
echo '<div class="quote-image"><div class="quote-image-inner"><img src="' . $_image . '" alt="' . esc_attr($_author) . '" loading="lazy" /></div></div>';
echo '<div class="quote-inner">';
echo '<div class="field-quote">' . $_quote . '</div>';
if ($_author):
    echo '<footer>';
    if ($_display_details_as_string) {
        echo $_author . ($_author_details ? ', ' . $_author_details : '');
    } else {
        echo '<span class="field-author">' . $_author . '</span>';
        if ($_author_details) {
            echo '<span class="field-author-details">' . $_author_details . '</span>';
        }
    }
    echo '</footer>';
endif;
echo '</div>';
echo '</blockquote>';
echo '</div>';
echo $_content_after;
echo '</div>';
