<?php
defined('ABSPATH') || die;
/*
* Reusable blocks
*/

/* ----------------------------------------------------------
  Post type
---------------------------------------------------------- */

add_action('init', function () {
    if (!apply_filters('wpu_acf_flexible__enable_reusable_blocks', false)) {
        return;
    }
    register_post_type('wpuacf_blocks', array(
        'public' => true,
        'publicly_queryable' => false,
        'label' => __('Reusable Blocks', 'wpu_acf_flexible'),
        'menu_icon' => 'dashicons-embed-generic',
        'labels' => array(
            'name' => __('Reusable Blocks', 'wpu_acf_flexible'),
            'singular_name' => __('Reusable Block', 'wpu_acf_flexible'),
            'add_new' => __('Add New', 'wpu_acf_flexible'),
            'add_new_item' => __('Add New Reusable Block', 'wpu_acf_flexible'),
            'edit_item' => __('Edit Reusable Block', 'wpu_acf_flexible'),
            'new_item' => __('New Reusable Block', 'wpu_acf_flexible'),
            'search_items' => __('Search Reusable Blocks', 'wpu_acf_flexible'),
            'not_found' => __('No Reusable Blocks found', 'wpu_acf_flexible'),
            'not_found_in_trash' => __('No Reusable Blocks found in Trash', 'wpu_acf_flexible')
        )
    ));
});

/* ----------------------------------------------------------
  Add block model
---------------------------------------------------------- */

add_filter('wpu_acf_flexible_content', function ($blocks) {
    if (!apply_filters('wpu_acf_flexible__enable_reusable_blocks', false)) {
        return $blocks;
    }
    $block_group_id = apply_filters('wpu_acf_flexible__reusable_blocks_group_id', 'content-blocks');
    if (isset($blocks[$block_group_id])) {
        /* Choose blocks on this post type */
        $blocks[$block_group_id]['location'][] = array(
            array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'wpuacf_blocks'
            )
        );
        /* Do not add model on reusable blocks page */
        if (wpuacfflex_get_current_post_type() != 'wpuacf_blocks') {
            /* Add as a block */
            $blocks[$block_group_id]['layouts']['wpuacf_blocks'] = array(
                'key' => 'wpuacf_blocks_layout',
                'wpuacf_model' => 'wpuacf_blocks',
                'label' => __('Reusable Blocks', 'wpu_acf_flexible'),
                'sub_fields' => array(
                    'wpuacf_blocks' => array(
                        'type' => 'post',
                        'label' => __('Reusable Block', 'wpu_acf_flexible'),
                        'instructions' => sprintf(__('View all <a %s>reusable blocks</a>', 'wpu_acf_flexible'), 'target="_blank" href="' . admin_url('edit.php?post_type=wpuacf_blocks') . '"'),
                        'post_type' => 'wpuacf_blocks'
                    )
                )
            );
        }
    }
    return $blocks;
}, 999, 1);
