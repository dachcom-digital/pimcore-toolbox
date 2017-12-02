# Javascript Plugins
We're providing some helpful Javascript Plugins to simplify your daily work with FormBuilder.
Of course it's up to you to copy those files into your project and modify them as required.

> Note: Be sure that jQuery has been initialized, before you load formbuilder.js.

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
       },
       videoHandler: {
           enabled: true,
           config: {
               videoIdExtractor: {
                   custom: function (videoId) {
                       console.log(videoId);
                       return videoId;
                   }
               }
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
      }
    });
});
```

### Options
--