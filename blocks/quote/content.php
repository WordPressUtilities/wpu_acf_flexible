<?php
$_title = get_sub_field('title');
$_author = get_sub_field('author');
$_author_details = get_sub_field('author_details');
$_quote = apply_filters('the_content', get_sub_field('quote'));
if (!$_quote) {
    return;
}

?><div class="centered-container cc-wpuacfflexible cc-block-quote cc-block-quote--<?php echo get_row_layout(); ?>">
    <div class="block--quote">
        <?php if ($_title): ?>
        <h2 class="field-title"><?php echo $_title; ?></h2>
        <?php endif;?>
        <blockquote>
            <div class="field-quote"><?php echo $_quote; ?></div>
            <?php if ($_author): ?>
            <footer> <?php echo $_author . ($_author_details ? ', ' . $_author_details : ''); ?></footer>
            <?php endif;?>
        </blockquote>
    </div>
</div>
