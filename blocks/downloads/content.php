<?php
$_files = get_sub_field('files');
if (empty($_files)) {
    return;
}

$files = array();
foreach ($_files as $_file) {
    $_url = false;
    $_label = 'link';
    $_download = false;
    $_extension = false;

    /* Get URL */
    if (!empty($_file['url'])) {
        $_url = $_file['url'];
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
        'label' => $_label,
        'download' => $_download,
        'extension' => $_extension
    );
}

?><div class="<?php echo get_wpu_acf_wrapper_classname('downloads'); ?>">
    <div class="block-downloads">
        <?php echo get_wpu_acf_title_content(); ?>
        <ul class="files-list">
        <?php foreach ($files as $file): ?>
            <li>
                <a class="acfflex-link" <?php echo ($file['download'] ? 'data-ext="' . $file['extension'] . '" download=""' : ''); ?> href="<?php echo $file['url']; ?>"><span><?php echo $file['label']; ?></span></a>
            </li>
        <?php endforeach;?>
        </ul>
    </div>
</div>
