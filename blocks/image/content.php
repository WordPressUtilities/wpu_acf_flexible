<?php
$_image = get_wpu_acf_figure(get_sub_field('image'),'medium');
if (!$_image) {
    return;
}

?><div class="<?php echo get_wpu_acf_wrapper_classname('image'); ?>">
    <div class="block--image">
        <?php echo get_wpu_acf_title_content(); ?>
        <?php echo $_image; ?>
    </div>
</div>
