<?php
$_anchor = preg_replace('/([^a-z0-9-])/', '', remove_accents(get_sub_field('slug')));
if (!$_anchor) {
    return;
}
?><div id="<?php echo esc_attr($_anchor); ?>" class="<?php echo get_wpu_acf_wrapper_classname('anchor'); ?>"></div>
