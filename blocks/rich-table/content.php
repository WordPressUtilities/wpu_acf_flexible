<?php
if (!get_sub_field('lines')) {
    return;
}

$_table_html = '';
while (has_sub_field('lines')) {
    if (!get_sub_field('columns')) {
        continue;
    }
    $_table_html .= '<tr class="columns-list">';
    while (has_sub_field('columns')):

        /* Get cell params */
        $type_champ = get_sub_field('cell_type');
        $tag = ($type_champ == 'heading' ? 'th' : 'td');
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
<div class="centered-container cc-block-rich-table cc-block-rich-table--<?php echo get_row_layout(); ?>">
    <div class="block-rich-table">
        <table class="block-rich-table--<?php echo get_row_layout(); ?>">
            <?php echo $_table_html; ?>
        </table>
    </div>
</div>
