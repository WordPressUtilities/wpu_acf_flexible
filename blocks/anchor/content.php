<?php
$_anchor = preg_replace('/([^a-z0-9-])/', '', remove_accents(get_sub_field('slug')));
if (!$_anchor) {
    return;
}
?><div id="<?php echo esc_attr($_anchor); ?>" class="cc-wpuacfflexible cc-block-anchor cc-block-anchor--<?php echo get_row_layout(); ?>"></div>
