# Upgrade Notes
![upgrade](https://user-images.githubusercontent.com/700119/31535145-3c01a264-affa-11e7-8d86-f04c33571f65.png)  

***

After every update you should check the pimcore extension manager. 
Just click the "update" button or execute the migration command to finish the bundle update.

#### Update from Version 2.7.0 to Version 2.7.1
- **[NEW FEATURE]**: Google Map API Key now configurable via systemsettings


#### Update from Version 2.6.x to Version 2.7.0
- **[ATTENTION]**: Installer has moved to the [MigrationBundle](https://github.com/dachcom-digital/pimcore-toolbox/issues/89). After updating to this version you need to enable this extension again!
- **[NEW FEATURE]**: Parameter for video elements (e.g. youtube api parameters)
- ([Milestone for 2.7.0](https://github.com/dachcom-digital/pimcore-toolbox/milestone/12?closed=1))

#### Update from Version 2.6.4 to Version 2.6.5
- **[NEW FEATURE]**: Allow Column Adjuster Context-based Configuration
- **[NEW FEATURE]**: Pimcore 5.6.0 ready.

#### Update from Version 2.6.3 to Version 2.6.4
- **[NEW FEATURE]**: GoogleMaps-Areabrick now supports Server API Key (systemconfig)

#### Update from Version 2.6.2 to Version 2.6.3
- **[NEW FEATURE]**: Pimcore 5.5.0 ready.
- **[VIEW CHANGES]**: Instead of `/admin/toolbox-ckeditor-style.js` you need to add the `toolbox_get_ckeditor_config_path()` twig helper method.
  - Changed View: `views/Toolbox/Snippet/teaser-default.html.twig`
  - Changed View: `views/Toolbox/Bootstrap3/Content/view.html.twig`
  - Changed View: `views/Toolbox/Bootstrap4/Content/view.html.twig`
- **[NEW FEATURE]**: CK-Editor is Context Ready. You can now use different CK-Editor configurations per context. Checkout the [context docs](docs/15_Context.md) to checkout the updated context resolver.
- ([Milestone for 2.6.3](https://github.com/dachcom-digital/pimcore-toolbox/milestone/11?closed=1))

#### Update from Version 2.6.1 to Version 2.6.2
- implemented [PackageVersionTrait](https://github.com/pimcore/pimcore/blob/master/lib/Extension/Bundle/Traits/PackageVersionTrait.php)
- Fix ColumnCalculator Issuse (https://github.com/dachcom-digital/pimcore-toolbox/pull/75)
- Allow changing video label (https://github.com/dachcom-digital/pimcore-toolbox/pull/76)

#### Update from Version 2.6.0 to Version 2.6.1
- **[NEW FEATURE]**: Pimcore 5.4.0 ready.
- Fix Composer PSR-4 Path

#### Update from Version 2.5.0 to Version 2.6.0
- **[NEW FEATURE]**: Pimcore 5.3.0 ready. Pimcore changed the areablock behaviour and so did we. It's backwards compatible since
  we're only loading the styling adoptions in Pimcore >= 5.3.0.
- **[NEW FEATURE]**: Image Link. There is a new config element `image_link`. If you already have added a image link in your existing project
  and it's not called `image_link`, you should disable the new config element otherwise there will be two of them:
  
```yaml
toolbox:
    areas:
        image:
            config_elements:
                image_link: ~ # disable this one
```

#### Update from Version 2.4.0 to Version 2.5.0

- **[HOTFIX]**: In some cases pimcore will remove links within the validity check without any response. If you're using object links within the DynamicLink element you should update immediately!
- **[BC BREAK]**: Use the `use_dynamic_links` Flag if you still want to use the dynamicLink element:

    ```yml
    toolbox:
        flags:
            use_dynamic_links: true
    ```
    Please [read more here](./docs/70_ConfigurationFlags.md#-use_dynamic_links-flag) if you want to migrate from dynamic links to the pimcore default links (highly recommended)

#### Update from Version 2.4.0 to Version 2.4.1
- **[NEW FEATURE]**: Introduce new `iFrame` element, see [docs](docs/11_ElementsOverview.md#iframe)
- **[BUGFIX]**: only load icon if area type is internal

#### Update from Version 2.3.0 to Version 2.4.0

- **[BUGFIX]**: Fix private service request in column adjuster
- **[BUGFIX]**: Don't throw exception if no google maps key has been defined in system settings
- **[IMPROVEMENT]**: improve google maps route link renderer, see [docs](docs/11_ElementsOverview.md#route-link-renderer)
- **[NEW FEATURE]**: Introduce `additionalClassesChained` type, see [docs](docs/11_ElementsOverview.md#additional-classes)
  - **Important**: If you have changed any views in `views/Areas/*/view.html.twig` check them against the original ones and adapt the changes!

#### Update from Version 2.2.x to Version 2.3.0

> Please check the symfony deprecation log. There are some deprecations which will be removed in TB 3.0

- **[BUGFIX]**: check auto play option in video element against boolean not integer
- **[IMPROVEMENT]**: Load backend google maps key for google map element if available
- **[IMPROVEMENT]**: Better area availability configuration:
  - `disallowed_subareas` is deprecated and will be removed in TB 3.0, use `areas_appearance` instead
  - `disallowed_content_snippet_areas` is deprecated and will be removed in TB 3.0, use `snippet_areas_appearance` instead
- **[IMPROVEMENT]**: Calculators need to be a [tagged service](docs/30_ToolboxTheme.md) now.
  - The config node `theme.calculators.ToolboxBundle\Calculator\*Calculator` is deprecated and will be removed in TB 3.0. Use `theme.calculators.column_calculator` or `theme.calculators.slide_calculator` instead.
- **[NEW FEATURE]**: [jQuery Plugins](docs/80_Javascript.md) available!
- **[NEW FEATURE]**: [Context Configuration](docs/15_Context.md) is available!
- **[NEW FEATURE]**: [Helper Commands](docs/2_Commands.md) available!
- **[BC BREAK]**: `toolbox-googleMaps.js` has been moved to `bundles/toolbox/js/frontend/legacy`. This file is now deprecated and will be removed in Version 3.0.0!
- **[BC BREAK]**: `toolbox-main.js` has been moved to `bundles/toolbox/js/frontend/legacy`. This file is now deprecated and will be removed in Version 3.0.0!
- **[BC BREAK]**: `toolbox-video.js` has been moved to `bundles/toolbox/js/frontend/legacy`. This file is now deprecated and will be removed in Version 3.0.0!
- **[BC BREAK]**: `vimeo-api.min.js` has been marked as deprecated and will be removed in Version 3.0.0! The toolbox-video extension will include the recent api by itself.

#### Update from Version 2.1.x to Version 2.2.0
**[BC BREAK]**: Use the `strict_column_counter` Flag if you're using offset columns! Read more about it [here](docs/70_ConfigurationFlags.md#strict_column_counter-flag)

- [Column Adjuster](docs/60_ColumnAdjuster.md) added
- Google Maps: Allow deactivation of InfoWindow per location
- In case you're using a custom column calculator: Please check the new additions before upgrading to toolbox 2.2.0:
  - The ColumnCalculator now needs the [ConfigManager](https://github.com/dachcom-digital/pimcore-toolbox/blob/master/src/ToolboxBundle/Calculator/Bootstrap4/ColumnCalculator.php#L18)!
  - Update `calculateColumns` [Method](https://github.com/dachcom-digital/pimcore-toolbox/blob/master/src/ToolboxBundle/Calculator/Bootstrap4/ColumnCalculator.php#L24)
  - Add `getColumnInfoForAdjuster` [Method](https://github.com/dachcom-digital/pimcore-toolbox/blob/master/src/ToolboxBundle/Calculator/Bootstrap4/ColumnCalculator.php#L139)

#### Update from Version 2.x to Version 2.1.0
The Bootstrap4 Layout is now enabled by default. If you still need B3 you need to add some params to your config:

```yaml
# set theme to bootstrap 3 and add all the default wrapper elements.
toolbox:
    theme:
        layout: 'Bootstrap3'
        # set b3 column calculators
        calculators:
            ToolboxBundle\Calculator\ColumnCalculator: ToolboxBundle\Calculator\Bootstrap3\ColumnCalculator
            ToolboxBundle\Calculator\SlideColumnCalculator: ToolboxBundle\Calculator\Bootstrap3\SlideColumnCalculator
        wrapper:
            image:
                - {tag: 'div', class: 'row'}
                - {tag: 'div', class: 'col-xs-12'}
            columns:
                - {tag: 'div', class: 'row'}
            gallery:
                - {tag: 'div', class: 'row'}
                - {tag: 'div', class: 'col-xs-12 col-gallery'}
            slideColumns:
                - {tag: 'div', class: 'row'}
            teaser:
                - {tag: 'div', class: 'row'}
                - {tag: 'div', class: 'col-xs-12'}
                
# if you're using the slideColumns: set column classes based on b3 classes.
toolbox:
    areas:
        slideColumns:
            config_parameter:
                column_classes:
                    '2': 'col-xs-12 col-sm-6'
```

#### Update from Version 1.x to Version 2.0.0
- rename globallink to dynamiclink:
```sql
UPDATE documents_elements SET type = 'dynamiclink' WHERE type = 'globallink';
```

- Shorten additional classes:
```sql
UPDATE documents_elements SET `name` = REPLACE(`name`, 'accordionAdditionalClasses', 'add_classes');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'containerAdditionalClasses', 'add_classes');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'contentAdditionalClasses', 'add_classes');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'downloadAdditionalClasses', 'add_classes');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'galleryAdditionalClasses', 'add_classes');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'googleMapAdditionalClasses', 'add_classes');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'headlineAdditionalClasses', 'add_classes');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'imageAdditionalClasses', 'add_classes');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'linklistAdditionalClasses', 'add_classes');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'parallaxContainerAdditionalClasses', 'add_classes');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'separatorContainerAdditionalClasses', 'add_classes');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'columnsAdditionalClasses', 'add_classes');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'spacerAdditionalClasses', 'add_classes');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'teaserAdditionalClasses', 'add_classes');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'videoContainerAdditionalClasses', 'add_classes');
```

- Upgrade element config names:

```sql
UPDATE documents_elements SET `name` = REPLACE(`name`, 'addlCls', 'add_classes');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'anchorName', 'anchor_name');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'anchorTitle', 'anchor_title');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'equalHeight', 'equal_height');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'fullWidthContainer', 'full_width_container');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'showPreviewImages', 'show_preview_images');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'showFileInfo', 'show_file_info');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'useLightbox', 'use_light_box');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'useThumbnails', 'use_thumbnails');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'mapZoom', 'map_zoom');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'mapType', 'map_type');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'iwOnInit', 'iw_on_init');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'headlineType', 'headline_type');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'headlineText', 'headline_text');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'showCaption', 'show_caption');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'backgroundImage', 'background_image'); #parallax
UPDATE documents_elements SET `name` = REPLACE(`name`, 'backgroundColor', 'background_color'); #parallax
UPDATE documents_elements SET `name` = REPLACE(`name`, 'imageFront', 'image_front');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'imagesBehind', 'image_behind');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'containerType', 'container_type');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'slidesPerView', 'slides_per_view');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'spacerClass', 'spacer_class');
```

- clear `cache` and `cache_tags` tables.
