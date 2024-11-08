<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  Embed
---------------------------------------------------------- */

add_filter('wpu_acf_flexible__field_types', function ($types) {
    $types['wpuacf_embed'] = array(
        'label' => __('Video', 'wpu_acf_flexible'),
        'type' => 'group',
        'sub_fields' => array(
            'cola' => 'wpuacf_50p',
            'embed' => array(
                'label' => __('Source', 'wpu_acf_flexible'),
                'type' => 'oembed'
            ),
            'colb' => 'wpuacf_50p',
            'use_thumb' => array(
                'label' => __('Use embed thumbnail if available', 'wpu_acf_flexible'),
                'type' => 'true_false'
            ),
            'cover_image' => array(
                'label' => __('Default cover image', 'wpu_acf_flexible'),
                'type' => 'image'
            )
        ),
        'field_vars_callback' => function ($id, $sub_field, $level) {
            return '$' . $id . ' = get_sub_field(\'' . $id . '\');' . "\n";
        },
        'field_html_callback' => function ($id, $sub_field, $level) {
            return '<?php
if($embed){
    echo get_wpu_acf_video_embed_image(array(
        \'video_field\' => $embed[\'embed\'],
        \'use_thumb\' => $embed[\'use_thumb\'],
        \'image_field\' => $embed[\'cover_image\']
    ));
}
?>' . "\n";
        }
    );
    return $types;
}, 10, 1);
