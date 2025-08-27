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
                'type' => 'true_false',
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

/* ----------------------------------------------------------
  Helpers
---------------------------------------------------------- */

/* Get video+embed image
-------------------------- */

function get_wpu_acf_video_embed_image($args = array()) {
    if (!is_array($args)) {
        $args = array();
    }

    $args = array_merge(array(
        'video_field_id' => 'video',
        'image_field_id' => 'image',
        'noimg_force_autoplay' => false,
        'use_thumb_id' => 'use_thumb',
        'only_embed' => false,
        'only_image' => false
    ), $args);

    /* Video */
    if (isset($args['video_field'])) {
        $_video = $args['video_field'];
    } else {
        $_video = get_sub_field($args['video_field_id']);
    }

    /* Image */
    if (isset($args['image_field']) && $args['image_field']) {
        $_image_id = $args['image_field'];
    } else {
        $_image_id = get_sub_field($args['image_field_id']);
    }

    /* Thumb */
    if (isset($args['use_thumb'])) {
        $_image_use_embed = $args['use_thumb'];
    } else {
        $_image_use_embed = get_sub_field($args['use_thumb_id']);
    }

    if (!$_video) {
        return false;
    }

    /* Thumb */
    $_image_embed = '';
    if ($_image_use_embed || ($args['only_image'] && !$_image_id)) {
        $_image_embed = get_wpu_acf_embed_image($_video);
    }

    /* Video shortcode detected */
    if (strpos($_video, '[video') !== false) {
        $_video = str_replace('[video', '<video controls autoplay', $_video);
        $_video = str_replace('/]', '></video>', $_video);
    }

    global $content_width;
    $iframe_width = 560;
    if (isset($content_width) && is_numeric($content_width)) {
        $iframe_width = $content_width;
    }
    $iframe_height = floor($iframe_width * 0.5625);
    /* Detect a video URL */
    if (filter_var($_video, FILTER_VALIDATE_URL) !== false) {
        $_video = '<iframe allowfullscreen allow="autoplay" width="' . $iframe_width . '" height="' . $iframe_height . '" src="' . strip_tags($_video) . '"></iframe>';
    }

    /* Do not embed if not an iframe or a video tag */
    if (strpos($_video, '<iframe') === false && strpos($_video, '<video') === false) {
        return false;
    }

    $_video = '<div class="content-video">' . $_video . '</div>';
    if (apply_filters('wpu_acf_flexible__video__nocookie', true) || is_admin()) {
        $_video = str_replace('youtube.com/embed', 'youtube-nocookie.com/embed', $_video);
    }

    $_image = '';
    $_image_item = '';
    $_image_size = apply_filters('wpu_acf_flexible__content__video__image_size', 'large');
    if ($_image_id || $_image_embed) {
        if ($_image_id) {
            $_image_item = get_wpu_acf_image($_image_id, $_image_size);
        }
        if ($_image_embed) {
            $_image_item = '<img src="' . $_image_embed . '" alt="" loading="lazy" />';
        }
    }

    if ($args['only_image']) {
        return $_image_item;
    }

    if (!is_admin()) {
        if ($_image_id || $_image_embed || $args['noimg_force_autoplay']) {
            $_video = str_replace('app_id=', 'autoplay=1&app_id=', $_video);
            $_video = str_replace('feature=oembed', 'feature=oembed&autoplay=1', $_video);
        }
        if (!$args['only_embed'] && ($_image_id || $_image_embed)) {
            $_video = str_replace('src=', 'data-src=', $_video);
            $_image = '<div class="wpuacf-video"><div class="cursor"></div><div class="cover-image">' . $_image_item . '</div>' . $_video . '</div>';
        } else {
            $_image = $_video;
        }
    } else {
        $_video = str_replace('controls autoplay', 'controls', $_video);
        $_video = str_replace('autoplay=1', '', $_video);
        $_image = $_video;
    }
    return $_image;
}

/* Get embed image
-------------------------- */

function get_wpu_acf_embed_image($embed_url) {

    if (strpos($embed_url, '<iframe') !== false) {
        preg_match('/src="([^"]+)"/', $embed_url, $matches);
        if (isset($matches[1]) && $matches[1]) {
            $embed_url = $matches[1];
        } else {
            return '';
        }
    }

    $embed_url = apply_filters('get_wpu_acf_embed_image__embed_url', $embed_url);

    /* Extract youtube : thx https://stackoverflow.com/a/64320469 */
    if (strpos($embed_url, 'youtu') !== false) {
        $youtube_id = get_wpu_acf_youtube_id_from_url($embed_url);
        $cache_key = 'youtube_thumb_' . md5($youtube_id);
        return wpuacfflex_get_file_cache($cache_key, YEAR_IN_SECONDS, function ($youtube_id) {
            $maxres = 'https://img.youtube.com/vi/' . $youtube_id . '/maxresdefault.jpg';
            $hq = 'https://img.youtube.com/vi/' . $youtube_id . '/hqdefault.jpg';
            $response = wp_remote_head($maxres);
            if (is_wp_error($response) || wp_remote_retrieve_response_code($response) == 404) {
                return $hq;
            }
            return $maxres;
        }, $youtube_id);
    }
    if (strpos($embed_url, 'vimeo') !== false) {
        $cache_key = 'vimeo_thumb_' . md5($embed_url);
        $vimeo_id = get_wpu_acf_vimeo_id_from_url($embed_url);
        return wpuacfflex_get_file_cache($cache_key, YEAR_IN_SECONDS, function ($vimeo_id) {
            $response = wp_remote_get('https://vimeo.com/api/v2/video/' . $vimeo_id . '.json');
            if (is_wp_error($response)) {
                return '';
            }
            $json = json_decode(wp_remote_retrieve_body($response));
            if (!isset($json[0]->thumbnail_large)) {
                return '';
            }
            return $json[0]->thumbnail_large;
        }, $vimeo_id);
    }

    return '';
}

/* Extract Vimeo ID from URL
-------------------------- */

function get_wpu_acf_vimeo_id_from_url($url) {
    $url_parts = explode('/', str_replace(array('&', '?', '#'), '/', $url));
    foreach ($url_parts as $part) {
        if (is_numeric($part)) {
            return $part;
        }
    }

    return false;
}

/* Extract YouTube ID from URL
-------------------------- */

function get_wpu_acf_youtube_id_from_url($embed_url) {
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $embed_url, $match);
    if (isset($match[1]) && $match[1]) {
        return $match[1];
    }
    return false;
}
