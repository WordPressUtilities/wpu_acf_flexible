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
echo wpuacfflexible_reusableblocks__get_content($new_post);
