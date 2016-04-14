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

        isBusy : false,

        lastWindowPosition : {},
        $doc: $(document),

        editMode : typeof _PIMCORE_EDITMODE !== 'undefined' && _PIMCORE_EDITMODE === true,

        config: {

            debug: false,
            settings : {}

        },

        init: function (options) {

            jQuery.extend(self.config, options);

            self.startSystem();

        },

        startSystem: function () {

            this.setupParallaxImages();

            this.setupParallaxVideos();

            $(window).on('scroll.toolbox', this.onScroll.bind(this));
            $(window).on('resize.toolbox', this.onResize.bind(this));

        },

        setupParallaxImages: function() {

        },

        setContentHeight : function() {

            var _self = this;

            $('.toolbox-parallax-container').each( function() {

                var isMinHeight = $(this).hasClass('window-full-height') && !_self.editMode,
                    $content = $(this).find('.slick-initialized').length > 0 ? $(this).find('.slick-slider') : $(this).find('.content'),
                    forceContentHeight = false;

                $(this).find('.content').css('min-height', '');
                $(this).find('.background').css('min-height', '');

                var $contentHeight = $content.outerHeight();
                if( isMinHeight && $content.outerHeight() < $(window).outerHeight() ) {
                    $contentHeight = $(window).height();
                    forceContentHeight = true;

                }

                if( _self.editMode ) {

                    $(this).find('.parallax-video > .inner').css('height', $contentHeight + 200 );

                } else {

                    $(this).find('.parallax-video > .inner > div').css('height', $contentHeight);

                }

                if( forceContentHeight ) {
                    $(this).find('.content').css('min-height', $contentHeight);
                } else {
                    $(this).find('.content').css('min-height', '');
                }

                $(this).find('.background').css('min-height', $contentHeight);

            });

        },

        setupParallaxVideos : function(){

            var _self = this, approaches = 0;

            if( this.editMode ) {

                var $v = $('.toolbox-parallax-container');

                if( $v.length > 0 ) {

                    var interval = setInterval(function() {

                        if( $v.find('.parallax-video > .inner > div').length > 0 ) {
                            clearInterval(interval);
                            _self.setContentHeight();
                        } else if( approaches > 10 ) {
                            clearInterval(interval);
                            _self.setContentHeight();
                        }

                        approaches++;

                    }, 50);


                } else {

                    _self.setContentHeight();
                }

            } else {

                _self.setContentHeight();
            }

        },

        onScroll : function(ev) {

            var _self = this;

            if( typeof Modernizr !== 'undefined' && Modernizr.touchevents ) return;

            var $parallaxContainers = $('.toolbox-parallax-container:not(.window-full-height) .parallax-container-image');

            $parallaxContainers.each(function() {

                var $el = $(this),
                    scrollTop = _self.$doc.scrollTop() - $el.offset().top,
                    pos = typeof $el.data('pos') == 'undefined' ? -50 : $el.data('pos'),
                    lastWindowPosition = typeof $el.data('lastWindowPosition') == 'undefined' ? 0 : $el.data('lastWindowPosition');

                pos += (scrollTop - lastWindowPosition) * .3;

                $el.data('lastWindowPosition', scrollTop);
                $el.data('pos', pos);

                $el.find('.canvas').css('background-position-y', pos + 'px');

            });

        },

        onResize : function(){

            this.setContentHeight();

        }

    };

    // API
    return {

        init: self.init

    }

})();

$(document).ready(DachcomToolbox.init.bind({debug: false, settings : null }));