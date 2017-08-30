# Pimcore 5 Toolbox

The Toolbox is a Kickstarter for your every day project. It provides some important bricks and structure basics which allows rapid and quality-orientated web development. 

![bildschirmfoto 2017-06-21 um 09 30 29](https://user-images.githubusercontent.com/700119/27372271-541e6106-5664-11e7-9159-7f4aefa26cb6.png)


## Requirements
* Pimcore 5. Only with Build 96 or greater.

### What's the meaning of Toolbox?
- create often used bricks in a second
- extend, override toolbox bricks 
- add config elements via yml configuration
- add consistent and beautiful config elements
- implement conditions to your config (for example: display a dropdown in config window if another checkbox has been checked)
- add your custom bricks while using the toolbox config environment
- removes the default `pimcore_area_*` element wrapper from each brick

### And what's not?
- It's not a Avada Theme. While the Toolbox provides some basic Javascript for you, you need to implement and mostly modifiy them by yourself.
- Toolbox supports only the twig template engine, so there is no way to activate the php template engine (and there will never be such thing).

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

## Available Toolbox Bricks 

The Toolbox provides a lot of ready-to-use Bricks:

- Accordion
- Anchor
- Columns
- Container
- Content
- Download
- Gallery
- Google Map
- Headline
- Image
- Link List
- Parallax Container
- Parallax Container Section
- Separator
- Slide Columns
- Spacer
- Teaser
- Video

## Further Information
- [Important Usage Information](docs/0_Usage.md)
- [Create a Custom Brick](docs/10_CustomBricks.md)
- [Dynamic Link](docs/20_DynamicLink.md)
- [Theme / Layout](docs/30_ToolboxTheme.md)
- [Overriding Views](docs/31_OverridingViews.md)
- [Data Attributes Generator](docs/40_DataAttributesGenerator.md)

## Pimcore Fixes / Overrides
- fix the pimcore iframe [maskFrames](src/ToolboxBundle/Resources/public/js/document/edit.js#L8) bug (in some cases the iframe overlay field does not apply to the right position)
- Transforms all the brick config buttons (`pimcore_area_edit_button_*`) to more grateful ones.

## Copyright and license
Copyright: [DACHCOM.DIGITAL](http://dachcom-digital.ch)  
For licensing details please visit [LICENSE.md](LICENSE.md)  

## Upgrade Info
Before updating, please [check our upgrade notes!](UPGRADE.md)
