<?php
$_image = get_wpu_acf_figure(get_sub_field('image'),'medium');
if (!$_image) {
    return;
}
$_content_before = apply_filters('wpu_acf_flexible__content__image__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__image__after', '');
?><div class="<?php echo get_wpu_acf_wrapper_classname('image'); ?>">
    <?php echo $_content_before; ?>
    <div class="block--image">
        <?php echo get_wpu_acf_title_content(); ?>
        <?php echo $_image; ?>
    </div>
    <?php echo $_content_after; ?>
</div>
