<?php
$_title = get_sub_field('title');
$_content = apply_filters('the_content', get_sub_field('content'));
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
        $_url = wp_get_attachment_url($_file['file']);
        $_label = pathinfo($_url, PATHINFO_BASENAME);
        $_extension = pathinfo($_url, PATHINFO_EXTENSION);
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
        'download' => $_download
    );
}

?><div class="centered-container cc-wpuacfflexible cc-block-downloads cc-block-downloads--<?php echo get_row_layout(); ?>">
    <div class="block-downloads">
        <?php if ($_title): ?>
        <h2 class="field-title"><?php echo $_title; ?></h2>
        <?php endif;?>
        <?php if ($_content): ?>
        <div class="field-content">
            <?php echo $_content; ?>
        </div>
        <?php endif; ?>
        <ul class="files-list">
        <?php foreach ($files as $file): ?>
            <li>
                <a <?php echo ($_download ? 'data-ext="' . $_extension . '" download=""' : ''); ?> href="<?php echo $file['url']; ?>"><?php echo $file['label']; ?></a>
            </li>
        <?php endforeach;?>
        </ul>
    </div>
</div>
