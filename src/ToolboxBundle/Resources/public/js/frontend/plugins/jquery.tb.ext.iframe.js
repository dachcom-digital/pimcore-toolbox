/*
 *  Project: PIMCORE Toolbox
 *  Extension: Iframe
 *  Version: 2.3
 *  Author: DACHCOM.DIGITAL
 *  License: GPL-3.0-or-later
*/
;(function ($, window, document) {
    'use strict';
    var clName = 'ToolboxIframe';

    function ToolboxIframe(element, options) {
        this.$element = $(element);
        this.$iframe = this.$element.find('iframe').first();
        this.options = $.extend(true, $.fn.toolboxVideo.defaults, options);
        this.init();
    }

    $.extend(ToolboxIframe.prototype, {

        editMode: false,
        isReady: false,

        init: function () {

            var tbElement = null;
            this.editMode = this.options.editmode;

            if (this.$iframe.length === 0) {
                this.$element.addClass('iframe-invalid');
                return;
            }

            tbElement = this.$iframe.closest('.toolbox-iframe');
            tbElement.trigger('toolbox.iframe.load');

            this.$iframe.on('load', function () {
                tbElement.addClass('iframe-loaded');
                tbElement.trigger('toolbox.iframe.loaded');
            }.bind(this));
        }
    });

    $.fn.toolboxIframe = function (options) {
        var els = [];
        this.each(function (i) {
            if (!$.data(this, 'tb-ext-iframe-' + clName)) {
                $.data(this, 'tb-ext-iframe-' + clName, new ToolboxIframe(this, options));
                els.push(this);
            }
        });

        return this;
    };

    $.fn.toolboxIframe.defaults = {
        editmode: false
    };

})
(jQuery, window, document);