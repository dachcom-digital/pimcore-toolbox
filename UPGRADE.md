# Upgrade Notes

#### Update from Version 1.6.x to Version 1.7.0
 
*All Area Elements*  
- All `edit.php` files for areas has been suspended ([#14](https://github.com/dachcom-digital/pimcore-toolbox/issues/14)). There is only one custom edit window for each element.
- The Image `Extra CSS-Class` property has been removed. Please use the `additional classes` functionality.
- The Brick Wrapper (`.pimcore_area_content`) has been removed ([#16](https://github.com/dachcom-digital/pimcore-toolbox/issues/16)). Please check your project CSS after updating!

*Video*  
- New video configuration in `toolbox_configuration.php`:
     ```php
     "video" => [
         "videoOptions" => [
             "youtube" => [
                 "posterImageThumbnail" => NULL,
             ]
         ],
         (...)
     ]
     ```
 
- Changes in DB table `document_elements`. All elements with type `video` should changed to `vhs`. Use this sql script:
 ``` sql
 UPDATE documents_elements SET type = 'vhs' WHERE type = 'video';
 ```

*Parallax Container*  
Because the parallax container has been changed from ground up, 
you need to check your previous installation before upgrading ([#15](https://github.com/dachcom-digital/pimcore-toolbox/issues/15)).
- remove the `parallaxContainer` array element from `var/config/toolbox_configuration.php`, a new configuration element will be shipped via the default configuration.

*Container*  
Container does now have a ```Fluid Container (Full Width)``` option which renders the default bootstrap ```.fluid-container``` class to the main element.

*Columns*  
We simplified the `scripts/toolbox/columns.php` and `scripts/toolbox/columns/column.php` file. If you've copied that file to your website folder, check the markup!

**New Element:** *Spacer*  
Disable the new Brick, if you don't need it.  
Add this to your `var/config/toolbox_configuration.php` array:

```php
"spacer" => [
    "configElements" => [
        [
            "type"    => "select",
            "name"    => "spacerClass",
            "title"   => "Space Class",
            "values"  => [
                "spacer-none" => "No Space",
                "spacer-50"   => "50 Pixel" //Example
            ],
            "default" => "spacer-none",
            "reload" => FALSE
        ],
        [
            "type"   => "additionalClasses",
            "values" => [],
        ]
    ]
]
```

#### Update from Version 1.5.0 to Version 1.5.1 and Version 1.6.0 to Version 1.6.1
- CKEditor Configuration change:

```php

//BEFORE
[
    "ckeditor" => [
    
        "styles" => [
            [
                'name' => 'test',
                'element' => 'p',
                'attributes' => ['class' => 'h5']
            ]
        ]
    ]
    
]

//AFTER
[
    "ckeditor" => [
    
        "globalStyleSets" => [
            "globalSet1" => [

                [
                    'name' => 'test',
                    'element' => 'p',
                    'attributes' => ['class' => 'globalSet1 A']
                ],
                [
                    'name' => 'test-2',
                    'element' => 'p',
                    'attributes' => ['class' => 'globalSet1 B']
                ]
                    
            ],
        ],
        
        "areaEditor" => [
        
            "uiColor" : "#efefef",
        
            "stylesSet" => [

                [
                    'name' => 'test',
                    'element' => 'p',
                    'attributes' => ['class' => 'custom1']
                ],
                [
                    'name' => 'test-2',
                    'element' => 'p',
                    'attributes' => ['class' => 'custom2']
                ]
                
            ],
        ],
        "objectEditor" => 
            [
                "uiColor" => "yellow",
                "stylesSet" => "globalSet1"
            ]
        ]
        
    ]
]
```

#### Update from Version 1.5.x to Version 1.6
- **Attention!** 1.6 only works with Pimcore 4.4 and above!

#### Update from Version 1.4.x to Version 1.5
- Toolbox\Tools namespace changed to Toolbox\Tool (check & upgrade your website files!)