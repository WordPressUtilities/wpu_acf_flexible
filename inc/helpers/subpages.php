<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  Get current post ID (with admin preview fallback)
---------------------------------------------------------- */

function wpu_acf_flexible_subpages_get_post_id() {
    if (is_admin() && isset($_POST['pll_post_id']) && is_numeric($_POST['pll_post_id'])) {
        return intval($_POST['pll_post_id'], 10);
    }
    return get_the_ID();
}

/* ----------------------------------------------------------
  Get child pages of a parent
---------------------------------------------------------- */

function wpu_acf_flexible_get_child_pages($parent_id) {
    if (!$parent_id) {
        return array();
    }
    return get_posts(apply_filters('wpu_acf_flexible__content__subpages__query', array(
        'post_type' => get_post_type($parent_id),
        'posts_per_page' => -1,
        'post_parent' => $parent_id,
        'orderby' => array(
            'menu_order' => 'ASC',
            'title' => 'ASC'
        )
    ), $parent_id));
}

/* ----------------------------------------------------------
  Get sibling pages (current page included)
---------------------------------------------------------- */

function wpu_acf_flexible_get_sibling_pages($post_id) {
    return wpu_acf_flexible_get_child_pages(wp_get_post_parent_id($post_id));
}

/* ----------------------------------------------------------
  Admin preview notice when there is nothing to display
---------------------------------------------------------- */

function wpu_acf_flexible_subpages_empty_notice() {
    if (is_admin()) {
        echo '<p style="padding:1em">' . esc_html__('No pages to display.', 'wpu_acf_flexible') . '</p>';
    }
}
