<?php
$_title = get_sub_field('title');
$_content = apply_filters('the_content', get_sub_field('content'));
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

?><div class="centered-container cc-wpuacfflexible cc-block-image-content cc-block-image-content--<?php echo get_row_layout(); ?>">
    <div class="block--image-content">
        <div class="acfflex-grid">
        <?php echo $_image_position == 'left' ? $_image_container : ''; ?>
        <div class="main-grid__content">
        <?php if ($_title): ?>
        <h2 class="field-title"><?php echo $_title; ?></h2>
        <?php endif;?>
        <?php if ($_content): ?>
        <div class="field-content">
            <?php echo $_content; ?>
        </div>
        <?php endif;?>
        <?php if (is_array($_cta_link)): ?>
        <div class="field-cta">
        <?php echo get_wpu_acf_link($_cta_link); ?>
        </div>
        <?php endif;?>
        </div>
        <?php echo $_image_position == 'right' ? $_image_container : ''; ?>
        </div>
    </div>
</div>
