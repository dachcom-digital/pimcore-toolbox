# Pimcore Toolbox

The Toolbox is a Kickstarter for your every day project. It provides some important bricks and structure basics which allow rapid and quality-oriented web development. 

[![Software License](https://img.shields.io/badge/license-GPLv3-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Software License](https://img.shields.io/badge/license-DCL-white.svg?style=flat-square&color=%23ff5c5c)](LICENSE.md)
[![Latest Release](https://img.shields.io/packagist/v/dachcom-digital/toolbox.svg?style=flat-square)](https://packagist.org/packages/dachcom-digital/toolbox)
[![Tests](https://img.shields.io/github/actions/workflow/status/dachcom-digital/pimcore-toolbox/.github/workflows/codeception.yml?branch=master&style=flat-square&logo=github&label=codeception)](https://github.com/dachcom-digital/pimcore-toolbox/actions?query=workflow%3ACodeception+branch%3Amaster)
[![PhpStan](https://img.shields.io/github/actions/workflow/status/dachcom-digital/pimcore-toolbox/.github/workflows/php-stan.yml?branch=master&style=flat-square&logo=github&label=phpstan%20level%204)](https://github.com/dachcom-digital/pimcore-toolbox/actions?query=workflow%3A"PHP+Stan"+branch%3Amaster)

![Pimcore Toolbox](https://user-images.githubusercontent.com/700119/135613598-a9ef2c69-9a44-41cd-8542-596a0322d3da.png)


### Release Plan

| Release | Supported Pimcore Versions        | Supported Symfony Versions | Release Date | Maintained     | Branch   |
|---------|-----------------------------------|----------------------------|--------------|----------------|----------|
| **5.x** | `11.0`                            | `6.4`                      | 28.09.2023   | Feature Branch | master   |
| **4.x** | `10.5`, `10.6`                    | `5.4`                      | 01.10.2021   | Bugfixes       | 4.x      |
| **3.x** | `6.0` - `6.9`                     | `3.4`, `^4.4`              | 16.07.2019   | Unsupported    | 3.x      |
| **2.8** | `5.4`, `5.5`, `5.6`, `5.7`, `5.8` | `3.4`                      | 30.06.2019   | Unsupported    | 2.8      |
| **1.8** | `4.0`                             | --                         | 28.04.2017   | Unsupported    | pimcore4 |

### Installation  

```json
"require" : {
    "dachcom-digital/toolbox" : "~5.3.0"
}
```

Add Bundle to `bundles.php`:
```php
return [
    ToolboxBundle\ToolboxBundle::class => ['all' => true],
];
```

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
It's not an Avada Theme. While the Toolbox provides some basic Javascript for you, you need to implement and mostly modify them by yourself.

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
- [Wysiwyg Configuration](docs/13_Wysiwyg_Editor.md)
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
- [Headless Mode](docs/90_Headless.md)

## Pimcore Fixes / Overrides
- Fix the pimcore iframe [maskFrames](public/js/document/edit.js) bug (in some cases the iframe overlay field does not apply to the right position)
- Transforms all the brick config buttons (`pimcore_area_edit_button_*`) to more grateful ones.

## Upgrade Info
Before updating, please [check our upgrade notes!](UPGRADE.md)

## License
**DACHCOM.DIGITAL AG**, Löwenhofstrasse 15, 9424 Rheineck, Schweiz  
[dachcom.com](https://www.dachcom.com), dcdi@dachcom.ch  
Copyright © 2025 DACHCOM.DIGITAL. All rights reserved.  

For licensing details please visit [LICENSE.md](LICENSE.md)  
