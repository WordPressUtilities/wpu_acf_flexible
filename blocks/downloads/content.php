<?php
$_files = get_sub_field('files');
if (empty($_files)) {
    return;
}

$files = array();
foreach ($_files as $_file) {
    $_url = false;
    $_label = 'link';
    $_target = '';
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
        'target' => '',
        'title' => $_label,
        'download' => $_download,
        'extension' => $_extension
    );
}
$_content_before = apply_filters('wpu_acf_flexible__content__downloads__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__downloads__after', '');
$_button_classname = apply_filters('wpu_acf_flexible__content__downloads__button_classname', '');
?><div class="<?php echo get_wpu_acf_wrapper_classname('downloads'); ?>">
    <?php echo $_content_before; ?>
    <div class="block-downloads">
        <?php
        echo get_wpu_acf_title_content();
        echo '<ul class="files-list">';
        foreach ($files as $file):
        echo '<li>';
        echo get_wpu_acf_link($file, $_button_classname, ($file['download'] ? 'data-ext="' . $file['extension'] . '" download=""' : ''));
        echo '</li>';
        endforeach;
        echo '</ul>';
        ?>
    </div>
    <?php echo $_content_after; ?>
</div>
