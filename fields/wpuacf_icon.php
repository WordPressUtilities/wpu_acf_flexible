<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  Icon
---------------------------------------------------------- */

add_filter('wpu_acf_flexible__field_types', function ($types) {
    $types['wpuacf_icon'] = array(
        'label' => __('Icon', 'wpu_acf_flexible'),
        'type' => 'select',
        'ui' => 1,
        'instructions' => '<a title="' . esc_attr(__('Icon list', 'wpu_acf_flexible')) . '" href="#TB_inline?height=500&width=780&inlineId=wpu_acf_flex_icon_list" class="thickbox">' . esc_html(__('View the list', 'wpu_acf_flexible')) . '</a>',
        'choices' => wpuacfflex__get_icons(),
        'field_html_callback' => function ($id, $sub_field, $level) {
            return '<?php echo get_wpu_acf_icon(get_sub_field(\'' . $id . '\')); ?>' . "\n";
        }
    );
    return $types;
}, 10, 1);

/* ----------------------------------------------------------
  Modal
---------------------------------------------------------- */

add_action('acf/input/admin_head', function () {
    if (wpuacfflex__get_icons()) {
        add_thickbox();
    }
});

add_action('acf/input/admin_footer', function () {
    $icons = wpuacfflex__get_icons();
    if (!$icons) {
        return;
    }
    echo '<div id="wpu_acf_flex_icon_list" style="display: none;"><div>';
    echo '<ul class="wpuacf-icons-list">';
    foreach ($icons as $icon_id => $icon_name) {
        if (!$icon_id) {
            continue;
        }
        echo '<li>' . get_wpu_acf_icon($icon_id) . ' : ' . $icon_id . ' </li>';
    }
    echo '</ul>';
    echo '</div></div>';
}, 10);

/* ----------------------------------------------------------
  Helpers
---------------------------------------------------------- */

/* Get all available icons
-------------------------- */

function wpuacfflex__get_icons() {
    $cache_id = 'wpuacfflex__get_icons';
    $cache_duration = 60;

    $icon_dir = get_stylesheet_directory() . '/src/icons/';
    if (!is_dir($icon_dir)) {
        return array();
    }

    $icons = wp_cache_get($cache_id);
    if ($icons === false) {
        $icons_raw = glob($icon_dir . '*.svg');
        $icons = array();
        foreach ($icons_raw as $icn) {
            $icn = str_replace('.svg', '', basename($icn));
            $icons[$icn] = $icn;
        }
        wp_cache_set($cache_id, $icons, '', $cache_duration);
    }
    return array(
        '' => apply_filters('wpuacfflex__get_icons__no_icon_label', __('-- No Icon --', 'wpu_acf_flexible'))
    ) + $icons;
}

/* Display an icon
-------------------------- */

function get_wpu_acf_icon($icon = '', $args = array()) {
    $icons = wpuacfflex__get_icons();
    if (!$icon || !is_string($icon) || !array_key_exists($icon, $icons)) {
        return '';
    }
    if (!is_array($args)) {
        $args = array();
    }
    $classname = 'icon icon_' . $icon . ' wpuacfflex-icn';
    if (isset($args['classname'])) {
        $classname .= ' ' . $args['classname'];
    }
    $html = '<i aria-hidden="true" class="' . esc_attr(trim($classname)) . '"></i>';

    $wrapper_classname = 'wpuacfflex-icon-wrapper';
    if (isset($args['wrapper_classname'])) {
        $wrapper_classname .= ' ' . $args['wrapper_classname'];
    }
    if (isset($args['wrapper']) && $args['wrapper']) {
        $html = '<div class="' . esc_attr($wrapper_classname) . '">' . $html . '</div>';
    }

    return $html;
}
