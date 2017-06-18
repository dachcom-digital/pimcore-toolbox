/*
        __           __                                ___       _ __        __
   ____/ /___ ______/ /_  _________  ____ ___     ____/ (_)___ _(_) /_____ _/ /
  / __  / __ `/ ___/ __ \/ ___/ __ \/ __ `__ \   / __  / / __ `/ / __/ __ `/ /
 / /_/ / /_/ / /__/ / / / /__/ /_/ / / / / / /  / /_/ / / /_/ / / /_/ /_/ / /
 \__,_/\__,_/\___/_/ /_/\___/\____/_/ /_/ /_/   \__,_/_/\__, /_/\__/\__,_/_/
                                                       /____/
 copyright © dachcom digital

 */
var DachcomToolboxGoogleMaps = (function () {

    'use strict';

    var self = {

        $doc: $ !== undefined ? $(document) : null,

        isBusy : false,

        editMode : false,

        init: function() {

            self.editMode = typeof _PIMCORE_EDITMODE !== 'undefined' && _PIMCORE_EDITMODE === true;

            self.startSystem();

        },

        startSystem: function() {

            this.setupGoogleMaps();

        },

        setupGoogleMaps: function() {

            var $maps = $('.toolbox-googlemap'),

                //internal callback 1
                isValidLocation = function(location) {
                    return location.hasOwnProperty('lat') && location.hasOwnProperty('lng') && !isNaN(location.lat) && !isNaN(location.lng);
                },

                //internal callback 2
                addMarker = function(map, location, markerIcon, showInfoWindowOnLoad) {

                    var marker, infoWindow;

                    if ( isValidLocation(location) ) {

                        marker = new google.maps.Marker({
                            position: {lat: location.lat, lng: location.lng},
                            map: map,
                            title: location.title,
                            icon: markerIcon,
                            contentLoaded: false
                        });

                        infoWindow = new google.maps.InfoWindow({
                            content: '<div class="info-window"><div class="loading"></div></div>'
                        });

                        marker.addListener('click', function() {

                            infoWindow.open(map, marker);

                            if(marker.contentLoaded === false) {
                                $.ajax({

                                    url: '/toolbox/ajax/gm-info-window',
                                    method: 'POST',
                                    data: {
                                        mapParams : location,
                                        language: $('html').attr('lang')
                                    },
                                    complete: function(result) {
                                        marker.contentLoaded = true;
                                        infoWindow.setContent(result.responseText);
                                        map.setCenter(marker.getPosition());
                                    }

                                });

                            }

                        });

                        if(showInfoWindowOnLoad === true) {
                            google.maps.event.trigger(marker, 'click');
                        }
                    }

                };

            $maps.each(function() {

                var $map = $(this),
                    map, latLngBounds, listener,
                    locations = $map.data('locations') || [],
                    mapStyleUrl = $map.data('mapstyleurl'),
                    markerIcon = $map.data('markericon'),
                    showInfoWindowOnLoad = $map.data('show-info-window-on-load') === 1,
                    mapOptions = {
                        center: new google.maps.LatLng (0, 0)
                    };

                $.each($map.data(), function(name, value) {

                    if ( name.substring(0,9) === 'mapoption' ) {
                        name = name.replace('mapoption', '');
                        name = name.charAt(0).toLowerCase() + name.slice(1);

                        mapOptions[name] = value;
                    }

                });

                map = new google.maps.Map($map.get(0), mapOptions);
                latLngBounds = new google.maps.LatLngBounds();

                if ( typeof mapStyleUrl === 'string' ) {

                    $.getJSON(mapStyleUrl).done(function(data) {
                        map.set('styles', data);
                    });

                }

                if ( locations.length > 0 ) {

                    $.each(locations, function(i, location) {

                        if ( isValidLocation(location) ) {
                            latLngBounds.extend( new google.maps.LatLng(location.lat, location.lng) );
                            addMarker(map, location, markerIcon, showInfoWindowOnLoad);
                        }

                    });

                    map.fitBounds( latLngBounds );

                    listener = google.maps.event.addListener(map, 'idle', function() {
                        var zoom = (typeof mapOptions.zoom === 'number' && (mapOptions.zoom % 1) === 0) ? mapOptions.zoom : 17;

                        map.setZoom(zoom);
                        google.maps.event.removeListener(listener);
                    });

                }

            });

        }

    };

    return {
        init: self.init
    };

})();

if( $ !== undefined) {

    $(function() {
        'use strict';
        DachcomToolboxGoogleMaps.init();
    });

}