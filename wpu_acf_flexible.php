<?php

/*
Plugin Name: WPU ACF Flexible
Description: Quickly generate flexible content in ACF
Version: 3.6.5
Plugin URI: https://github.com/WordPressUtilities/wpu_acf_flexible/
Update URI: https://github.com/WordPressUtilities/wpu_acf_flexible/
Author: Darklg
Author URI: https://darklg.me/
Text Domain: wpu_acf_flexible
Domain Path: /lang
Requires at least: 6.2
Requires PHP: 8.0
Network: Optional
License: MIT License
License URI: https://opensource.org/licenses/MIT
*/

defined('ABSPATH') || die;

class wpu_acf_flexible {
    public $basetoolbox;
    public $plugin_description;
    private $plugin_version = '3.6.5';
    public $field_types = array();

    public $plugin_dir_path;
    public $contents;

    /* Base */
    public $base_field = array(
        'key' => 'field_598c51a00af6c',
        'label' => 'Title',
        'name' => 'title',
        'type' => 'text',
        'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => ''
        ),
        '_name' => '',
        '_prepare' => 0,
        'ajax' => 0,
        'allow_null' => 0,
        'append' => '',
        'conditional_logic' => 0,
        'default_value' => '',
        'display' => 'block',
        'filters' => array('search', 'post_type', 'taxonomy'),
        'instructions' => '',
        'library' => 'all',
        'max' => '',
        'max_height' => '',
        'max_size' => '',
        'max_width' => '',
        'maxlength' => '',
        'mime_types' => '',
        'min' => '',
        'min_height' => '',
        'min_size' => '',
        'min_width' => '',
        'multiple' => 0,
        'new_lines' => '',
        'placeholder' => '',
        'prefix' => '',
        'prepend' => '',
        'preview_size' => 'thumbnail',
        'required' => 0,
        'sub_fields' => array(),
        'taxonomy' => array(),
        'translations' => 'translate',
        'ui' => 0
    );

    public $editor_heights = array();

    private $default_content = <<<EOT
<?php
###varsblockid###
?><div class="centered-container cc-block--###testblockid###">
    <div class="block--###testblockid###">
###valuesblockid###
    </div>
</div>
EOT;

    private $default_var_tax = <<<EOT
$##ID##_tax = get_sub_field('##ID##');
if(is_numeric($##ID##_tax)){
    $##ID##_tax = get_term_by('term_taxonomy_id', $##ID##_tax);
}
EOT;

    private $default_var_relationship_repeater = <<<EOT
$##ID## = get_sub_field('##ID##');
if(!$##ID##){
    return;
}
EOT;

    private $default_var_gallery = <<<EOT
$##ID##_gallery = get_sub_field('##ID##');
EOT;

    private $default_value_relationship = <<<EOT
<?php
$##ID## = get_sub_field('##ID##');
if($##ID##):
foreach ($##ID## as \$tmp_post_id):
    echo '<a href="'.get_permalink(\$tmp_post_id).'">';
    if (has_post_thumbnail(\$tmp_post_id)) {
        echo get_the_post_thumbnail(\$tmp_post_id, 'medium', array('loading' => 'lazy'));
    }
    echo get_the_title(\$tmp_post_id);
    echo '</a>';
endforeach;
endif;
?>
EOT;

    private $default_value_relationship_nocond = <<<EOT
<?php
foreach ($##ID## as \$tmp_post_id):
    echo '<a href="'.get_permalink(\$tmp_post_id).'">';
    if (has_post_thumbnail(\$tmp_post_id)) {
        echo get_the_post_thumbnail(\$tmp_post_id, 'medium', array('loading' => 'lazy'));
    }
    echo get_the_title(\$tmp_post_id);
    echo '</a>';
endforeach;
?>
EOT;

    private $default_value_repeater = <<<EOT
<?php
$##ID## = get_sub_field('##ID##');
if($##ID##):
?>
<ul class="##ID##-list">
<?php while (have_rows('##ID##')): the_row(); ?>
    <li>
    <div class="##ID##-list__item">
##REPEAT##
    </div>
    </li>
<?php endwhile;?>
</ul>
<?php endif; ?>
EOT;

    private $default_value_repeater_nocond = <<<EOT
<ul class="##ID##-list">
<?php while (have_rows('##ID##')): the_row(); ?>
    <li>
    <div class="##ID##-list__item">
##REPEAT##
    </div>
    </li>
<?php endwhile;?>
</ul>
EOT;
    private $default_value_group = <<<EOT
<div class="group-##ID##">
##REPEAT##
</div>
EOT;

    public function __construct() {
        $this->plugin_dir_path = __DIR__ . '/';
        add_action('init', array(&$this,
            'init'
        ));
        add_action('plugins_loaded', array(&$this,
            'plugins_loaded'
        ));
        add_action('after_setup_theme', array(&$this,
            'after_setup_theme'
        ));
        add_action('acf/save_post', array(&$this,
            'save_post'
        ), 99);
        add_action('wpu_acf_flexible__trigger_save_post', array(&$this,
            'trigger_save_post'
        ), 10, 1);
        add_action('acf/input/admin_footer', array(&$this,
            'add_draft_validation'
        ), 10);

        add_action('admin_enqueue_scripts', array(&$this,
            'admin_assets'
        ));
        add_action('wp_enqueue_scripts', array(&$this,
            'front_assets'
        ));

        add_action('admin_head', array(&$this,
            'admin_head'
        ));
        add_filter('acf/fields/wysiwyg/toolbars', array(&$this,
            'add_toolbars'
        ));
        add_filter('acf/prepare_field', array(&$this,
            'conditionally_show_hide_fields'
        ));
        add_action('admin_head', array(&$this,
            'admin_set_editor_height'
        ));
        add_action('admin_head', array(&$this,
            'admin_set_styles'
        ));
        add_action('admin_bar_menu', array(&$this,
            'admin_bar_menu'
        ), 99);
        add_filter('acfe/flexible/layouts/icons', array(&$this,
            'set_acfe_flexible_layouts_icons'
        ), 999, 1);
        add_filter('acf/validate_value', array(&$this,
            'validate_value'
        ), 10, 4);
        add_filter('acfe/flexible/secondary_actions', array($this,
            'secondary_actions'
        ), 20, 2);
    }

    public function plugins_loaded() {

        # TOOLBOX
        require_once __DIR__ . '/inc/WPUBaseToolbox/WPUBaseToolbox.php';
        $this->basetoolbox = new \wpu_acf_flexible\WPUBaseToolbox(array(
            'need_form_js' => false,
            'plugin_name' => 'WPU ACF Flexible'
        ));
        $this->basetoolbox->check_plugins_dependencies(array(
            'acfpro' => array(
                'path' => 'advanced-custom-fields-pro/acf.php',
                'url' => 'https://www.advancedcustomfields.com/',
                'name' => 'Advanced Custom Fields PRO'
            ),
            'acfext' => array(
                'path' => 'acf-extended/acf-extended.php',
                'url' => 'https://wordpress.org/plugins/acf-extended/',
                'name' => 'ACF Extended'
            )
        ));

        /* Check if Polylang Pro is installed and version is higher than 3.7 */
        if ($this->pll_has_translate_once()) {
            $this->base_field['translations'] = 'translate_once';
        }
    }

    public function pll_has_translate_once() {
        return (defined('POLYLANG_VERSION') && version_compare(POLYLANG_VERSION, '3.7', '>'));
    }

    # Translations
    public function after_setup_theme() {
        $lang_dir = dirname(plugin_basename(__FILE__)) . '/lang/';
        if (strpos(__DIR__, 'mu-plugins') !== false) {
            load_muplugin_textdomain('wpu_acf_flexible', $lang_dir);
        } else {
            load_plugin_textdomain('wpu_acf_flexible', false, $lang_dir);
        }

        $this->plugin_description = __('Quickly generate flexible content in ACF', 'wpu_acf_flexible');
    }

    public function init() {

        if (apply_filters('wpu_acf_flexible__apply_copy_metas_bugfix', true) && !$this->pll_has_translate_once()) {
            add_filter('pll_copy_post_metas', array($this,
                'pll_copy_post_metas'
            ), 20, 3);
        }

        if (!function_exists('acf_add_local_field_group')) {
            return;
        }
        $this->base_field = apply_filters('wpu_acf_flexible__base_field', $this->base_field);
        $this->field_types = $this->get_custom_field_types();
        $this->contents = apply_filters('wpu_acf_flexible_content', array());
        foreach ($this->contents as $id => $content) {
            $this->add_field_group($id, $content);
        }

        /* Hook to disable base CSS in front-end */
        if (apply_filters('wpu_acf_flexible__disable_front_css', false)) {
            add_filter('wpu_acf_flexible__admin_css', array(&$this, 'disable_front_css'), 10, 1);
            add_filter('wpu_acf_flexible__front_css', array(&$this, 'disable_front_css'), 10, 1);
        }
    }

    public function admin_assets($hook_details) {

        /* Extra JS */
        wp_enqueue_script('wpu_acf_flexible-script-wpuacfadmin', plugins_url('assets/admin-scripts.js', __FILE__), array(), $this->plugin_version);
        wp_localize_script('wpu_acf_flexible-script-wpuacfadmin', 'wpu_acf_flexible_script_wpuacfadmin', apply_filters('wpu_acf_flexible_script_wpuacfadmin_settings', array(
            'color_picker_palettes' => array()
        )));

        /* Nav */
        $hooks_menus = array(
            'nav-menus.php'
        );
        if (in_array($hook_details, $hooks_menus)) {
            $custom_css = apply_filters('wpu_acf_flexible__admin_css__nav_menus', array(
                'admin-nav-menus' => plugins_url('assets/admin-nav.css', __FILE__)
            ));
            $custom_css['admin-interface'] = plugins_url('assets/admin-interface.css', __FILE__);
            foreach ($custom_css as $id => $file) {
                wp_enqueue_style('wpu_acf_flexible-style-admin-nav-menus-' . $id, $file, array(), $this->plugin_version);
            }
        }

        /* Post */
        $hooks_ok = array(
            'post.php',
            'post-new.php',
            'edit.php',
            'term.php',
            'edit-tags.php'
        );
        $hooks_ok = apply_filters('wpu_acf_flexible__admin_js__hooks_ok', $hooks_ok, $hook_details);
        if (!in_array($hook_details, $hooks_ok)) {
            return;
        }
        $custom_css = apply_filters('wpu_acf_flexible__admin_css', array(
            'admin-blocks' => plugins_url('assets/admin-blocks.css', __FILE__),
            'front-blocks' => plugins_url('assets/front-blocks.css', __FILE__)
        ));
        $custom_css['admin-interface'] = plugins_url('assets/admin-interface.css', __FILE__);
        foreach ($custom_css as $id => $file) {
            wp_enqueue_style('wpu_acf_flexible-style-admin-' . $id, $file, array(), $this->plugin_version);
        }

        do_action('wpu_acf_flexible__admin_assets');
    }

    public function front_assets($hook_details) {
        /* Styles */
        $custom_css = apply_filters('wpu_acf_flexible__front_css', array(
            'front-blocks' => plugins_url('assets/front-blocks.css', __FILE__)
        ));
        foreach ($custom_css as $id => $file) {
            wp_enqueue_style('wpu_acf_flexible-style-front-' . $id, $file, array(), $this->plugin_version);
        }
        /* Front */
        $custom_js = apply_filters('wpu_acf_flexible__front_js', array(
            'front-blocks' => plugins_url('assets/front-blocks.js', __FILE__)
        ));

        foreach ($custom_js as $id => $file) {
            wp_enqueue_script('wpu_acf_flexible-script-front-' . $id, $file, array(), $this->plugin_version);
        }
    }

    public function admin_head() {
        $current_admin_language = wpuacfflex_get_current_admin_language();
        if ($current_admin_language) {
            echo '<script>window.wpuacfflex_current_admin_language="' . esc_attr($current_admin_language) . '";</script>';
        }
    }

    public function disable_front_css($styles = array()) {
        if (isset($styles['front-blocks'])) {
            unset($styles['front-blocks']);
        }
        return $styles;
    }

    public function get_custom_field_types() {
        $upload_size = floor(wp_max_upload_size() / 1024 / 1024);
        $field_types = array(
            'wpuacf_image' => array(
                'label' => __('Image', 'wpu_acf_flexible'),
                'type' => 'image',
                'required' => 1
            ),
            'wpuacf_video' => array(
                'label' => __('Video', 'wpu_acf_flexible'),
                'instructions' => sprintf(__('MP4 format. Max %sMB. Rec: 1.5MB.', 'wpu_acf_flexible'), $upload_size),
                'type' => 'file',
                'mime_types' => 'mp4'
            ),
            'wpuacf_cta' => array(
                'label' => __('Link', 'wpu_acf_flexible'),
                'type' => 'link'
            ),
            'wpuacf_imagecta' => array(
                'label' => __('Clickable image', 'wpu_acf_flexible'),
                'type' => 'group',
                'sub_fields' => array(
                    'cola' => 'wpuacf_50p',
                    'image' => 'wpuacf_image',
                    'colb' => 'wpuacf_50p',
                    'cta' => 'wpuacf_cta'
                ),
                'field_html_callback' => function ($id, $sub_field, $level) {
                    return '<?php echo get_wpu_acf_imagecta(get_sub_field(\'' . $id . '\')); ?>' . "\n";
                }
            ),
            'wpuacf_title' => array(
                'label' => __('Title', 'wpu_acf_flexible'),
                'type' => 'text'
            ),
            'wpuacf_uniqid' => array(
                'label' => __('Unique ID', 'wpu_acf_flexible'),
                'type' => 'text',
                'wrapper' => array(
                    'width' => '',
                    'class' => 'wpu-acf-flex-hidden-field',
                    'id' => ''
                )
            ),
            'wpuacf_text' => array(
                'label' => __('Text', 'wpu_acf_flexible'),
                'type' => 'textarea',
                'rows' => 3,
                'field_vars_callback' => function ($id, $sub_field, $level) {
                    return '';
                },
                'field_html_callback' => function ($id, $sub_field, $level) {
                    return '<?php echo get_wpu_acf_text(get_sub_field(\'' . $id . '\')); ?>' . "\n";
                }
            ),
            'wpuacf_minieditor' => array(
                'label' => __('Editor', 'wpu_acf_flexible'),
                'type' => 'editor',
                'editor_height' => 150,
                'toolbar' => 'wpuacf_mini',
                'field_vars_callback' => function ($id, $sub_field, $level) {
                    return '';
                },
                'field_html_callback' => function ($id, $sub_field, $level) {
                    return '<?php echo get_wpu_acf_minieditor(get_sub_field(\'' . $id . '\')); ?>' . "\n";
                }
            ),
            'wpuacf_image_position' => array(
                'label' => __('Image position', 'wpu_acf_flexible'),
                'type' => 'select',
                'choices' => array(
                    'left' => __('Left', 'wpu_acf_flexible'),
                    'right' => __('Right', 'wpu_acf_flexible')
                )
            )
        );

        /* Hook */
        $fields_types = apply_filters('wpu_acf_flexible__field_types', $field_types);

        /* Ensure field format is ok */
        foreach ($fields_types as $k => $field_type) {
            $fields_types[$k] = $this->get_default_field($field_type, $k);
        }

        return $fields_types;
    }

    public function add_toolbars($toolbars) {
        $toolbars['wpuacf_mini'] = array();
        $toolbars['wpuacf_mini'][1] = array(
            'bold',
            'italic',
            'underline',
            'link'
        );
        return $toolbars;
    }

    public function get_default_field($field, $field_id) {

        /* Load from common fields */
        if (!is_array($field)) {
            if (array_key_exists($field, $this->field_types)) {
                $field_type = $field;
                $field = $this->field_types[$field];
                $field['original_field_type'] = $field_type;
            } else {
                $field = array();
            }
        }

        /* Sub fields */
        if (!isset($field['sub_fields'])) {
            $field['sub_fields'] = array();
        }
        if (isset($field['wpuacf_extra_sub_fields']) && is_array($field['wpuacf_extra_sub_fields'])) {
            $field['sub_fields'] = array_merge($field['sub_fields'], $field['wpuacf_extra_sub_fields']);
        }

        /* Allow common fields with overrides */
        if (isset($field['type']) && array_key_exists($field['type'], $this->field_types)) {
            $field = array_merge($this->field_types[$field['type']], $field);
            if (isset($this->field_types[$field['type']]['sub_fields']) && !empty($this->field_types[$field['type']]['sub_fields']) && empty($field['sub_fields'])) {
                $field['sub_fields'] = $this->field_types[$field['type']]['sub_fields'];
            }
            $field['type'] = $this->field_types[$field['type']]['type'];
        }

        /* Label */
        if (!isset($field['label'])) {
            $field['label'] = ucfirst($field_id);
        }
        if (!isset($field['title'])) {
            $field['title'] = $field['label'];
        }
        $field['name'] = $field_id;

        /* Type */
        if (!isset($field['type'])) {
            $field['type'] = 'text';
        }

        if (!isset($field['original_field_type'])) {
            $field['original_field_type'] = $field['type'];
        }

        if ($field['type'] == 'true_false' && !isset($field['ui'])) {
            $field['ui'] = 1;
        }

        /* Instructions */
        $instructions_part = array();
        if (isset($field['min_width'], $field['min_height'])) {
            $instructions_part[] = sprintf(__('Dimensions: min %s', 'wpu_acf_flexible'), $field['min_width'] . '&times;' . $field['min_height'] . 'px');
        }
        if (isset($field['mime_types'])) {
            $format = $field['mime_types'];
            if (!is_array($format)) {
                $format = explode(',', $field['mime_types']);
            }
            $instructions_part[] = sprintf(__('Format: %s', 'wpu_acf_flexible'), strtoupper(implode('/', $format)));
        }
        if ($instructions_part && (!isset($field['instructions']) || !$field['instructions'])) {
            $field['instructions'] = implode('. ', $instructions_part) . '.';
        }

        if (isset($field['wpuacf_nav_item_depth']) && is_array($field['wpuacf_nav_item_depth'])) {
            if (!isset($field['wrapper'])) {
                $field['wrapper'] = array();
            }
            if (!isset($field['wrapper']['class'])) {
                $field['wrapper']['class'] = '';
            }
            $field['wrapper']['class'] .= ' wpuacf-nav-item-depth-variable';
            foreach ($field['wpuacf_nav_item_depth'] as $depth) {
                $field['wrapper']['class'] .= ' wpuacf-nav-item-depth-' . $depth;
            }
        }

        /* Required */
        if (!isset($field['required'])) {
            $field['required'] = false;
        }

        return $field;
    }

    public function set_field($id, $field, $field_id, $extras = array()) {
        $acf_field = $this->base_field;

        $field = $this->get_default_field($field, $field_id);

        if (!is_array($extras)) {
            $extras = array();
        }

        /* Choices */
        if (!isset($field['choices'])) {
            $field['choices'] = array(__('No'), __('Yes'));
        }

        if (isset($extras['group'])) {
            $file_path = $this->get_controller_path($extras['group']);
            $file_id = $file_path . $field_id . '.php';
            $tpl_file = false;
            if (file_exists($file_id)) {
                $tpl_file = $file_id;
            } else {
                if (isset($field['wpuacf_model'])) {
                    $tpl_file = $this->plugin_dir_path . 'blocks/' . $field['wpuacf_model'] . '/content.php';
                }
            }

            $thumbnail = false;

            $thumbnail_folders = apply_filters('wpu_acf_flexible__thumbnail_folders', array(
                $this->plugin_dir_path . 'assets/images/blocks/',
                get_stylesheet_directory() . '/images/blocks/',
                get_stylesheet_directory() . '/assets/images/blocks/'
            ));

            $thumbnails_formats = apply_filters('wpu_acf_flexible__thumbnails_formats', array(
                'jpg',
                'png'
            ));

            foreach ($thumbnail_folders as $thumbnail_folder) {
                $thumbnail_folder_url = str_replace(ABSPATH, get_site_url() . '/', $thumbnail_folder);
                foreach ($thumbnails_formats as $thumb_format) {
                    if (file_exists($thumbnail_folder . $field_id . '.' . $thumb_format)) {
                        $thumbnail = $thumbnail_folder_url . $field_id . '.' . $thumb_format;
                    }
                }
            }
            if ($tpl_file) {
                $field['acfe_flexible_render_template'] = $tpl_file;
                $field['acfe_flexible_thumbnail'] = $thumbnail;
                $field['acfe_flexible_render_style'] = '';
                $field['acfe_flexible_render_script'] = '';
            }
        }

        /* Conditional logic */
        if (isset($extras['wpuacf_parent_id']) && isset($field['wpuacf_condition']) && is_array($field['wpuacf_condition'])) {
            if (!isset($field['conditional_logic'])) {
                $field['conditional_logic'] = array();
            }
            foreach ($field['wpuacf_condition'] as $condition_id => $condition_value) {
                $condition_value_parts = explode(':', $condition_value);
                $condition_operator = '==';
                if (count($condition_value_parts) > 1) {
                    switch ($condition_value_parts[0]) {
                    case 'not':
                        $condition_operator = '!=';
                        break;
                    }
                    $condition_value = $condition_value_parts[1];
                }
                $field['conditional_logic'][] = array(
                    'field' => $extras['wpuacf_parent_id'] . $condition_id,
                    'operator' => $condition_operator,
                    'value' => $condition_value
                );
            }
        }

        /* Return */
        if (isset($field['type'])) {
            if ($field['type'] == 'select' && !isset($field['return_format'])) {
                $field['return_format'] = 'value';
            }
            if (($field['type'] == 'image' || $field['type'] == 'file' || $field['type'] == 'taxonomy') && !isset($field['return_format'])) {
                $field['return_format'] = 'id';
            }
            if ($field['type'] == 'color') {
                $field['type'] = 'color_picker';
            }
            if ($field['type'] == 'editor' || $field['type'] == 'wysiwyg') {
                $field['type'] = 'wysiwyg';
                if (!isset($field['media_upload'])) {
                    $field['media_upload'] = false;
                }
                if (!isset($field['toolbar'])) {
                    $field['toolbar'] = 'basic';
                }
                if (isset($field['editor_height']) && is_numeric($field['editor_height'])) {
                    $this->editor_heights[] = array(
                        'field_id' => $id,
                        'editor_height' => $field['editor_height']
                    );
                }
            }
            if ($field['type'] == 'post' || $field['type'] == 'post_object') {
                $field['type'] = 'post_object';
                if (!isset($field['multiple'])) {
                    $field['multiple'] = 0;
                }
                if (!isset($field['return_format'])) {
                    $field['return_format'] = 'id';
                }
                if (!isset($field['ui'])) {
                    $field['ui'] = 1;
                }
            }
        }

        $languages = wpuacfflex_get_languages();
        if (is_array($field) && !empty($languages) && isset($field['wpuacf_lang']) && $field['wpuacf_lang']) {
            unset($field['wpuacf_lang']);
            $base_field = $field;
            $field['type'] = 'group';
            $field['sub_fields'] = array();
            foreach ($languages as $key => $lang) {
                $field['sub_fields']['wpuacf_lang_tab_' . $key] = array(
                    'type' => 'tab',
                    'label' => $lang['name']
                );
                $field['sub_fields']['val_' . $key] = $base_field;
            }
        }

        foreach ($field as $field_key => $field_value) {
            $acf_field[$field_key] = $field_value;
        }
        $acf_field['key'] = $id;

        /* Handle groups & repeaters */
        if (isset($acf_field['sub_fields']) && is_array($acf_field['sub_fields'])) {
            $sub_fields = array();
            foreach ($acf_field['sub_fields'] as $sub_field_id => $sub_field) {
                $sub_fields[$sub_field_id] = $this->set_field($id . $sub_field_id, $sub_field, $sub_field_id, array(
                    'wpuacf_parent_id' => $id
                ));
            }
            $acf_field['sub_fields'] = $sub_fields;
        }

        /* Handle flexible content */
        if (isset($acf_field['layouts']) && is_array($acf_field['layouts'])) {
            $layouts = array();
            foreach ($acf_field['layouts'] as $layout_id => $layout) {
                $layouts[$layout_id] = $this->set_field($id . $layout_id, $layout, $layout_id);
            }
            $acf_field['layouts'] = $layouts;
        }

        return $acf_field;

    }

    public function get_protected_dollar_var_name($var_name) {
        return str_replace('-', '_', $var_name);
    }

    public function get_var_content_field($id, $sub_field, $level = 2, $nb_subfields = 0) {
        $sub_field = $this->get_default_field($sub_field, $id);

        $default_call = '$' . $this->get_protected_dollar_var_name($id) . ' = get_sub_field(\'' . $id . '\');' . "\n";
        $vars = '';
        switch ($sub_field['type']) {
        case 'taxonomy':
            $vars = str_replace('##ID##', $id, $this->default_var_tax) . "\n";
            break;
        case 'gallery':
            $vars = str_replace('##ID##', $id, $this->default_var_gallery) . "\n";
            break;
        case 'image':
            if (!isset($sub_field['required']) || !$sub_field['required']) {
                $vars = $default_call;
            }
            break;
        case 'color':
        case 'textarea':
        case 'editor':
        case 'color_picker':
        case 'url':
        case 'true_false':
        case 'file':
            $vars = $default_call;
            break;
        default:

        }

        if ($level < 2) {
            $vars = str_replace('get_sub_field', 'get_field', $vars);
        }

        if (($nb_subfields == 1 || $sub_field['required']) && ($sub_field['type'] == 'relationship' || $sub_field['type'] == 'repeater')) {
            $vars = str_replace('##ID##', $id, $this->default_var_relationship_repeater) . "\n";
        }

        if (isset($sub_field['field_vars_callback'])) {
            $vars = call_user_func($sub_field['field_vars_callback'], $id, $sub_field, $level);
        }

        return $vars;
    }

    public function get_value_content_field($id, $sub_field, $level = 2, $nb_subfields = 0) {
        $sub_field = $this->get_default_field($sub_field, $id);

        $c__start = '<?php if($' . $this->get_protected_dollar_var_name($id) . '): ?>';
        $c__end = '<?php endif; ?>';

        $values = '';
        $class_id = 'field-' . $id;
        $classname = 'class="' . $class_id . '"';
        switch ($sub_field['type']) {
        case 'image':
            if (isset($sub_field['required']) && $sub_field['required']) {
                $values = '<?php echo get_wpu_acf_image(get_sub_field(\'' . $id . '\'),\'medium\'); ?>' . "\n";
            } else {
                $values = '<?php echo $' . $this->get_protected_dollar_var_name($id) . ' ? get_wpu_acf_image($' . $this->get_protected_dollar_var_name($id) . ',\'medium\') : \'\'; ?>' . "\n";
            }
            break;
        case 'file':
            $attachment_url = '<?php echo wp_get_attachment_url($' . $this->get_protected_dollar_var_name($id) . '); ?>';
            if (isset($sub_field['mime_types']) && $sub_field['mime_types'] == 'mp4') {
                $values = $c__start . '<?php echo get_wpu_acf_video($' . $this->get_protected_dollar_var_name($id) . '); ?>' . $c__end . "\n";
            } else {
                $values = $c__start . $attachment_url . '' . $c__end . "\n";
            }
            break;
        case 'editor':
            $value_content = 'wpautop($' . $this->get_protected_dollar_var_name($id) . ')';
            if (isset($sub_field['toolbar']) && $sub_field['toolbar'] == 'wpuacf_mini') {
                $value_content = 'get_wpu_acf_minieditor($' . $this->get_protected_dollar_var_name($id) . ')';
            }
            $values = $c__start . '<div class="' . $class_id . ' cssc-content"><?php echo ' . $value_content . '; ?></div>' . $c__end . "\n";
            break;
        case 'textarea':
            $values = $c__start . '<div class="' . $class_id . ' cssc-content"><?php echo wpautop(wp_strip_all_tags($' . $this->get_protected_dollar_var_name($id) . ')); ?></div>' . $c__end . "\n";
            break;
        case 'true_false':
            $values = $c__start . $c__end . "\n";
            break;
        case 'taxonomy':
            $values = '<?php if($' . $this->get_protected_dollar_var_name($id) . '_tax):?>';
            if (isset($sub_field['field_type']) && $sub_field['field_type'] == 'checkbox') {
                $values .= '<?php foreach($' . $this->get_protected_dollar_var_name($id) . '_tax as $tmp_tax_id): $tmp_tax = get_term($tmp_tax_id); ?>';
                $values .= '<a href="<?php echo get_term_link($tmp_tax); ?>"><?php echo $tmp_tax->name; ?></a> ';
                $values .= '<?php endforeach; ?>';
            } else {
                $values .= '<a href="<?php echo get_term_link($' . $this->get_protected_dollar_var_name($id) . '_tax); ?>"><?php echo $' . $this->get_protected_dollar_var_name($id) . '_tax->name; ?></a>';
            }

            $values .= $c__end . "\n";
            break;
        case 'gallery':
            $values = '<div ' . $classname . '><?php foreach($' . $this->get_protected_dollar_var_name($id) . '_gallery as $img): ?><?php echo get_wpu_acf_image($img[\'ID\'], \'large\');?><?php endforeach; ?></div>' . "\n";
            break;
        case 'link':
            $values = '<?php echo get_wpu_acf_link(get_sub_field(\'' . $id . '\')); ?>' . "\n";
            break;
        case 'url':
            $values =
            $c__start . '<a ' . $classname . ' href="<?php echo esc_url($' . $this->get_protected_dollar_var_name($id) . '); ?>">' . $c__end . "\n" .
                $c__start . '</a>' . $c__end . "\n";
            break;
        case 'color':
        case 'color_picker':
            $values = $c__start . '<div ' . $classname . ' style="background-color:<?php echo $' . $this->get_protected_dollar_var_name($id) . ' ?>;"><?php echo $' . $this->get_protected_dollar_var_name($id) . '; ?></div>' . $c__end . "\n";
            break;
        case 'relationship':
            $tmp_val = (($nb_subfields == 1 && $level == 2) || $sub_field['required']) ? $this->default_value_relationship_nocond : $this->default_value_relationship;
            $values = str_replace('##ID##', $id, $tmp_val) . "\n";
            if ($level < 2) {
                $values = str_replace('get_sub_field', 'get_field', $values);
            }
            break;
        case 'group':
        case 'repeater':
            $is_group = ($sub_field['type'] == 'group');
            $tmp_value_values = '';
            $tmp_value_content = '';
            foreach ($sub_field['sub_fields'] as $sub_id => $sub_sub_field) {
                if ($is_group) {
                    $sub_id = $id . '_' . $sub_id;
                }
                $field_value = trim($this->get_var_content_field($sub_id, $sub_sub_field));
                if ($field_value) {
                    $tmp_value_values .= $field_value . "\n";
                }
                $tmp_value_content .= $this->get_value_content_field($sub_id, $sub_sub_field, $level + 1);
            }
            if ($tmp_value_values) {
                $tmp_value_content = "<?php\n" . trim($tmp_value_values) . "\n?>\n" . $tmp_value_content;
            }
            $tmp_val = (($nb_subfields == 1 && $level == 2) || $sub_field['required']) ? $this->default_value_repeater_nocond : $this->default_value_repeater;
            if ($is_group) {
                $tmp_val = $this->default_value_group;
            }
            $tmp_value = str_replace('##ID##', $id, $tmp_val) . "\n";
            if ($level < 2) {
                $tmp_value = str_replace('get_sub_field', 'get_field', $tmp_value);
                $tmp_value = str_replace('has_sub_field', 'has_rows', $tmp_value);
                $tmp_value_content = str_replace('get_sub_field', 'get_field', $tmp_value_content);
                $tmp_value_content = str_replace('has_sub_field', 'has_rows', $tmp_value_content);
            }
            $tmp_value_content = trim($tmp_value_content);
            if (!empty($tmp_value_content)) {
                $values = str_replace('##REPEAT##', $tmp_value_content, $tmp_value) . "\n";
            }

            break;
        case 'tab':
        case 'message':
        case 'acfe_column':
            break;
        default:
            $tag = 'div';
            $content_value = ($level < 2 ? 'get_field' : 'get_sub_field') . '(\'' . $id . '\')';
            if ($id == 'title' || $sub_field['name'] == 'title') {
                $tag = 'h' . $level;
                $classname = str_replace('class="', 'class="h' . $level . ' ', $classname);
                $values = '<' . $tag . ' ' . $classname . '><?php echo wp_strip_all_tags(' . $content_value . ') ?></' . $tag . '>' . "\n";
            } else {
                $helper_args = array(
                    'classname' => $class_id
                );
                $values = '<?php echo get_wpu_acf_field_html(' . $content_value . ', ' . var_export($helper_args, true) . '); ?>';
            }
        }

        if (isset($sub_field['field_html_callback'])) {
            $values = call_user_func($sub_field['field_html_callback'], $id, $sub_field, $level);
        }

        $values = apply_filters('wpu_acf_flexible__value_content_field', $values, $id, $sub_field, $level);

        return $values;
    }

    public function add_field_group($content_id, $content = array()) {
        $content_name = (isset($content['name']) && !empty($content['name'])) ? $content['name'] : 'Default';
        $post_types = (isset($content['post_types']) && is_array($content['post_types'])) ? $content['post_types'] : array('post');
        $page_ids = (isset($content['page_ids']) && is_array($content['page_ids'])) ? $content['page_ids'] : array();
        $page_templates = (isset($content['page_templates']) && is_array($content['page_templates'])) ? $content['page_templates'] : array();
        $layouts = (isset($content['layouts']) && is_array($content['layouts'])) ? $content['layouts'] : array();
        $fields = (isset($content['fields']) && is_array($content['fields'])) ? $content['fields'] : array();
        $hide_on_screen = (isset($content['hide_on_screen']) && is_array($content['hide_on_screen'])) ? $content['hide_on_screen'] : array('the_content');
        $position = isset($content['position']) ? $content['position'] : 'acf_after_title';
        $style = isset($content['style']) ? $content['style'] : 'seamless';
        $menu_order = isset($content['menu_order']) ? $content['menu_order'] : 0;
        $label_placement = isset($content['label_placement']) ? $content['label_placement'] : 'top';
        $custom_acf_location = isset($content['location']) ? $content['location'] : array();
        $layout_key = isset($content['key']) ? $content['key'] : 'field_' . md5($content_id);
        $acf_extras_layout = isset($content['acf_extras_layout']) && is_array($content['acf_extras_layout']) ? $content['acf_extras_layout'] : array();

        /* Build Layouts */
        $base_fields = array();
        if (!empty($fields)) {
            foreach ($fields as $field_id => $field) {
                $field_key = isset($field['key']) ? $field['key'] : md5($content_id . $field_id);
                $base_fields[$field_key] = $this->set_field($field_key, $field, $field_id);
            }
        }

        if (!empty($layouts)) {
            $base_field_layouts = array(
                'key' => $layout_key,
                'label' => $content_name,
                'name' => $content_id,
                'type' => 'flexible_content',
                'acfe_flexible_copy_paste' => 1,
                'acfe_flexible_layouts_ajax' => 0,
                'acfe_flexible_layouts_templates' => 1,
                'acfe_flexible_layouts_previews' => 1,
                'acfe_flexible_toggle' => 1,
                'acfe_flexible_title_edition' => 1,
                'acfe_flexible_close_button' => 1,
                'acfe_flexible_layouts_state' => 'collapse',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => ''
                ),
                'layouts' => array(),
                'button_label' => __('Add block', 'wpu_acf_flexible'),
                'min' => '',
                'max' => ''
            );

            if (apply_filters('wpu_acf_flexible__enable_thumbnails', false, $content_id)) {
                $base_field_layouts['acfe_flexible_layouts_thumbnails'] = 1;
                $base_field_layouts['acfe_flexible_modal'] = array(
                    'acfe_flexible_modal_enabled' => '1',
                    'acfe_flexible_modal_title' => __('Add block', 'wpu_acf_flexible'),
                    'acfe_flexible_modal_size' => 'large',
                    'acfe_flexible_modal_col' => '6',
                    'acfe_flexible_modal_categories' => true
                );
            }

            if (is_array($acf_extras_layout)) {
                $base_field_layouts = array_merge($base_field_layouts, $acf_extras_layout);
            }

            foreach ($layouts as $layout_id => $layout) {
                if (isset($layout['wpuacf_model'])) {
                    $layout_tmp = $this->get_layout_model($layout['wpuacf_model'], $layout_id);
                    if (is_array($layout_tmp)) {
                        $base_layout = $layout;
                        $layout = $layout_tmp;
                        foreach ($base_layout as $property => $value) {
                            if ($property != 'wpuacf_model') {
                                $layout[$property] = $value;
                            }
                        }
                        $layouts[$layout_id] = $layout;
                    }
                }
                $layout_key = isset($layout['key']) ? $layout['key'] : md5($content_id . $layout_id);

                $base_field_layouts['layouts'][$layout_key] = $this->set_field($layout_key, $layout, $layout_id, array('group' => $base_field_layouts['name']));
                unset($base_field_layouts['layouts'][$layout_key]['type']);
            }
            $base_fields[] = $base_field_layouts;
        }

        /* Init */
        if (isset($content['init_files']) && $content['init_files']) {
            if (!empty($layouts)) {
                foreach ($layouts as $layout_id => $layout) {
                    /* Do not create file if it's a Model */
                    if (isset($layout['wpuacf_model']) && !isset($layout['override_view'])) {
                        continue;
                    }
                    $vars = '';
                    $values = '';
                    if (!isset($layout['sub_fields']) || !is_array($layout['sub_fields'])) {
                        continue;
                    }
                    $nb_subfields = count($layout['sub_fields']);
                    foreach ($layout['sub_fields'] as $id => $sub_field) {
                        $vars .= $this->get_var_content_field($id, $sub_field, 2, $nb_subfields);
                        $values .= $this->get_value_content_field($id, $sub_field, 2, $nb_subfields);
                    }
                    $this->set_file_content($layout_id, $vars, $values, $content);
                }
            }

            if (!empty($fields)) {
                $vars = '';
                $values = '';
                foreach ($fields as $id => $field) {
                    if (isset($field['type']) && $field['type'] == 'group') {
                        foreach ($field['sub_fields'] as $child_id => $sub_field) {
                            $vars .= $this->get_var_content_field($id . '_' . $child_id, $sub_field, 1);
                            $values .= $this->get_value_content_field($id . '_' . $child_id, $sub_field, 1);
                        }
                    } else {
                        $vars .= $this->get_var_content_field($id, $field, 1);
                        $values .= $this->get_value_content_field($id, $field, 1);
                    }
                }
                $this->set_file_content($content_id, $vars, $values, $content);
            }
        }

        $acf_location = array();

        /* Build post types */
        if (!empty($page_ids)) {
            foreach ($page_ids as $page_id) {
                $acf_location[] = array(
                    array(
                        'param' => 'post',
                        'operator' => '==',
                        'value' => $page_id
                    )
                );
            }
        } else if (!empty($page_templates)) {
            foreach ($page_templates as $page_template) {
                $acf_location[] = array(
                    array(
                        'param' => 'page_template',
                        'operator' => '==',
                        'value' => $page_template
                    )
                );
            }
        } else {
            foreach ($post_types as $post_type) {
                $acf_location[] = array(array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => $post_type
                ));
            }
        }

        if (!empty($custom_acf_location)) {
            $acf_location = $custom_acf_location;
        }

        /* Base content */
        $group = array(
            'key' => 'group_' . md5($content_id),
            'title' => $content_name,
            'fields' => $base_fields,
            'location' => $acf_location,
            'menu_order' => $menu_order,
            'position' => $position,
            'style' => $style,
            'label_placement' => $label_placement,
            'instruction_placement' => 'label',
            'hide_on_screen' => $hide_on_screen,
            'active' => 1,
            'description' => ''
        );

        acf_add_local_field_group($group);

        return $group;
    }

    public function get_layout_model($id, $layout_id) {
        $model_file = $this->plugin_dir_path . 'blocks/' . $id . '/model.php';
        if (!file_exists($model_file)) {
            return false;
        }
        include $model_file;
        if (isset($model) && !empty($model)) {
            $model['wpuacf_model'] = $id;
            $model = apply_filters('wpu_acf_flexible__override_model', $model, $layout_id);
            $model = apply_filters('wpu_acf_flexible__override_model__' . $layout_id, $model);
            return $model;
        }
        return false;
    }

    public function set_file_content($layout_id, $vars, $values, $group) {
        $content = str_replace('###varsblockid###', $vars, $this->default_content);
        $content = str_replace('###valuesblockid###', $values, $content);
        $content = str_replace('###testblockid###', $layout_id, $content);
        $content = apply_filters('wpu_acf_flexible__file_content', $content, $layout_id, $vars, $values, $group);

        /* Remove empty */
        $content = preg_replace('/<\?php(\s\n)\?>/isU', '', $content);
        $content = preg_replace('/(?:(?:\r\n|\r|\n)){2}/s', "\n", $content);
        $file_path = $this->get_controller_path($group);

        if (!is_dir($file_path)) {
            error_log(sprintf('The folder %s does not exist. Please create it.', $file_path));
            return;
        }

        $file_id = $file_path . $layout_id . '.php';

        do_action('wpu_acf_flexible__set_file_content', $layout_id, $group);
        if (!file_exists($file_id)) {
            file_put_contents($file_id, $content);
        }
    }

    public function get_controller_path($group = false) {
        $folder_name = 'blocks';
        if (is_array($group) && isset($group['folder_name'])) {
            $folder_name = $group['folder_name'];
        }

        $controller_path = apply_filters('wpu_acf_flexible__path', get_stylesheet_directory() . '/tpl/' . $folder_name . '/', $group);
        if (!is_dir($controller_path)) {
            @mkdir($controller_path, 0755);
            @chmod($controller_path, 0755);
        }
        return $controller_path;
    }

    public function get_block_context($value, $field) {
        /* Repeaters */
        if (isset($field['type'], $field['sub_fields']) && $field['type'] == 'repeater' && is_array($value)) {
            /* Get all values in repeater */
            foreach ($value as $value_id => $item) {
                /* Get all values for repeated item */
                foreach ($item as $item_id => $item_value) {
                    if (isset($field['sub_fields'][$item_id])) {
                        /* Check is value is ok */
                        $value[$value_id][$item_id] = $this->get_block_context($item_value, $field['sub_fields'][$item_id]);
                    }
                }
            }
        }

        /* Images */
        $image_display_format = isset($field['image_display_format']) ? $field['image_display_format'] : 'thumbnail';
        if (isset($field['type']) && $field['type'] == 'image' && is_numeric($value)) {
            $image = wp_get_attachment_image_src($value, $image_display_format);
            if (is_array($image)) {
                $value = $image[0];
            }
        }
        return $value;
    }

    /* Context for block */

    public function get_row_context($group, $layout) {
        $context = array();

        $acf_contents = apply_filters('wpu_acf_flexible_content', array());

        if (!isset($acf_contents[$group])) {
            return array();
        }

        if (!isset($acf_contents[$group]['layouts'][$layout])) {
            return array();
        }

        /* Build context */
        $group_details = $acf_contents[$group]['layouts'][$layout];
        if (isset($group_details['wpuacf_model'])) {
            $group_details = $this->get_layout_model($group_details['wpuacf_model'], $layout);
        }
        if (isset($group_details['sub_fields'])) {
            foreach ($group_details['sub_fields'] as $id => $field) {
                $context[$id] = $this->get_block_context(get_sub_field($id), $field);
            }
        }

        return $context;

    }

    /**
     * Hide a field conditionally
     * @param  object $field ACF field
     * @return object
     */
    public function conditionally_show_hide_fields($field) {
        if (!is_admin()) {
            return $field;
        }
        global $pagenow;
        $page_post_type = isset($_GET['post_type']) ? $_GET['post_type'] : 'post';
        if (isset($field['wpuacf_hidden_on']) && is_array($field['wpuacf_hidden_on'])) {
            foreach ($field['wpuacf_hidden_on'] as $post_type) {
                if ('post-new.php' === $pagenow && $page_post_type == $post_type) {
                    return false;
                }
                if ('post.php' === $pagenow && isset($_GET['post']) && is_numeric($_GET['post']) && get_post_type($_GET['post']) == $post_type) {
                    return false;
                }
            }
        }
        if (isset($field['wpuacf_visible_on']) && is_array($field['wpuacf_visible_on'])) {
            foreach ($field['wpuacf_visible_on'] as $post_type) {
                if ('post-new.php' === $pagenow && $page_post_type == $post_type) {
                    return $field;
                }
                if ('post.php' === $pagenow && isset($_GET['post']) && is_numeric($_GET['post']) && get_post_type($_GET['post']) == $post_type) {
                    return $field;
                }
            }
            return false;
        }
        return $field;
    }

    public function secure_post_content($content) {
        $allowed_tags = apply_filters('wpu_acf_flexible__save_post_allowed_tags', '<p><br><a><strong><em><h1><h2><h3><h4><h5><ol><ul><li><img><table><tr><td><th><tbody><thead><tfoot>');

        /* Disable form content */
        $content = preg_replace('/<form(.*?)>(.*?)<\/form>/isU', '', $content);

        /* Replace content between <script> tags */
        $content = preg_replace('/<script(.*?)>(.*?)<\/script>/isU', '', $content);

        /* Replace content between <style> tags */
        $content = preg_replace('/<style(.*?)>(.*?)<\/style>/isU', '', $content);

        /* Keep only some useful tags */
        $content = wp_strip_all_tags($content, $allowed_tags);

        /* Ensure content is correct and secure */
        $content = wp_kses_post($content);

        /* Remove useless spaces */
        $content = preg_replace('/\s+/', ' ', $content);
        $content = str_replace('<p></p>', '', $content);

        /* Remove useless attributes */
        $content = preg_replace('/\s(class|style|loading|target|rel)=[\'|"][^\'"]*[\'|"]/', '', $content);

        return trim($content);
    }

    /**
     * Save post rows HTML in content
     * @param  int $post_ID
     */
    public function save_post($post_ID) {
        if (empty($_POST)) {
            return;
        }
        $this->trigger_save_post($post_ID);
    }

    public function trigger_save_post($post_ID) {
        $content_html = $this->secure_post_content(apply_filters('wpu_acf_flexible__save_post_default_content_html', '', $post_ID));

        foreach ($this->contents as $group => $blocks) {
            if (!isset($blocks['save_post']) || !$blocks['save_post']) {
                continue;
            }
            $content_html .= $this->secure_post_content(get_wpu_acf_flexible_content($group, 'admin', array('opt_group' => $post_ID, 'save_post_mode' => true)));
        }

        $content_html .= $this->secure_post_content(apply_filters('wpu_acf_flexible__save_post_default_content_html__after', '', $post_ID));

        if (empty($content_html)) {
            return;
        }

        $_p = get_post($post_ID);
        $post_infos = array(
            'ID' => $post_ID,
            'post_content' => $content_html
        );

        if (empty($_p->post_excerpt)) {
            $post_infos['post_excerpt'] = wp_trim_words(wp_strip_all_tags($content_html), 20, '');
        }

        wp_update_post($post_infos);
        do_action('wpu_acf_flexible__custom_save_post', $post_ID);
    }

    /* Custom validation */
    public function validate_value($valid, $value, $field, $input_name) {

        // Bail early if value is already invalid.
        if ($valid !== true) {
            return $valid;
        }

        /* Validate HTML */
        if (isset($field['wpuacf_validate_html']) && $field['wpuacf_validate_html']) {
            if ($value && is_string($value) && !wpuacfflex_is_html_valid($value)) {
                return __('HTML is invalid', 'wpu_acf_flexible');
            }
        }

        /* Required only on publish */
        if (isset($field['wpuacf_required_on_publish']) && $field['wpuacf_required_on_publish']) {
            if ($this->acf_is_publishing()) {
                if (empty($value) || (is_array($value) && count($value) == 0) || !$value) {
                    return __('This field is required to publish', 'wpu_acf_flexible');
                }
            }
        }

        return $valid;
    }

    public function acf_is_publishing() {

        $is_publishing = false;

        // Ignore autosave/revisions
        if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || (defined('DOING_AJAX') && DOING_AJAX && !isset($_POST['action']))) {
            return false;
        }

        // Classic editor flow: changing from non-publish to publish
        if (isset($_POST['original_post_status'], $_POST['post_status'])) {
            if ($_POST['post_status'] === 'publish' && $_POST['original_post_status'] !== 'publish') {
                return true;
            }
        }

        // Block editor / updating an already published post
        if (isset($_POST['post_status'], $_POST['hidden_post_status'])) {
            if ($_POST['post_status'] === 'publish' && $_POST['hidden_post_status'] === 'publish') {
                return true;
            }
        }

        // Fallback: explicit status check
        if (!$is_publishing && isset($_POST['post_status']) && $_POST['post_status'] === 'publish') {
            return true;
        }
        return false;
    }

    public function secondary_actions($actions) {
        $actions['wpu-acf-flex-reduce'] = '<a href="#" data-acfe-flexible-control-action="wpu-acf-flex-reduce">' . __('Reduce all layouts', 'wpu_acf_flexible') . '</a>';
        $actions['wpu-acf-flex-expand'] = '<a href="#" data-acfe-flexible-control-action="wpu-acf-flex-expand">' . __('Expand all layouts', 'wpu_acf_flexible') . '</a>';
        return $actions;
    }

    /* Add draft validation */
    /* Thanks to https://support.advancedcustomfields.com/forums/topic/is-it-possible-to-apply-validation-to-draft-post/#post-154429 */
    public function add_draft_validation() {
        echo '<script>';
        echo "acf.addAction('prepare', function(){";
        echo "acf.validation.removeEvents({";
        echo "'click #save-post': 'onClickSave',";
        echo "});";
        echo "});";
        echo '</script>';
    }

    /* Set editor height */
    public function admin_set_editor_height() {
        $css = '';
        foreach ($this->editor_heights as $editor) {
            $css .= '[data-key="' . $editor['field_id'] . '"] iframe,';
            $css .= '[data-key="' . $editor['field_id'] . '"] textarea';
            $css .= '{';
            $css .= 'min-height:' . $editor['editor_height'] . 'px!important;';
            $css .= 'height: ' . $editor['editor_height'] . 'px!important;';
            $css .= '}';
        }
        if ($css) {
            echo '<style>' . $css . '</style>';
        }
    }

    public function set_acfe_flexible_layouts_icons($icons) {
        $toggle_title = __('Click to reduce/enlarge the layout', 'wpu_acf_flexible');
        $icons['wpu-acf-flex-toggle'] = '<a class="acf-icon -down small" href="#" data-name="wpu-acf-flex-toggle" title="' . esc_attr($toggle_title) . '"></a>';
        return $icons;
    }

    public function admin_set_styles() {
        echo '<style>';
        echo '.layout.wpuacf-hidden-preview .acf-fields,';
        echo '.layout.wpuacf-hidden-preview .acfe-fc-placeholder ,';
        echo '.wpu-acf-flex-hidden-field{z-index:1!important;position:absolute!important;top:0!important;left:-999em!important;height:1px!important;width:1px!important;overflow:hidden!important;}';
        echo '</style>';
    }

    /* Admin bar */
    public function admin_bar_menu($wp_admin_bar) {
        if (!function_exists('have_rows')) {
            return;
        }
        if (!is_singular()) {
            return;
        }
        $p = get_the_ID();
        if (!current_user_can('edit_post', $p)) {
            return;
        }

        $groups = apply_filters('wpu_acf_flexible_content', array());
        foreach ($groups as $group_id => $group) {
            if (!have_rows($group_id)) {
                continue;
            }
            $menu_id = 'wpu-acf-flex-menu-' . $group_id;
            $wp_admin_bar->add_menu(
                array(
                    'id' => $menu_id,
                    'parent' => null,
                    'href' => get_edit_post_link($p),
                    'title' => $group['name']
                )
            );
            $values = get_field_object($group_id);
            if (isset($values['value'])) {
                foreach ($values['value'] as $i => $val) {
                    $label = $val['acf_fc_layout'];
                    if (isset($val['acfe_flexible_layout_title']) && $val['acfe_flexible_layout_title']) {
                        $label = $val['acfe_flexible_layout_title'];
                    }
                    $wp_admin_bar->add_menu(
                        array(
                            'parent' => $menu_id,
                            'title' => $label,
                            'id' => $menu_id . '-' . $i,
                            'href' => get_edit_post_link($p) . '#wpu-acf-row' . $i
                        )
                    );
                }
                $wp_admin_bar->add_menu(
                    array(
                        'parent' => $menu_id,
                        'title' => '&rarr; ' . __('Add a block', 'wpu_acf_flexible'),
                        'id' => $menu_id . '-add',
                        'href' => get_edit_post_link($p) . '#wpu-acf-add'
                    )
                );
            }
        }

    }

    /**
     * Copy post meta fix : Ensure flexible content is only copied and translated once
     *
     * @param array $metas
     * @return array
     */
    public function pll_copy_post_metas($metas) {
        $flexible_contents = apply_filters('wpu_acf_flexible_content', array());

        /* Loop through all contents groups */
        foreach ($flexible_contents as $key => $layout) {

            /* If this group is not in the metas list */
            if (!in_array($key, $metas)) {
                continue;
            }

            /* Look if a clean layout exists */
            $count = 0;
            foreach ($metas as $meta) {
                if (strpos($meta, $key . '_0') === 0) {
                    $count++;
                }
            }

            /* No clean layout found : remove all traces from this content group from copied fields */
            /* POST data is found : this is an edit and fields should not be synced */
            if (!$count || !empty($_POST)) {
                $new_metas = array();
                foreach ($metas as $meta) {
                    if (strpos($meta, $key) !== 0) {
                        $new_metas[] = $meta;
                    }
                }
                $metas = $new_metas;
            }
        }

        /* Clean return array */
        return array_values($metas);
    }
}

$wpu_acf_flexible = new wpu_acf_flexible();

require_once __DIR__ . '/inc/reusable-blocks.php';
require_once __DIR__ . '/inc/master-generator.php';
require_once __DIR__ . '/inc/helpers.php';

/* Load fields */
$wpu_acf_flexible_fields_files = glob(__DIR__ . '/fields/*.php');
foreach ($wpu_acf_flexible_fields_files as $wpu_acf_flexible_fields_file) {
    require_once $wpu_acf_flexible_fields_file;
}
