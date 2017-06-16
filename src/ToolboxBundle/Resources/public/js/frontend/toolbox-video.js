/*
        __           __                                ___       _ __        __
   ____/ /___ ______/ /_  _________  ____ ___     ____/ (_)___ _(_) /_____ _/ /
  / __  / __ `/ ___/ __ \/ ___/ __ \/ __ `__ \   / __  / / __ `/ / __/ __ `/ /
 / /_/ / /_/ / /__/ / / / /__/ /_/ / / / / / /  / /_/ / / /_/ / / /_/ /_/ / /
 \__,_/\__,_/\___/_/ /_/\___/\____/_/ /_/ /_/   \__,_/_/\__, /_/\__/\__,_/_/
                                                       /____/
 copyright © dachcom digital

 */
var DachcomToolboxVideo = (function () {

    'use strict';

    var self = {

        $doc: $ !== undefined ? $(document) : null,

        isBusy : false,

        editMode : false,

        video: {},

        videoElements: 0,

        videoInitialized: 0,

        init: function() {

            self.editMode = typeof _PIMCORE_EDITMODE !== 'undefined' && _PIMCORE_EDITMODE === true;

            self.startSystem();

        },

        startSystem: function() {

            this.setupVideoElements();
            $(window).on('scroll.toolbox', this.onScroll.bind(this));

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

                this.videoElements += this.video.youtubeVideos.length;

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
                            playInLightBox = $player.data('play-in-lightbox'),
                            posterPath = $player.data('poster-path');

                        if( playInLightBox && posterPath !== '' ) {

                            _self._checkVideoState(true);
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

                                            if( autostart === true ) {
                                                _self._playVideo( player, 'youtube');
                                            }
                                        }
                                    }

                                });

                                $container.addClass('player-ready');
                                if ( $container.hasClass('autoplay') ) {

                                    _self.video.autoplayVideos.push({
                                        type: 'youtube',
                                        container: $container,
                                        player: player
                                    });
                                }

                                _self._checkVideoState( autostart !== true );

                            };

                            if( posterPath === '' ) {

                                initPlayer($player.get(0));

                            } else {

                                _self._checkVideoState(true);

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

                this.videoElements += this.video.vimeoVideos.length;

                this.video.vimeoVideos.each(function() {

                    var $container = $(this),
                        $player = $container.find('.player'),
                        videoId = _self._getVideoId($player.data('video-uri'), 'vimeo'),
                        playInLightBox = $player.data('play-in-lightbox'),
                        posterPath = $player.data('poster-path');

                    if( playInLightBox && posterPath !== '' ) {

                        _self._checkVideoState(true);

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

                                if( autostart === true ) {
                                    _self._playVideo( player, 'vimeo');
                                }

                            });

                            $container.addClass('player-ready');
                            if ( $container.hasClass('autoplay') ) {

                                _self.video.autoplayVideos.push({
                                    type: 'vimeo',
                                    container: $container,
                                    player: player
                                });
                            }

                            _self._checkVideoState( autostart !== true );

                        };

                        if( posterPath === '') {

                            initPlayer($player.get(0));

                        } else {

                            _self._checkVideoState(true);

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

        _checkVideoAutoPlay: function() {

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

                if(typeof player.getPlayerState === 'function') {
                    if ( player.getPlayerState() !== window.YT.PlayerState.PLAYING ) {
                        player.playVideo();
                    }
                }

            } else if ( type === 'vimeo' ) {

                if(typeof player.getPaused === 'function') {
                    player.getPaused().then(function(paused) {
                        if (paused) {
                            player.play();
                        }
                    }).catch(function(error) { /* fail silently */ });
                }
            }

        },

        _pauseVideo: function(player, type) {

            if( type === 'asset' ) {

                if( !player.paused ) player.pause();

            } else if( type === 'youtube' ) {

                if(typeof player.getPlayerState === 'function') {
                    if( player.getPlayerState() === window.YT.PlayerState.PLAYING ) {
                        player.pauseVideo();
                    }
                }

            } else if( type === 'vimeo' ) {

                if(typeof player.getPaused === 'function') {
                    player.getPaused().then(function(paused) {
                        if( !paused ) player.pause();
                    }).catch(function(error) { /* fail silently */ });
                }
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

        _checkVideoState: function( increase ) {

            if( increase === true) {
                this.videoInitialized++;
            }

            if( this.videoElements === this.videoInitialized ) {
                this._onAllVideosLoaded();
            }
        },

        _onAllVideosLoaded: function() {

            this._checkVideoAutoPlay();
        },

        onScroll: function(ev) {

            if( typeof Modernizr !== 'undefined' && Modernizr.touchevents ) {
                return;
            }

            if ( this.video.autoplayVideos.length > 0 ) {
                this._checkVideoAutoPlay();
            }

        }

    };

    return {
        init: self.init
    };

})();

if( $ !== undefined) {

    $(function() {
        'use strict';
        DachcomToolboxVideo.init();
    });

}