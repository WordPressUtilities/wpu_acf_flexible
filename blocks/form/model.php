<?php

$model = array(
    'label' => __('[WPUACF] Form', 'wpu_acf_flexible'),
    'sub_fields' => apply_filters('wpu_acf_flexible__model__form__sub_fields', array())
);

if (!class_exists('wpucontactforms')) {
    $model = array();
}
