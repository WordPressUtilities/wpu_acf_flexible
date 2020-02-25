<?php
$_title = get_sub_field('title');
$_content = apply_filters('the_content', get_sub_field('content'));
$_image = get_wpu_acf_figure(get_sub_field('image'),'medium');
if (!$_image) {
    return;
}

?><div class="centered-container cc-wpuacfflexible cc-block-image cc-block-image--<?php echo get_row_layout(); ?>">
    <div class="block--image">
        <?php if ($_title): ?>
        <h2 class="field-title"><?php echo $_title; ?></h2>
        <?php endif;?>
        <?php if ($_content): ?>
        <div class="field-content">
            <?php echo $_content; ?>
        </div>
        <?php endif;?>
        <?php echo $_image; ?>
    </div>
</div>
