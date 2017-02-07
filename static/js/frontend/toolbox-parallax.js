/*
        __           __                                ___       _ __        __
   ____/ /___ ______/ /_  _________  ____ ___     ____/ (_)___ _(_) /_____ _/ /
  / __  / __ `/ ___/ __ \/ ___/ __ \/ __ `__ \   / __  / / __ `/ / __/ __ `/ /
 / /_/ / /_/ / /__/ / / / /__/ /_/ / / / / / /  / /_/ / / /_/ / / /_/ /_/ / /
 \__,_/\__,_/\___/_/ /_/\___/\____/_/ /_/ /_/   \__,_/_/\__, /_/\__/\__,_/_/
                                                       /____/
 copyright © dachcom digital

 */
var DachcomToolboxParallax = (function () {

    'use strict';

    var self = {

        $doc: $ !== undefined ? $(document) : null,

        isBusy : false,

        editMode : false,

        parallax : {},

        init: function() {

            self.parallax.containerElements = $('.toolbox-parallax-container');
            self.editMode = typeof _PIMCORE_EDITMODE !== 'undefined' && _PIMCORE_EDITMODE === true;

            self.startSystem();

        },

        startSystem: function() {

            this.setupParallaxContainer();

            $(document).on('toolbox.checkContentHeights', this.checkContentHeight.bind(this));
            $(window).on('scroll.toolbox', this.onScroll.bind(this));

        },

        setupParallaxContainer: function() {

            var _self = this,
                $parallaxContainer = $('.toolbox-parallax-container:not(.window-full-height) .parallax-container-image .canvas'),
                $v = this.parallax.containerElements;

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

            var $contentHeight = $content.outerHeight(),
                $w = $(window),
                windowHeight = $w.outerHeight(),
                scrollTop = $w.scrollTop(),
                contentOffset = 0;

            if( !this.editMode && $contentHeight > windowHeight ) {

                if ( scrollTop >= $el.offset().top && ($contentHeight + $content.offset().top > scrollTop + windowHeight ) ) {

                    $el.find('.background > .canvas').css('top', 0);

                } else {

                    contentOffset = $el.offset().top - scrollTop;

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
                return false;
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
    };

})();

if( $ !== undefined) {

    $(function() {
        'use strict';
        DachcomToolboxParallax.init();
    });

}