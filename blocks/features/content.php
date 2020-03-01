<?php
$_features = get_sub_field('features');
if (empty($_features)) {
    return;
}

$_features_content = '';
while (has_sub_field('features')) {
    $image_src = get_wpu_acf_image_src(get_sub_field('image'), 'thumbnail');
    $link_link = get_sub_field('link');
    $_features_content .= '<li><div class="features-item">';
    if ($image_src) {
        $_features_content .= '<img class="field-image" src="' . $image_src . '" alt="" />';
    }
    $_features_content .= '<h3 class="field-title">' . get_sub_field('title') . '</h3>';
    $_features_content .= '<div class="field-content">' . get_sub_field('content') . '</div>';
    if (is_array($link_link)) {
        $_features_content .= '<div class="field-cta"><a class="field-link acfflex-link" target="' . $link_link['target'] . '" href="' . $link_link['url'] . '"><span>' . $link_link['title'] . '</span></a></div>';
    }

    $_features_content .= '</div></li>';
}

?><div class="<?php echo get_wpu_acf_wrapper_classname('features'); ?>">
    <div class="block-features">
        <?php echo get_wpu_acf_title_content(); ?>
        <ul class="features-list">
            <?php echo $_features_content; ?>
        </ul>
    </div>
</div>
