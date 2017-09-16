# VHS Element
Create a Video Element. This Element extends the default pimcore video element.

> Note: This element is also used in the toolbox video element.

## Usage

```twig
{{ pimcore_vhs('video', {
    'attributes': {
        'class': 'video-js vjs-default-skin vjs-big-play-centered',
        'data-setup': '{}'
    },
    'thumbnail': toolbox_get_image_thumbnail('video_thumbnail'),
    'disableProgressReload' : true,
    'height': 250
}) }}
```