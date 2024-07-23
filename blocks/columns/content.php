<?php
defined('ABSPATH') || die;
$columns = get_sub_field('columns');
if (!$columns) {
    return;
}

$columns_list_classname = apply_filters('wpu_acf_flexible__content__columns__classname', 'acfflex-grid');
$image_size = apply_filters('wpu_acf_flexible__content__columns__image_size', 'large');
$cta_classname_main = apply_filters('wpu_acf_flexible__content__columns__cta_main_classname', 'cta-main');
$cta_classname_secondary = apply_filters('wpu_acf_flexible__content__columns__cta_secondary_classname', 'cta-secondary');

/* ----------------------------------------------------------
  Columns content
---------------------------------------------------------- */

$columns = array();

while (have_rows('columns')): the_row();

    $column = array(
        'surtitle' => '',
        'title' => '',
        'content' => '',
        'image' => '',
        'gallery' => '',
        'embed' => '',
        'cta' => ''
    );

    /* Content */
    $surtitle = get_sub_field('surtitle');
    $title = get_sub_field('title');
    $content = get_sub_field('content');
    if ($surtitle) {
        $column['surtitle'] = '<div class="columns-list__item__surtitle">' . strip_tags($surtitle) . '</div>';
    }
    if ($title) {
        $column['title'] = '<h2 class="columns-list__item__title">' . strip_tags($title) . '</h2>';
    }
    if ($content) {
        $column['content'] = '<div class="columns-list__item__content">' . get_wpu_acf_minieditor($content) . '</div>';
    }

    /* Image */
    $image = get_sub_field('image');
    $gallery = get_sub_field('gallery');
    if ($image) {
        $column['image'] = '<div class="columns-list__item__image">' . get_wpu_acf_image($image, $image_size) . '</div>';
    }
    if ($gallery) {
        $column['gallery'] .= '<div class="columns-list__item__gallery">';
        foreach ($gallery as $img):
            $column['gallery'] .= '<div class="img">' . get_wpu_acf_image($img['ID'], $image_size) . '</div>';
        endforeach;
        $column['gallery'] .= '</div>';
    }

    /* Embed */
    $embed = get_sub_field('embed');
    $column['embed'] = get_wpu_acf_video_embed_image(array(
        'video_field' => $embed['embed'],
        'use_thumb' => $embed['use_thumb'],
        'image_field' => $embed['cover_image']
    ));

    /* CTA */
    $cta_main = get_sub_field('cta_main');
    $cta_secondary = get_sub_field('cta_secondary');
    if ($cta_main || $cta_secondary) {
        $column['cta'] .= '<div class="columns-list__item__cta">';
        $column['cta'] .= get_wpu_acf_link($cta_main, $cta_classname_main);
        $column['cta'] .= get_wpu_acf_link($cta_secondary, $cta_classname_secondary);
        $column['cta'] .= '</div>';
    }

    $columns[] = apply_filters('wpu_acf_flexible__content__columns__column', $column, get_row_index());

endwhile;

/* ----------------------------------------------------------
  Content
---------------------------------------------------------- */

echo '<section class="centered-container cc-block--columns ' . envoidunet_theme(get_sub_field('envoidunet_theme')) . '"><div class="block--columns">';
echo '<ul class="columns-list__list ' . $columns_list_classname . '">';
foreach ($columns as $column_parts) {
    echo '<li>';
    echo '<div class="columns-list__item">';
    echo implode('', $column_parts);
    echo '</div>';
    echo '</li>';
}
echo '</ul>';
echo '</div></section>';
