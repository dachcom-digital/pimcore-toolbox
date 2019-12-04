/*
 *  Project: PIMCORE Toolbox
 *  Extension: Video
 *  Version: 2.3
 *  Author: DACHCOM.DIGITAL
 *  License: GPL-3.0-or-later
*/
;(function ($, window, document) {
    'use strict';
    var clName = 'ToolboxVideo';

    function VideoIdExtractor(options) {
        this.userMethods = options;
        this.extrator = {
            asset: function (videoId) {
                return videoId;
            },
            youtube: function (url) {
                var regExp, match, url = url.toString();
                if (!url.match(/(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?/)) {
                    return url;
                } else {
                    regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\??v?=?))([^#\&\?]*).*/;
                    match = url.match(regExp);
                    if (match && match[7]) {
                        return match[7];
                    } else {
                        console.log('Unable to parse video id from url: ' + url);
                    }
                }
            },

            vimeo: function (url) {
                var regExp, match, url = url.toString();
                if (!url.match(/(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?/)) {
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
        };

        this.getVideoId = function () {

            var args = Array.prototype.slice.call(arguments),
                type = args.shift();

            if (typeof this.userMethods[type] === 'function') {
                return this.userMethods[type].apply(null, args);
            }

            if (typeof this.extrator[type] === 'function') {
                return this.extrator[type].apply(null, args);
            }

            console.warn('no valid video id extractor for ' + type + ' found!');

            return null;
        }
    }

    function ToolboxVideo(element, options) {
        this.$element = $(element);
        this.options = $.extend(true, $.fn.toolboxVideo.defaults, options);
        this.videoIdExtractor = new VideoIdExtractor(this.options.videoIdExtractor);
        this.init();
    }

    $.extend(ToolboxVideo.prototype, {

        $player: null,
        elementId: null,
        videoId: null,
        videoType: null,
        playerEngine: null,
        autoPlay: false,
        playInLightBox: false,
        videoParameter: {},

        hasPoster: false,
        posterPath: null,

        editMode: false,

        internalTypes: ['youtube', 'vimeo', 'asset'],
        isReady: false,

        init: function () {
            this.editMode = this.options.editmode;

            if (this.editMode == true) {
                return;
            }

            this.setupVideoElement();
            $(window).on('scroll.toolbox.video', this.pingAutoPlay.bind(this));
        },

        setupVideoElement: function () {

            this.elementId = this.$element.data('tb-ext-video-index');
            this.videoType = this.$element.data('type');

            if (this.$element.hasClass('autoplay')) {
                this.autoPlay = true;
            }

            this.$player = this.$element.find('.player');
            if (this.$player.length === 0) {
                return;
            }

            this.videoId = this.getVideoId(this.$player.data('video-uri'));
            this.playInLightBox = this.$player.data('play-in-lightbox');
            this.posterPath = this.$player.data('poster-path');

            if (this.$player.data('video-parameter') !== undefined) {
                this.videoParameter = this.$player.data('video-parameter');
            }

            if (this.posterPath) {
                this.hasPoster = true;
            }

            switch (this.videoType) {
                case 'youtube' :
                    this.setupYoutubeVideo();
                    break;
                case 'asset' :
                    this.setupAssetVideo();
                    break;
                case 'vimeo':
                    this.setupVimeoVideo();
                    break;
                default:
                    this.setupCustomVideo();
            }

            this.checkElementEnvironment();

        },

        checkElementEnvironment: function () {

            var _ = this;

            // special treatment for a auto play video inside accordion and tabs
            if (this.autoPlay === true && (this.options.theme === 'bootstrap3' || this.options.theme === 'bootstrap4')) {
                $(document).on('shown.bs.collapse', function (ev) {
                    var $target = $(ev.target);
                    if ($target && $.contains($target[0], _.$element[0])) {
                        _.playVideo();
                    }
                }).on('hide.bs.collapse', function (ev) {
                    var $target = $(ev.target);
                    if ($target && $.contains($target[0], _.$element[0])) {
                        _.pauseVideo();
                    }
                }).on('shown.bs.tab shown-tabs.bs.tab-collapse', function (ev) {
                    var $target = $($(ev.target).attr('href'));
                    if ($target && $.contains($target[0], _.$element[0])) {
                        _.playVideo();
                    }
                }).on('hide.bs.tab hide-tabs.bs.tab-collapse', function (ev) {
                    var $target = $($(ev.target).attr('href'));
                    if ($target && $.contains($target[0], _.$element[0])) {
                        _.pauseVideo();
                    }
                });
            }
        },

        setupAssetVideo: function () {

            var _ = this;

            if (_.playInLightBox && _.hasPoster) {
                _.$element.on('click', function (ev) {
                    ev.preventDefault();
                    _.$element.trigger('toolbox.video.asset.lightbox', [{
                        'videoId': _.videoId,
                        'posterPath': _.posterPath
                    }]);
                });

            } else {

                if (!_.hasPoster) {
                    _.isReady = true;
                    _.$element.addClass('player-ready');
                    _.$element.trigger('toolbox.video.asset.init');
                } else {
                    _.$element.one('click', function (ev) {
                        ev.preventDefault();
                        _.isReady = true;
                        _.$element.find('.poster-overlay').remove();
                        _.$element.addClass('player-ready');
                        _.$element.trigger('toolbox.video.asset.init');
                    });
                }
            }
        },

        setupYoutubeVideo: function () {

            var _ = this,
                tag,
                deferred,
                currentIndex = this.elementId,
                apiIsReady = $('body').data('toolbox-youtube-api-ready') === true;

            if (apiIsReady === false) {
                deferred = $.Deferred();
                if (window.toolboxVideoYoutubeDeferer === undefined) {
                    window.toolboxVideoYoutubeDeferer = {};
                }
                if (!window.toolboxVideoYoutubeDeferer[currentIndex]) {
                    window.toolboxVideoYoutubeDeferer[currentIndex] = deferred;
                }
            }

            if ($('body').find('script.tb-video-youtube-api').length === 0) {
                tag = document.createElement('script');
                tag.src = this.options.resources.youtube;
                tag.className = 'tb-video-youtube-api';
                $('body').append(tag);

                window.onYouTubeIframeAPIReady = function () {
                    $('body').data('toolbox-youtube-api-ready', true);
                    if (window.toolboxVideoYoutubeDeferer) {
                        $.each(window.toolboxVideoYoutubeDeferer, function (i, d) {
                            delete window.toolboxVideoYoutubeDeferer[i];
                            if (Object.keys(window.toolboxVideoYoutubeDeferer).length === 0) {
                                delete window.toolboxVideoYoutubeDeferer;
                            }
                            d.resolve();
                        })
                    }
                };
            }

            var init = function () {

                if (_.playInLightBox && _.hasPoster) {
                    _.$element.on('click', function (ev) {
                        ev.preventDefault();
                        _.$element.trigger('toolbox.video.youtube.lightbox', [{
                            'videoId': _.videoId,
                            'posterPath': _.posterPath,
                            'YT': window.YT
                        }]);
                    });

                } else {

                    var initPlayer = function ($el, autostart) {
                        var player,
                            playerVars = $.extend({}, _.options.apiParameter.youtube, _.videoParameter),
                            options = {
                                videoId: _.videoId,
                                events: {
                                    'onReady': function () {
                                        _.isReady = true;
                                        _.playerEngine = player;
                                        _.$element.addClass('player-ready');
                                        if (autostart === true) {
                                            _.playVideo();
                                        }
                                    }
                                }
                            };

                        player = new window.YT.Player(
                            $el, $.extend({}, {playerVars: playerVars}, options)
                        );
                    };

                    if (!_.hasPoster) {
                        initPlayer(_.$player.get(0));
                    } else {
                        _.$element.one('click', function (ev) {
                            ev.preventDefault();
                            initPlayer(_.$player.get(0), true);
                            _.$element.find('.poster-overlay').remove();
                        });
                    }
                }
            }

            if (apiIsReady) {
                init();
            } else {
                deferred.done(function (YT) {
                    init();
                });
            }
        },

        setupVimeoVideo: function () {

            var _ = this,
                tag,
                deferred,
                currentIndex = this.elementId,
                apiIsReady = $('body').data('toolbox-vimeo-api-ready') === true;

            if (apiIsReady === false) {
                deferred = $.Deferred();
                if (window.toolboxVideoVimeoStatus === undefined) {
                    window.toolboxVideoVimeoStatus = 'init';
                }

                if (window.toolboxVideoVimeoDeferer === undefined) {
                    window.toolboxVideoVimeoDeferer = {};
                }

                if (!window.toolboxVideoVimeoDeferer[currentIndex]) {
                    window.toolboxVideoVimeoDeferer[currentIndex] = deferred;
                }
            }

            if (window.toolboxVideoVimeoStatus === 'init') {
                window.toolboxVideoVimeoStatus = 'loading';
                $.getScript(this.options.resources.vimeo, function (data, textStatus, jqxhr) {
                    window.toolboxVideoVimeoStatus = 'loaded';
                    $('body').data('toolbox-vimeo-api-ready', true);
                    if (window.toolboxVideoVimeoDeferer) {
                        $.each(window.toolboxVideoVimeoDeferer, function (i, d) {
                            delete window.toolboxVideoVimeoDeferer[i];
                            if (Object.keys(window.toolboxVideoVimeoDeferer).length === 0) {
                                delete window.toolboxVideoVimeoDeferer;
                            }
                            d.resolve();
                        })
                    }
                });
            }

            var init = function () {

                if (_.playInLightBox && _.hasPoster) {
                    _.$element.on('click', function (ev) {
                        ev.preventDefault();
                        _.$element.trigger('toolbox.video.vimeo.lightbox', [{
                            'videoId': _.videoId,
                            'posterPath': _.posterPath,
                            'Vimeo': Vimeo.Player
                        }]);
                    });

                } else {

                    var initPlayer = function (el, autostart) {

                        var player,
                            options = {
                                id: _.videoId
                            };

                        player = new Vimeo.Player(
                            el, $.extend({}, _.options.apiParameter.vimeo, _.videoParameter, options)
                        );

                        player.on('loaded', function () {
                            _.isReady = true;
                            _.playerEngine = player;
                            _.$element.addClass('player-ready');
                            if (autostart === true) {
                                _.playVideo();
                            }
                        });
                    };

                    if (!_.hasPoster) {
                        initPlayer(_.$player.get(0));
                    } else {
                        _.$element.one('click', function (ev) {
                            ev.preventDefault();
                            initPlayer(_.$player.get(0), true);
                            _.$element.find('.poster-overlay').remove();
                        });
                    }
                }
            }

            if (apiIsReady) {
                init();
            } else {
                deferred.done(function (YT) {
                    init();
                });
            }
        },

        setupCustomVideo: function () {
            this.$element.trigger('toolbox.video.custom.setup', [this]);
        },

        playVideo: function () {

            var _ = this;

            if (this.isReady === false) {
                return;
            }

            switch (this.videoType) {
                case 'youtube' :
                    if (typeof this.playerEngine.getPlayerState === 'function') {
                        if (this.playerEngine.getPlayerState() !== window.YT.PlayerState.PLAYING) {
                            this.playerEngine.playVideo();
                        }
                    }
                    break;
                case 'asset' :
                    this.$element.trigger('toolbox.video.asset.play');
                    break;
                case 'vimeo':
                    if (typeof this.playerEngine.getPaused === 'function') {
                        this.playerEngine.getPaused().then(function (paused) {
                            if (paused) {
                                _.playerEngine.play();
                            }
                        }).catch(function (error) { /* fail silently */
                        });
                    }
                    break;
                default:
                    this.$element.trigger('toolbox.video.custom.play');
            }
        },

        pauseVideo: function (player, type) {

            var _ = this;

            if (this.isReady === false) {
                return;
            }

            switch (this.videoType) {
                case 'youtube' :
                    if (typeof this.playerEngine.getPlayerState === 'function') {
                        if (this.playerEngine.getPlayerState() === window.YT.PlayerState.PLAYING) {
                            this.playerEngine.pauseVideo();
                        }
                    }
                    break;
                case 'asset' :
                    this.$element.trigger('toolbox.video.asset.pause');
                    break;
                case 'vimeo':
                    if (typeof this.playerEngine.getPaused === 'function') {
                        this.playerEngine.getPaused().then(function (paused) {
                            if (!paused) {
                                _.playerEngine.pause();
                            }
                        }).catch(function (error) { /* fail silently */
                        });
                    }
                    break;
                default:
                    this.$element.trigger('toolbox.video.custom.pause');
            }
        },

        getVideoId: function (url) {
            return this.videoIdExtractor.getVideoId(this.videoType, url);
        },

        pingAutoPlay: function (ev) {

            if (typeof Modernizr !== 'undefined' && Modernizr.touchevents) {
                return;
            }

            if (!this.isReady || this.autoPlay === false) {
                return;
            }

            var _ = this,
                tolerancePixel = 40,
                scrollTop = $(window).scrollTop() + tolerancePixel,
                scrollBottom = $(window).scrollTop() + $(window).height() - tolerancePixel;

            var yTopMedia = this.$element.offset().top,
                yBottomMedia = this.$element.height() + yTopMedia;

            if (scrollTop < yBottomMedia && scrollBottom > yTopMedia) {
                _.playVideo();
            } else {
                _.pauseVideo();
            }
        }
    });

    $.fn.toolboxVideo = function (options) {
        var els = [];
        this.each(function (i) {
            if (!window.toolboxYoutubeElementCounter) {
                window.toolboxYoutubeElementCounter = 0;
            }
            if (!$.data(this, 'tb-ext-video-' + clName)) {
                $.data(this, 'tb-ext-video-index', window.toolboxYoutubeElementCounter);
                $.data(this, 'tb-ext-video-' + clName, new ToolboxVideo(this, options));
                window.toolboxYoutubeElementCounter++;
                els.push(this);
            }
        });

        return this;
    };

    $.fn.toolboxVideo.defaults = {
        editmode: false,
        videoIdExtractor: {},
        theme: 'bootstrap4',
        resources: {
            youtube: 'https://www.youtube.com/iframe_api',
            vimeo: 'https://player.vimeo.com/api/player.js',
        },
        apiParameter: {
            youtube: {},
            vimeo: {}
        }
    };

})
(jQuery, window, document);
