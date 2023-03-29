<?php

$_numbers_list_classname = trim(apply_filters('wpu_acf_flexible__content__numbers__numberslist__classname', 'numbers-list'));

$_numbers = get_sub_field('numbers');
if (empty($_numbers)) {
    return;
}

$_numbers_html = '';
while (has_sub_field('numbers')) {
    $_number_html = '';
    $_number_html .= '<div class="number">' . strip_tags(get_sub_field('number')) . '</div>';
    $label = get_sub_field('label');
    if ($label) {
        $_number_html .= '<div class="label">' . strip_tags($label) . '</div>';
    }
    $_numbers_html .= '<li><div class="' . $_numbers_list_classname . '__item">' . $_number_html . '</div></li>';
}
$_content_before = apply_filters('wpu_acf_flexible__content__numbers__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__numbers__after', '');

echo '<section data-nb="' . count($_numbers) . '" class="' . get_wpu_acf_wrapper_classname('numbers') . '">';
echo $_content_before;
echo '<div class="block--numbers">' . get_wpu_acf_title_content();
echo '<div class="' . $_numbers_list_classname . '__wrapper"><ul class="' . $_numbers_list_classname . '">' . $_numbers_html . '</ul></div>';
echo '</div>';
echo $_content_after;
echo '</section>';
