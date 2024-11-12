<?php
defined('ABSPATH') || die;
$p_id = get_sub_field('wpuacf_blocks');
if (!$p_id) {
    return;
}
$new_post = get_post($p_id);
if (!$new_post) {
    return;
}
$block_group_id = apply_filters('wpu_acf_flexible__reusable_blocks_group_id', 'content-blocks');

$old_post = $post;
$post = $new_post;
echo get_wpu_acf_flexible_content($block_group_id);
$post = $old_post;
