# WPU ACF Flexible

Quickly generate flexible content in ACF

## Add Flexible Content

### Display in frontend

```php
echo get_wpu_acf_flexible_content('home-blocks');
```

### Add admin

```php
add_filter('wpu_acf_flexible_content', 'example_wpu_acf_flexible_content', 10, 1);
function example_wpu_acf_flexible_content($contents) {
    $contents['home-blocks'] = array(
        /* Save HTML content in post_content */
        'save_post' => 1,
        /* Create initial layout files */
        'init_files' => 1,
        /* Target post types */
        'post_types' => array('post','page'),
        /* Target page templates */
        # 'page_templates' => array('page-template-flexible.php'),
        /* Target post ids */
        # 'page_ids' => array(1234),
        /* Global Conf */
        'name' => 'Blocks',
        'layouts' => array(
            'basique' => array(
                'label' => 'Basique',
                'sub_fields' => array(
                    'title' => array(
                        'label' => 'Titre'
                    ),
                    'content' => array(
                        'label' => 'Contenu',
                        'type' => 'textarea'
                    ),
                    'link' => array(
                        'label' => 'URL Bouton',
                        'type' => 'link'
                    )
                )
            ),
            'icons' => array(
                'label' => 'Icones',
                'sub_fields' => array(
                    'title' => array(
                        'label' => 'Titre'
                    ),
                    'icons' => array(
                        'label' => 'Icones',
                        'type' => 'repeater',
                        'sub_fields' => array(
                            'icons_title' => array(
                                'label' => 'Titre'
                            ),
                            'icons_image' => array(
                                'label' => 'Image',
                                'type' => 'image'
                            )
                        )
                    )
                )
            )
            /* Use native model with a rich table */
            'table_rich' => array(
                'wpuacf_model' => 'rich-table'
            )
        )
    );
    return $contents;
}
```


## Todo

- [ ] Add French translation.
- [ ] Remove Twig/Timber compatibility.
