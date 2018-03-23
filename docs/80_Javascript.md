# Javascript Plugins
We're providing some helpful Javascript Plugins to simplify your daily work with the ToolboxBundle.
Of course it's up to you to copy those files into your project and modify them as required.

> Note: Be sure that jQuery has been initialized, before you load one of those toolbox extensions.

## Overview
- [Core Plugin](#core-plugin)
- [Google Maps Extension](#google-maps-extension)
- [Video Extension](#video-extension)
- [iFrame Extension](#iframe-extension)
- [Google Opt-Out Extension](#google-opt-out-link-extension)

## Core Plugin
This Plugin will automatically register all toolbox extensions:

### Enable Plugin

```html
<script type="text/javascript" src="{{ asset('bundles/toolbox/js/frontend/plugins/jquery.tb.core.js') }}"></script>
```

```javascript
$(function () {
   $.toolboxCore({});
});
```

### Extended Usage
```javascript
$(function () {
   $.toolboxCore({
       editmode: false,
       theme: 'bootstrap4',
       googleMapsHandler: {
           enabled: true,
           selector: '.toolbox-googlemap',
           config: {
                centerMapOnResize: true
           }
       },
       videoHandler: {
           enabled: true,
           config: {
               videoIdExtractor: {
                   custom: function (videoId) {
                       console.log(videoId);
                       return videoId;
                   }
               },
               apiParameter: {
                   youtube: {
                       rel: 0 //disable related videos
                   },
                   vimeo: {}
               }
           }
       },
       iframeHandler: {
           enabled: true,
           config: {
           }
       }
    });
});
```

## Google Maps Extension
This Extension will enable the google maps rendering. 
If you're using the `toolboxCore` instance, you only need to include the javascript file:

### Enable Extension
```html
<script type="text/javascript" src="{{ asset('bundles/toolbox/js/frontend/plugins/jquery.tb.ext.google-maps.js') }}"></script>
```

Single Call (after new elements has been dynamically added for example)
```javascript
$(function () {
    $('.toolbox-googlemap').toolboxGoogleMaps({});
});
```

### Extended Usage
```javascript
$(function () {
   $.toolboxGoogleMaps({
       centerMapOnResize: true,
       theme: 'bootstrap4'
    });
});
```

### Options
--

## Video Extension
This Extension will enable the video rendering. 
If you're using the `toolboxCore` instance, you only need to include the javascript file:

### Enable Extension
```html
<script type="text/javascript" src="{{ asset('bundles/toolbox/js/frontend/plugins/jquery.tb.ext.video.js') }}"></script>
```

Single Call (after new elements has been dynamically added for example)

```javascript
$(function () {
    $('.toolbox-video').toolboxVideo({});
});
```

### Extended Usage
```javascript
$(function () {
   $.toolboxVideo({
       theme: 'bootstrap4',
       videoIdExtractor: {
          custom: function (videoId) {
              console.log(videoId);
              return videoId;
          }
      },
      resources: {
          youtube: 'https://www.youtube.com/iframe_api',
          vimeo: 'https://player.vimeo.com/api/player.js',
      },
      apiParameter: {
          youtube: {
              rel: 0 //disable related videos
          },
          vimeo: {}
      }
    });
});
```

### Video Extended I: Open Video in a Light Box
If have selected the Light Box option you need to take care about the video by yourself:

```javascript
$('.toolbox-video')
    .on('toolbox.video.youtube.lightbox', function (ev, params) {
        // implement your own openVideo() function somewhere.
        openVideo('https://youtube.com/watch?v=' + params.videoId);
    })
    .on('toolbox.video.vimeo.lightbox', function (ev, params) {
        // implement your own openVideo() function somewhere.
        openVideo('https://vimeo.com/' + params.videoId);
    });
```


### Video Extended II: Use Pimcore Assets as Video
If you're using pimcore video assets, you need to provide your own player api.
Pimcore will render a default html5 video tag in frontend.

If you want to add the autoplay function, you need to add a play and pause event:

```javascript
$('.toolbox-video[data-type="asset"]')
    .on('toolbox.video.asset.play', function (ev, params) {
        // hit the play button of your html5 video.
        // this is also the place where to trigger the play state for your custom framework (video.js for example)
        console.log($(this).find('video'))
        $(this).find('video').get(0).play();
    })
    .on('toolbox.video.asset.pause', function (ev, params) {
        // hit the pause button of your html5 video.
        $(this).find('video').get(0).pause();
    });
```

### Video Extended III: Use a custom player engine
If you have a different engine, you need to do some further work.

#### Add some markup
```twig
<div class="col-12">
    <div class="toolbox-element toolbox-video" data-type="custom">
        <div class="video-inner">
            <div class="player" data-play-in-lightbox="false" data-video-uri="Ue80bTM1vmc"></div>
        </div>
    </div>
</div>
```

#### Initialize Player
```javascript
$(function () {
   $.toolboxVideo({
       theme: 'bootstrap4',
       videoIdExtractor: {
          custom: function (videoId) {
              // parse your video id
              console.log(videoId);
              return videoId;
          }
      }
   });
});
```

### Add a Setup Listener
```
$('.toolbox-video[data-type="custom"]')
    .on('toolbox.video.custom.setup', function (ev, videoClass) {
        // setup your element
        console.log(videoClass);
    });
```

## iFrame Extension
This Extension will enable the iFrame rendering.
We can't provide any out-of-the-box solution for changing the iframe height dynamically (cross-domain policy), so you need to take care about that by yourself.

If you're using the `toolboxCore` instance, you only need to include the javascript file:

### Enable Extension
```html
<script type="text/javascript" src="{{ asset('bundles/toolbox/js/frontend/plugins/jquery.tb.ext.iframe.js') }}"></script>
```

### Single Call
```javascript
$(function () {
    $('.toolbox-iframe').toolboxIframe({});
});
```

### Events
There are two events available:

#### Event toolbox.iframe.load

```javascript
$('.toolbox-iframe').on('toolbox.iframe.load', function(ev) {
    console.log($(this), ev);
})
```

#### Event toolbox.iframe.loaded

```javascript
$('.toolbox-iframe').on('toolbox.iframe.loaded', function(ev) {
    console.log($(this), ev);
    // use the iframe-resizer plugin for example
    // @see https://github.com/davidjbradshaw/iframe-resizer
    // $(this).find('iframe').iFrameResize( [{options}] );
})
```

## Google Opt-Out Link Extension
This Extension searches for google opt-out links.
By clicking on a link with the class `a.google-opt-out-link` a cookie will be stored to prevent future analytic tracking.

If you're using the `toolboxCore` instance, you only need to include the javascript file:

### Enable Extension
```html
<script type="text/javascript" src="{{ asset('bundles/toolbox/js/frontend/plugins/jquery.tb.ext.googleOptOutLink.js') }}"></script>
```

### Single Call
```javascript
$(function () {
    $('a.google-opt-out-link').toolboxGoogleOptOutLink({});
});
```

### Extended Usage
```javascript
$(function () {
   $('a.google-opt-out-link').toolboxGoogleOptOutLink({
       notify: function(message) {
            // implement your message style here
            console.log(message);
       }
    });
});
```