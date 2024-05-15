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

$files = array();
foreach ($_files as $_file) {
    $_url = false;
    $_label = 'link';
    $_target = '';
    $_size = '';
    $_download = false;
    $_extension = false;

    /* Get URL */
    if (!empty($_file['url'])) {
        $_url = $_file['url'];
        $_target = '_blank';
    }
    if (is_numeric($_file['file'])) {
        $_download = true;
        $_url = wp_get_attachment_url($_file['file']);
        $_label = pathinfo($_url, PATHINFO_BASENAME);
        $_extension = strtolower(pathinfo($_url, PATHINFO_EXTENSION));
        $file_obj = get_attached_file($_file['file']);
        if (file_exists($file_obj)) {
            $_size = wpuacfflex_human_filesize(filesize($file_obj), 1);
        }
    }
    if (!$_url) {
        return false;
    }

    /* Get label */
    if (!empty($_file['filename'])) {
        $_label = $_file['filename'];
    }

    $files[] = array(
        'url' => $_url,
        'size' => $_size,
        'target' => '',
        'title' => $_label,
        'download' => $_download,
        'extension' => $_extension
    );
}

/* ----------------------------------------------------------
  Content
---------------------------------------------------------- */

echo '<div class="' . get_wpu_acf_wrapper_classname('downloads') . '">';
echo $_content_before;
echo '<div class="block-downloads block--downloads">' . get_wpu_acf_title_content();
echo '<ul class="files-list">';
foreach ($files as $file):
    echo '<li><div class="files-list__item">';
    echo get_wpu_acf_link($file, $_button_classname, ($file['download'] ? 'data-ext="' . $file['extension'] . '" download=""' : ''));
    if ($_display_filesize && $file['size']) {
        echo '<div class="file-size">' . $file['size'] . '</div>';
    }
    echo '</div></li>';
endforeach;
echo '</ul>';
echo '</div>';
echo $_content_after;
echo '</div>';
