<?php
$_content_before = apply_filters('wpu_acf_flexible__content__form__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__form__after', '');
$_form_type = apply_filters('wpu_acf_flexible__content__form__type', 'default');
?><div class="<?php echo get_wpu_acf_wrapper_classname('form'); ?>">
    <?php echo $_content_before; ?>
    <div class="block--form">
        <?php do_action('wpucontactforms_content', false, $_form_type);?>
    </div>
    <?php echo $_content_after; ?>
</div>
