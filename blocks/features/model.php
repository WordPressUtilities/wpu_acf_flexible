<?php

$model = array(
    'label' => '[WPUACF] Features',
    'sub_fields' => array(
        'title' => array(
            'label' => 'Titre'
        ),
        'content' => array(
            'label' => 'Contenu',
            'type' => 'textarea',
            'rows' => 2,
        ),
        'features' => array(
            'label' => 'Features',
            'type' => 'repeater',
            'required' => 1,
            'min' => 1,
            'max' => 3,
            'sub_fields' => array(
                'image' => array(
                    'label' => 'Image',
                    'type' => 'image'
                ),
                'title' => array(
                    'label' => 'Titre'
                ),
                'content' => array(
                    'label' => 'Contenu',
                    'type' => 'textarea',
                    'rows' => 2,
                ),
                'link' => array(
                    'label' => 'URL Bouton',
                    'type' => 'link'
                )
            )
        )
    )
);
