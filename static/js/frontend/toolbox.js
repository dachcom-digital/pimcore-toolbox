/*

        __           __                                ___       _ __        __
   ____/ /___ ______/ /_  _________  ____ ___     ____/ (_)___ _(_) /_____ _/ /
  / __  / __ `/ ___/ __ \/ ___/ __ \/ __ `__ \   / __  / / __ `/ / __/ __ `/ /
 / /_/ / /_/ / /__/ / / / /__/ /_/ / / / / / /  / /_/ / / /_/ / / /_/ /_/ / /
 \__,_/\__,_/\___/_/ /_/\___/\____/_/ /_/ /_/   \__,_/_/\__, /_/\__/\__,_/_/
                                                       /____/

 copyright @ 2016, dachcom digital
 don't be a dick. don't copy.

 */
var DachcomToolbox = (function () {

    var self = {

        $doc: $ !== undefined ? $(document) : null,

        isBusy : false,

        lastWindowPosition : {},

        editMode : typeof _PIMCORE_EDITMODE !== 'undefined' && _PIMCORE_EDITMODE === true,

        parallax : {},

        video: {},

        config: {

            debug: false,
            settings : {}

        },

        init: function (options) {

            jQuery.extend(self.config, options);

            self.startSystem();

        },

        startSystem: function () {

            this.parallax.containerElements = $('.toolbox-parallax-container');

            this.setupParallaxContainer();
            this.setupVideoElements();

            $(document).on('toolbox.checkContentHeights', this.checkContentHeight.bind(this));

            $(window).on('scroll.toolbox', this.onScroll.bind(this));
            $(window).on('resize.toolbox', this.onResize.bind(this));

        },

        setupParallaxContainer : function() {

            var _self = this, approaches = 0;

            if( this.editMode ) {

                var $v = this.parallax.containerElements;

                if( $v.length > 0 ) {

                    var interval = setInterval(function() {

                        if( $v.find('.parallax-video > .inner > div').length > 0 ) {
                            clearInterval(interval);
                            _self.checkContentHeight();
                        } else if( approaches > 20 ) {
                            clearInterval(interval);
                            _self.checkContentHeight();
                        }

                        approaches++;

                    }, 50);


                } else {

                    _self.checkContentHeight();
                }

            } else {

                _self.checkContentHeight();
            }

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
                tag.src = "https://www.youtube.com/iframe_api";
                var firstScriptTag = document.getElementsByTagName('script')[0];
                firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

                var YTdeferred = $.Deferred();
                window.onYouTubeIframeAPIReady = function() {
                    YTdeferred.resolve();
                }

                YTdeferred.done(function() {

                    _self.video.youtubeVideos.each(function() {

                        var $container = $(this),
                            $player = $container.find('.player'),
                            videoId = _self._getVideoId($player.data('video-uri'), 'youtube');

                        var player = new window.YT.Player($player.get(0), {
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

                                }
                            }
                        });

                    });

                });

            }


            if ( this.video.vimeoVideos.length > 0 ) {

                this.video.vimeoVideos.each(function() {

                    var $container = $(this),
                        $player = $container.find('.player'),
                        videoId = _self._getVideoId($player.data('video-uri'), 'vimeo');

                    var player = new Vimeo.Player($player.get(0), {
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

                    });

                });

            }

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

            if ( type == 'asset' ) {

                if ( player.paused ) player.play();

            } else if ( type == 'youtube' ) {

                if ( player.getPlayerState() != window.YT.PlayerState.PLAYING ) player.playVideo();

            } else if ( type == 'vimeo' ) {

                player.getPaused().then(function(paused) {
                    if (paused) player.play();
                }).catch(function(error) {

                });

            }

        },

        _pauseVideo: function(player, type) {


            if ( type == 'asset' ) {

                if ( !player.paused ) player.pause();

            } else if ( type == 'youtube' ) {

                if ( player.getPlayerState() == window.YT.PlayerState.PLAYING ) player.pauseVideo();

            } else if ( type == 'vimeo' ) {

                player.getPaused().then(function(paused) {
                    if (!paused) player.pause();
                }).catch(function(error) {

                });

            }

        },

        _getVideoId: function(url, type) {

            if ( type == 'youtube' ) {

                var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\??v?=?))([^#\&\?]*).*/
                var match = url.match(regExp);

                if (match && match[7]) {
                    return match[7];
                } else {
                    console.log('Unable to parse video id from url: ' + url);
                }

            } else if ( type == 'vimeo' ) {

                var regExp = /https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/;
                var match = url.match(regExp);

                if (match && match[3]) {
                    return match[3];
                } else {
                    console.log('Unable to parse video id from url: ' + url);
                }


            }


        },

        checkContentHeight : function() {

            var _self = this;

            this.parallax.containerElements.each( function() {

                var $el = $(this);

                $el.find('.background > .canvas').css({
                    'top': $el.offset().top - $(window).scrollTop(),
                    'position': 'fixed'
                });

                _self.parseContainerHeight( $el,  _self.getParallaxContentElement( $el ) );

            });

        },

        checkContentHeightOnScroll : function() {

            var _self = this;

            this.parallax.containerElements.each( function() {

                var $el = $(this);
                _self.parseContainerHeight( $el, _self.getParallaxContentElement( $el ) );

            });

        },

        parseContainerHeight : function( $el, $content) {

            var isMinHeight = $el.hasClass('window-full-height') && !this.editMode;

            var $contentHeight = $content.outerHeight(), $w = $(window), windowHeight = $w.outerHeight(), scrollTop = $w.scrollTop();

            if( isMinHeight && $contentHeight > windowHeight ) {

                if ( scrollTop >= $el.offset().top && ($contentHeight + $content.offset().top > scrollTop + windowHeight) ) {

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

        onScroll : function(ev) {

            var _self = this;

            if( typeof Modernizr !== 'undefined' && Modernizr.touchevents ) return;

            $('.toolbox-parallax-container .parallax-container-image .canvas').parallaxScroll({
                friction: 0.5
            });

            this.checkContentHeightOnScroll();

            if ( this.video.autoplayVideos.length > 0 ) {
                this._checkVideoAutoplay();
            }

        },

        getParallaxContentElement : function( $el )
        {
            if( $el.find('.slick-slider').length > 0 ) {
                return $el.find('.slick-slider');
            }

            return $el.find('.content').first();

        },

        onResize : function(){



        }

    };

    // API
    return {

        init: self.init

    }

})();

if( $ !== undefined) {
    $(document).ready(DachcomToolbox.init.bind({debug: false, settings : null }));
}