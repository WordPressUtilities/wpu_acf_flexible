<?php
$_video = get_sub_field('video');
if (!$_video) {
    return;
}
$_video = '<div class="content-video">' . $_video . '</div>';
if (apply_filters('wpu_acf_flexible__video__nocookie', true) || is_admin()) {
    $_video = str_replace('youtube.com/embed', 'youtube-nocookie.com/embed', $_video);
}

$_image_size = apply_filters('wpu_acf_flexible__content__video__image_size', 'large');
$_content_before = apply_filters('wpu_acf_flexible__content__video__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__video__after', '');

$_image_id = get_sub_field('image');
$_image = '';
if ($_image_id && !is_admin()) {
    $_video = str_replace('src=', 'data-src=', $_video);
    $_video = str_replace('app_id=', 'autoplay=1&app_id=', $_video);
    $_video = str_replace('feature=oembed', 'feature=oembed&autoplay=1', $_video);
    $_image = '<div class="cursor"></div><div class="cover-image">' . get_wpu_acf_image($_image_id, $_image_size) . '</div>';
}

?><div class="<?php echo get_wpu_acf_wrapper_classname('video'); ?>">
    <?php echo $_content_before; ?>
    <div class="block--video">
        <?php echo get_wpu_acf_title_content(); ?>
        <div class="field-video"><?php echo $_image . $_video; ?></div>
    </div>
    <?php echo $_content_after; ?>
</div>
