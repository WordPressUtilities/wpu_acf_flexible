<?php
$_content = get_wpu_acf_title_content();
$_cta_link = get_wpu_acf_cta();
if (!$_content && !$_cta_link) {
    return;
}

$nb_buttons = apply_filters('wpu_acf_flexible__content__content_classic__buttons_number', 3);
for ($i = 2; $i <= $nb_buttons; $i++) {
    $_cta_link .= get_wpu_acf_cta('cta' . $i);
}

$_content_before = apply_filters('wpu_acf_flexible__content__content_classic__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__content_classic__after', '');
?><div class="<?php echo get_wpu_acf_wrapper_classname('content-classic'); ?>">
    <?php echo $_content_before; ?>
    <div class="block--content-classic"><?php echo $_content . $_cta_link; ?></div>
    <?php echo $_content_after; ?>
</div>
