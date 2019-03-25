# Pimcore 5 Toolbox

The Toolbox is a Kickstarter for your every day project. It provides some important bricks and structure basics which allow rapid and quality-oriented web development. 

[![Join the chat at https://gitter.im/pimcore/pimcore](https://img.shields.io/gitter/room/pimcore/pimcore.svg?style=flat-square)](https://gitter.im/pimcore/pimcore)
[![Software License](https://img.shields.io/badge/license-GPLv3-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Release](https://img.shields.io/packagist/v/dachcom-digital/toolbox.svg?style=flat-square)](https://packagist.org/packages/dachcom-digital/toolbox)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/dachcom-digital/pimcore-toolbox.svg?style=flat-square)](https://www.scrutinizer-ci.com/g/dachcom-digital/pimcore-toolbox)
[![Travis](https://img.shields.io/travis/com/dachcom-digital/pimcore-toolbox/master.svg?style=flat-square)](https://travis-ci.com/dachcom-digital/pimcore-toolbox)
[![PhpStan](https://img.shields.io/badge/PHPStan-level%202-brightgreen.svg?style=flat-square)](#)

![bildschirmfoto 2017-06-21 um 09 30 29](https://user-images.githubusercontent.com/700119/27372271-541e6106-5664-11e7-9159-7f4aefa26cb6.png)

## Requirements
* Pimcore 5.

#### Pimcore 4 
Get the Pimcore4 Version [here](https://github.com/dachcom-digital/pimcore-toolbox/tree/pimcore4).

### Installation  

```json
"require" : {
    "dachcom-digital/toolbox" : "~2.7.0"
}
```

### Installation via Extension Manager
After you have installed the Toolbox Bundle via composer, open pimcore backend and go to `Tools` => `Extension`:
- Click the green `+` Button in `Enable / Disable` row
- Click the green `+` Button in `Install/Uninstall` row

## Upgrading

### Upgrading via Extension Manager
After you have updated the Toolbox Bundle via composer, open pimcore backend and go to `Tools` => `Extension`:
- Click the green `+` Button in `Update` row

### Upgrading via CommandLine
After you have updated the Toolbox Bundle via composer:
- Execute: `$ bin/console pimcore:bundle:update ToolboxBundle`

### Migrate via CommandLine
Does actually the same as the update command and preferred in CI-Workflow:
- Execute: `$ bin/console pimcore:migrations:migrate -b ToolboxBundle`

## What's the meaning of Toolbox?
- create often used bricks in a second
- extend, override toolbox bricks 
- add config elements via yml configuration
- add consistent and beautiful config elements
- implement conditions to your config (for example: display a dropdown in config window if another checkbox has been checked)
- add your custom bricks while using the toolbox config environment
- removes the default `pimcore_area_*` element wrapper from each brick

## And what's not?
- It's not an Avada Theme. While the Toolbox provides some basic Javascript for you, you need to implement and mostly modify them by yourself.
- Toolbox supports only the twig template engine, so there is no way to activate the php template engine (and there will never be such thing).

**Frontend JS Implementation**  
We're providing some helpful Javascript Plugins to simplify your daily work with the ToolboxBundle. 
Read more about the javascript implementation [here](docs/80_Javascript.md).

## Available Toolbox Bricks 

The Toolbox provides a lot of [ready-to-use Bricks](docs/11_ElementsOverview.md):

- Accordion
- Anchor
- Columns
- Container
- Content
- Download
- Gallery
- Google Map
- Headline
- iFrame
- Image
- Link List
- Parallax Container
- Parallax Container Section
- Separator
- Slide Columns
- Spacer
- Teaser
- Video

## Additional Editables
- [VHS Video](docs/21_VhsElement.md)
- [Google Maps Element](docs/22_GoogleMapsElement.md)

## Further Information
- [Important Usage Information](docs/0_Usage.md)
- [Code Style](docs/1_CodeStyle.md)
- [Helper Commands](docs/2_Commands.md)
- [Toolbox Elements Overview](docs/11_ElementsOverview.md)
- [Conditional Logic in Configuration](docs/12_ConditionalLogic.md)
- [CK-Editor Configuration](docs/13_CkEditor.md)
- [Image Thumbnails Strategy](docs/14_ImageThumbnails.md)
- [Configuration Context](docs/15_Context.md) (New!)
- [Create a Custom Brick](docs/10_CustomBricks.md)
- [Theme / Layout](docs/30_ToolboxTheme.md)
- [Overriding Views](docs/31_OverridingViews.md)
- [Data Attributes Generator](docs/40_DataAttributesGenerator.md)
- [Column Adjuster](docs/60_ColumnAdjuster.md)
- [Configuration Flags](docs/70_ConfigurationFlags.md)
- [Javascript Plugins](docs/80_Javascript.md)

## Pimcore Fixes / Overrides
- fix the pimcore iframe [maskFrames](src/ToolboxBundle/Resources/public/js/document/edit.js#L8) bug (in some cases the iframe overlay field does not apply to the right position)
- Transforms all the brick config buttons (`pimcore_area_edit_button_*`) to more grateful ones.

## Copyright and license
Copyright: [DACHCOM.DIGITAL](http://dachcom-digital.ch)  
For licensing details please visit [LICENSE.md](LICENSE.md)  

## Upgrade Info
Before updating, please [check our upgrade notes!](UPGRADE.md)
