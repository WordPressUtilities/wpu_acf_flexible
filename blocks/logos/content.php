<?php
$_title = get_sub_field('title');
$_content = apply_filters('the_content', get_sub_field('content'));
$_logos = get_sub_field('logos');
if (empty($_logos)) {
    return;
}

$_logos = '';
while (has_sub_field('logos')) {
    $url = get_sub_field('url');
    $_logos .= '<li>';
    if ($url) {
        $_logos .= '<a class="field-url" href="' . $url . '">';
    }
    $_logos .= wp_get_attachment_image(get_sub_field('image'), 'medium');
    if ($url) {
        $_logos .= '</a>';
    }
    $_logos .= '</li>';
}

?><div class="centered-container cc-wpuacfflexible cc-block-logos cc-block-logos--<?php echo get_row_layout(); ?>">
    <div class="block--logos">
        <?php if ($_title): ?>
        <h2 class="field-title"><?php echo $_title; ?></h2>
        <?php endif;?>
        <?php if ($_content): ?>
        <div class="field-content">
            <?php echo $_content; ?>
        </div>
        <?php endif; ?>
        <ul class="logos-list">
            <?php echo $_logos; ?>
        </ul>
    </div>
</div>
