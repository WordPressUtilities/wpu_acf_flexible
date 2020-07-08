<?php

$_logos = get_sub_field('logos');
if (empty($_logos)) {
    return;
}

$_logos = '';
while (has_sub_field('logos')) {
    $url = get_sub_field('url');
    $_logos .= '<li>';
    if ($url) {
        $_logos .= '<a class="field-url" href="' . $url . '">';
    }
    $_logos .= wp_get_attachment_image(get_sub_field('image'), 'medium');
    if ($url) {
        $_logos .= '</a>';
    }
    $_logos .= '</li>';
}
$_content_before = apply_filters('wpu_acf_flexible__content__logos__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__logos__after', '');

$logos_list_classname = apply_filters('wpu_acf_flexible__content__logos__logoslist__classname', 'logos-list');

?><div class="<?php echo get_wpu_acf_wrapper_classname('logos'); ?>">
    <?php echo $_content_before; ?>
    <div class="block--logos">
        <?php echo get_wpu_acf_title_content(); ?>
        <ul class="<?php echo $logos_list_classname; ?>">
            <?php echo $_logos; ?>
        </ul>
    </div>
    <?php echo $_content_after; ?>
</div>
