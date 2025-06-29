<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  GPS Coordinates
---------------------------------------------------------- */

add_filter('wpu_acf_flexible__field_types', function ($types) {
    $types['wpuacf_gps'] = array(
        'label' => __('GPS Coordinates', 'wpu_acf_flexible'),
        'type' => 'group',
        'sub_fields' => array(
            'wpuacf_autocomplete_address' => array(
                'label' => __('Address', 'wpu_acf_flexible'),
                'type' => 'text'
            ),
            'cola' => 'wpuacf_50p',
            'lat' => array(
                'required' => 1,
                'label' => __('Latitude', 'wpu_acf_flexible'),
                'type' => 'number'
            ),
            'colb' => 'wpuacf_50p',
            'lng' => array(
                'required' => 1,
                'label' => __('Longitude', 'wpu_acf_flexible'),
                'type' => 'number'
            )
        ),
        'field_vars_callback' => function ($id, $sub_field, $level) {
            return '';
        },
        'field_html_callback' => function ($id, $sub_field, $level) {
            return '<?php echo json_encode(wpuacf_gps(get_sub_field(\'' . $id . '\'))); ?>' . "\n";
        }

    );
    return $types;
}, 10, 1);

/* ----------------------------------------------------------
  Helper
---------------------------------------------------------- */

function wpuacf_gps($field) {
    if (!is_array($field)) {
        return;
    }
    if (!isset($field['lat']) || !isset($field['lng'])) {
        return;
    }
    return array(
        'lat' => floatval($field['lat']),
        'lng' => floatval($field['lng'])
    );
}

/* ----------------------------------------------------------
  Autocomplete
---------------------------------------------------------- */

add_action('wpu_acf_flexible__admin_assets', function () {
    $token = apply_filters('wpu_acf_flexible__mapbox_token', '');
    if (defined('WPUACF_GPS_MAPBOX_TOKEN')) {
        $token = WPUACF_GPS_MAPBOX_TOKEN;
    }
    if (!$token) {
        return;
    }

    $script_src = apply_filters('wpu_acf_flexible__mapbox_search_script_src', 'https://api.mapbox.com/search-js/v1.1.0/web.js');
    echo <<<EOT
<script>
document.addEventListener("DOMContentLoaded", function() {
    'use strict';
    var script = document.createElement('script');
    script.defer = true;
    document.body.appendChild(script);
    script.onload = function(){
        wpuacf_load_mapbox('{$token}');
    }
    script.src = "{$script_src}";
});
</script>
EOT;
});
