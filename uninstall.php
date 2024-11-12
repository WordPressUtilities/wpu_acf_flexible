<?php
defined('ABSPATH') || die;
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

/* Delete all posts */
$allposts = get_posts(array(
    'post_type' => 'wpuacf_blocks',
    'numberposts' => -1,
    'fields' => 'ids'
));
foreach ($allposts as $p) {
    wp_delete_post($p, true);
}
