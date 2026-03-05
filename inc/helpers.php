<?php
defined('ABSPATH') || die;

include __DIR__ . '/helpers/flexible.php';
include __DIR__ . '/helpers/migration.php';
include __DIR__ . '/helpers/media.php';
include __DIR__ . '/helpers/cta.php';
include __DIR__ . '/helpers/language.php';
include __DIR__ . '/helpers/text.php';
include __DIR__ . '/helpers/similar.php';

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

function get_wpu_acf__title($field_name = 'title', $classname = '') {
    $_title = get_sub_field($field_name);
    if ($_title) {
        return apply_filters('get_wpu_acf__title__html', '<h2 class="field-title ' . esc_attr($classname) . '"><span>' . nl2br(trim($_title)) . '</span></h2>', $_title, $field_name);
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
  Download : Files
---------------------------------------------------------- */

function wpuacfflex_downloads_parse_files($files) {
    if (!is_array($files)) {
        return array();
    }
    $_files = array();
    foreach ($files as $_file) {
        $_url = false;
        $_label = 'link';
        $_target = '';
        $_size = '';
        $_download = false;
        $_extension = false;

        /* Get URL */
        if (!empty($_file['url'])) {
            $_url = $_file['url'];
            $_target = '_blank';
        }
        if (is_numeric($_file['file'])) {
            $_download = true;
            $_url = wp_get_attachment_url($_file['file']);
            $_label = pathinfo($_url, PATHINFO_BASENAME);
            $_extension = strtolower(pathinfo($_url, PATHINFO_EXTENSION));
            $file_obj = get_attached_file($_file['file']);
            if (file_exists($file_obj)) {
                $_size = wpuacfflex_human_filesize(filesize($file_obj), 1);
            }
        }
        if (!$_url) {
            continue;
        }

        /* Get label */
        if (!empty($_file['filename'])) {
            $_label = $_file['filename'];
        }

        $_files[] = array(
            'url' => esc_url($_url),
            'size' => $_size,
            'target' => $_target,
            'title' => strip_tags($_label),
            'download' => $_download,
            'extension' => strip_tags($_extension)
        );
    }
    return $_files;
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

    return force_balance_tags($string);
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

function wpuacfflex_get_file_cache($cache_key, $duration, $callback, $callback_args = array()) {
    require_once __DIR__ . '/WPUBaseFileCache/WPUBaseFileCache.php';
    $wpubasefilecache = new \wpu_acf_flexible\WPUBaseFileCache('wpu_acf_flexible');
    $query_value = $wpubasefilecache->get_cache($cache_key, $duration);
    if (!$query_value) {
        $query_value = call_user_func($callback, $callback_args);
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

/* ----------------------------------------------------------
  Get row ID
---------------------------------------------------------- */

function wpuacfflex_get_row_id() {
    return 'wpuacfflex_id_' . get_row_index() . '_' . get_row_layout();
}

/* ----------------------------------------------------------
  Get field group
---------------------------------------------------------- */

function wpuacfflex_get_master_header_group() {
    if (is_post_type_archive()) {
        return get_post_type() . '_options';
    }
    if (is_tax() || is_category() || is_tag()) {
        $queried_object = get_queried_object();
        if (isset($queried_object->taxonomy)) {
            return $queried_object->taxonomy . '_' . $queried_object->term_id;
        }
    }
    return get_the_ID();
}

/* ----------------------------------------------------------
  WP-CLI : Display ACF Flexible Content layouts & fields HTML
---------------------------------------------------------- */

if (defined('WP_CLI')) {
    /* Layout */
    WP_CLI::add_command('wpu-acf-flex-get-layout-content', function ($args, $assoc_args) {
        if (empty($args[0])) {
            WP_CLI::error('Please provide a layout name as first argument.');
        }
        do_action('wpu_acf_flexible_generate_layout_content', $args[0]);
    }, array(
        'when' => 'wp_loaded'
    ));

    /* Fields */
    WP_CLI::add_command('wpu-acf-flex-get-field-content', function ($args, $assoc_args) {
        if (empty($args[0])) {
            WP_CLI::error('Please provide a field name as first argument.');
        }
        do_action('wpu_acf_flexible_generate_field_content', $args[0], isset($args[1]) ? $args[1] : null);
    }, array(
        'when' => 'wp_loaded'
    ));
}
