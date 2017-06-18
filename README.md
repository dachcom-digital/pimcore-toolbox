# Pimcore 5 Toolbox

This Toolbox Repo is for pimcore 5 only and under heavy development. 

## Asset Management

```
/var/www bin/console assets:install --symlink
```

**Frontend JS Implementation**  
Add the sources to your `gulpfile.js` or add it with plain html. For example:
```html
<script type="text/javascript" src="{{ asset('bundles/toolbox/js/frontend/vendor/vimeo-api.min.js')}}" ></script>
<script type="text/javascript" src="{{ asset('bundles/toolbox/js/frontend/toolbox-main.js')}}" ></script>
<script type="text/javascript" src="{{ asset('bundles/toolbox/js/frontend/toolbox-video.js')}}" ></script>
<script type="text/javascript" src="{{ asset('bundles/toolbox/js/frontend/toolbox-googleMaps.js')}}" ></script>
```

## Copyright and license
Copyright: [DACHCOM.DIGITAL](http://dachcom-digital.ch)  
For licensing details please visit [LICENSE.md](LICENSE.md)  

## Upgrade Info
Before updating, please [check our upgrade notes!](UPGRADE.md)