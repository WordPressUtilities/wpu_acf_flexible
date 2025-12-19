<?php
defined('ABSPATH') || die;
$questions = get_sub_field('questions');
if (!$questions) {
    return;
}
$_content_before = apply_filters('wpu_acf_flexible__content__faq__before', '');
$_content_before_title = apply_filters('wpu_acf_flexible__content__faq__before_title', '');
$_content_before_list = apply_filters('wpu_acf_flexible__content__faq__before_list', '');
$_content_after = apply_filters('wpu_acf_flexible__content__faq__after', '');
$_block_attribute = '';
if (!defined('WPUACFFLEXIBLE_BLOCK_FAQ_DISPLAYED')) {
    define('WPUACFFLEXIBLE_BLOCK_FAQ_DISPLAYED', true);
    $_block_attribute = ' itemscope itemtype="https://schema.org/FAQPage" ';
}
$_block_attribute = apply_filters('wpu_acf_flexible__content__faq__block_attributes', $_block_attribute);
echo '<section class="' . get_wpu_acf_wrapper_classname('faq') . '">';
echo $_content_before;
echo '<div class="block--faq" ' . $_block_attribute . '>';
echo $_content_before_title;
echo get_wpu_acf_title_content();
echo $_content_before_list;
echo '<div class="faq-list__items">';
while (have_rows('questions')):
    the_row();
    $answer = get_sub_field('answer');
    $faq_id = sanitize_title('faq-' . $answer) . uniqid();
    $question = get_sub_field('question');
    $attributes = apply_filters('wpu_acf_flexible__content__faq__item_attributes', '', get_row());
    echo '<dl ' . $attributes . ' class="faq-list__item wpuacfflexfaq-list__item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question" data-is-open="false">';
    echo '<dt itemprop="name" class="h3 field-question"><button aria-expanded="false" aria-controls="' . esc_attr($faq_id) . '"><span>' . $question . '</span></button></dt>';
    echo '<dd id="' . esc_attr($faq_id) . '" class="block-answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">';
    echo '<div class="field-answer cssc-content" itemprop="text">' . trim(wpautop($answer)) . '</div>';
    echo '</dd>';
    echo '</dl>';
endwhile;
echo '</div>';
echo '</div>';
echo $_content_after;
echo '</section>';
