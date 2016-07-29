# Pimcore Toolbox

## Usage

**Bricks**

If you're using an AreaBlock Brick in your View, use this method to get grouped elements in toolbar (if configured):

```php
<?= $this->areablock('content', \Toolbox\Tools\Area::getAreaBlockConfiguration() ); ?>
```