<?php
$_features = get_sub_field('features');
if (empty($_features)) {
    return;
}

$_features_content = '';
$_features_count = 0;
while (has_sub_field('features')) {
    $image_src = get_wpu_acf_image_src(get_sub_field('image'), apply_filters('wpu_acf_flexible__features__image_size', 'thumbnail'));
    $_features_content .= '<li><div class="features-item">';
    if ($image_src) {
        $_features_content .= '<img class="field-image" src="' . $image_src . '" alt="" />';
    }
    $_features_content .= '<h3 class="field-title">' . get_sub_field('title') . '</h3>';
    $_features_content .= '<div class="field-content cssc-content">' . get_sub_field('content') . '</div>';
    $_features_content .= get_wpu_acf_cta('link');
    $_features_content .= '</div></li>';
    $_features_count++;
}
$_content_before = apply_filters('wpu_acf_flexible__content__features__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__features__after', '');
?><div class="<?php echo get_wpu_acf_wrapper_classname('features'); ?>">
    <?php echo $_content_before; ?>
    <div class="block-features">
        <?php echo get_wpu_acf_title_content(); ?>
        <ul class="features-list" data-nb="<?php echo $_features_count; ?>">
            <?php echo $_features_content; ?>
        </ul>
    </div>
    <?php echo $_content_after; ?>
</div>
