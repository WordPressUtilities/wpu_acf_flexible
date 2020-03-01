<?php
$_video = get_sub_field('video');
if (!$_video) {
    return;
}

?><div class="<?php echo get_wpu_acf_wrapper_classname('video'); ?>">
    <div class="block--video">
        <?php echo get_wpu_acf_title_content(); ?>
        <div class="field-video"><?php echo $_video; ?></div>
    </div>
</div>
