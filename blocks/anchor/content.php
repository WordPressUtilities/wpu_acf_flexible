<?php
if(isset($wpuacfflex_args) && $wpuacfflex_args['save_post_mode']){
    return;
}
$_anchor = preg_replace('/([^a-z0-9-])/', '', remove_accents(get_sub_field('slug')));
if (!$_anchor) {
    return;
}
if (is_admin()) {
    echo '<p style="padding:1em">#' . $_anchor . '</p>';
    return;
}
echo '<div id="' . esc_attr($_anchor) . '" class="' . get_wpu_acf_wrapper_classname('anchor') . '"></div>';
