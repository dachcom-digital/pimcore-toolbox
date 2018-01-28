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

Use the `snippet_areas_appearance`  config node to disable Bricks in specific Snippets.

```yaml
# define which elements should not appear in snippet documents
toolbox:
    disallowed_content_snippet_areas:
        snippet_c:
            dissallowed:
                - parallaxContainer
                - teaser
                - container
                - snippet
                - accordion
                - anchor
                - container
                - teaser
        accordion:
            allowed: # if "allowed" is configured the "disallowed" node will be ignored
                - teaser
                - download
```

#### In Area-Blocks

Use the `areas_appearance` config node to disable Bricks in specific Areas.

**Example**  
```yaml
toolbox:
    areas_appearance:
        container:
            allowed: # if "allowed" is configured the "disallowed" node will be ignored
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