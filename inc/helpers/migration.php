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

    $post_already_migrated = get_post_meta($post_id, 'wpu_acf_flex_migrated', true);
    if ($post_already_migrated) {
        return false;
    }

    foreach ($group_item['layouts'] as $layout_id => $layout) {
        /* If the default model is found */
        if (isset($layout['wpuacf_model']) && $layout['wpuacf_model'] == 'content-classic') {
            $original_post = get_post($post_id);

            $blocks = array(
                array(
                    'layout' => 'content',
                    'value' => $original_post->post_content
                )
            );

            $blocks = apply_filters('wpu_acf_flex__migrate_content_to_blocks__filter_blocks', $blocks, $post_id, $group, $layout_id);

            /* Create native ACF field */
            update_field($group, '');

            /* Load each blocks */
            $layouts = array();
            foreach ($blocks as $i => $block) {
                $layouts[] = $block['layout'];
                update_post_meta($post_id, $group . '_' . $i . '_content', $blocks[$i]['value']);
            }

            /* Save layout */
            update_post_meta($post_id, $group, $layouts);
            update_post_meta($post_id, '_' . $group, 'field_' . md5($group));

            do_action('wpu_acf_flex__migrate_content_to_blocks__after', $post_id, $group, $blocks);
            $migration_done = true;
            break;
        }
    }

    if ($migration_done) {
        update_post_meta($post_id, 'wpu_acf_flex_migrated', 1);
    }

    return $migration_done;
}
