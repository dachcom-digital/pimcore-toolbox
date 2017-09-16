# Usage

Some important advices if you're going to use this Bundle in your Projekt.

## Area Brick
![bildschirmfoto 2017-08-30 um 08 43 46](https://user-images.githubusercontent.com/700119/29858787-6198a2c4-8d5f-11e7-8376-8c3acd9d267f.png)

The Toolbox Bundle will help you to display area bricks with some nice additions.

#### Area Brick Configuration
**Important!** Be sure, that every `pimcore_areablock` in your project implements the `toolbox_areablock_config()` element. 
If you miss this, the toolbar will get messy.

```twig
 <main>
    {#  
        choose a project wide unique name to dis-/allow elements in your areablock 
        through the toolbox configuration (see section "allow/disallow elements" below
     #}
    {{ pimcore_areablock('mainContentBlock', toolbox_areablock_config('mainContentBlock')) }}
</main>
```

## Toolbar Configuration
Of course, you're able to extend/modify the toolbar configuration:

```yaml
area_block_configuration:
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

```
## Allow/Disallow Elements

#### In Snippets
```yaml
# define which elements should not appear in snippet documents
disallowed_content_snippet_areas:
    - parallaxContainer
    - teaser
    - container
    - snippet
    - accordion
    - anchor
    - container
    - teaser        
```

#### In Area-Blocks

Use the `disallowed_subareas` config node to disable Bricks in specific Areas.

**Example**  
```yaml
toolbox:
    disallowed_subareas:
        container:
            disallowed:
                - container
                - parallaxContainer
        columns:
            disallowed:
                - container
                - parallaxContainer
        mainContentBlock: #this is a project related areablock example, see section "Area Brick Configuration" above.
            disallowed:
                - container
                - yourCustomBrick
```