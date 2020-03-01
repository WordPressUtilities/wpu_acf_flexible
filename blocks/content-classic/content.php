<?php
$_content = get_wpu_acf_title_content();
$_cta_link = get_wpu_acf_cta();
if(!$_content && !$_cta_link){
    return;
}
?><div class="<?php echo get_wpu_acf_wrapper_classname('content-classic'); ?>">
    <div class="block--content-classic">
        <?php echo $_content; ?>
        <?php echo $_cta_link; ?>
    </div>
</div>
