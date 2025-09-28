<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  Get default query for similar posts
---------------------------------------------------------- */

function wpu_acf_flexible_similar_get_default_values($type = 'post') {

    $types = apply_filters('wpu_acf_flexible_similar_post_types', array('post' => array()));

    if (!isset($types[$type])) {
        return false;
    }

    $return = array_merge(array(
        'title' => __('Similar posts', 'wpu_acf_flexible'),
        'button_url' => home_url(),
        'button_title' => __('Home', 'wpu_acf_flexible'),
        'similar_tax' => 'category',
        'posts_per_page' => 3
    ), $types[$type]);

    $query = array(
        'post_type' => $type,
        'posts_per_page' => $return['posts_per_page'],
    );

    if (is_singular($type) && class_exists('WPUSimilar')) {
        global $WPUSimilar;
        $query['orderby'] = 'post__in';
        $query['post__in'] = $WPUSimilar->get_similar(get_the_ID(), $type, $return['similar_tax'], array(
            'return_ids' => true,
            'max_number' => $query['posts_per_page']
        ));
        unset($query['posts_per_page']);
    }

    $return['query'] = $query;
    $return['button'] = array(
        'url' => $return['button_url'],
        'title' => $return['button_title']
    );

    return $return;

}

/* ----------------------------------------------------------
  Get title and CTA for similar posts
---------------------------------------------------------- */

function wpu_acf_flexible_similar_get_title_and_cta($type = 'post') {
    $default_values = wpu_acf_flexible_similar_get_default_values($type);
    if (!$default_values) {
        return array(
            'title' => '',
            'button' => ''
        );
    }
    $use_default = get_sub_field('use_default_values');
    $title = get_sub_field('title');
    $button = get_sub_field('cta');
    return array(
        'title' => $use_default && !$title ? $default_values['title'] : $title,
        'button' => $use_default && !$button ? $default_values['button'] : $button
    );
}

/* ----------------------------------------------------------
  Get similar posts
---------------------------------------------------------- */

function wpu_acf_flexible_similar_get_items($type, $post_ids = array()) {
    $default_values = wpu_acf_flexible_similar_get_default_values($type);
    if (!$default_values) {
        return array();
    }
    $similar_q = $default_values['query'];
    if ($post_ids) {
        $similar_q = array(
            'post_type' => $type,
            'post__in' => $post_ids,
            'orderby' => 'post__in'
        );
    }
    return get_posts($similar_q);
}
