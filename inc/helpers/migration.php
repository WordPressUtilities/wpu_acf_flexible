<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  Migration
---------------------------------------------------------- */

function wpu_acf_flex__migrate_content_to_blocks($post_id, $group = 'content-blocks') {

    $migration_done = false;

    $group_item = $group;
    $groups = apply_filters('wpu_acf_flexible_content', array());
    if (!isset($groups[$group])) {
        return;
    }
    $group_item = $groups[$group];

    foreach ($group_item['layouts'] as $layout_id => $layout) {
        /* If the default model is found */
        if (isset($layout['wpuacf_model']) && $layout['wpuacf_model'] == 'content-classic') {
            $original_post = get_post($post_id);
            /* Create native ACF field */
            update_field($group, '');
            /* Load one content field and one meta value */
            update_post_meta($post_id, $group . '_0_content', $original_post->post_content);
            update_post_meta($post_id, $group, array($layout_id));
            $migration_done = true;
            break;
        }
    }

    return $migration_done;
}
