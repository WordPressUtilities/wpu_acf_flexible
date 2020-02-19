<?php
$_title = get_sub_field('title');
$_content = apply_filters('the_content', get_sub_field('content'));
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
        $_features_content .= '<div class="field-cta"><a class="field-link" target="' . $link_link['target'] . '" href="' . $link_link['url'] . '">' . $link_link['title'] . '</a></div>';
    }

    $_features_content .= '</div></li>';
}

?><div class="centered-container cc-wpuacfflexible cc-block-features cc-block-features--<?php echo get_row_layout(); ?>">
    <div class="block-features">
        <?php if ($_title): ?>
            <h2 class="field-title">
                <?php echo $_title; ?>
            </h2>
        <?php endif;?>
        <?php if ($_content): ?>
            <div class="field-content">
                <?php echo $_content; ?>
            </div>
        <?php endif;?>
        <ul class="features-list">
            <?php echo $_features_content; ?>
        </ul>
    </div>
</div>
