<?php
$_cta_link = get_sub_field('cta');
$_image = get_wpu_acf_figure(get_sub_field('image'), 'medium');
if (!$_image) {
    return;
}
$_image_position = get_sub_field('image_position');
if (!$_image_position) {
    $_image_position = 'left';
}

$_image_container = '<div class="main-grid__image">' . $_image . '</div>';

?><div class="<?php echo get_wpu_acf_wrapper_classname('image-content'); ?>">
    <div class="block--image-content">
        <div class="acfflex-grid">
        <?php echo $_image_position == 'left' ? $_image_container : ''; ?>
        <div class="main-grid__content">
            <?php echo get_wpu_acf_title_content(); ?>
            <?php echo get_wpu_acf_cta(); ?>
        </div>
        <?php echo $_image_position == 'right' ? $_image_container : ''; ?>
        </div>
    </div>
</div>
