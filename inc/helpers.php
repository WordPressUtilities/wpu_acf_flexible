<?php

/* ----------------------------------------------------------
  Get flexible blocks
---------------------------------------------------------- */

function get_wpu_acf_flexible_content($group = 'blocks', $mode = 'front') {
    global $post, $wpu_acf_flexible;
    if (!have_rows($group)) {
        return '';
    }

    ob_start();

    $group_item = $group;
    $groups = apply_filters('wpu_acf_flexible_content', array());
    if (!isset($groups[$group])) {
        return '';
    }
    $group_item = $groups[$group];

    while (have_rows($group)):
        the_row();
        $layout = get_row_layout();
        if (!isset($group_item['layouts'][$layout])) {
            return '';
        }

        $_layout_settings = $group_item['layouts'][$layout];

        /* Do not save if this layout is not allowed */
        if ($mode == 'admin' && isset($_layout_settings['no_save_post'])) {
            continue;
        }

        /* Load controller or template file */
        $controller_path = $wpu_acf_flexible->get_controller_path($group_item);
        $layout_file = $controller_path . $layout . '.php';
        $context = $wpu_acf_flexible->get_row_context($group, $layout);

        /* Include theme layout file */
        if (file_exists($layout_file)) {
            include $layout_file;
        } else {
            /* Include default model if available */
            if (isset($_layout_settings['wpuacf_model'])) {
                include $wpu_acf_flexible->plugin_dir_path . 'blocks/' . $_layout_settings['wpuacf_model'] . '/content.php';
            }
        }

    endwhile;
    return '<div data-group="' . esc_attr($group) . '">' . ob_get_clean() . '</div>';
}

/* ----------------------------------------------------------
  Helpers
---------------------------------------------------------- */

function get_wpu_acf_image_src($image, $size = 'thumbnail') {
    if (!is_numeric($image)) {
        return '';
    }
    $item_src = '';
    if (is_numeric($image)) {
        $image = wp_get_attachment_image_src($image, $size);
        if (is_array($image)) {
            $item_src = $image[0];
        }
    }
    return $item_src;
}

function get_wpu_acf_image($image, $size = 'thumbnail') {
    if (!is_numeric($image)) {
        return '';
    }
    /* Retrieve image HTML without srcset */
    add_filter('wp_calculate_image_srcset_meta', '__return_null');
    $html = wp_get_attachment_image($image, $size);
    remove_filter('wp_calculate_image_srcset_meta', '__return_null');
    return $html;
}

function get_wpu_acf_figure($image, $size = 'thumbnail') {
    if (!is_numeric($image)) {
        return '';
    }

    $html = get_wpu_acf_image($image, $size);

    if (apply_filters('get_wpu_acf_figure__display_figcaption', true)) {
        $thumb_details = get_post($image);
        $_figure_content = '';
        if (isset($thumb_details->post_title) && $thumb_details->post_title) {
            $_figure_content .= '<p class="figure-title">' . trim($thumb_details->post_title) . '</p>';
        }
        if (isset($thumb_details->post_excerpt) && $thumb_details->post_excerpt) {
            $_figure_content .= '<p class="figure-excerpt">' . trim($thumb_details->post_excerpt) . '</p>';
        }
        if (!empty($_figure_content)) {
            $html .= '<figcaption>' . $_figure_content . '</figcaption>';
        }
    }

    return '<figure class="acfflex-figure">' . $html . '</figure>';
}

function get_wpu_acf_link($link, $classname = '') {
    if (!$link || !is_array($link) || !isset($link['url'])) {
        return '';
    }
    $classname = apply_filters('get_wpu_acf_link_classname', $classname);
    return '<a class="acfflex-link ' . esc_attr($classname) . '" target="' . $link['target'] . '" href="' . $link['url'] . '"><span>' . $link['title'] . '</span></a>';
}

/* ----------------------------------------------------------
  Internal helpers
---------------------------------------------------------- */

function get_wpu_acf_wrapper_classname($block_type) {
    $classes = array(
        'centered-container',
        'cc-wpuacfflexible',
        'cc-block-' . $block_type,
        'cc-block-' . $block_type . '--' . get_row_layout()
    );
    $classes = apply_filters('get_wpu_acf_wrapper_classname', $classes, $block_type);
    return implode(' ', $classes);
}

function get_wpu_acf_title_content() {
    return get_wpu_acf__title() . get_wpu_acf__content();
}

function get_wpu_acf__title() {
    $_title = get_sub_field('title');
    if ($_title) {
        return '<h2 class="field-title"><span>' . $_title . '</span></h2>';
    }
    return '';
}

function get_wpu_acf__content() {
    $_content = apply_filters('the_content', get_sub_field('content'));
    if ($_content) {
        return '<div class="field-content cssc-content">' . $_content . '</div>';
    }
    return '';
}

function get_wpu_acf_cta($link_id = 'cta', $classname = '') {
    $_cta_link = get_sub_field($link_id);
    $_return = '';
    if (is_array($_cta_link)) {
        $_return .= '<div class="field-cta">' . get_wpu_acf_link($_cta_link, $classname) . '</div>';
    }
    return $_return;
}
