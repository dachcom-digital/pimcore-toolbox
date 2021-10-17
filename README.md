# Pimcore Toolbox

The Toolbox is a Kickstarter for your every day project. It provides some important bricks and structure basics which allow rapid and quality-oriented web development. 

[![Join the chat at https://gitter.im/pimcore/pimcore](https://img.shields.io/gitter/room/pimcore/pimcore.svg?style=flat-square)](https://gitter.im/pimcore/pimcore)
[![Software License](https://img.shields.io/badge/license-GPLv3-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Release](https://img.shields.io/packagist/v/dachcom-digital/toolbox.svg?style=flat-square)](https://packagist.org/packages/dachcom-digital/toolbox)
[![Tests](https://img.shields.io/github/workflow/status/dachcom-digital/pimcore-toolbox/Codeception/master?style=flat-square&logo=github&label=codeception)](https://github.com/dachcom-digital/pimcore-toolbox/actions?query=workflow%3ACodeception+branch%3Amaster)
[![PhpStan](https://img.shields.io/github/workflow/status/dachcom-digital/pimcore-toolbox/PHP%20Stan/master?style=flat-square&logo=github&label=phpstan%20level%204)](https://github.com/dachcom-digital/pimcore-toolbox/actions?query=workflow%3A"PHP+Stan"+branch%3Amaster)

![Pimcore Toolbox](https://user-images.githubusercontent.com/700119/135613598-a9ef2c69-9a44-41cd-8542-596a0322d3da.png)


### Release Plan

| Release | Supported Pimcore Versions        | Supported Symfony Versions | Release Date | Maintained     | Branch     |
|---------|-----------------------------------|----------------------------|--------------|----------------|------------|
| **4.x** | `10.1` - `10.2`                   | `5.3`                      | 01.10.2021   | Feature Branch | master     |
| **3.x** | `6.0` - `6.9`                     | `3.4`, `^4.4`              | 16.07.2019   | Unsupported    | 3.x        |
| **2.8** | `5.4`, `5.5`, `5.6`, `5.7`, `5.8` | `3.4`                      | 30.06.2019   | Unsupported    | 2.8        |
| **1.8** | `4.0`                             | --                         | 28.04.2017   | Unsupported    | pimcore4   |

### Installation  

```json
"require" : {
    "dachcom-digital/toolbox" : "~4.0.0"
}
```

- Execute: `$ bin/console pimcore:bundle:enable ToolboxBundle`
- Execute: `$ bin/console pimcore:bundle:install ToolboxBundle`

## Upgrading
- Execute: `$ bin/console doctrine:migrations:migrate --prefix 'ToolboxBundle\Migrations'`

## What's the meaning of Toolbox?
- create often used bricks in a second
- extend, override toolbox bricks 
- add config elements via yml configuration
- add consistent and beautiful config elements
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
- [CK-Editor Configuration](docs/13_CkEditor.md)
- [Image Thumbnails Strategy](docs/14_ImageThumbnails.md)
- [Configuration Context](docs/15_Context.md)
- [Editable Store Provider](docs/16_EditableStoreProvider.md)
- [Create a Custom Brick](docs/10_CustomBricks.md)
- [Theme / Layout](docs/30_ToolboxTheme.md)
- [Overriding Views](docs/31_OverridingViews.md)
- [Data Attributes Generator](docs/40_DataAttributesGenerator.md)
- [Column Adjuster](docs/60_ColumnAdjuster.md)
- [Configuration Flags](docs/70_ConfigurationFlags.md)
- [Javascript Plugins](docs/80_Javascript.md)

## Pimcore Fixes / Overrides
- Fix the pimcore iframe [maskFrames](src/ToolboxBundle/Resources/public/js/document/edit.js) bug (in some cases the iframe overlay field does not apply to the right position)
- Transforms all the brick config buttons (`pimcore_area_edit_button_*`) to more grateful ones.

## Copyright and license
Copyright: [DACHCOM.DIGITAL](http://dachcom-digital.ch)  
For licensing details please visit [LICENSE.md](LICENSE.md)  

## Upgrade Info
Before updating, please [check our upgrade notes!](UPGRADE.md)
