# Upgrade Notes
![upgrade](https://user-images.githubusercontent.com/700119/31535145-3c01a264-affa-11e7-8d86-f04c33571f65.png)  
***
After every update you should check the pimcore extension manager. Just click the "update" button to finish the bundle update.

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
**[BC BREAK]**: Use the `strict_column_counter` Flag if you're using offset columns! Read more about it [here](docs/70_ConfgurationFlags.md#strict_column_counter-flag)

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
