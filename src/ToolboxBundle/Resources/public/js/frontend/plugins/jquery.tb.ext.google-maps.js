/*
 *  Project: PIMCORE Toolbox
 *  Extension: Google Maps
 *  Version: 2.3
 *  Author: DACHCOM.DIGITAL
 *  License: GPLv3
*/
;(function ($, window, document) {
    'use strict';
    var clName = 'ToolboxGoogleMaps';

    function ToolboxGoogleMaps(map, options) {
        this.$map = $(map);
        this.options = $.extend(true, $.fn.toolboxGoogleMaps.defaults, options);
        this.init();
    }

    $.extend(ToolboxGoogleMaps.prototype, {

        editMode: false,
        mapInstance: null,

        init: function () {
            this.editMode = this.options.editmode;
            this.setupGoogleMaps();
        },

        setupGoogleMaps: function () {

            var _ = this,
                $map = this.$map,
                latLngBounds,
                listener,
                locations = $map.data('locations') || [],
                mapStyleUrl = $map.data('mapstyleurl'),
                markerIcon = $map.data('markericon'),
                showInfoWindowOnLoad = $map.data('show-info-window-on-load') === 1,
                mapOptions = {
                    center: new google.maps.LatLng(0, 0)
                };

            $.each($map.data(), function (name, value) {
                if (name.substring(0, 9) === 'mapoption') {
                    name = name.replace('mapoption', '');
                    name = name.charAt(0).toLowerCase() + name.slice(1);
                    mapOptions[name] = value;
                }
            });

            this.mapInstance = new google.maps.Map($map.get(0), mapOptions);
            latLngBounds = new google.maps.LatLngBounds();

            if (typeof mapStyleUrl === 'string') {
                $.getJSON(mapStyleUrl).done(function (data) {
                    _.mapInstance.set('styles', data);
                });
            }

            if (locations.length > 0) {
                $.each(locations, function (i, location) {
                    if (_.isValidLocation(location)) {
                        latLngBounds.extend(new google.maps.LatLng(location.lat, location.lng));
                        _.addMarker(location, markerIcon, showInfoWindowOnLoad);
                    }
                });

                this.mapInstance.fitBounds(latLngBounds);

                listener = google.maps.event.addListener(this.mapInstance, 'idle', function () {
                    var zoom = (typeof mapOptions.zoom === 'number' && (mapOptions.zoom % 1) === 0) ? mapOptions.zoom : 17;
                    _.mapInstance.setZoom(zoom);
                    google.maps.event.removeListener(listener);
                });
            }
            //store map as data attribute
            $map.data('toolbox-google-map', this.mapInstance);

            this.checkElementEnvironment();

            if (this.options.centerMapOnResize === true) {
                $(window).on('resize.toolbox.googleMap', this.checkResizeContext.bind(this));
            }
        },

        isValidLocation: function (location) {
            return location.hasOwnProperty('lat')
                && location.hasOwnProperty('lng')
                && !isNaN(location.lat)
                && !isNaN(location.lng);
        },

        addMarker: function (location, markerIcon, showInfoWindowOnLoad) {
            var _ = this, marker, infoWindow;

            if (!this.isValidLocation(location)) {
                return;
            }

            marker = new google.maps.Marker({
                position: {lat: location.lat, lng: location.lng},
                map: this.mapInstance,
                title: location.title,
                icon: markerIcon,
                contentLoaded: false
            });

            if (location.hideInfoWindow !== true) {

                infoWindow = new google.maps.InfoWindow({
                    content: '<div class="info-window"><div class="loading"></div></div>'
                });

                marker.addListener('click', function () {
                    infoWindow.open(this.mapInstance, marker);
                    if (marker.contentLoaded === false) {
                        $.ajax({
                            url: '/toolbox/ajax/gm-info-window',
                            method: 'POST',
                            data: {
                                mapParams: location,
                                language: $('html').attr('lang')
                            },
                            complete: function (result) {
                                marker.contentLoaded = true;
                                infoWindow.setContent(result.responseText);
                                _.mapInstance.setCenter(marker.getPosition());
                            }
                        });
                    }
                });

                if (showInfoWindowOnLoad === true) {
                    google.maps.event.trigger(marker, 'click');
                }
            }
        },

        checkElementEnvironment: function () {

            var _ = this;

            // special treatment for a re-init map inside accordion and tabs
            if (this.options.theme === 'bootstrap3' || this.options.theme === 'bootstrap4') {
                $(document).on('shown.bs.collapse', function (ev) {
                    var $target = $(ev.target);
                    if ($target && $.contains($target[0], _.$map[0])) {
                        var x = _.mapInstance.getZoom(),
                            c = _.mapInstance.getCenter();
                        google.maps.event.trigger(_.mapInstance, 'resize');
                        _.mapInstance.setZoom(x);
                        _.mapInstance.setCenter(c);
                    }
                }).on('shown.bs.tab shown-tabs.bs.tab-collapse', function (ev) {
                    var $target = $($(ev.target).attr('href'));
                    if ($target && $.contains($target[0], _.$map[0])) {
                        var x = _.mapInstance.getZoom(),
                            c = _.mapInstance.getCenter();
                        google.maps.event.trigger(_.mapInstance, 'resize');
                        _.mapInstance.setZoom(x);
                        _.mapInstance.setCenter(c);
                    }
                });
            }
        },

        checkResizeContext: function (ev) {
            var x = this.mapInstance.getZoom(),
                c = this.mapInstance.getCenter();
            google.maps.event.trigger(this.mapInstance, 'resize');
            this.mapInstance.setZoom(x);
            this.mapInstance.setCenter(c);
        }

    });

    $.fn.toolboxGoogleMaps = function (options) {
        this.each(function () {
            if (!$.data(this, 'tb-ext-google-maps-' + clName)) {
                $.data(this, 'tb-ext-google-maps-' + clName, new ToolboxGoogleMaps(this, options));
            }
        });
        return this;
    };

    $.fn.toolboxGoogleMaps.defaults = {
        editmode: false,
        centerMapOnResize: true,
        theme: 'bootstrap4'
    };

})(jQuery, window, document);