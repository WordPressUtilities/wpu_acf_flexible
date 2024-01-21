<?php
defined('ABSPATH') || die;
$_image = get_wpu_acf_video_embed_image();
if (!$_image) {
    return;
}
$_content_before = apply_filters('wpu_acf_flexible__content__video__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__video__after', '');
?><div class="<?php echo get_wpu_acf_wrapper_classname('video'); ?>">
    <?php echo $_content_before; ?>
    <div class="block--video">
        <?php echo get_wpu_acf_title_content(); ?>
        <div class="field-video"><?php echo $_image; ?></div>
    </div>
    <?php echo $_content_after; ?>
</div>
