/*
 *  Project: PIMCORE Toolbox
 *  Extension: Google Analytics Opt Out Link
 *  Version: 2.4
 *  Author: DACHCOM.DIGITAL
 *  License: GPL-3.0-or-later
*/
;(function ($, window, document) {
    'use strict';
    var clName = 'ToolboxGoogleOptOutLink';

    function ToolboxGoogleOptOutLink(element, options) {
        this.$element = $(element);
        this.options = $.extend(true, $.fn.toolboxVideo.defaults, options);
        this.translations = window['toolboxJsTranslations'] ? window['toolboxJsTranslations'] : {};
        this.init();
    }

    $.extend(ToolboxGoogleOptOutLink.prototype, {

        editMode: false,

        init: function () {

            var _ = this;
            this.editMode = this.options.editmode;

            if (this.editMode == true) {
                return;
            }

            if (_.readCookie('tb-google-opt-out-link') !== null) {
                window['ga-disable-' + _.readCookie('tb-google-opt-out-link')] = true;
                _.$element.addClass('disabled');
            }

            _.$element.on('click', function (ev) {
                ev.preventDefault();
                if (_.readCookie('tb-google-opt-out-link') !== null) {
                    alert(_.translations['toolbox.goptout_alreay_opt_out']);
                } else {
                    _.createCookie('tb-google-opt-out-link', $(this).attr('name'), 999);
                    alert(_.translations['toolbox.goptout_successfully_opt_out']);
                    $(this).addClass('disabled');
                }
            });
        },

        readCookie: function (name) {
            var nameEQ = name + '=';
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        },

        createCookie: function (name, value, days) {
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                var expires = '; expires=' + date.toGMTString();
            }
            else var expires = '';
            document.cookie = name + '=' + value + expires + '; path=/';
        }
    });

    $.fn.toolboxGoogleOptOutLink = function (options) {
        var els = [];
        this.each(function (i) {
            if (!$.data(this, 'tb-ext-google-opt-out-link')) {
                $.data(this, 'tb-ext-google-opt-out-link', new ToolboxGoogleOptOutLink(this, options));
            }
        });

        return this;
    };

    $.fn.toolboxGoogleOptOutLink.defaults = {
        editmode: false
    };

})
(jQuery, window, document);