<?php
defined('ABSPATH') || die;

class wpu_acf_flexible__master_generator extends wpu_acf_flexible {

    private $is_debug = false;
    private $number_of_iterations = false;
    private $random_datas = array();
    private $only_layout = false;
    private $is_dry_run = false;
    private $option_id = 'wpu_acf_flexible_page_master';
    private $post_details = array(
        'post_type' => 'page',
        'post_title' => 'Page Master',
        'post_status' => 'draft'
    );

    public function __construct($args = array(), $assoc_args = array()) {
        parent::__construct();
        if (in_array('debug', $args)) {
            $this->is_debug = true;
        }
        if (in_array('dry-run', $args)) {
            $this->is_dry_run = true;
        }
        if (isset($assoc_args['only_layout'])) {
            $this->only_layout = $assoc_args['only_layout'];
        }
        if (isset($assoc_args['nb_iterations']) && is_numeric($assoc_args['nb_iterations'])) {
            $this->number_of_iterations = intval($assoc_args['nb_iterations'], 10);
        }
        parent::init();

        add_action('init', array(&$this, '_plugins_loaded'), 999);
    }

    public function _plugins_loaded() {
        $layout_id = apply_filters('wpu_acf_flexible__master_generator__layout_id', 'content-blocks');
        $layout_masterheader_id = apply_filters('wpu_acf_flexible__master_generator__layout_masterheader_id', 'master-header');
        $this->random_datas = $this->generate_random_datas();
        $this->post_details = apply_filters('wpu_acf_flexible__master_generator__post_details', $this->post_details);

        $layouts = apply_filters('wpu_acf_flexible_content', array());
        $layouts_details = $this->add_field_group($layout_id, $layouts[$layout_id]);
        $layouts_details_list = $layouts_details['fields'][0]['layouts'];
        $metas = array(
            '_' . $layout_id => $layouts_details['fields'][0]['key'],
            $layout_id => array()
        );

        if (isset($layouts[$layout_masterheader_id])) {
            $layout_master_header = $this->add_field_group($layout_masterheader_id, $layouts[$layout_masterheader_id]);
            $layout_master_header_parts = array_keys($layout_master_header['fields']);
            $metas = $this->get_layout_value($metas, $layout_master_header['fields'][$layout_master_header_parts[0]]['sub_fields'], $layout_master_header_parts[0]);
        }

        /* If only_layout mode is enabled : load 10 random versions of the same block */
        $number_of_iterations = 1;
        if ($this->only_layout) {
            if (!isset($layouts_details_list[$this->only_layout . '_layout']) && isset($layouts_details_list[$this->only_layout])) {
                $layouts_details_list[$this->only_layout . '_layout'] = $layouts_details_list[$this->only_layout];
            }

            if (!isset($layouts_details_list[$this->only_layout . '_layout'])) {
                $closest = $this->find_closest_layout_by_name($this->only_layout, $layouts_details_list);
                WP_CLI::confirm('- The layout "' . strip_tags($this->only_layout) . '" does not exist.' . "\n" . '- Do you mean "' . $closest . '" ?');
                $this->only_layout = $closest;
            }

            /* Default layouts */
            if (!isset($layouts_details_list[$this->only_layout . '_layout']) && isset($layouts_details_list[$this->only_layout])) {
                $layouts_details_list[$this->only_layout . '_layout'] = $layouts_details_list[$this->only_layout];
            }

            $layouts_details_list = array(
                $this->only_layout . '_layout' => $layouts_details_list[$this->only_layout . '_layout']
            );
            $number_of_iterations = 10;
        }

        if ($this->number_of_iterations) {
            $number_of_iterations = $this->number_of_iterations;
        }

        /* Shuffle layouts order */
        shuffle($layouts_details_list);

        /* Parse layout */
        $i = 0;
        for ($y = 0; $y < $number_of_iterations; $y++) {
            foreach ($layouts_details_list as $layout_key => $layout) {
                $metas[$layout_id][] = $layout['name'];
                $prefix = $layout_id . '_' . $i;
                $metas = $this->get_layout_value($metas, $layout['sub_fields'], $prefix);
                $i++;
            }
        }

        /* Display debug values */
        if ($this->is_debug) {
            echo '<pre>';
            var_dump($metas);
            echo '</pre>';
        }

        /* Stop if needed */
        if ($this->is_dry_run) {
            return;
        }

        /* Delete old page if needed */
        $option_value = get_option($this->option_id);
        if ($option_value) {
            wp_delete_post($option_value);
            delete_option($option_value);
        }

        $post_id = wp_insert_post($this->post_details);
        update_option($this->option_id, $post_id);
        foreach ($metas as $key => $value) {
            update_post_meta($post_id, $key, $value);
        }

        do_action('wpu_acf_flexible__master_generator__after_insert_post', $post_id);
    }

    function find_closest_layout_by_name($layout_name, $layouts) {
        $layouts = array_keys($layouts);
        $closest = '';
        $shortest = -1;
        foreach ($layouts as $word) {
            $word = str_replace('_layout', '', $word);
            $lev = levenshtein($layout_name, $word);
            if ($lev <= $shortest || $shortest < 0) {
                $closest = $word;
                $shortest = $lev;
            }
        }
        return $closest;
    }

    public function get_random_images($number = 5) {
        $images = get_posts(array(
            'post_type' => 'attachment',
            'post_mime_type' => 'image/jpeg',
            'fields' => 'ids',
            'cache_results' => false,
            'post_status' => 'inherit',
            'posts_per_page' => $number,
            'orderby' => 'rand'
        ));

        if (empty($images)) {
            echo "You should add some images";
            die;
        }

        return $images;
    }

    public function generate_random_datas() {
        $random_datas = array();

        /* Images */
        $random_datas['images'] = $this->get_random_images(5);

        /* Strings */
        $html_string = '<a href="#">a</a> text with <strong><em><span style="text-decoration:underline">HTML</span></em></strong> and an emoji 😬';
        $long_word = 'thiswordistoolongthiswordistoolongthiswordistoolongthiswordistoolongthiswordistoolongthiswordistoolongthiswordistoolongthiswordistoolongthiswordistoolongthiswordistoolongthiswordistoolongthiswordistoolongthiswordistoolongthiswordistoolongthiswordistoolongthiswordistoolongthiswordistoolongthiswordistoolong';
        $short_word = 'oh';

        /* Texts */
        $random_datas['texts'] = array(
            'a text',
            'a long text',
            $html_string,
            $long_word,
            $short_word,
            'a longer text, but not the longest',
            'a longer text : seriously, this should probably not happen, but just in case dont forget to have a look at me.'
        );

        /* Textareas */
        $random_datas['textareas'] = array(
            "lorem\nipsum\nfacto",
            $html_string,
            $long_word,
            $short_word,
            "We don’t get a chance to do that many things, and every one should be really excellent. Because this is our life. Life is brief, and then you die, you know? And we’ve all chosen to do this with our lives. So it better be damn good. It better be worth it.",
            "Your work is going to fill a large part of your life, and the only way to be truly satisfied is to do what you believe is great work. And the only way to do great work is to love what you do.\n\nIf you haven’t found it yet, keep looking. Don’t settle. As with all matters of the heart, you’ll know when you find it. And, like any great relationship, it just gets better and better as the years roll on. So keep looking until you find it. Don’t settle."
        );

        /* Link Labels */
        $random_datas['link_labels'] = array(
            'My Link',
            'Click here',
            $html_string,
            $long_word,
            $short_word,
            'A long label, which is rare but happens'
        );

        /* Videos */
        $random_datas['videos'] = array(
            'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'https://vimeo.com/109903713',
        );

        /* Editors */
        $random_datas['editors'] = array(
            '<h2>Liste de citations</h2> <h3>Various quotes</h3> <p>For me, I am driven by two main philosophies, know more today about the world than I knew yesterday. And along the way, lessen the suffering of others. You’d be surprised how far that gets you. <a href="/">Neil deGrasse Tyson</a>. History does not repeat itself, but it does rhyme. <em>Mark Twain</em>.</p> <p>Any man who can drive safely while kissing a pretty girl is simply not giving the kiss the attention it deserves. <em>A. Einstein.</em> <img src="https://via.placeholder.com/16x16" alt="" /> When I am abroad, I always make it a rule never to criticize or attack the government of my own country. I make up for lost time when I come home. <strong>Winston Churchill.</strong></p> <h4>Other cool quotes</h4> <p>A hundredth of a second here, a hundredth of a second there - even if you put them end to end, they still only add up to one, two, perhaps three seconds, snatched from eternity. <a href="#">Robert Doisneau.</a> All I know is what the words know, and dead things, and that makes a handsome little sum, with a beginning and a middle and an end, as in the well-built phrase and the long sonata of the dead. <strong>Samuel Beckett.</strong></p> <p>And this, our life, exempt from public haunt, finds tongues in trees, books in the running brooks, sermons in stones, and good in everything. <a href="#"><strong>William Shakespeare.</strong></a></p> <h3>Abraham Lincoln</h3> <ul> <li>A friend is one who has the same enemies as you have. </li> <li>Am I not destroying my enemies when I make friends of them? </li> <li>Any people anywhere, being inclined and having the power, have the right to rise up, and shake off the existing government, and form a new one that suits them better. <img src="https://via.placeholder.com/16x16" alt="" /> This is a most valuable - a most sacred right - a right, which we hope and believe, is to liberate the world. </li> <li>Be sure you put your feet in the right place, then stand firm. </li> </ul> <blockquote><p>The world needs dreamers and the world needs doers. But above all, the world needs dreamers who do — Sarah Ban Breathnach.</p></blockquote> <h3>Martin Luther King, Jr</h3> <ul> <li>A man can’t ride your back unless it’s bent.</li> <li>Take the first step in faith. <ul> <li>You don’t have to see the whole staircase :</li> <li>just take the first step.</li> </ul> </li> <li>A genuine leader is not a searcher for consensus but a molder of consensus.</li> </ul> <table> <thead> <tr> <th>Title 1</th> <th>Title 2</th> <th>Title 3</th> </tr> </thead> <tbody> <tr> <td>Data 1</td> <td>Data 2</td> <td>Data 3</td> </tr> <tr> <td>Data 3</td> <td>Data 4 </td> <td>Data 4</td> </tr> </tbody> </table> <ol><li>An item</li><li>Another item</li><li>This item too</li></ol> <p>' . $long_word . '</p>',
            '<p>A short quote. <strong>Strong</strong> words, <em>Emphasized</em> words. Very nice <a href="#">links</a></p>'
        );

        return apply_filters('wpu_acf_flexible__master_generator__random_datas', $random_datas);
    }

    public function get_layout_value($metas, $fields, $prefix, $layout_type = 'default') {

        $nb_repeats = 1;
        $base_prefix = $prefix;

        if ($layout_type == 'repeater') {
            if (!isset($fields['max']) || !$fields['max']) {
                $minmax = isset($fields['min']) && $fields['min'] ? $fields['min'] : 3;
                $fields['max'] = mt_rand($minmax, $minmax + 7);
            }
            if (!isset($fields['min']) || !$fields['min']) {
                $fields['min'] = mt_rand(0, $fields['max']);
            }
            $nb_repeats = mt_rand($fields['min'], $fields['max']);
            $fields = $fields['sub_fields'];
            $metas[$base_prefix] = $nb_repeats;
        }
        if ($layout_type == 'group') {
            $fields = $fields['sub_fields'];
        }

        for ($i = 0; $i < $nb_repeats; $i++) {
            foreach ($fields as $field_key => $field) {
                $base_field_key = $prefix . '_' . $field_key;
                if ($layout_type == 'repeater') {
                    $base_field_key = $prefix . '_' . $i . '_' . $field_key;
                }
                if ($layout_type == 'group') {
                    $base_field_key = $prefix . '_' . $field_key;
                }
                $metas = $this->get_field_value($metas, $field, $prefix, $base_field_key);
            }
        }
        return $metas;
    }

    public function get_field_value($metas, $field, $prefix, $base_field_key) {

        if ($field['type'] == 'taxonomy' || $field['type'] == 'relationship') {
            $field['max'] = isset($field['max']) && $field['max'] ? $field['max'] : mt_rand(3, 10);
        }

        switch ($field['type']) {
        case 'select':
            $choices = $field['choices'];
            if (isset($choices['empty']) && $field['name'] == 'cell_type' && count($choices) > 1) {
                unset($choices['empty']);
            }
            $metas[$base_field_key] = array_rand($choices);
            break;
        case 'relationship':
            $posts = get_posts(array(
                'post_type' => $field['post_type'],
                'fields' => 'ids',
                'posts_per_page' => $field['max']
            ));
            if (empty($posts)) {
                break;
            }
            $metas[$base_field_key] = $posts;

            break;
        case 'taxonomy':
            $args = array(
                'taxonomy' => $field['taxonomy'],
                'hide_empty' => true,
                'fields' => 'ids',
                'number' => $field['max'],
                'orderby' => 'rand'
            );
            $terms = get_terms($args);
            if (empty($terms)) {
                $args['hide_empty'] = false;
                $terms = get_terms($args);
                if (empty($terms)) {
                    break;
                }
            }
            $metas[$base_field_key] = $terms;
            if (isset($field['field_type']) && $field['field_type'] == 'select') {
                $metas[$base_field_key] = $terms[0];
            }

            break;
        case 'group':
            $metas = $this->get_layout_value($metas, $field, $base_field_key, 'group');
            break;
        case 'repeater':
            $metas = $this->get_layout_value($metas, $field, $base_field_key, 'repeater');
            break;
        case 'oembed':
            $metas[$base_field_key] = $this->get_random_value($this->random_datas['videos']);
            break;
        case 'url':
            $metas[$base_field_key] = site_url();
            break;
        case 'text':
            $metas[$base_field_key] = $this->get_random_value($this->random_datas['texts']);
            break;
        case 'textarea':
            $metas[$base_field_key] = $this->get_random_value($this->random_datas['textareas']);
            break;
        case 'wysiwyg':
        case 'editor':
            $metas[$base_field_key] = $this->get_random_value($this->random_datas['editors']);
            break;
        case 'gallery':
            $metas[$base_field_key] = $this->get_random_images(mt_rand(1, 10));
            break;
        case 'file':
        case 'image':
            $metas[$base_field_key] = $this->get_random_value($this->random_datas['images']);
            break;
        case 'number':
            $field['min'] = isset($field['min']) && $field['min'] ? $field['min'] : 5;
            $field['max'] = isset($field['max']) && $field['max'] ? $field['max'] : 600;
            $metas[$base_field_key] = mt_rand($field['min'], $field['max']);
            break;
        case 'link':
            $metas[$base_field_key] = array(
                'title' => $this->get_random_value($this->random_datas['link_labels']),
                'url' => site_url(),
                'target' => ""
            );
            break;
        case 'tab':
        case 'acfe_column':
            break;
        case 'true_false':
            $metas[$base_field_key] = !!mt_rand(0, 1);
            break;
        default:
            //echo '<pre>';
            //var_dump($field['type']);
            //echo '</pre>';
        }

        /* 1 in 5 chances to set an empty field if not required  */
        if (!isset($field['required']) || !$field['required']) {
            if (!mt_rand(0, 5)) {
                $metas[$base_field_key] = '';
            }
        }

        if (isset($field['maxlength']) && $field['maxlength'] > 0) {
            $metas[$base_field_key] = substr($metas[$base_field_key], 0, $field['maxlength']);
        }

        if (isset($metas[$base_field_key])) {
            $metas[$base_field_key] = apply_filters('wpu_acf_flexible__master_generator__meta_item', $metas[$base_field_key], $metas, $field);
        }

        return $metas;
    }

    public function get_random_value($array = array()) {
        return $array[array_rand($array)];
    }
}

if (defined('WP_CLI')) {
    WP_CLI::add_command('wpu-acf-flex-master-generator', function ($args, $assoc_args) {
        add_action('wpu_acf_flexible__master_generator__after_insert_post', function ($post_id) {
            WP_CLI::success('Page Master');
            WP_CLI::success(get_page_link($post_id));
        });
        $wpu_acf_flexible__master_generator = new wpu_acf_flexible__master_generator($args, $assoc_args);
        $wpu_acf_flexible__master_generator->_plugins_loaded();
    }, array(
        'shortdesc' => 'Generate a page with all blocks, filled with random data.'
    ));
}
