document.addEventListener("DOMContentLoaded", function() {
    'use strict';

    /* Force correct lang tab selection */
    (function() {
        var current_lang = window.wpuacfflex_current_admin_language;
        if (!current_lang) {
            return;
        }
        var $langs = document.querySelectorAll('.acf-tab-wrap a[data-key*="wpuacf_lang_tab_' + current_lang + '"]');
        for (var i = 0, len = $langs.length; i < len; i++) {
            $langs[i].click();
        }
    }());
});
