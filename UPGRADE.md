# Upgrade Notes

#### Update from Version 1.x to Version 2.0.0
- rename globallink to dynamiclink:
```sql
UPDATE documents_elements SET type = 'dynamiclink' WHERE type = 'globallink';
```

shorten additional classes:
```sql
UPDATE documents_elements SET `name` = REPLACE(`name`, 'accordionAdditionalClasses', 'addlCls');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'containerAdditionalClasses', 'addlCls');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'contentAdditionalClasses', 'addlCls');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'downloadAdditionalClasses', 'addlCls');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'galleryAdditionalClasses', 'addlCls');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'googleMapAdditionalClasses', 'addlCls');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'headlineAdditionalClasses', 'addlCls');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'imageAdditionalClasses', 'addlCls');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'linklistAdditionalClasses', 'addlCls');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'parallaxContainerAdditionalClasses', 'addlCls');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'separatorContainerAdditionalClasses', 'addlCls');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'columnsAdditionalClasses', 'addlCls');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'spacerAdditionalClasses', 'addlCls');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'teaserAdditionalClasses', 'addlCls');
UPDATE documents_elements SET `name` = REPLACE(`name`, 'videoContainerAdditionalClasses', 'addlCls');
```

- clear `cache` and `cache_tags` tables.