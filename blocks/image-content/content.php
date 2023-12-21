<?php
$_image_id = get_sub_field('image');
if (!$_image_id) {
    return;
}
$_image_size = apply_filters('wpu_acf_flexible__content__image_content__image_size', 'medium', $_image_id);
$_image_type = apply_filters('wpu_acf_flexible__content__image_content__image_type', 'figure', $_image_id);
$_image = $_image_type == 'figure' ? get_wpu_acf_figure($_image_id, $_image_size) : get_wpu_acf_image($_image_id, $_image_size);
$_image_position = get_sub_field('image_position');
if (!$_image_position) {
    $_image_position = 'left';
}

$_image_container = '<div class="main-grid__image">' . $_image . '</div>';

$_title_position = apply_filters('wpu_acf_flexible__content__image_content__title_position', 'inner');
$_content_before = apply_filters('wpu_acf_flexible__content__image_content__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__image_content__after', '');
?><div data-image-position="<?php echo esc_attr($_image_position); ?>" class="<?php echo get_wpu_acf_wrapper_classname('image-content'); ?>">
    <?php echo $_content_before; ?>
    <div class="block--image-content">
        <?php echo $_title_position == 'over' ? get_wpu_acf__title() : ''; ?>
        <div class="acfflex-grid">
        <?php echo $_image_position == 'left' ? $_image_container : ''; ?>
        <div class="main-grid__content">
            <?php echo $_title_position == 'inner' ? get_wpu_acf__title() : ''; ?>
            <?php echo get_wpu_acf__content(); ?>
            <?php echo get_wpu_acf_cta(); ?>
        </div>
        <?php echo $_image_position == 'right' ? $_image_container : ''; ?>
        </div>
    </div>
    <?php echo $_content_after; ?>
</div>
