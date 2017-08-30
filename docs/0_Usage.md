# Usage

Some important advices if you're going to use this Bundle in your Projekt.

## Area Brick

![bildschirmfoto 2017-08-30 um 08 43 46](https://user-images.githubusercontent.com/700119/29858787-6198a2c4-8d5f-11e7-8376-8c3acd9d267f.png)

The Toolbox Bundle will help you to display area bricks with some nice additions.

### Area Brick Configuration

Be sure, that every `pimcore_areablock` implements the `toolbox_areablock_config()` element. 
If you miss this, the toolbar will get messy.

```twig
 <main>
    {{ pimcore_areablock('mainContentBlock', toolbox_areablock_config()) }}
</main>
```

Of course, you're able to extend the toolbar configuration:

```yaml
areaBlockConfiguration:
    toolbar:
        title: 'Your Toolbar Title'
        width: 200
        x: 10
        y: 125
        buttonWidth: 200
        
    # define custom groups. Note: The "toolbox" group will be generated automatically.
    groups:
        -
            name: Project
            elements:
                - your_custom_area_brick

# define which elements should not appear in snippet documents
disallowedContentSnippetAreas:
    - parallaxContainer
    - teaser
    - container
    - snippet
    - accordion
    - anchor
    - container
    - teaser        
```