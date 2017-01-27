/*
        __           __                                ___       _ __        __
   ____/ /___ ______/ /_  _________  ____ ___     ____/ (_)___ _(_) /_____ _/ /
  / __  / __ `/ ___/ __ \/ ___/ __ \/ __ `__ \   / __  / / __ `/ / __/ __ `/ /
 / /_/ / /_/ / /__/ / / / /__/ /_/ / / / / / /  / /_/ / / /_/ / / /_/ /_/ / /
 \__,_/\__,_/\___/_/ /_/\___/\____/_/ /_/ /_/   \__,_/_/\__, /_/\__/\__,_/_/
                                                       /____/
 copyright @ 2016, dachcom digital

 */
var DachcomToolbox = (function () {

    'use strict';

    var self = {

        $doc: $ !== undefined ? $(document) : null,

        isBusy : false,

        lastWindowPosition : {},

        editMode : false,

        parallax : {},

        video: {},

        init: function() {

            self.parallax.containerElements = $('.toolbox-parallax-container');
            self.editMode = typeof _PIMCORE_EDITMODE !== 'undefined' && _PIMCORE_EDITMODE === true;

            self.startSystem();

        },

        startSystem: function() {

            this.setupParallaxContainer();

            this.setupVideoElements();

            this.setupGoogleMaps();

            $(document).on('toolbox.checkContentHeights', this.checkContentHeight.bind(this));
            $(window).on('scroll.toolbox', this.onScroll.bind(this));

        },

        setupGoogleMaps: function() {

            var $maps = $('.toolbox-googlemap'),

                //internal callback 1
                isValidLocation = function(location) {
                    return location.hasOwnProperty('lat') && location.hasOwnProperty('lng') && !isNaN(location.lat) && !isNaN(location.lng);
                },

                //internal callback 2
                addMarker = function(map, location, markerIcon) {

                    if ( isValidLocation(location) ) {

                        var marker = new google.maps.Marker({
                            position: {lat: location.lat, lng: location.lng},
                            map: map,
                            title: location.title,
                            icon: markerIcon
                        });

                        marker.addListener('click', function() {

                            var infoWindow = new google.maps.InfoWindow({
                                content: '<div class="info-window"><div class="loading"></div></div>'
                            });

                            infoWindow.open(map, marker);

                            $.ajax({

                                url: '/plugin/Toolbox/GoogleMap/info-window',
                                method: 'POST',
                                data: {
                                    mapParams : location,
                                    language: $('html').attr('lang')
                                },
                                complete: function(result) {
                                    infoWindow.setContent(result.responseText);
                                    map.setCenter(marker.getPosition());
                                }

                            });

                        });

                    }

                };

            $maps.each(function() {

                var $map = $(this),
                    locations = $map.data('locations') || [],
                    mapStyleUrl = $map.data('mapstyleurl'),
                    markerIcon = $map.data('markericon'),
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

                var map = new google.maps.Map($map.get(0), mapOptions),
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
                            addMarker(map, location, markerIcon);
                        }

                    });

                    map.fitBounds( latLngBounds );

                    var listener = google.maps.event.addListener(map, 'idle', function() {
                        var zoom = (typeof mapOptions.zoom === 'number' && (mapOptions.zoom % 1) === 0) ? mapOptions.zoom : 17;

                        map.setZoom(zoom);
                        google.maps.event.removeListener(listener);
                    });

                }

            });

        },

        setupParallaxContainer: function() {

            var _self = this,
                $parallaxContainer = $('.toolbox-parallax-container:not(.window-full-height) .parallax-container-image .canvas');

            var $v = this.parallax.containerElements;

            this.waitForElementInEditMode( $v, '.parallax-video > .inner > div').then(
                function() { _self.checkContentHeight(); }
            );

            if( this.editMode ) {

                $parallaxContainer.each(function() {
                    $(this).css('background-image', 'url("' + $(this).data('image-src') + '")')
                });

            } else {

                $parallaxContainer.parallax({
                    friction: 0.5
                });
            }

        },

        waitForElementInEditMode: function( $container, $els ) {

            var interval = null,
                approaches = 0,
                dfd = jQuery.Deferred();

            if( !this.editMode || $container.length === 0) {
                dfd.resolve(true);
                return dfd.promise();
            }

            interval = setInterval(function() {

                if( $container.find($els).length > 0 ) {
                    clearInterval(interval);
                    dfd.resolve($container);
                } else if( approaches > 20 ) {
                    clearInterval(interval);
                    dfd.resolve($container);
                }

                approaches++;

            }, 50);

            return dfd.promise();
        },

        setupVideoElements: function() {

            var _self = this;

            this.video.autoplayVideos = [];

            if( this.editMode) {
                return false;
            }

            this.video.youtubeVideos = $('.toolbox-video[data-type="youtube"]');
            this.video.vimeoVideos = $('.toolbox-video[data-type="vimeo"]');

            $('.toolbox-video.autoplay[data-type="asset"]').each(function() {

                _self.video.autoplayVideos.push({
                    type: 'asset',
                    container: $(this),
                    player: $(this).find('video').get(0)
                });

            });

            if ( this.video.youtubeVideos.length > 0 ) {

                var tag = document.createElement('script');
                tag.src = 'https://www.youtube.com/iframe_api';

                var firstScriptTag = document.getElementsByTagName('script')[0];
                firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

                var YTdeferred = $.Deferred();
                window.onYouTubeIframeAPIReady = function() {
                    YTdeferred.resolve();
                };

                YTdeferred.done(function() {

                    _self.video.youtubeVideos.each(function() {

                        var $container = $(this),
                            $player = $container.find('.player'),
                            videoId = _self._getVideoId($player.data('video-uri'), 'youtube'),
                            playInLightbox = $player.data('play-in-lightbox'),
                            posterPath = $player.data('poster-path');

                        if( playInLightbox && posterPath !== '' ) {

                            $container.on('click', function(ev) {
                                ev.preventDefault();
                                $container.trigger('toolbox.video.youtube.lightbox', [{'videoId' : videoId, 'posterPath' : posterPath, 'YT' : window.YT}]);
                            });

                        } else {

                            var initPlayer = function(el, autostart) {

                                var player = new window.YT.Player(el, {
                                    videoId: videoId,
                                    events: {
                                        'onReady': function() {

                                            $container.addClass('player-ready');
                                            if ( $container.hasClass('autoplay') ) {

                                                _self.video.autoplayVideos.push({
                                                    type: 'youtube',
                                                    container: $container,
                                                    player: player
                                                });
                                            }

                                            if( autostart === true )
                                            {
                                                _self._playVideo( player, 'youtube');
                                            }
                                        }
                                    }

                                });
                            };

                            if( posterPath === '') {

                                initPlayer($player.get(0));

                            } else {

                                $container.one('click', function(ev) {
                                    ev.preventDefault();
                                    initPlayer($player.get(0), true);
                                    $container.find('.poster-overlay').remove();
                                });
                            }
                        }
                    });
                });
            }

            if ( this.video.vimeoVideos.length > 0 ) {

                this.video.vimeoVideos.each(function() {

                    var $container = $(this),
                        $player = $container.find('.player'),
                        videoId = _self._getVideoId($player.data('video-uri'), 'vimeo'),
                        playInLightbox = $player.data('play-in-lightbox'),
                        posterPath = $player.data('poster-path');

                    if( playInLightbox && posterPath !== '' ) {

                        $container.on('click', function(ev) {
                            ev.preventDefault();
                            $container.trigger('toolbox.video.vimeo.lightbox', [{'videoId' : videoId, 'posterPath' : posterPath, 'Vimeo' : Vimeo.Player}]);
                        });

                    } else {

                        var initPlayer = function(el, autostart) {

                            var player = new Vimeo.Player(el, {
                                id: videoId
                            });

                            player.on('loaded', function() {

                                $container.addClass('player-ready');

                                if ( $container.hasClass('autoplay') ) {

                                    _self.video.autoplayVideos.push({
                                        type: 'vimeo',
                                        container: $container,
                                        player: player
                                    });

                                }

                                if( autostart === true )
                                {
                                    _self._playVideo( player, 'vimeo');
                                }

                            });

                        };

                        if( posterPath === '') {

                            initPlayer($player.get(0));

                        } else {

                            $container.one('click', function(ev) {
                                ev.preventDefault();
                                initPlayer($player.get(0), true);
                                $container.find('.poster-overlay').remove();
                            });
                        }
                    }

                });

            }

            // special treatment for videos inside accordion (bootstrap collapse)
            $.each(this.video.autoplayVideos, function(i, videoObj) {

                var $el = videoObj.container;

                if ( $el.parents('.panel-collapse').length > 0 ) {

                    $el.parents('.panel-group').on('shown.bs.collapse', function () {
                        _self._playVideo( videoObj.player, videoObj.type );
                    });

                    $el.parents('.panel-group').on('hide.bs.collapse', function () {
                        _self._pauseVideo( videoObj.player, videoObj.type );
                    });
                }

            });

        },

        _checkVideoAutoplay: function() {

            var _self = this,
                tolerancePixel = 40,
                scrollTop = $(window).scrollTop() + tolerancePixel,
                scrollBottom = $(window).scrollTop() + $(window).height() - tolerancePixel;

            $.each(this.video.autoplayVideos, function(i, videoObj) {

                var yTopMedia = videoObj.container.offset().top,
                    yBottomMedia = videoObj.container.height() + yTopMedia;

                if(scrollTop < yBottomMedia && scrollBottom > yTopMedia) {
                    _self._playVideo( videoObj.player, videoObj.type );
                } else {
                    _self._pauseVideo( videoObj.player, videoObj.type );
                }

            });

        },

        _playVideo: function(player, type) {

            if ( type === 'asset' ) {

                if ( player.paused ) player.play();

            } else if ( type === 'youtube' ) {

                if ( player.getPlayerState() != window.YT.PlayerState.PLAYING ) player.playVideo();

            } else if ( type === 'vimeo' ) {

                player.getPaused().then(function(paused) {
                    if (paused) player.play();
                }).catch(function(error) {

                });

            }

        },

        _pauseVideo: function(player, type) {

            if( type === 'asset' ) {

                if( !player.paused ) player.pause();

            } else if( type === 'youtube' ) {

                if( player.getPlayerState() == window.YT.PlayerState.PLAYING ) player.pauseVideo();

            } else if( type === 'vimeo' ) {

                player.getPaused().then(function(paused) {
                    if( !paused ) player.pause();
                }).catch(function(error) {

                });

            }

        },

        _getVideoId: function(url, type) {

            var regExp, match;

            url = url.toString();

            if( type === 'youtube' ) {

                if( !url.match(/(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?/) ) {
                    return url;
                } else {
                    regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\??v?=?))([^#\&\?]*).*/;
                    match = url.match(regExp);

                    if ( match && match[7] ) {
                        return match[7];
                    } else {
                        console.log('Unable to parse video id from url: ' + url);
                    }
                }

            } else if( type === 'vimeo' ) {

                if( !url.match(/(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?/) ) {
                    return url;
                } else {
                    regExp = /https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/;
                    match = url.match(regExp);

                    if (match && match[3]) {
                        return match[3];
                    } else {
                        console.log('Unable to parse video id from url: ' + url);
                    }

                }

            }

        },

        checkContentHeight: function() {

            var _self = this;

            this.parallax.containerElements.each( function() {

                var $el = $(this);

                if( $el.hasClass('window-full-height') ) {

                    $el.find('.background > .canvas').css({
                        'top': $el.offset().top - $(window).scrollTop(),
                        'position': 'fixed'
                    });

                    _self.parseContainerHeight( $el,  _self.getParallaxContentElement( $el ) );
                }

            });

        },

        checkContentHeightOnScroll: function() {

            var _self = this;

            this.parallax.containerElements.each( function() {

                var $el = $(this);
                _self.parseContainerHeight( $el, _self.getParallaxContentElement( $el ) );

            });

        },

        parseContainerHeight: function( $el, $content) {

            var $contentHeight = $content.outerHeight(), $w = $(window), windowHeight = $w.outerHeight(), scrollTop = $w.scrollTop();

            if( !this.editMode && $contentHeight > windowHeight ) {

                if ( scrollTop >= $el.offset().top && ($contentHeight + $content.offset().top > scrollTop + windowHeight ) ) {

                    $el.find('.background > .canvas').css('top', 0);

                } else {

                    var contentOffset = $el.offset().top - scrollTop;

                    if ( $el.offset().top < scrollTop ) {
                        contentOffset = $content.offset().top - scrollTop + ($contentHeight - windowHeight);
                    }

                    $el.find('.background > .canvas').css('top', contentOffset);

                }

            }

        },

        onScroll: function(ev) {

            this.checkContentHeightOnScroll();

            if( typeof Modernizr !== 'undefined' && Modernizr.touchevents ) {
                return;
            }

            if ( this.video.autoplayVideos.length > 0 ) {
                this._checkVideoAutoplay();
            }

        },

        getParallaxContentElement: function( $el ) {

            if( $el.find('.slick-slider').length > 0 ) {
                return $el.find('.slick-slider');
            }

            return $el.find('.content').first();

        }

    };

    return {

        init: self.init

    }

})();

if( $ !== undefined) {

    $(function() {
        'use strict';
        DachcomToolbox.init();
    });

}