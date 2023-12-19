document.addEventListener("DOMContentLoaded", function() {
    'use strict';

    /* Toggle visibility on layouts */
    (function() {
        jQuery('body').on('click', '.acf-icon[data-name="wpu-acf-flex-toggle"]', function(e) {
            e.preventDefault();
            var $icn = jQuery(this);
            $icn.closest('.layout[data-layout]').toggleClass('wpuacf-hidden-preview');
            $icn.toggleClass('-down').toggleClass('-up');
        });
    }());

    /* Scroll to */
    (function() {
        if (window.location.hash.substring(0, 12) != '#wpu-acf-row') {
            return;
        }
        setTimeout(function() {
            var _id = window.location.hash.substring(12);
            var $layout = document.querySelector('.layout[data-id="row-' + _id + '"]');
            if (!$layout) {
                return;
            }
            $layout.scrollIntoView();
        }, 1000);
    }());

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

    /* Create random field */
    (function() {
        if (typeof acf == "undefined") {
            return;
        }

        function _field_set_random_value(_field) {
            _field.val('hidden_' + Math.random().toString(36).slice(2, 12) + Date.now());
        }

        var _createActionsCallback = function(field) {
            if (!field.$el.hasClass('wpu-acf-flex-hidden-field')) {
                return;
            }
            var _tmpField = field.$el.find('input[name*="acf"]');
            if (!_tmpField.val()) {
                _field_set_random_value(_tmpField);
            }
        };

        /* Load value on existing empty fields */
        acf.addAction('load_field/type=text', _createActionsCallback);

        /* Set initial random value */
        acf.addAction('new_field', _createActionsCallback);

        /* On duplicate : randomize new value */
        acf.addAction('append', function($el) {
            if (!$el.hasClass('layout')) {
                return;
            }
            var $hiddenFields = $el.find('.wpu-acf-flex-hidden-field input[name*="acf"]');
            if (!$hiddenFields.length) {
                return;
            }
            $hiddenFields.each(function() {
                _field_set_random_value(jQuery(this));
            });
        });
    }());

});
