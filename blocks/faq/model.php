<?php
$model = array(
    'label' => __('[WPUACF] FAQ', 'wpu_acf_flexible'),
    'sub_fields' => array(
        'title' => array(
            'label' => __('Title', 'wpu_acf_flexible')
        ),
        'questions' => array(
            'required' => 1,
            'min' => 1,
            'label' => __('Questions', 'wpu_acf_flexible'),
            'type' => 'repeater',
            'sub_fields' => array(
                'question' => array(
                    'label' => __('Question', 'wpu_acf_flexible'),
                    'required' => 1,
                    'type' => 'text'
                ),
                'answer' => array(
                    'label' => __('Answer', 'wpu_acf_flexible'),
                    'required' => 1,
                    'type' => 'wpuacf_minieditor'
                )
            )
        )
    )
);
