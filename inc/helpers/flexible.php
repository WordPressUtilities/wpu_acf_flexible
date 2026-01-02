<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  Get flexible blocks
---------------------------------------------------------- */

function get_wpu_acf_flexible_content($group = 'blocks', $mode = 'front', $wpuacfflex_args = array()) {
    global $post, $wpu_acf_flexible;
    if (!is_object($wpu_acf_flexible)) {
        return '';
    }

    $query_monitor_block_id = 'get_wpu_acf_flexible_content-' . $group . '-' . $mode;

    do_action('qm/start', $query_monitor_block_id);

    /* Test args */
    if (!is_array($wpuacfflex_args)) {
        $wpuacfflex_args = array();
    }

    $wpuacfflex_args['save_post_mode'] = isset($wpuacfflex_args['save_post_mode']) ? $wpuacfflex_args['save_post_mode'] : false;

    /* Init context */
    $wpuacfflex_args['init_context'] = isset($wpuacfflex_args['init_context']) ? $wpuacfflex_args['init_context'] : false;

    /* Specify opt group (post id, option name, ...) */
    $opt_group = get_the_ID();
    if (is_post_type_archive()) {
        $opt_group = get_post_type() . '_options';
    }
    if (is_tax() || is_category() || is_tag()) {
        $term = get_queried_object();
        $opt_group = $term->taxonomy . '_' . $term->term_id;
    }
    if (isset($wpuacfflex_args['opt_group'])) {
        $opt_group = $wpuacfflex_args['opt_group'];
    }

    $group_item = $group;
    $groups = apply_filters('wpu_acf_flexible_content', array());
    if (!isset($groups[$group])) {
        return '';
    }
    $group_item = $groups[$group];

    if (!have_rows($group, $opt_group)) {
        $default_html = '';

        /* Display default content */
        if (is_singular() && apply_filters('get_wpu_acf_flexible_content__display__default_content', false)) {
            $default_html .= apply_filters('get_wpu_acf_flexible_content__before_default_content', '<section class="centered-container cc-wpuacfflex-default-content section"><div class="wpuacfflex-default-content cssc-content">');
            $default_html .= apply_filters('get_wpu_acf_flexible_content__default_content', get_the_content());
            $default_html .= apply_filters('get_wpu_acf_flexible_content__after_default_content', '</div></section>');
        }

        /* Migrate default content */
        if (is_singular() && apply_filters('get_wpu_acf_flexible_content__migrate_default_content', false)) {
            wpu_acf_flex__migrate_content_to_blocks(get_the_ID(), $group);
        } else {
            return $default_html;
        }
    }

    ob_start();
    while (have_rows($group, $opt_group)):

        the_row();
        if (is_singular() && post_password_required()) {
            echo apply_filters('get_wpu_acf_flexible_content__before_password_form', '<section class="centered-container cc-wpuacfflex-password-form section"><div class="wpuacfflex-password-form">');
            echo apply_filters('get_wpu_acf_flexible_content__password_form', get_the_password_form());
            echo apply_filters('get_wpu_acf_flexible_content__after_password_form', '</div></section>');
            break;
        }

        $layout = get_row_layout();
        if (!isset($group_item['layouts'][$layout])) {
            continue;
        }

        $_layout_settings = $group_item['layouts'][$layout];

        /* Do not save if this layout is not allowed */
        $has_no_save = false;
        if ($mode == 'admin') {
            if (isset($_layout_settings['no_save_post']) || get_sub_field('acfe_flexible_toggle')) {
                continue;
            }
            if (isset($_layout_settings['wpuacf_model'])) {
                $model = $wpu_acf_flexible->get_layout_model($_layout_settings['wpuacf_model'], $layout);
                if (isset($model['no_save_post']) && $model['no_save_post']) {
                    continue;
                }
            }
        }

        /* Load controller or template file */
        $controller_path = $wpu_acf_flexible->get_controller_path($group_item);
        $layout_file = $controller_path . $layout . '.php';
        if ($wpuacfflex_args['init_context']) {
            $context = $wpu_acf_flexible->get_row_context($group, $layout);
        }

        $is_last_block = false;

        /* Include theme layout file */
        do_action('get_wpu_acf_flexible_content__before_layout', '', $layout);
        do_action('get_wpu_acf_flexible_content__before_layout__' . $layout, '');

        /* Store variables */
        $wpuacfflex_variables = array(
            'layout' => $layout,
            '_layout_settings' => $_layout_settings
        );

        $wpuacfflex_layout_index = get_row_index();

        if (file_exists($layout_file)) {
            include $layout_file;
        } else {
            /* Include default model if available */
            if (isset($_layout_settings['wpuacf_model'])) {
                include $wpu_acf_flexible->plugin_dir_path . 'blocks/' . $_layout_settings['wpuacf_model'] . '/content.php';
            }
        }

        /* Restore variables */
        $layout = $wpuacfflex_variables['layout'];
        $_layout_settings = $wpuacfflex_variables['_layout_settings'];

        do_action('get_wpu_acf_flexible_content__after_layout__' . $layout, '');
        do_action('get_wpu_acf_flexible_content__after_layout', '', $layout);

        do_action('qm/lap', $query_monitor_block_id, get_row_layout());

        $is_last_block = apply_filters('get_wpu_acf_flexible_content__is_last_block', $is_last_block, $layout, $_layout_settings);
        if ($is_last_block) {
            break;
        }

    endwhile;
    $html = '<div data-group="' . esc_attr($group) . '">' . ob_get_clean() . '</div>';
    do_action('qm/stop', $query_monitor_block_id);
    return $html;
}
