<?php

/* ----------------------------------------------------------
  Get flexible blocks
---------------------------------------------------------- */

function get_wpu_acf_flexible_content($group = 'blocks', $mode = 'front') {
    global $post, $wpu_acf_flexible;

    $opt_group = get_the_ID();

    if (!have_rows($group, $opt_group)) {
        if (is_post_type_archive()) {
            $opt_group = get_post_type() . '_options';
        }
        if (!have_rows($group, $opt_group)) {
            return '';
        }
    }

    ob_start();

    $group_item = $group;
    $groups = apply_filters('wpu_acf_flexible_content', array());
    if (!isset($groups[$group])) {
        return '';
    }
    $group_item = $groups[$group];

    while (have_rows($group, $opt_group)):
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

function get_wpu_acf_video_embed_image() {
    $_video = get_sub_field('video');
    if (!$_video) {
        return false;
    }
    $_video = '<div class="content-video">' . $_video . '</div>';
    if (apply_filters('wpu_acf_flexible__video__nocookie', true) || is_admin()) {
        $_video = str_replace('youtube.com/embed', 'youtube-nocookie.com/embed', $_video);
    }

    $_image_size = apply_filters('wpu_acf_flexible__content__video__image_size', 'large');
    $_image_id = get_sub_field('image');
    $_image = '';
    if (!is_admin()) {
        if ($_image_id) {
            $_video = str_replace('src=', 'data-src=', $_video);
            $_video = str_replace('app_id=', 'autoplay=1&app_id=', $_video);
            $_video = str_replace('feature=oembed', 'feature=oembed&autoplay=1', $_video);
            $_image = '<div class="wpuacf-video"><div class="cursor"></div><div class="cover-image">' . get_wpu_acf_image($_image_id, $_image_size) . '</div>' . $_video . '</div>';
        } else {
            $_image = $_video;
        }
    } else {
        $_video = str_replace('autoplay=1', '', $_video);
        $_image = $_video;
    }
    return $_image;
}

function get_wpu_acf_video($video_id, $args = array()) {
    if (!is_numeric($video_id)) {
        return '';
    }
    if (!is_array($args)) {
        $args = array();
    }
    $args['data-wpu-acf-video'] = '1';
    $args_html = '';
    foreach ($args as $k => $v) {
        $args_html .= ' ' . $k . '="' . esc_attr($v) . '"';
    }
    $attachment_url = wp_get_attachment_url($video_id);
    $item_src = '';
    if ($attachment_url) {
        $item_src = '<video ' . $args_html . ' autoplay loop muted playsinline><source src="' . $attachment_url . '" type="video/mp4" /></video>';
    }

    return $item_src;
}

function get_wpu_acf_image_src($image, $size = 'thumbnail') {
    if (!is_numeric($image)) {
        return '';
    }
    $image = wp_get_attachment_image_src($image, $size);
    return is_array($image) ? $image[0] : '';
}

function get_wpu_acf_image($image, $size = 'thumbnail') {
    if (!is_numeric($image)) {
        return '';
    }

    $attr = apply_filters('get_wpu_acf_image__image_attr', array(
        'loading' => 'lazy'
    ));

    $has_srcset = apply_filters('get_wpu_acf_image__has_srcset', false);

    /* Retrieve image HTML without srcset */
    if (!$has_srcset) {
        add_filter('wp_calculate_image_srcset_meta', '__return_null');
    }
    $html = wp_get_attachment_image($image, $size, false, $attr);
    if (!$has_srcset) {
        remove_filter('wp_calculate_image_srcset_meta', '__return_null');
    }
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

function get_wpu_acf_link($link, $classname = '', $attributes = '') {
    if (!$link || !is_array($link) || !isset($link['url'])) {
        return '';
    }
    $link = apply_filters('get_wpu_acf_link__link', $link);
    $classname = apply_filters('get_wpu_acf_link_classname', $classname);
    return '<a title="' . esc_attr(strip_tags($link['title'])) . '" class="acfflex-link ' . esc_attr($classname) . '" ' . $attributes . ' target="' . $link['target'] . '" href="' . $link['url'] . '"><span>' . $link['title'] . '</span></a>';
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
        return apply_filters('get_wpu_acf__title__html', '<h2 class="field-title"><span>' . nl2br(trim($_title)) . '</span></h2>', $_title);
    }
    return '';
}

function get_wpu_acf__content($field_name = 'content') {
    $_content = apply_filters('the_content', get_sub_field($field_name));
    if ($_content) {
        return apply_filters('get_wpu_acf__content__html', '<div class="field-content cssc-content">' . trim($_content) . '</div>', $_content);
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

/* ----------------------------------------------------------
  Loop
---------------------------------------------------------- */

function get_wpu_acf_loop() {
    $_loop_files = apply_filters('get_wpu_acf_loop__files', array(
        'loop-short.php',
        'tpl/loop-short.php'
    ));
    $_loop_dirs = apply_filters('get_wpu_acf_loop__dirs', array(
        get_stylesheet_directory(),
        get_template_directory()
    ));

    foreach ($_loop_files as $file) {
        foreach ($_loop_dirs as $dir) {
            $_file = $dir . '/' . $file;
            if (file_exists($_file)) {
                ob_start();
                include $_file;
                return ob_get_clean();
            }
        }
    }

    return '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
}
