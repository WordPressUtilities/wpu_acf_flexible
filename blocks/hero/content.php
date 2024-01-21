<?php
defined('ABSPATH') || die;
$_image = get_wpu_acf_image_src(get_sub_field('image'), apply_filters('wpu_acf_flexible__content__hero__image_size', 'large'));
$_content = get_wpu_acf_title_content();
$_cta_link = get_wpu_acf_cta();
if (!$_content && !$_cta_link) {
    return;
}
?><div class="<?php echo get_wpu_acf_wrapper_classname('hero'); ?>" style="background-image: url(<?php echo $_image; ?>);">
    <div class="block--hero">
        <?php echo $_content; ?>
        <?php echo $_cta_link; ?>
    </div>
</div>
