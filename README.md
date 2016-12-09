# Pimcore Toolbox

### Installation
Some installation advices. 

**Override Templates**

To override the Toolbox scripts, just create a toolbox folder in your scripts folder to override templates:
 
 `/website/views/scripts/toolbox/gallery.php`

### Usage

**Bricks**

If you're using an AreaBlock Brick in your View, use this method to get grouped elements in toolbar (if configured):

```php
<?= $this->areablock('content', \Toolbox\Tool\Area::getAreaBlockConfiguration() ); ?>
```

**CKEditor Configuration**

```php
"ckeditor" => 
[
    /*
        Define some custom Style Sets. You may use them in the area / object editor
    */
    "globalStyleSets" => [
    
        "default" => [
    
            [
                "name" => "Button Default",
                "element" => "a",
                "attributes" => [
                    "class" => "btn btn-default"
                ]
            ]
        ],
    ],
    
    /*
        All toolbox area editors which have a custom customConfig
    */
    "areaEditor" => [
    
        /*
            All the CKEDITOR.config properties.
            @see http://docs.ckeditor.com/#!/api/CKEDITOR.config
        */
        "uiColor" => "#efefef",
        
         /*
            Custom styleSets
        */
        "stylesSet" => [
            [
                "name" => "Link",
                "element" => "a",
                "attributes" => [
                    "class" => "custom-link-class"
                ]
            ]
        ],
    ],
    
    /*
        All object editors
    */
    "objectEditor" => [
       
        /*
            Linked to globalStyleSets
        */
        "stylesSet" => "default"
    ]
];
                
```

## Upgrade Info
Before updating, please [check our upgrade notes!](UPGRADE.md)