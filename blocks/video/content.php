<?php
$_title = get_sub_field('title');
$_content = apply_filters('the_content', get_sub_field('content'));
$_video = get_sub_field('video');
if (!$_video) {
    return;
}

?><div class="centered-container cc-wpuacfflexible cc-block-video cc-block-video--<?php echo get_row_layout(); ?>">
    <div class="block--video">
        <?php if ($_title): ?>
        <h2 class="field-title"><?php echo $_title; ?></h2>
        <?php endif;?>
        <?php if ($_content): ?>
        <div class="field-content">
            <?php echo $_content; ?>
        </div>
        <?php endif;?>
        <div class="field-video"><?php echo $_video; ?></div>
    </div>
</div>
