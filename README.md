# Pimcore Toolbox

## Installation
**Handcrafted Installation**   
Because of additional dependencies you need to install this plugin via composer.

**Composer Installation**  
1. Add code below to your `composer.json`    
2. Activate & install it through backend

Use the [Pimcore Members](https://github.com/dachcom-digital/pimcore-members) Plugin to restrict your downloads (optional).

```json
"require" : {
    "dachcom-digital/toolbox" : "~1.8.0",
}
```
## Asset Management
The AssetHandler is disabled by default. You can either load the required javascript by your self or you're using the build-in asset handler.

**Manual Implementation**  
Add the sources to your `gulpfile.js` or add it with plain html. For example:
```html
<script type="text/javascript" src="/plugins/Toolbox/static/js/frontend/vendor/vimeo-api.min.js"></script>
<script type="text/javascript" src="/plugins/Toolbox/static/js/frontend/toolbox-main.js"></script>
<script type="text/javascript" src="/plugins/Toolbox/static/js/frontend/toolbox-video.js"></script>
<script type="text/javascript" src="/plugins/Toolbox/static/js/frontend/toolbox-googleMaps.js"></script>
```

**AssetHandler**  
Use the AssetHandler to add javascript/css resources to your website. If the debug mode is disabled, it also concatenates and minifies the sources on the fly.
Of course you can also use the AssetHandler to add your custom javascript and css files.

- Enable the AssetHandler in your toolbox_configuration: `enableAssetHandler`:`true`.
- Add this to your `Controller/Action.php`:
```php
\Pimcore::getEventManager()->attach('toolbox.addAsset', function (\Zend_EventManager_Event $e) {
    $assetHandler = $e->getTarget();
    $assetHandler->appendScript('toolbox-vendor-vimeo-api', '/plugins/Toolbox/static/js/frontend/vendor/vimeo-api.min.js', [], ['showInFrontEnd' => TRUE]);
    $assetHandler->appendScript('toolbox-frontend-main', '/plugins/Toolbox/static/js/frontend/toolbox-main.js', [], ['showInFrontEnd' => TRUE]);
    $assetHandler->appendScript('toolbox-frontend-video', '/plugins/Toolbox/static/js/frontend/toolbox-video.js', [], ['showInFrontEnd' => TRUE]);
    $assetHandler->appendScript('toolbox-frontend-google-maps', '/plugins/Toolbox/static/js/frontend/toolbox-googleMaps.js', [], ['showInFrontEnd' => TRUE]);
});
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
.toolbox-video {
    position:relative;
    width: 640px;
    height: 360px;
}

.toolbox-video .poster-overlay {

    background-repeat:no-repeat;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
}

.toolbox-video .poster-overlay .icon {
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
