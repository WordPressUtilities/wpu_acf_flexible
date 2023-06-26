<?php
$questions = get_sub_field('questions');
if (!$questions) {
    return;
}
$_content_before = apply_filters('wpu_acf_flexible__content__faq__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__faq__after', '');
echo '<section class="' . get_wpu_acf_wrapper_classname('faq') . '">';
echo $_content_before;
echo '<div class="block--faq">';
echo get_wpu_acf__title();
echo '<div class="faq-list__items">';
while (have_rows('questions')):
    the_row();
    $answer = get_sub_field('answer');
    $faq_id = sanitize_title('faq-' . $answer) . uniqid();
    $question = get_sub_field('question');
    echo '<dl class="faq-list__item wpuacfflexfaq-list__item" itemscope="" itemtype="https://schema.org/Question" data-is-open="false">';
    echo '<dt itemprop="name" class="h3 field-question"><button aria-expanded="false" aria-controls="' . esc_attr($faq_id) . '"><span>' . $question . '</span></button></dt>';
    echo '<dd id="' . esc_attr($faq_id) . '" class="block-answer" itemscope="" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">';
    echo '<div class="field-answer cssc-content" itemprop="text">' . trim(wpautop($answer)) . '</div>';
    echo '</dd>';
    echo '</dl>';
endwhile;
echo '</div>';
echo '</div>';
echo $_content_after;
echo '</section>';
