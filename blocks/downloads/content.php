<?php
defined('ABSPATH') || die;
$_files = get_sub_field('files');
if (empty($_files)) {
    return;
}

/* ----------------------------------------------------------
  Settings
---------------------------------------------------------- */

$_display_filesize = apply_filters('wpu_acf_flexible__content__downloads__display_filesize', false);
$_content_before = apply_filters('wpu_acf_flexible__content__downloads__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__downloads__after', '');
$_button_classname = apply_filters('wpu_acf_flexible__content__downloads__button_classname', '');

$files = wpuacfflex_downloads_parse_files($_files);

/* ----------------------------------------------------------
  Content
---------------------------------------------------------- */

echo '<div class="' . get_wpu_acf_wrapper_classname('downloads') . '">';
echo $_content_before;
echo '<div class="block-downloads block--downloads">' . get_wpu_acf_title_content();
echo '<ul class="files-list">';
foreach ($files as $file):
    $link_attr = '';
    if ($file['download']) {
        $link_attr .= ' data-ext="' . $file['extension'] . '" download=""';
    }
    if ($file['target']) {
        $link_attr .= ' target="' . $file['target'] . '"';
    }
    echo '<li><div class="files-list__item">';
    echo get_wpu_acf_link($file, $_button_classname, $link_attr);
    if ($_display_filesize && $file['size']) {
        echo '<div class="file-size">' . $file['size'] . '</div>';
    }
    echo '</div></li>';
endforeach;
echo '</ul>';
echo '</div>';
echo $_content_after;
echo '</div>';
