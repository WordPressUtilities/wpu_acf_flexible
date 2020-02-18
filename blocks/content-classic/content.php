<?php
$title = get_sub_field('title');
$cta_link = get_sub_field('cta');
$content = apply_filters('the_content', get_sub_field('content'));
?>
<div class="centered-container cc-block--content-classic cc-block--content-classic--<?php echo get_row_layout(); ?>">
    <div class="block--content-classic">
        <?php if ($title): ?>
            <h2 class="field-title">
                <?php echo $title; ?>
            </h2>
        <?php endif;?>
        <div class="field-content">
            <?php echo $content; ?>
        </div>
        <?php if (is_array($cta_link)): ?>
        <a target="<?php echo $cta_link['target']; ?>" href="<?php echo $cta_link['url']; ?>">
            <?php echo $cta_link['title']; ?>
        </a>
        <?php endif;?>
    </div>
</div>
