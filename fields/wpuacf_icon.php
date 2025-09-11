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
        'allow_null' => 1,
        'wrapper' => array(
            'class' => 'wpuacf-icons-list'
        ),
        'instructions' => '<a data-wpuacf-thickbox-classname="wpuacf-icon-window" title="' . esc_attr(__('Icon list', 'wpu_acf_flexible')) . '" href="#" class="thickbox-wpuacf-icon">' . esc_html(__('View the list', 'wpu_acf_flexible')) . '</a>',
        'choices' => array_map(function ($icon) {
            $extra_names = apply_filters('wpuacf_icon_admin_choices', array(), $icon);
            $extra_names = array_filter(array_unique($extra_names));
            asort($extra_names);
            return get_wpu_acf_icon($icon) . ' ' . $icon . ($extra_names ? ' <small>(' . implode(', ', $extra_names) . ')</small>' : '');
        }, wpuacfflex__get_icons()),
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

    $icon_dir = apply_filters('wpuacf_icon_dir', get_stylesheet_directory() . '/src/icons/');
    if (!is_dir($icon_dir)) {
        return array();
    }

    $icons = wp_cache_get($cache_id);
    if ($icons === false) {
        $icons_raw = glob($icon_dir . '*.svg');
        $icons = array();
        foreach ($icons_raw as $icn) {
            $icn = strtolower(str_replace('.svg', '', basename($icn)));
            $icons[$icn] = $icn;
        }
        ksort($icons);
        wp_cache_set($cache_id, $icons, '', $cache_duration);
    }
    return $icons;
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
