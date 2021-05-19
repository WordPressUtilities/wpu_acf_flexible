<?php
$_content = get_wpu_acf_title_content();
$_cta_link = get_wpu_acf_cta();
if(!$_content && !$_cta_link){
    return;
}
$_cta_link .= get_wpu_acf_cta('cta2');
$_cta_link .= get_wpu_acf_cta('cta3');

$_content_before = apply_filters('wpu_acf_flexible__content__content_classic__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__content_classic__after', '');
?><div class="<?php echo get_wpu_acf_wrapper_classname('content-classic'); ?>">
    <?php echo $_content_before; ?>
    <div class="block--content-classic cssc-content"><?php echo $_content . $_cta_link; ?></div>
    <?php echo $_content_after; ?>
</div>
