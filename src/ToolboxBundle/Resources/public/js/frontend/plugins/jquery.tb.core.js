/*
 *  Project: PIMCORE Toolbox
 *  Extension: Core
 *  Version: 2.3
 *  Author: DACHCOM.DIGITAL
 *  License: GPL-3.0-or-later
*/
;(function ($) {
    'use strict';
    $.extend({
        toolboxCore: function (options) {
            var defaults = {
                    editmode: false,
                    theme: 'bootstrap3',
                    googleMapsHandler: {
                        enabled: true,
                        selector: '.toolbox-googlemap',
                        config: {}
                    },
                    videoHandler: {
                        enabled: true,
                        selector: '.toolbox-video',
                        config: {}
                    },
                    iframeHandler: {
                        enabled: true,
                        selector: '.toolbox-iframe',
                        config: {}
                    },
                    gOptOutLinkHandler: {
                        enabled: true,
                        selector: 'a.google-opt-out-link',
                        config: {}
                    }
                },
                _ = this,
                options = options || {};

            _.init = function () {
                this.settings = $.extend(true, defaults, options);
                $.data(document, 'toolboxCore', this.settings);
                this.initSystem();
            };

            _.initSystem = function () {
                if (this.settings.googleMapsHandler.enabled === true) {
                    var settings = this.settings.googleMapsHandler.config;
                    settings.editmode = this.settings.editmode;
                    settings.theme = this.settings.theme;
                    $(this.settings.googleMapsHandler.selector).toolboxGoogleMaps(settings);
                }

                if (this.settings.videoHandler.enabled === true) {
                    var settings = this.settings.videoHandler.config;
                    settings.editmode = this.settings.editmode;
                    settings.theme = this.settings.theme;
                    $(this.settings.videoHandler.selector).toolboxVideo(settings);
                }

                if (this.settings.iframeHandler.enabled === true) {
                    var settings = this.settings.iframeHandler.config;
                    settings.editmode = this.settings.editmode;
                    $(this.settings.iframeHandler.selector).toolboxIframe(settings);
                }

                if (this.settings.gOptOutLinkHandler.enabled === true) {
                    var settings = this.settings.gOptOutLinkHandler.config;
                    settings.editmode = this.settings.editmode;
                    $(this.settings.gOptOutLinkHandler.selector).toolboxGoogleOptOutLink(settings);
                }
            };

            _.init();
        }
    });

    $.toolboxCore.init = function (callback) {
        callback();
    }

})(jQuery);