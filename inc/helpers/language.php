<?php
defined('ABSPATH') || die;

/* ----------------------------------------------------------
  Multilingual
---------------------------------------------------------- */

function wpuacfflex_get_languages() {
    global $polylang;
    if (function_exists('pll_the_languages') && is_object($polylang)) {
        $poly_langs = $polylang->model->get_languages_list();
        $languages = array();
        foreach ($poly_langs as $lang) {
            $languages[$lang->slug] = array(
                'name' => $lang->name,
                'flag' => $lang->flag
            );
        }
        return $languages;
    }

    return array();
}

function wpuacfflex_get_current_language() {
    global $polylang;
    if (function_exists('pll_current_language')) {
        return pll_current_language();
    }
    return false;
}

function wpuacfflex_get_current_admin_language() {
    global $polylang;
    $current_language = false;

    // Obtaining from Polylang
    if (function_exists('pll_the_languages') && is_object($polylang) && $polylang->pref_lang) {
        $current_language_tmp = $polylang->pref_lang->slug;
        if ($current_language_tmp != 'all') {
            $current_language = $current_language_tmp;
        }
    }

    return $current_language;
}

function wpuacfflex_lang_get_field($selector, $post_id = false, $format_value = true) {
    $lang = wpuacfflex_get_languages();
    $current_lang = wpuacfflex_get_current_language();
    $base_field = get_field($selector, $post_id, $format_value);
    if (!$lang || !is_array($lang) || !$current_lang || !is_array($base_field) || !isset($base_field['val_' . $current_lang])) {
        return $base_field;
    }
    return $base_field['val_' . $current_lang];
}

/* ----------------------------------------------------------
  Multilingual fields
---------------------------------------------------------- */

/* Handle multiple lang for each field
-------------------------- */

function wpuacfflex_i18n_get_field_group($fields) {
    if (!function_exists('pll_languages_list')) {
        return $fields;
    }
    $poly_langs = pll_languages_list();
    $new_fields = array();
    foreach ($poly_langs as $code) {
        $new_fields['wpuacfflex_i18n_tab___' . $code] = array(
            'label' => strtoupper($code),
            'type' => 'tab'
        );
        foreach ($fields as $field_id => $field) {
            $new_fields[$field_id . '___' . $code] = $field;
        }
    }
    return $new_fields;
}

/* Return current value  for a field
-------------------------- */

function wpuacfflex_i18n_get_field($selector, $post_id = false, $format_value = true, $escape_html = false) {
    $lang_id = '';
    if (function_exists('pll_current_language')) {
        $selector .= '___' . pll_current_language('slug');
    }
    return get_field($selector, $post_id, $format_value, $escape_html);
}
