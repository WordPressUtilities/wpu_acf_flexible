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

    $found_layout_id = false;

    foreach ($group_item['layouts'] as $layout_id => $layout) {
        /* If the default model is found */
        if (isset($layout['wpuacf_model']) && $layout['wpuacf_model'] == 'content-classic') {
            $found_layout_id = $layout_id;
            break;
        }
    }

    if (!$found_layout_id) {
        return false;
    }

    $original_post = get_post($post_id);

    $blocks = apply_filters('wpu_acf_flex__migrate_content_to_blocks__filter_blocks', array(
        array(
            'layout' => 'content',
            'value' => $original_post->post_content
        )
    ), $post_id, $group, $found_layout_id);

    /* Load each blocks */
    foreach ($blocks as $block) {
        wpu_acf_flex__add_row($post_id, array(
            'acf_fc_layout' => $block['layout'],
            'content' => $block['value']
        ), $group);
    }

    do_action('wpu_acf_flex__migrate_content_to_blocks__after', $post_id, $group, $blocks);

    update_post_meta($post_id, 'wpu_acf_flex_migrated', 1);

    return true;
}

/* ----------------------------------------------------------
  Helper functions
---------------------------------------------------------- */

/**
 * Add a row to a flexible content field
 * @param int $post_id The post ID
 * @param array $metas The metas to add (must contain 'acf_fc_layout')
 * @param string $group The group name (default: 'content-blocks')
 * @return bool True if the row was added, false otherwise
 */
function wpu_acf_flex__add_row($post_id, $metas, $group = 'content-blocks') {
    $groups = apply_filters('wpu_acf_flexible_content', array());
    if (!isset($groups[$group])) {
        return false;
    }
    $group_item = $groups[$group];
    if (!isset($group_item['layouts'][$metas['acf_fc_layout']])) {
        return false;
    }

    $layout = $group_item['layouts'][$metas['acf_fc_layout']];
    if (!isset($layout['wpuacf_model'])) {
        return false;
    }

    $group_field = get_field($group, $post_id);
    if (!$group_field) {
        update_field($group, '');
    }

    add_row($group, $metas, $post_id);
}
