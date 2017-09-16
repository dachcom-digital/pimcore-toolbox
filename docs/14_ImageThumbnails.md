# Image Thumbnails

To stay organized, the ToolboxBundle provides a dedicated configuration and a simple helper for image thumbnails.
Every required thumbnail size is defined in the [toolbox configuration](https://github.com/dachcom-digital/pimcore-toolbox/blob/master/src/ToolboxBundle/Resources/config/pimcore/image_thumbnails.yml).
So it's easy for you to simplify or change the [Image Thumbnail](https://www.pimcore.org/docs/5.0.0/Assets/Working_with_Thumbnails/Image_Thumbnails.html) reference. 

Although this is just an optional feature, it makes sense to use the view helper in your project and custom bricks.

## Configuration

```yaml
toolbox:
    image_thumbnails:
        # get merged or overrides the toolbox defaults
        gallery_element: 'myOverriddenGalleryThumbnailSize'
        my_thumbnail_size: 'myThumbnailSize'
```

## View Helper:

```twig
{{ asset.getThumbnail(toolbox_get_image_thumbnail('my_thumbnail_size')) }}
```