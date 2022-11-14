<?php

/* ----------------------------------------------------------
  Get flexible blocks
---------------------------------------------------------- */

function get_wpu_acf_flexible_content($group = 'blocks', $mode = 'front', $wpuacfflex_args = array()) {
    global $post, $wpu_acf_flexible;
    if (!is_object($wpu_acf_flexible)) {
        return '';
    }

    /* Test args */
    if (!is_array($wpuacfflex_args)) {
        $wpuacfflex_args = array();
    }

    /* Init context */
    $wpuacfflex_args['init_context'] = isset($wpuacfflex_args['init_context']) ? $wpuacfflex_args['init_context'] : false;

    /* Specify opt group (post id, option name, ...) */
    $opt_group = get_the_ID();
    if (is_post_type_archive()) {
        $opt_group = get_post_type() . '_options';
    }
    if (isset($wpuacfflex_args['opt_group'])) {
        $opt_group = $wpuacfflex_args['opt_group'];
    }

    if (!have_rows($group, $opt_group)) {
        return '';
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
            continue;
        }

        $_layout_settings = $group_item['layouts'][$layout];

        /* Do not save if this layout is not allowed */
        if ($mode == 'admin' && isset($_layout_settings['no_save_post'])) {
            continue;
        }

        /* Load controller or template file */
        $controller_path = $wpu_acf_flexible->get_controller_path($group_item);
        $layout_file = $controller_path . $layout . '.php';
        if ($wpuacfflex_args['init_context']) {
            $context = $wpu_acf_flexible->get_row_context($group, $layout);
        }

        $is_last_block = false;

        /* Include theme layout file */
        if (file_exists($layout_file)) {
            include $layout_file;
        } else {
            /* Include default model if available */
            if (isset($_layout_settings['wpuacf_model'])) {
                include $wpu_acf_flexible->plugin_dir_path . 'blocks/' . $_layout_settings['wpuacf_model'] . '/content.php';
            }
        }

        $is_last_block = apply_filters('get_wpu_acf_flexible_content__is_last_block', $is_last_block, $layout, $_layout_settings);

        if ($is_last_block) {
            break;
        }

    endwhile;
    return '<div data-group="' . esc_attr($group) . '">' . ob_get_clean() . '</div>';
}

/* ----------------------------------------------------------
  Helpers
---------------------------------------------------------- */

function get_wpu_acf_video_embed_image($args = array()) {
    if (!is_array($args)) {
        $args = array();
    }
    if (!isset($args['video_field_id'])) {
        $args['video_field_id'] = 'video';
    }
    if (!isset($args['image_field_id'])) {
        $args['image_field_id'] = 'image';
    }

    $_video = get_sub_field($args['video_field_id']);
    if (!$_video) {
        return false;
    }

    global $content_width;
    $iframe_width = 560;
    if (isset($content_width) && is_numeric($content_width)) {
        $iframe_width = $content_width;
    }
    $iframe_height = floor($iframe_width * 0.5625);
    if (filter_var($_video, FILTER_VALIDATE_URL) !== false) {
        $_video = '<iframe allowfullscreen allow="autoplay" width="' . $iframe_width . '" height="' . $iframe_height . '" src="' . strip_tags($_video) . '"></iframe>';
    }

    /* Do not embed if not an iframe */
    if (strpos($_video, '<iframe') === false) {
        return false;
    }

    $_video = '<div class="content-video">' . $_video . '</div>';
    if (apply_filters('wpu_acf_flexible__video__nocookie', true) || is_admin()) {
        $_video = str_replace('youtube.com/embed', 'youtube-nocookie.com/embed', $_video);
    }

    $_image_size = apply_filters('wpu_acf_flexible__content__video__image_size', 'large');
    $_image_id = get_sub_field($args['image_field_id']);
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
    $attachment_url = wp_get_attachment_url($video_id);
    if (!$attachment_url) {
        return '';
    }
    if (!is_array($args)) {
        $args = array();
    }
    $args['data-wpu-acf-video'] = '1';

    $src_attr = 'src';
    if (isset($args['data-intersect-only']) || isset($args['data-mobile-only']) || isset($args['data-desktop-only'])) {
        $src_attr = 'data-src';
    }

    $item_src = '<video';
    foreach ($args as $k => $v) {
        $item_src .= ' ' . $k . '="' . esc_attr($v) . '"';
    }
    $item_src .= ' autoplay loop muted playsinline><source ' . $src_attr . '="' . $attachment_url . '" type="video/mp4" /></video>';
    return $item_src;
}

function get_wpu_acf_image_src($image, $size = 'thumbnail') {
    if (is_array($image) && isset($image['ID']) && is_numeric($image['ID'])) {
        $image = $image['ID'];
    }
    if (!is_numeric($image)) {
        return '';
    }
    $image = wp_get_attachment_image_src($image, $size);
    return is_array($image) ? $image[0] : '';
}

function get_wpu_acf_image($image, $size = 'thumbnail', $attr = array()) {
    if (is_array($image) && isset($image['ID']) && is_numeric($image['ID'])) {
        $image = $image['ID'];
    }
    if (!is_numeric($image)) {
        return '';
    }

    if (!is_array($attr)) {
        $attr = array();
    }
    if (!isset($attr['loading'])) {
        $attr['loading'] = 'lazy';
    }
    $attr = apply_filters('get_wpu_acf_image__image_attr', $attr);

    $has_srcset = apply_filters('get_wpu_acf_image__has_srcset', false);
    if (isset($attr['has_srcset'])) {
        $has_srcset = $attr['has_srcset'];
    }

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

function get_wpu_acf_figure($image, $size = 'thumbnail', $attr = array()) {
    if (!is_numeric($image)) {
        return '';
    }
    if (!is_array($attr)) {
        $attr = array();
    }
    if (!isset($attr['figcaption'])) {
        $attr['figcaption'] = true;
    }
    if (!isset($attr['figcaption_content'])) {
        $attr['figcaption_content'] = '';
    }
    if (!isset($attr['img_wrapper'])) {
        $attr['img_wrapper'] = false;
    }

    $html = get_wpu_acf_image($image, $size);
    if ($attr['img_wrapper']) {
        $html = '<div class="figure-img-wrapper">' . $html . '</div>';
    }

    if (apply_filters('get_wpu_acf_figure__display_figcaption', $attr['figcaption'])) {
        $thumb_details = get_post($image);
        $_figure_content = '';
        if ($attr['figcaption_content']) {
            $_figure_content .= '<p class="figure-title">' . trim($attr['figcaption_content']) . '</p>';
        } else {
            if (isset($thumb_details->post_title) && $thumb_details->post_title) {
                $_figure_content .= '<p class="figure-title">' . trim($thumb_details->post_title) . '</p>';
            }
            if (isset($thumb_details->post_excerpt) && $thumb_details->post_excerpt) {
                $_figure_content .= '<p class="figure-excerpt">' . trim($thumb_details->post_excerpt) . '</p>';
            }
        }
        if (!empty($_figure_content)) {
            $html .= '<figcaption>' . $_figure_content . '</figcaption>';
        }
    }

    return '<figure class="acfflex-figure">' . $html . '</figure>';
}

function get_wpu_acf_link($link, $classname = '', $attributes = '') {
    if ($link && is_string($link) && substr($link, 0, 1) == '{') {
        $link = json_decode($link, true);
    }
    if (is_array($link)) {
        if (!isset($link['url']) && isset($link['href'])) {
            $link['url'] = $link['href'];
        }
        if (!isset($link['title']) && isset($link['text'])) {
            $link['title'] = $link['text'];
        }
        if (!isset($link['title_visible'])) {
            $link['title_visible'] = $link['title'];
        }
        if (!isset($link['target'])) {
            $link['target'] = '';
        }
    }
    if (!$link || !is_array($link) || !isset($link['url'])) {
        return '';
    }
    $link = apply_filters('get_wpu_acf_link__link', $link);
    $link['title_visible'] = strip_tags($link['title_visible'], '<u><i><strong><em><span>');
    $classname = apply_filters('get_wpu_acf_link_classname', $classname);
    return '<a title="' . esc_attr(strip_tags($link['title'])) . '"' .
    ' class="acfflex-link ' . esc_attr($classname) . '"' .
        ' ' . $attributes .
        ' rel="noopener" ' .
        ($link['target'] ? ' target="' . $link['target'] . '"' : '') .
        ' href="' . $link['url'] . '">' .
        '<span>' . $link['title_visible'] . '</span>' .
        '</a>';
}

function get_wpu_acf_responsive_image($field_value, $classname = '') {
    $mobile_max = apply_filters('get_wpu_acf_responsive_image__mobile_max', 767);
    $classname = apply_filters('get_wpu_acf_responsive_image__classname', 'wpu-acf-responsive-image ' . $classname);
    $html = '<picture class="' . trim(esc_attr($classname)) . '">';
    if ($field_value['image_mobile']):
        $html .= '<source media="(max-width: ' . $mobile_max . 'px)" srcset="' . get_wpu_acf_image_src($field_value['image_mobile'], 'large') . '">';
    endif;
    $html .= get_wpu_acf_image($field_value['image'], 'large');
    $html .= '</picture>';
    return $html;
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

function get_wpu_acf__title($field_name = 'title') {
    $_title = get_sub_field($field_name);
    if ($_title) {
        return apply_filters('get_wpu_acf__title__html', '<h2 class="field-title"><span>' . nl2br(trim($_title)) . '</span></h2>', $_title, $field_name);
    }
    return '';
}

function get_wpu_acf__content($field_name = 'content') {
    $_content = apply_filters('the_content', get_sub_field($field_name));
    if ($_content) {
        return apply_filters('get_wpu_acf__content__html', '<div class="field-content cssc-content">' . trim($_content) . '</div>', $_content, $field_name);
    }
    return '';
}

function get_wpu_acf_cta($link_item = 'cta', $classname = '', $attributes = '') {
    if (is_string($link_item) && substr($link_item, 0, 2) == '{"') {
        $_cta_link = json_decode($link_item, true);
    } elseif (is_array($link_item)) {
        $_cta_link = $link_item;
    } else {
        $_cta_link = get_sub_field($link_item);
    }
    $_return = '';
    if (is_array($_cta_link)) {
        $_return .= '<div class="field-cta">' . get_wpu_acf_link($_cta_link, $classname, $attributes) . '</div>';
    }
    return $_return;
}

/* ----------------------------------------------------------
  Mini editor
---------------------------------------------------------- */

function get_wpu_acf_minieditor($field, $args = array()) {
    if (!is_array($args)) {
        $args = array();
    }
    if (!isset($args['allowed_tags'])) {
        $args['allowed_tags'] = '<u><a><strong><span><em>';
    }
    if (isset($args['extra_allowed_tags'])) {
        $args['allowed_tags'] .= $args['extra_allowed_tags'];
    }
    $args['allowed_tags'] = apply_filters('wpu_acf_flexible__get_wpu_acf_minieditor__allowed_tags', $args['allowed_tags'], $field, $args);
    $field = strip_tags($field, $args['allowed_tags']);
    $field = apply_filters('wpu_acf_flexible__get_wpu_acf_minieditor__before_wpautop', $field, $args['allowed_tags']);
    return wpautop($field);
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

/* ----------------------------------------------------------
  Multilingual
---------------------------------------------------------- */

function wpuacfflex_get_languages() {
    global $polylang;
    if (function_exists('pll_the_languages') && is_object($polylang)) {
        $poly_langs = $polylang->model->get_languages_list();
        $languages = array();
        foreach ($poly_langs as $lang) {
            $languages[$lang->slug] = array(
                'name' => $lang->name,
                'flag' => $lang->flag
            );
        }
        return $languages;
    }

    return array();
}

function wpuacfflex_get_current_language() {
    global $polylang;
    if (function_exists('pll_current_language')) {
        return pll_current_language();
    }
    return false;
}

function wpuacfflex_get_current_admin_language() {
    global $polylang;
    $current_language = false;

    // Obtaining from Polylang
    if (function_exists('pll_the_languages') && is_object($polylang) && $polylang->pref_lang) {
        $current_language_tmp = $polylang->pref_lang->slug;
        if ($current_language_tmp != 'all') {
            $current_language = $current_language_tmp;
        }
    }

    return $current_language;
}

function wpuacfflex_lang_get_field($selector, $post_id = false, $format_value = true) {
    $lang = wpuacfflex_get_languages();
    $current_lang = wpuacfflex_get_current_language();
    $base_field = get_field($selector, $post_id, $format_value);
    if (!$lang || !is_array($lang) || !$current_lang || !is_array($base_field) || !isset($base_field['val_' . $current_lang])) {
        return $base_field;
    }
    return $base_field['val_' . $current_lang];
}

/* ----------------------------------------------------------
  Template
---------------------------------------------------------- */

function wpuacfflex_template_get_layout_css($layout_id, $layout, $css_rule_prefix = '') {
    $css = '';
    $fields = isset($layout['sub_fields']) && is_array($layout['sub_fields']) ? $layout['sub_fields'] : array();

    /* Ensure prefix has a space */
    if ($css_rule_prefix) {
        $css_rule_prefix = trim($css_rule_prefix) . ' ';
    }

    /* Add a rule for each field */
    foreach ($fields as $field_id => $field) {
        $css .= "\n" . $css_rule_prefix;
        $css .= ".field-" . $field_id;
        $css .= " {\n\n}\n";
    }
    return $css;
}
