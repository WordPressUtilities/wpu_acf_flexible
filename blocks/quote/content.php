<?php
$_author = get_sub_field('author');
$_author_details = get_sub_field('author_details');
$_quote = apply_filters('the_content', get_sub_field('quote'));
if (!$_quote) {
    return;
}

?><div class="<?php echo get_wpu_acf_wrapper_classname('quote'); ?>">
    <div class="block--quote">
        <?php echo get_wpu_acf_title_content(); ?>
        <blockquote>
            <div class="field-quote"><?php echo $_quote; ?></div>
            <?php if ($_author): ?>
            <footer> <?php echo $_author . ($_author_details ? ', ' . $_author_details : ''); ?></footer>
            <?php endif;?>
        </blockquote>
    </div>
</div>
