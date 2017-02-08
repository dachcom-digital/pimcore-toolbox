# Pimcore Toolbox

## Installation
**Handcrafted Installation**   
1. Download Plugin  
2. Rename it to `Toolbox`  
3. Place it in your plugin directory  
4. Activate & install it through backend 

**Composer Installation**  
1. Add code below to your `composer.json`    
2. Activate & install it through backend

```json
"require" : {
    "dachcom-digital/toolbox" : "~1.6.0",
}
```

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
        
        /*
            The shipped toolbar is not good enough for you? change it here!
        */
         "toolbar" => [
            ["name" => "insert", "items" => [ "Iframe", "Smiley" ] ]
        ],
        
        /*
            String ( append|prepend|replace)
            If you have defined some custom toolbar elements, 
            use this parameter to set the location of your toolbar elements. 
            Append, prepend or replace them with the default values.
        */
        "toolbarModification" => "prepend",
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

**CSS Styles for Video**

The video element needs this minimal css. Copy it to your template and customize it if desired.

```css
.pimcore_area_video .toolbox-video {
    position:relative;
    width: 640px;
    height: 360px;
}

.pimcore_area_video .poster-overlay {

    background-repeat:no-repeat;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
}

.pimcore_area_video .poster-overlay .icon {
    position: absolute;
    left: 50%;
    top: 50%;
    font-size: 70px;
    margin-left: -35px;
    margin-top: -35px;
    color: #fff;
}
```

## Copyright and license
Copyright: [DACHCOM.DIGITAL](http://dachcom-digital.ch)  
For licensing details please visit [LICENSE.md](LICENSE.md)  

## Upgrade Info
Before updating, please [check our upgrade notes!](UPGRADE.md)