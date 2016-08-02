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
<?= $this->areablock('content', \Toolbox\Tools\Area::getAreaBlockConfiguration() ); ?>
```
