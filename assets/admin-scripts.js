document.addEventListener("DOMContentLoaded", function() {
    'use strict';

    jQuery(document).on('click', 'a.thickbox-wpuacf-icon', function(e) {
        e.preventDefault();

        // call thickbox manually
        tb_show(this.title, '#TB_inline?height=500&width=780&inlineId=wpu_acf_flex_icon_list');

        // Add a custom CSS class
        setTimeout(function() {
            document.getElementById("TB_window").classList.add("wpu-acf-thickbox-icons");
        }, 100);
    });


    /* Toggle visibility on layouts */
    (function() {
        jQuery('body').on('click', '.acf-icon[data-name="wpu-acf-flex-toggle"]', function(e) {
            e.preventDefault();
            var $icn = jQuery(this);
            $icn.closest('.layout[data-layout]').toggleClass('wpuacf-hidden-preview');
            $icn.toggleClass('-down').toggleClass('-up');
        });
    }());

    (function() {
        /* Reduce all layouts */
        jQuery('body').on('mousedown touchstart', '[data-acfe-flexible-control-action="wpu-acf-flex-reduce"]', function(e) {
            e.preventDefault();
            jQuery('.acf-icon[data-name="wpu-acf-flex-toggle"].-down').click();
        });
        /* Expand all layouts */
        jQuery('body').on('mousedown touchstart', '[data-acfe-flexible-control-action="wpu-acf-flex-expand"]', function(e) {
            e.preventDefault();
            jQuery('.acf-icon[data-name="wpu-acf-flex-toggle"].-up').click();
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

    /* Scroll to */
    (function() {
        if (window.location.hash.substring(0, 12) != '#wpu-acf-add') {
            return;
        }
        setTimeout(function() {
            var $layout = document.querySelector('.acf-actions [data-name="add-layout"]');
            if (!$layout) {
                return;
            }
            $layout.scrollIntoView();
            jQuery('.acf-actions [data-name="add-layout"]').click();
        }, 500);
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

    /* Colors */
    (function() {
        if (typeof acf == "undefined") {
            return;
        }
        acf.add_filter('color_picker_args', function(args) {
            if (wpu_acf_flexible_script_wpuacfadmin.color_picker_palettes.length) {
                args.palettes = wpu_acf_flexible_script_wpuacfadmin.color_picker_palettes;
            }
            return args;
        });
    }());
});


/* exported wpuacf_load_mapbox */
function wpuacf_load_mapbox(_token) {
    var $wrapper = jQuery('#wpwrap');
    if (!$wrapper.length) {
        return;
    }

    function wpuacf_setup_input() {
        var jq_the_input = jQuery(this),
            the_input = jq_the_input.get(0);

        if (jq_the_input.closest('.acf-clone').length) {
            return;
        }

        /* Only once */
        if (jq_the_input.attr('data-has-wpuacf-autocomplete-address')) {
            return;
        }
        jq_the_input.attr('data-has-wpuacf-autocomplete-address', 1);

        /* Build autocomplete */
        var autofillElement = new mapboxsearch.MapboxAddressAutofill(),
            the_form = the_input.parentElement,
            the_wrapper = the_input.closest('[data-type="group"]')
        autofillElement.accessToken = _token;
        autofillElement.options = {}
        autofillElement.appendChild(the_input);
        the_form.appendChild(autofillElement);

        autofillElement.addEventListener('retrieve', function(event) {
            /* When an address is selected : fill lat/lng fields */
            the_wrapper.querySelector('input[name*="lat"]').value = event.detail.features[0].geometry.coordinates[1];
            the_wrapper.querySelector('input[name*="lng"]').value = event.detail.features[0].geometry.coordinates[0];
            /* Save full address */
            setTimeout(function() {
                the_input.value = event.detail.features[0].properties.full_address;
            }, 100);
        });
    }

    var _input_query = '.values input[name*="wpuacf_autocomplete_address"]';

    $wrapper.on('focus', _input_query, wpuacf_setup_input);
    $wrapper.find(_input_query).each(function() {
        wpuacf_setup_input.call(this);
    });
}
