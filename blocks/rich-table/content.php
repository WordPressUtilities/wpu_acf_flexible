<?php
if (!get_sub_field('lines')) {
    return;
}

$_title = get_sub_field('title');
$_content = apply_filters('the_content', get_sub_field('content'));

$_table_html = '';
while (has_sub_field('lines')) {
    if (!get_sub_field('columns')) {
        continue;
    }
    $_table_html .= '<tr class="columns-list">';
    while (has_sub_field('columns')):

        /* Get cell params */
        $cell_type = get_sub_field('cell_type');
        $tag = ($cell_type == 'heading' ? 'th' : 'td');
        $image_src = get_wpu_acf_image_src(get_sub_field('image'), 'thumbnail');
        $nb_cols = get_sub_field('nb_cols');
        $nb_rows = get_sub_field('nb_rows');

        /* Set cell attributes */
        $cell_attrs = '';
        if (is_numeric($nb_cols) && $nb_cols > 1) {
            $cell_attrs .= ' colspan="' . $nb_cols . '"';
        }
        if (is_numeric($nb_rows) && $nb_rows > 1) {
            $cell_attrs .= ' rowspan="' . $nb_rows . '"';
        }
        if($cell_type == 'empty'){
            $cell_attrs .= ' data-empty="1"';
        }

        /* Set cell content */
        $_table_html .= '<' . $tag . ' ' . $cell_attrs . '>';
        $_table_html .= apply_filters('the_content', get_sub_field('text'));
        if ($image_src):
            $_table_html .= '<img class="cell-image" src="' . $image_src . '" alt="" />';
        endif;
        $_table_html .= '</' . $tag . '>';
    endwhile;
    $_table_html .= '</tr>';
}
if (!$_table_html) {
    return;
}
?>
<div class="centered-container cc-wpuacfflexible cc-block-rich-table cc-block-rich-table--<?php echo get_row_layout(); ?>">
    <div class="block-rich-table">
        <?php if ($_title): ?>
            <h2 class="field-title">
                <?php echo $_title; ?>
            </h2>
        <?php endif;?>
        <?php if($_content): ?>
        <div class="field-content">
            <?php echo $_content; ?>
        </div>
        <?php endif; ?>
        <table class="block-rich-table--<?php echo get_row_layout(); ?>">
            <?php echo $_table_html; ?>
        </table>
    </div>
</div>
