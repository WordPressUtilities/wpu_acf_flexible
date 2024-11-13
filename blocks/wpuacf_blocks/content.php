<?php
defined('ABSPATH') || die;
$p_id = get_sub_field('wpuacf_blocks');
if (!$p_id) {
    return;
}
global $post;
$new_post = get_post($p_id);
if (!$new_post) {
    return;
}
$old_post = $post;
$block_group_id = apply_filters('wpu_acf_flexible__reusable_blocks_group_id', 'content-blocks');
$post = $new_post;
echo get_wpu_acf_flexible_content($block_group_id);
$post = $old_post;
