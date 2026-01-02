<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  Data
---------------------------------------------------------- */

$_post_types = apply_filters('wpu_acf_flexible_similar_post_types', array('post' => array()));
$_choices = array();
$_type_fields = array();
foreach ($_post_types as $_post_type => $_data) {
    $obj = get_post_type_object($_post_type);
    if (!$obj) {
        continue;
    }
    $_choices[$_post_type] = $obj->labels->name;
    $_type_fields['similar_' . $_post_type] = array(
        'label' => sprintf(__('Similar %s', 'wpu_acf_flexible'), $obj->labels->name),
        'type' => 'relationship',
        'post_type' => $_post_type,
        'return_format' => 'id',
        'max' => apply_filters('wpu_acf_flexible_similar_max_items', 16, $_post_type),
        'wpuacf_condition' => array(
            'similar_type' => $_post_type
        )
    );
}

/* ----------------------------------------------------------
  Model
---------------------------------------------------------- */

$model = array(
    'label' => __('[WPUACF] Similar', 'wpu_acf_flexible'),
    'key' => 'similar_layout',
    'no_save_post' => true,
    'sub_fields' => array()
);

$model['sub_fields']['title'] = 'wpuacf_title';
$model['sub_fields']['similar_type'] = array(
    'label' => __('Type', 'wpu_acf_flexible'),
    'type' => 'select',
    'default_value' => 'post',
    'choices' => $_choices
);
foreach ($_type_fields as $_field_id => $_field) {
    $model['sub_fields'][$_field_id] = $_field;
}

$model['sub_fields']['cta'] = 'wpuacf_cta';
$model['sub_fields']['use_default_values'] = array(
    'label' => __('Use the default CTA and the default title if no content', 'wpu_acf_flexible'),
    'type' => 'true_false',
    'default_value' => 1
);
