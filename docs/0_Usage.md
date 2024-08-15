# Usage

Some important advices if you're going to use this bundle in your project.

## Area Brick
![areabrick](https://user-images.githubusercontent.com/700119/29858787-6198a2c4-8d5f-11e7-8376-8c3acd9d267f.png)

The Toolbox Bundle will help you to display area bricks with some nice additions.

### Enable Bricks
Every preconfigured brick needs to be enabled:

```yaml
toolbox:
    enabled_core_areas:
        - accordion
        - anchor
        - columns
        - container
        - content
        - download
        - gallery
        - googleMap
        - headline
        - iFrame
        - image
        - linkList
        - parallaxContainer
        - parallaxContainerSection
        - separator
        - slideColumns
        - snippet
        - spacer
        - teaser
        - video
```

#### Area Brick Configuration
**Important!** Make sure that every `pimcore_areablock` in your project implements the `toolbox_areablock_config()` element. 
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
        width: 200
        buttonWidth: 200
        buttonMaxCharacters: 40
    controlsAlign: 'top'
    controlsTrigger: 'hover'
        
    # define custom groups. Note: The "toolbox" group will be generated automatically.
    groups:
        -
            name: Project
            # optional, set sorting to "manually" to respect given order, otherwise sorting will be alphabetically
            sorting: !php/const ToolboxBundle\Manager\AreaManagerInterface::BRICK_GROUP_SORTING_MANUALLY
            elements:
                - your_custom_area_brick

```
## Allow/Disallow Elements

#### Globally
You're able to disable editables completely:

```yaml
toolbox:
    areas:
        accordion:
            enabled: false
```

If you want to disable any area from third party bundles (for example the members brick) just use their brick id to disable them:
```yaml
    areas:
        members_login:
            enabled: false
```

#### In Snippets

Use the `snippet_areablock_restriction` config node to disable bricks in specific snippets.

```yaml
# define which elements should not appear in snippet documents
toolbox:
    snippet_areablock_restriction:
        snippet_c:
            disallowed:
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

Use the `areablock_restriction` config node to disable bricks in specific areas.

**Example**  
```yaml
toolbox:
    areablock_restriction:
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
