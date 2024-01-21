<?php
defined('ABSPATH') || die;
global $_post;

/* Obtain post ID */
$post_id = get_the_ID();
if (is_admin() && isset($_POST['pll_post_id']) && is_numeric($_POST['pll_post_id'])) {
    $post_id = intval($_POST['pll_post_id'], 10);
}
if (!$post_id) {
    return;
}

$_type = get_sub_field('type');
if (!$_type) {
    return;
}

/* Obtain query */
$q = array();
switch ($_type) {
case 'last_posts':
    $q = apply_filters('wpu_acf_flexible__content__posts__query_last_posts', array(
        'posts_per_page' => 5,
        'post_type' => 'post'
    ));
    break;
case 'child_posts':
    $q = apply_filters('wpu_acf_flexible__content__posts__query_child_posts', array(
        'post_type' => get_post_type($post_id),
        'posts_per_page' => -1,
        'post_parent' => $post_id
    ));
    break;
case 'manual_posts':
    $p = get_sub_field('p');
    $q = apply_filters('wpu_acf_flexible__content__posts__query_manual_posts', array(
        'posts_per_page' => -1,
        'post__in' => $p,
        'post_type' => 'any',
        'orderby' => 'post__in'
    ));
    break;
default:

}

if (empty($q)) {
    return '';
}

/* Get posts */
$posts = get_posts($q);
if (empty($posts)) {
    return;
}
global $post;


/* Template */
$_list_classname = apply_filters('wpu_acf_flexible__content__posts__list_classname', 'post-list');
$_content_before = apply_filters('wpu_acf_flexible__content__posts__before', '');
$_content_after = apply_filters('wpu_acf_flexible__content__posts__after', '');

ob_start();
$old_post = $post;
echo '<div class="' . $_list_classname . '">';
foreach ($posts as $_p) {
    $post = $_p;
    echo '<div class="item">';
    echo get_wpu_acf_loop();
    echo '</div>';
}
echo '</div>';
$post = $old_post;
$_posts = ob_get_clean();


?><div class="<?php echo get_wpu_acf_wrapper_classname('posts'); ?>">
    <?php echo $_content_before; ?>
    <div class="block--posts">
        <?php echo get_wpu_acf_title_content(); ?>
        <div class="block--posts__list">
            <?php echo $_posts; ?>
        </div>
    </div>
    <?php echo $_content_after; ?>
</div>
