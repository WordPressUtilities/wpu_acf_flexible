<?php
$_author = get_sub_field('author');
$_author_details = get_sub_field('author_details');
$_quote = apply_filters('the_content', get_sub_field('quote'));
if (!$_quote) {
    return;
}
$_content_before = apply_filters('wpu_acf_flexible__content__quote__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__quote__after', '');
$_footer_classname = apply_filters('wpu_acf_flexible__content__quote__footer_classname', '');
?><div class="<?php echo get_wpu_acf_wrapper_classname('quote'); ?>">
    <?php echo $_content_before; ?>
    <div class="block--quote">
        <?php echo get_wpu_acf_title_content(); ?>
        <blockquote>
            <div class="field-quote"><?php echo $_quote; ?></div>
            <?php if ($_author): ?>
            <footer class="<?php echo $_footer_classname; ?>"><?php echo $_author . ($_author_details ? ', ' . $_author_details : ''); ?></footer>
            <?php endif;?>
        </blockquote>
    </div>
    <?php echo $_content_after; ?>
</div>
