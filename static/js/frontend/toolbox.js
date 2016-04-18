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

        $doc: $(document),

        isBusy : false,

        lastWindowPosition : {},

        editMode : typeof _PIMCORE_EDITMODE !== 'undefined' && _PIMCORE_EDITMODE === true,

        parallax : {},

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

            this.setupParallaxImages();
            this.setupParallaxVideos();

            $(document).on('toolbox.checkContentHeights', this.checkContentHeight.bind(this));

            $(window).on('scroll.toolbox', this.onScroll.bind(this));
            $(window).on('resize.toolbox', this.onResize.bind(this));

        },

        setupParallaxImages: function() {

        },

        setupParallaxVideos : function() {

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

        checkContentHeight : function() {

            var _self = this;

            this.parallax.containerElements.each( function() {

                var $el = $(this);
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

            var isMinHeight = $el.hasClass('window-full-height') && !this.editMode,
                forceContentHeight = false;

            $el.find('.parallax-video > .inner > div').css('height', '');

            $el.find('.content').css('min-height', '');
            $el.find('.background').css('min-height', '');

            var $contentHeight = $content.outerHeight();
            if( isMinHeight && $content.outerHeight() < $(window).outerHeight() ) {
                $contentHeight = $(window).height();
                forceContentHeight = true;
            }

            $el.find('.parallax-video > .inner > div').css('height', $contentHeight);

            if( forceContentHeight ) {
                $el.find('.content').first().css('min-height', $contentHeight);
            } else {
                $el.find('.content').first().css('min-height', '');
            }

            $el.find('.background').css('min-height', $contentHeight);

        },

        onScroll : function(ev) {

            var _self = this;

            if( typeof Modernizr !== 'undefined' && Modernizr.touchevents ) return;

            $('.toolbox-parallax-container .parallax-container-image .canvas').parallaxScroll({
                friction: 0.5
            });

        },

        getParallaxContentElement : function( $el )
        {
            if( $el.find('.slick-slider').length > 0 ) {
                return $el.find('.slick-slider');
            }

            return $el.find('.content').first();

        },

        onResize : function(){

            this.checkContentHeightOnScroll();

        }

    };

    // API
    return {

        init: self.init

    }

})();

$(document).ready(DachcomToolbox.init.bind({debug: false, settings : null }));