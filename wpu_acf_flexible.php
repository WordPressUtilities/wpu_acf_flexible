<?php

/*
Plugin Name: WPU ACF Flexible
Description: Quickly generate flexible content in ACF
Version: 2.10.2
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class wpu_acf_flexible {
    private $plugin_version = '2.10.2';
    private $field_types = array();

    /* Base */
    private $base_field = array(
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
        'ui' => 0
    );

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
$##ID##_tax = get_term_by('term_taxonomy_id', get_sub_field('##ID##'));
EOT;

    private $default_var_gallery = <<<EOT
$##ID##_gallery = get_sub_field('##ID##');
EOT;

    private $default_value_relationship = <<<EOT
<?php
$##ID## = get_sub_field('##ID##');
if($##ID##):
foreach ($##ID## as \$tmp_post_id):
    \$thumb_url = get_the_post_thumbnail_url(\$tmp_post_id,'thumbnail');
    \$post_title = get_the_title(\$tmp_post_id);
    echo '<a href="'.get_permalink(\$tmp_post_id).'">';
    if(!empty(\$thumb_url)){
        echo '<img src="'.\$thumb_url.'" alt="'.esc_attr(\$post_title).'" />';
    }
    echo \$post_title;
    echo '</a>';
endforeach;
endif;
?>
EOT;

    private $default_value_repeater = <<<EOT
<?php if (get_sub_field('##ID##')): ?>
    <ul class="##ID##-list">
    <?php while (have_rows('##ID##')): the_row(); ?>
        <li>
##REPEAT##
        </li>
    <?php endwhile;?>
    </ul>
<?php endif; ?>
EOT;

    public function __construct() {
        $this->plugin_dir_path = dirname(__FILE__) . '/';
        add_action('init', array(&$this,
            'init'
        ));
        add_action('acf/save_post', array(&$this,
            'save_post'
        ));
        add_action('admin_enqueue_scripts', array(&$this,
            'admin_assets'
        ));
        add_action('wp_enqueue_scripts', array(&$this,
            'front_assets'
        ));
        add_action('plugins_loaded', array(&$this,
            'load_translation'
        ));
    }

    public function load_translation() {
        load_muplugin_textdomain('wpu_acf_flexible', dirname(plugin_basename(__FILE__)) . '/lang/');
    }

    public function init() {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }
        $this->field_types = $this->get_custom_field_types();
        $this->contents = apply_filters('wpu_acf_flexible_content', array());
        foreach ($this->contents as $id => $content) {
            $this->add_field_group($id, $content);
        }
    }

    public function admin_assets($hook_details) {
        $hooks_ok = array(
            'post.php',
            'post-new.php',
            'edit.php'
        );
        if (!in_array($hook_details, $hooks_ok)) {
            return;
        }
        $custom_css = apply_filters('wpu_acf_flexible__admin_css', array(
            'admin-blocks' => plugins_url('assets/admin-blocks.css', __FILE__),
            'front-blocks' => plugins_url('assets/front-blocks.css', __FILE__)
        ));
        foreach ($custom_css as $id => $file) {
            wp_enqueue_style('wpu_acf_flexible-style-admin-' . $id, $file, array(), $this->plugin_version);
        }
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

    public function get_custom_field_types() {
        $upload_size = floor(wp_max_upload_size() / 1024 / 1024);
        $field_types = array(
            'wpuacf_25p' => array(
                'type' => 'acfe_column',
                'columns' => '3/12'
            ),
            'wpuacf_33p' => array(
                'type' => 'acfe_column',
                'columns' => '4/12'
            ),
            'wpuacf_50p' => array(
                'type' => 'acfe_column',
                'columns' => '3/6'
            ),
            'wpuacf_66p' => array(
                'type' => 'acfe_column',
                'columns' => '8/12'
            ),
            'wpuacf_100p' => array(
                'type' => 'acfe_column',
                'columns' => '6/6',
                'endpoint' => true
            ),
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
            'wpuacf_title' => array(
                'label' => __('Title', 'wpu_acf_flexible'),
                'type' => 'text'
            ),
            'wpuacf_text' => array(
                'label' => __('Text', 'wpu_acf_flexible'),
                'type' => 'textarea',
                'rows' => 3
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
        return apply_filters('wpu_acf_flexible__field_types', $field_types);
    }

    public function get_default_field($field, $field_id) {

        /* Load from common fields */
        if (!is_array($field)) {
            if (array_key_exists($field, $this->field_types)) {
                $field = $this->field_types[$field];
            } else {
                $field = array();
            }
        }

        /* Allow common fields with overrides */
        if (isset($field['type']) && array_key_exists($field['type'], $this->field_types)) {
            $field = array_merge($this->field_types[$field['type']], $field);
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
            $file_css = $file_path . $field_id . '.css';
            $tpl_file = false;
            if (file_exists($file_id)) {
                $tpl_file = $file_id;
            } else {
                if (isset($field['wpuacf_model'])) {
                    $tpl_file = $this->plugin_dir_path . 'blocks/' . $field['wpuacf_model'] . '/content.php';
                }
            }

            if ($tpl_file) {
                $field['acfe_flexible_render_template'] = $tpl_file;
                $field['acfe_flexible_render_style'] = '';
                $field['acfe_flexible_render_script'] = '';
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

        foreach ($field as $field_key => $field_value) {
            $acf_field[$field_key] = $field_value;
        }
        $acf_field['key'] = $id;

        /* Handle groups & repeaters */
        if (isset($acf_field['sub_fields']) && is_array($acf_field['sub_fields'])) {
            $sub_fields = array();
            foreach ($acf_field['sub_fields'] as $sub_field_id => $sub_field) {
                $sub_fields[$sub_field_id] = $this->set_field($id . $sub_field_id, $sub_field, $sub_field_id);
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

    public function get_var_content_field($id, $sub_field, $level = 2) {
        $sub_field = $this->get_default_field($sub_field, $id);

        $vars = '';
        switch ($sub_field['type']) {
        case 'taxonomy':
            $vars = str_replace('##ID##', $id, $this->default_var_tax) . "\n";
            break;
        case 'gallery':
            $vars = str_replace('##ID##', $id, $this->default_var_gallery) . "\n";
            break;
        case 'color':
        case 'image':
        case 'textarea':
        case 'color_picker':
        case 'url':
        case 'file':
            $vars = '$' . $id . ' = get_sub_field(\'' . $id . '\');' . "\n";
            break;
        default:

        }

        if ($level < 2) {
            $vars = str_replace('get_sub_field', 'get_field', $vars);
        }

        return $vars;
    }

    public function get_value_content_field($id, $sub_field, $level = 2) {
        $sub_field = $this->get_default_field($sub_field, $id);

        $c__start = '<?php if($' . $id . '): ?>';
        $c__end = '<?php endif; ?>';

        $values = '';
        $class_id = 'field-' . $id;
        $classname = 'class="' . $class_id . '"';
        switch ($sub_field['type']) {
        case 'image':
            $values = '<?php echo $' . $id . ' ? get_wpu_acf_image($' . $id . ',\'medium\') : \'\'; ?>' . "\n";
            break;
        case 'file':
            $attachment_url = '<?php echo wp_get_attachment_url($' . $id . '); ?>';
            if (isset($sub_field['mime_types']) && $sub_field['mime_types'] == 'mp4') {
                $values = $c__start . '<?php echo get_wpu_acf_video($' . $id . '); ?>' . $c__end . "\n";
            } else {
                $values = $c__start . $attachment_url . '' . $c__end . "\n";
            }
            break;
        case 'editor':
        case 'textarea':
            $values = $c__start . '<div class="' . $class_id . ' cssc-content"><?php echo wpautop($' . $id . '); ?></div>' . $c__end . "\n";
            break;
        case 'taxonomy':
            $values = '<?php if($' . $id . '_tax):?><a href="<?php echo get_term_link($' . $id . '_tax); ?>"><?php echo $' . $id . '_tax->name; ?></a>' . $c__end . "\n";
            break;
        case 'gallery':
            $values = '<div ' . $classname . '><?php foreach($' . $id . '_gallery as $img): ?><?php echo get_wpu_acf_image($img[\'ID\'], \'large\');?><?php endforeach; ?></div>' . "\n";
            break;
        case 'link':
            $values = '<?php echo get_wpu_acf_link(get_sub_field(\'' . $id . '\')); ?>' . "\n";
            break;
        case 'url':
            $values =
                $c__start . '<a ' . $classname . ' href="<?php echo esc_url($' . $id . '); ?>">' . $c__end . "\n" .
                $c__start . '</a>' . $c__end . "\n";
            break;
        case 'color':
        case 'color_picker':
            $values = $c__start . '<div ' . $classname . ' style="background-color:<?php echo $' . $id . ' ?>;"><?php echo $' . $id . '; ?></div>' . $c__end . "\n";
            break;
        case 'relationship':
            $values = str_replace('##ID##', $id, $this->default_value_relationship) . "\n";
            if ($level < 2) {
                $tmp_value = str_replace('get_sub_field', 'get_field', $tmp_value);
            }
            break;
        case 'repeater':
            $tmp_value_values = '';
            $tmp_value_content = '';
            foreach ($sub_field['sub_fields'] as $sub_id => $sub_sub_field) {
                $field_value = trim($this->get_var_content_field($sub_id, $sub_sub_field));
                if ($field_value) {
                    $tmp_value_values .= $field_value . "\n";
                }
                $tmp_value_content .= $this->get_value_content_field($sub_id, $sub_sub_field, $level + 1);
            }
            if ($tmp_value_values) {
                $tmp_value_content = "<?php\n" . trim($tmp_value_values) . "\n?>\n" . $tmp_value_content;
            }
            $tmp_value = str_replace('##ID##', $id, $this->default_value_repeater) . "\n";
            if ($level < 2) {
                $tmp_value = str_replace('get_sub_field', 'get_field', $tmp_value);
                $tmp_value = str_replace('has_sub_field', 'has_rows', $tmp_value);
            }
            $tmp_value_content = trim($tmp_value_content);
            if (!empty($tmp_value_content)) {
                $values = str_replace('##REPEAT##', $tmp_value_content, $tmp_value) . "\n";
            }

            break;
        case 'tab':
        case 'acfe_column':
            break;
        default:
            $tag = 'div';
            if ($id == 'title') {
                $tag = 'h' . $level;
            }
            $values = '<' . $tag . ' ' . $classname . '><?php echo ' . ($level < 2 ? 'get_field' : 'get_sub_field') . '(\'' . $id . '\') ?></' . $tag . '>' . "\n";
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
                    foreach ($layout['sub_fields'] as $id => $sub_field) {
                        $vars .= $this->get_var_content_field($id, $sub_field);
                        $values .= $this->get_value_content_field($id, $sub_field);
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
     * Save post rows HTML in content
     * @param  int $post_ID
     */
    public function save_post($post_ID) {
        if (empty($_POST)) {
            return;
        }

        $allowed_tags = apply_filters('wpu_acf_flexible__save_post_allowed_tags', '<p><a><strong><em><h1><h2><h3><h4><h5><ol><ul><li><img>');

        $content_html = '';
        foreach ($this->contents as $group => $blocks) {
            if (!isset($blocks['save_post']) || !$blocks['save_post']) {
                continue;
            }
            $content = get_wpu_acf_flexible_content($group, 'admin');
            /* Keep only some useful tags */
            $content = strip_tags($content, $allowed_tags);
            /* Ensure content is correct and secure */
            $content = wp_kses_post($content);
            /* Remove useless spaces */
            $content = preg_replace('/\s+/', ' ', $content);
            $content = str_replace('<p></p>', '', $content);
            $content_html .= trim($content);
        }

        if (!empty($content_html)) {
            $_post = get_post($post_ID);
            $post_infos = array(
                'ID' => $post_ID,
                'post_content' => $content_html
            );

            if (empty($_post->post_excerpt)) {
                $post_infos['post_excerpt'] = wp_trim_words(wp_strip_all_tags($content_html), 20, '');
            }

            wp_update_post($post_infos);
        }
    }
}

$wpu_acf_flexible = new wpu_acf_flexible();

include dirname(__FILE__) . '/inc/master-generator.php';
include dirname(__FILE__) . '/inc/helpers.php';
