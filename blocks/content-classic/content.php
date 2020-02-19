<?php
$_title = get_sub_field('title');
$_cta_link = get_sub_field('cta');
$_content = apply_filters('the_content', get_sub_field('content'));
?>
<div class="centered-container cc-wpuacfflexible cc-block--content-classic cc-block--content-classic--<?php echo get_row_layout(); ?>">
    <div class="block--content-classic">
        <?php if ($_title): ?>
            <h2 class="field-title">
                <?php echo $_title; ?>
            </h2>
        <?php endif;?>
        <div class="field-content">
            <?php echo $_content; ?>
        </div>
        <?php if (is_array($_cta_link)): ?>
        <div class="field-cta">
        <a target="<?php echo $_cta_link['target']; ?>" href="<?php echo $_cta_link['url']; ?>">
            <?php echo $_cta_link['title']; ?>
        </a>
        </div>
        <?php endif;?>
    </div>
</div>
