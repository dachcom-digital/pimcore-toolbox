# Upgrade Notes

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