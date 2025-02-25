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
    if (is_tax()) {
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
        if ($mode == 'admin' && (isset($_layout_settings['no_save_post']) || get_sub_field('acfe_flexible_toggle'))) {
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
        do_action('get_wpu_acf_flexible_content__before_layout', '', $layout);
        do_action('get_wpu_acf_flexible_content__before_layout__' . $layout, '');
        if (file_exists($layout_file)) {
            include $layout_file;
        } else {
            /* Include default model if available */
            if (isset($_layout_settings['wpuacf_model'])) {
                include $wpu_acf_flexible->plugin_dir_path . 'blocks/' . $_layout_settings['wpuacf_model'] . '/content.php';
            }
        }
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

/* ----------------------------------------------------------
  Helpers
---------------------------------------------------------- */

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
        unset($attr['has_srcset']);
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

    /* Load default attributes */
    if (!is_array($attr)) {
        $attr = array();
    }
    $default_attr = array(
        'figcaption' => true,
        'figcaption_content' => '',
        'img_wrapper' => false
    );
    foreach ($default_attr as $k => $v) {
        if (!isset($attr[$k])) {
            $attr[$k] = $v;
        }
    }

    /* Keep only valid attributes in image */
    $image_attr = $attr;
    foreach ($default_attr as $k => $v) {
        if (isset($image_attr[$k])) {
            unset($image_attr[$k]);
        }
    }

    $html = get_wpu_acf_image($image, $size, $image_attr);
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
    $link['title_visible'] = strip_tags($link['title_visible'], '<u><i><strong><em><span><img>');
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

function get_wpu_acf_imagecta($field, $classname = '', $attributes = '', $args = array()) {
    if (!is_array($field) || !isset($field['image'], $field['cta'])) {
        return '';
    }
    $image_size = is_array($args) && isset($args['image_size']) ? $args['image_size'] : 'thumbnail';
    $html = get_wpu_acf_image($field['image'], $image_size);
    if ($field['cta'] && $html) {
        $field['cta']['title_visible'] = $html;
        $html = get_wpu_acf_link($field['cta'], $classname, $attributes);
    }
    return $html;
}

function get_wpu_acf_responsive_image($field_value, $classname = '') {
    $mobile_max = apply_filters('get_wpu_acf_responsive_image__mobile_max', 767);
    $classname = apply_filters('get_wpu_acf_responsive_image__classname', 'wpu-acf-responsive-image ' . $classname);
    $html = '<picture class="' . trim(esc_attr($classname)) . '">';
    if (isset($field_value['image_mobile']) && $field_value['image_mobile']):
        $html .= '<source media="(max-width: ' . $mobile_max . 'px)" srcset="' . get_wpu_acf_image_src($field_value['image_mobile'], 'large') . '">';
    endif;
    $html .= get_wpu_acf_image($field_value['image'], 'large');
    $html .= '</picture>';
    return $html;
}

function get_wpu_acf_slider($slider, $image_size = 'large') {
    if (!$slider || !$slider['gallery']) {
        return '';
    }
    $slider_html = '';
    $slider_attributes = '';
    if ($slider['slider_options']['autoplay']) {
        $slider_attributes .= ' data-slider-autoplay="' . $slider['slider_options']['autoplay'] . '"';
    }
    if ($slider['slider_options']['autoplay_speed']) {
        $slider_attributes .= ' data-slider-autoplay-speed="' . $slider['slider_options']['autoplay_speed'] . '"';
    }
    $slider_html .= '<div class="wpuacf-slider " ' . $slider_attributes . '>';
    foreach ($slider['gallery'] as $img):
        $slider_html .= '<div><div class="img">' . get_wpu_acf_image($img['ID'], $image_size) . '</div></div>';
    endforeach;
    $slider_html .= '</div>';
    return $slider_html;
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
        $args['allowed_tags'] = '<u><a><strong><span><em><p>';
    }
    if (isset($args['extra_allowed_tags'])) {
        $args['allowed_tags'] .= $args['extra_allowed_tags'];
    }
    if (!isset($args['add_wrapper'])) {
        $args['add_wrapper'] = false;
    }
    if (!isset($args['wrapper_classname'])) {
        $args['wrapper_classname'] = 'field-minieditor cssc-content';
    }
    $args['allowed_tags'] = apply_filters('wpu_acf_flexible__get_wpu_acf_minieditor__allowed_tags', $args['allowed_tags'], $field, $args);
    $field = strip_tags($field, $args['allowed_tags']);
    $field = apply_filters('wpu_acf_flexible__get_wpu_acf_minieditor__before_wpautop', $field, $args['allowed_tags']);
    $field_content = wpautop($field);
    if (!$field_content) {
        return '';
    }
    if ($args['add_wrapper']) {
        return '<div class="' . esc_attr($args['wrapper_classname']) . '">' . $field_content . '</div>';
    }
    return $field_content;
}

/* ----------------------------------------------------------
  Text
---------------------------------------------------------- */

function get_wpu_acf_text($field_value, $args = array()) {
    if (!is_array($args)) {
        $args = array();
    }

    /* Null field */
    if (!$field_value) {
        return '';
    }

    $args = array_merge(array(
        'classname' => 'field-text cssc-content',
        'allowed_tags' => ''
    ), $args);
    $field_value = trim(strip_tags($field_value, $args['allowed_tags']));
    if (!$field_value) {
        return '';
    }
    return '<div class="' . esc_attr($args['classname']) . '">' . wpautop($field_value) . '</div>';
}

/* ----------------------------------------------------------
  Gallery
---------------------------------------------------------- */

function get_wpu_acf_gallery($gallery, $args = array()) {
    if (!is_array($gallery) || empty($gallery)) {
        return '';
    }
    $args = !is_array($args) ? array() : $args;
    $default_args = array(
        'format' => 'medium',
        'bigimage_format' => 'large',
        'wrapper_classname' => '',
        'link_classname' => '',
        'list_classname' => '',
        'item_classname' => '',
        'link_bigimage' => true
    );
    $args = array_merge($default_args, $args);
    $args['wrapper_classname'] .= ' wpuacf-gallery__wrapper';
    $args['list_classname'] .= ' wpuacf-gallery';
    $args['item_classname'] .= ' wpuacf-gallery__item';
    $args['link_classname'] .= ' wpuacf-gallery__item-link';

    $html = '<div class="' . trim(esc_attr($args['wrapper_classname'])) . '">';
    $html .= '<ul class="' . trim(esc_attr($args['list_classname'])) . '">';
    foreach ($gallery as $image) {
        $bigimage = $args['link_bigimage'] ? wp_get_attachment_image_url($image['ID'], $args['bigimage_format']) : false;
        $html .= '<li>';
        $html .= '<div class="' . trim(esc_attr($args['item_classname'])) . '">';
        $html .= $bigimage ? '<a class="' . trim(esc_attr($args['link_classname'])) . '" href="' . $bigimage . '">' : '';
        $html .= wp_get_attachment_image($image['ID'], $args['format']);
        $html .= $bigimage ? '</a>' : '';
        $html .= '</div>';
        $html .= '</li>';
    }
    $html .= '</ul>';
    $html .= '</div>';

    return $html;
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

    $excluded_field_types = apply_filters('wpuacfflex_template_get_layout_css__excluded_field_types', array(
        'tab',
        'acfe_column',
        'message'
    ));

    /* Add a rule for each field */
    foreach ($fields as $field_id => $field) {
        if (isset($field['type']) && in_array($field['type'], $excluded_field_types)) {
            continue;
        }
        $css .= "\n" . $css_rule_prefix;
        $css .= ".field-" . $field_id;
        $css .= " {\n\n}\n";
    }
    return $css;
}

/* ----------------------------------------------------------
  File size
---------------------------------------------------------- */

/* Thanks to https://www.php.net/manual/en/function.filesize.php#106569 */
function wpuacfflex_human_filesize($bytes, $decimals = 2) {
    $sz = str_split('bkmgtp');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . $sz[$factor];
}

/* ----------------------------------------------------------
  Validate HTML
---------------------------------------------------------- */

/**
 * Check if a HTML string is valid
 * Thanks to https://stackoverflow.com/a/3167315/975337
 * @param  string     $string    HTML to test
 * @return boolean
 */
function wpuacfflex_is_html_valid($string) {

    /* Remove line breaks */
    $string = str_replace(array("\n", "\r"), " ", $string);

    /* Clean HTML */
    $string = str_replace(array('& ', ' &'), '&amp;', $string);
    $string = str_replace(array('\"'), '"', $string);
    $string = str_replace(array('<br>'), '<br/>', $string);

    /* Force one root */
    $string = "<div>$string</div>";

    /* Load XML & check errors */
    libxml_use_internal_errors(true);
    libxml_clear_errors();
    simplexml_load_string($string);

    $count_error = count(libxml_get_errors());

    /* Return error count */
    return $count_error == 0;
}

/* ----------------------------------------------------------
  Fix HTML
---------------------------------------------------------- */

/**
 * Fix HTML
 * @param  string     $string    HTML to fix
 * @return string
 */
function wpuacfflex_fix_html_validity($string) {

    if (wpuacfflex_is_html_valid($string)) {
        return $string;
    }

    $non_closing_tags = array(
        'br',
        'hr',
        'img'
    );

    /* List all open tags */
    $open_tags = array();
    preg_match_all('/<([a-z]+)(?: .*)?>/i', $string, $matches);
    foreach ($matches[1] as $tag) {
        if (!in_array($tag, $non_closing_tags)) {
            $open_tags[] = $tag;
        }
    }

    /* List all close tags */
    $close_tags = array();
    preg_match_all('/<\/([a-z]+)>/i', $string, $matches);
    foreach ($matches[1] as $tag) {
        if (!in_array($tag, $non_closing_tags)) {
            $close_tags[] = $tag;
        }
    }

    /* Close open tags */
    $close_tags_diff = array_diff($open_tags, $close_tags);
    foreach ($close_tags_diff as $tag) {
        $string .= "</$tag>";
    }

    /* Open closed tags */
    $open_tags_diff = array_diff($close_tags, $open_tags);
    foreach ($open_tags_diff as $tag) {
        $string = "<$tag>$string";
    }

    return $string;
}

/* ----------------------------------------------------------
  Multilingual fields
---------------------------------------------------------- */

/* Handle multiple lang for each field
-------------------------- */

function wpuacfflex_i18n_get_field_group($fields) {
    if (!function_exists('pll_languages_list')) {
        return $fields;
    }
    $poly_langs = pll_languages_list();
    $new_fields = array();
    foreach ($poly_langs as $code) {
        $new_fields['wpuacfflex_i18n_tab___' . $code] = array(
            'label' => strtoupper($code),
            'type' => 'tab'
        );
        foreach ($fields as $field_id => $field) {
            $new_fields[$field_id . '___' . $code] = $field;
        }
    }
    return $new_fields;
}

/* Return current value  for a field
-------------------------- */

function wpuacfflex_i18n_get_field($selector, $post_id = false, $format_value = true, $escape_html = false) {
    $lang_id = '';
    if (function_exists('pll_current_language')) {
        $selector .= '___' . pll_current_language('slug');
    }
    return get_field($selector, $post_id, $format_value, $escape_html);
}

/* ----------------------------------------------------------
  Conditional values
---------------------------------------------------------- */

/**
 * Get current post type on early calls in admin
 *
 * @return string
 */
function wpuacfflex_get_current_post_type() {
    $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : 'post';
    if ((strpos($_SERVER['REQUEST_URI'], 'post.php') !== false || strpos($_SERVER['REQUEST_URI'], 'post-new.php') !== false) && isset($_GET['post']) && is_numeric($_GET['post'])) {
        return get_post_type($_GET['post']);
    }
    return $post_type;
}

/**
 * Conditional values based on post types
 *
 * @param array $values
 * @return mixed
 */
function wpuacfflex_get_value_based_on_post_type($values = array()) {
    if (!is_array($values)) {
        return $values;
    }
    $post_type = wpuacfflex_get_current_post_type();
    if (isset($values[$post_type])) {
        return $values[$post_type];
    }

    if (isset($values['default'])) {
        return $values['default'];
    }
    return false;
}

/* ----------------------------------------------------------
  FileCache
---------------------------------------------------------- */

function wpuacfflex_get_file_cache($cache_key, $duration, $callback) {
    require_once __DIR__ . '/WPUBaseFileCache/WPUBaseFileCache.php';
    $wpubasefilecache = new \wpu_acf_flexible\WPUBaseFileCache('wpu_acf_flexible');
    $query_value = $wpubasefilecache->get_cache($cache_key, $duration);
    if (!$query_value) {
        $query_value = call_user_func($callback);
        $wpubasefilecache->set_cache($cache_key, $query_value);
    }
    return $query_value;
}

/* ----------------------------------------------------------
  Test if a link is external
---------------------------------------------------------- */

function wpuacfflex_is_external_link($url) {
    if (strpos($url, 'http') !== 0) {
        return false;
    }
    $site_url = get_site_url();
    if (strpos($url, $site_url) === 0) {
        return false;
    }
    return true;
}
