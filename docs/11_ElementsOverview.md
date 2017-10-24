# Elements Overview

## Accordion / Tab
Create a accordion or tab element.
It's possible to change the layout from accordion to tabs without loosing the content.

### Available Options

| Name | Type | Description | Default Value | Frontend
|------|------|-----------------------------|---------------|-------------------------------|
| `type` | select | Choose a accordion type | panel-default | `pimcore_select('type')` |
| `component` | select | Choose the component type | accordion | `pimcore_select('component')` |
| `additional_classes` | select | Add custom classes | - | `pimcore_select('add_classes')` |

## Anchor
Create a anchor element.

### Available Options

| Name | Type | Description | Default Value | Frontend
|------|------|-----------------------------|---------------|-------------------------------|
| `anchor_name` | input | Set the anchor name | - | `pimcore_input('anchor_name')` |
| `anchor_title` | input | Set the anchor title | - | `pimcore_input('anchor_title')` |
| `additional_classes` | select | Add custom classes | - | `pimcore_select('add_classes')` |

## Columns

Create (nested) columns.

### Configuration

By default, the toolbox will transform the store values into bootstrap grid values.
If you're using a different grid system, you probably need to change the [grid calculation](30_ToolboxTheme.md).

**Note:** If you're changing the column amounts during a layout change, the nested content may gets lost depending on the new column amount

- Changing from 3 columns to 2 columns: the content of the third column gets lost.
- Changing from 2 columns to 4 columns: the content of the first two columns stays the same.

### Available Options

| Name | Type | Description | Default Value | Frontend
|------|------|-----------------------------|---------------|-------------------------------|
| `type` | select | Set the column type. see example below. | column_6_6 | `pimcore_input('type')` |
| `equal_height` | checkbox | Appends some equal height classes | false | `pimcore_checkbox('equal_height')` |
| `additional_classes` | select | Add custom classes | - | `pimcore_select('add_classes')` |

### Column Calculation Example

```yaml
toolbox:
    areas:
        columns:
            config_elements:
                type:
                    config:
                        store:
                            column_8_4: '2 Columns'
```

`column_8_4` will generates two columns:
 
1. column with `col-xs-12 col-sm-8 col-md-8` 
2. column with `col-xs-12 col-sm-4 col-md-4`
 
It's possible, however, to define custom breakpoint classes and also offset elements:

```yaml
toolbox:
    areas:
        columns:
            config_elements:
                type:
                    config:
                        store:
                            column_4_4_4: '3 Columns (33:33:33)'
                            column_o1_4_o1_5:
                                name: '2 Columns (1 Offset, 33%%, 1 Offset, 40%%, 1 Offset)'
                                breakpoints:
                                    xs: o0_12_o0_12
                                    sm: o0_6_o0_6
                                    md: o1_4_o1_5
                                    lg: o1_4_o1_5
                            column_o2_4_o1_3:
                                name: '2 Columns (2 Offset, 33%%, 1 Offset, 25%%, 2 Offset)'
                                breakpoints:
                                    xs: o0_12_o0_12
                                    sm: o1_5_o1_4
                                    md: o1_5_o1_4
                                    lg: o2_4_o1_3
                            column_4_3_o1_3:
                                name: '3 Columns (33%%, 25%%, 1 Offset, 25%%, 1 Offset)'
                                breakpoints:
                                    xs: 12_12_o0_12
                                    sm: 6_6_o0_12
                                    md: 4_4_o0_4
                                    lg: 4_3_o1_3
```

## Container
Create a container element. Useful if you're using a full width layout.

### Available Options

| Name | Type | Description | Default Value | Frontend
|------|------|-----------------------------|---------------|-------------------------------|
| `full_width_container` | checkbox | Adds the `container-fluid` class | false | `pimcore_checkbox('full_width_container')` |
| `additional_classes` | select | Add custom classes | - | `pimcore_select('add_classes')` |

## Content
Create a wysiwyg editor.

### Available Options

| Name | Type | Description | Default Value | Frontend
|------|------|-----------------------------|---------------|-------------------------------|
| `additional_classes` | select | Add custom classes | - | `pimcore_select('add_classes')` |


## Download
Create download elements.

> Note: If you're using the [MembersBundle](https://github.com/dachcom-digital/pimcore-members), the download element will automatically check for a restriction.

### Available Options

| Name | Type | Description | Default Value | Frontend
|------|------|-----------------------------|---------------|-------------------------------|
| `downloads` | multihref | Add download files / folders | - | `pimcore_multihref('downloads')` |
| `show_preview_images` | checkbox | Display Preview Images | false | `pimcore_checkbox('show_preview_images')` |
| `show_file_info` | checkbox | Display File Info | false | `pimcore_checkbox('show_file_info')` |
| `additional_classes` | select | Add custom classes | - | `pimcore_select('add_classes')` |

### Configuration Parameter
Use the `event_tracker` Parameter to build a helper markup for google events:

**Example**  
```yaml
toolbox:
    areas:
        download:
            config_parameter:
                event_tracker:
                    category: 'PDF'
                    action: 'download'
                    label: ['getMetadata', ['name']]
                    noninteraction: true
```
### View Helper
In `Download/Partial/item.html.twig`, you'll find a `toolbox_download_info()` helper. 
This Twig Extension will generate some download info.

**Usage**:   
```twig
{% set downloadInfo = toolbox_download_info(download, true, 'optimized', 0) %}
```
**Arguments:**  
1. Asset, Download File
1. Bool, Show Preview Image 
1. String, File Size Unit (mb, kb, optimized). Optimized means: get the best matching unit.
4. File Size Precision: Round Precision

## Gallery
Create image galleries.

### Available Options

| Name | Type | Description | Default Value | Frontend
|------|------|-----------------------------|---------------|-------------------------------|
| `images` | multihref | Add images / folders | - | `pimcore_multihref('images')` |
| `use_light_box` | checkbox | Add a `light-box` class and a wrapping link | false | `pimcore_checkbox('use_light_box')` |
| `use_thumbnails` | checkbox | Add a thumbnail slider | false | `pimcore_checkbox('use_thumbnails')` |
| `additional_classes` | select | Add custom classes | - | `pimcore_select('add_classes')` |


## Google Map
Create a Google Map Element. You're able to define one or multiple markers. 
Toolbox will automatically generates the long/lat information after saving the document.
Please be sure that you've included a valid google maps api.

> Note: This is a [custom toolbox element](22_GoogleMapsElement.md).

### Available Options

| Name | Type | Description | Default Value | Frontend
|------|------|-----------------------------|---------------|-------------------------------|
| `map_zoom` | numeric | Map Zoom | 12 | `pimcore_numeric('map_zoom')` |
| `map_type` | select | Map Type (ROADMAP, HYBRID ..) | roadmap | `pimcore_select('map_type')` |
| `iw_on_init` | checkbox | Open Info Window by Default | false | `pimcore_checkbox('iw_on_init')` |
| `additional_classes` | select | Add custom classes | - | `pimcore_select('add_classes')` |

### Configuration Parameter

- Use the `map_options` Parameter to define all the google maps parameter in yaml
- Use the `map_style_url` Parameter to define a custom map style (optional)
- Use the `marker_icon` Parameter to define a custom marker_icon (optional)
- Use the `map_api_key` Parameter to set a map api key (optional). To extend the daily request to 2.500 per day.
**Example**  
```yaml
toolbox:
    areas:
        googleMap:
            config_parameter:
                map_options:
                    streetViewControl: true
                    mapTypeControl: false
                    panControl: false
                    scrollwheel: false
                map_style_url: false
                marker_icon: false
                map_api_key: false
```

## Headline
Create a headline.

### Available Options

| Name | Type | Description | Default Value | Frontend
|------|------|-----------------------------|---------------|-------------------------------|
| `headline_type` | select | Define the headline size | h3 | `pimcore_select('headline_type')` |
| `anchor_name` | input | Define a anchor name | - | `pimcore_input('anchor_name')` |
| `additional_classes` | select | Add custom classes | - | `pimcore_select('add_classes')` |


## Image
Create a image field.

### Available Options

| Name | Type | Description | Default Value | Frontend
|------|------|-----------------------------|---------------|-------------------------------|
| `use_light_box` | checkbox | Add a `light-box` class and a wrapping link | false | `pimcore_checkbox('use_light_box')` |
| `show_caption` | checkbox | Render image caption | false | `pimcore_checkbox('show_caption')` |
| `additional_classes` | select | Add custom classes | - | `pimcore_select('add_classes')` |


## Link List
Create a link list (via pimcore block element).

> Note: This element uses a custom toolbox element: "[dynamic link](20_DynamicLinkElement.md)".

### Available Options

| Name | Type | Description | Default Value | Frontend
|------|------|-----------------------------|---------------|-------------------------------|
| `additional_classes` | select | Add custom classes | - | `pimcore_select('add_classes')` |

## Parallax Container
Build a Parallax Container.

### Available Options

| Name | Type | Description | Default Value | Frontend
|------|------|-----------------------------|---------------|-------------------------------|
| `template` | select | Define a Parallax Template | no-template | `pimcore_select('template')` |
| `background_image` | href | Define a background image | - | `pimcore_href('background_image')` |
| `background_color` | select | Define a background color | no-background-color | `pimcore_select('background_color')` |
| `image_front` | parallaximage | Parallax Images behind content | - | *not available* |
| `image_behind` | parallaximage | Parallax Images in front of content | - | *not available* |
| `additional_classes` | select | Add custom classes | - | `pimcore_select('add_classes')` |

### Configuration Parameter

- Use the `window_size` Parameter to render a larger edit window
- Use the `background_mode` Parameter to define the background mode (wrap, prepend)
- Use the `background_image_mode` Parameter to define the append mode (data, class)
- Use the `background_color_mode` Parameter to define the append mode (data, class)

**Example**  
```yaml
toolbox:
    areas:
        parallaxContainer:
            config_parameter:
                window_size: large
                background_mode: wrap
                background_image_mode: data
                background_color_mode: data
```

## Parallax Container Section

Build a Parallax Container Section.

> Note: This element is only available in a parallax container element.

### Available Options

| Name | Type | Description | Default Value | Frontend
|------|------|-----------------------------|---------------|-------------------------------|
| `template` | select | Define a Parallax Section Template | no-template | `pimcore_select('template')` |
| `container_type` | select | Define a Container Type | none | `pimcore_select('container_type')` |
| `background_image` | href | Define a background image | - | `pimcore_href('background_image')` |
| `background_color` | select | Define a background color | no-background-color | `pimcore_select('background_color')` |
| `additional_classes` | select | Add custom classes | - | `pimcore_select('add_classes')` |

### Configuration Parameter

- Use the `background_image_mode` Parameter to define the append mode (data, class)
- Use the `background_color_mode` Parameter to define the append mode (data, class)

**Example**  
```yaml
toolbox:
    areas:
        parallaxContainerSection:
            config_parameter:
                background_image_mode: data
                background_color_mode: data
```

## Separator
Create a Separator Element (hr)

### Available Options

| Name | Type | Description | Default Value | Frontend
|------|------|-----------------------------|---------------|-------------------------------|
| `space` | select | Add some seperator spacer classes | default | `pimcore_select('space')` |
| `additional_classes` | select | Add custom classes | - | `pimcore_select('add_classes')` |


## Slide Columns
Create a sliding column element.

> Note: You need to implement your own javascript logic

### Available Options

| Name | Type | Description | Default Value | Frontend
|------|------|-----------------------------|---------------|-------------------------------|
| `slides_per_view` | select | Slides per View | 4 | `pimcore_select('slides_per_view')` |
| `equal_height` | checkbox | Appends some equal height classes | false | `pimcore_checkbox('equal_height')` |
| `additional_classes` | select | Add custom classes | - | `pimcore_select('add_classes')` |

### Configuration Parameter

- Use the `column_classes` Parameter to define column classes
- Use the `breakpoints` Parameter to define special breakpoint classes

**Example**  
```yaml
toolbox:
    areas:
        slideColumns:
            config_parameter:
                column_classes:
                    '2': col-xs-12 col-sm-6
                breakpoints: []
```

## Spacer
Create a spacer element.

### Available Options

| Name | Type | Description | Default Value | Frontend
|------|------|-----------------------------|---------------|-------------------------------|
| `spacer_class` | select | Spacer classes | spacer-none | `pimcore_select('spacer_class')` |
| `additional_classes` | select | Add custom classes | - | `pimcore_select('add_classes')` |

## Teaser
Create teaser elements.

> Note: This element uses a custom toolbox element: "[dynamic link](20_DynamicLinkElement.md)".

### Available Options

| Name | Type | Description | Default Value | Frontend
|------|------|-----------------------------|---------------|-------------------------------|
| `type` | select | Define Teaser Type: direct or as snippet | direct | `pimcore_select('type')`. Read more about below in "Teaser Type" Section. |
| `layout` | select | Define Teaser Layout | default | `pimcore_select('layout')` |
| `use_light_box` | checkbox | Add a `light-box` class and a wrapping link for teaser image | false | `pimcore_checkbox('use_light_box')` |
| `additional_classes` | select | Add custom classes | - | `pimcore_select('add_classes')` |

### Teaser Types
Like explained above, it's possible to switch between two Types of Teasers:

#### Direct
The `direct` teaser type allows you to place a teaser structure at any place in your document. Just change the options through the edit window.

#### Snippet
Sometimes you want do add teasers in a more reusable way. For that you should use the `snippet` type.
The ToolboxBundle will add a `Teaser Snippet` document type during installation, use it to create teaser elements in snippet context. 
This document type will also add a all custom elements (`layout`, `use_light_box`, `additional_classes`) with all given teaser layouts.

> Note: If you're using the `snippet` type, all option fields (`layout`, `use_light_box`, `additional_classes`) will be unavailable in your document anymore.

## Video
Create a Video Element.

> Note: This is a [custom toolbox element](21_VhsElement.md).

### Available Options

| Name | Type | Description | Default Value | Frontend
|------|------|-----------------------------|---------------|-------------------------------|
| `autoplay` | checkbox | Start/Stop Video if it's in a visible viewport | false | `pimcore_checkbox('autoplay')` |
| `additional_classes` | select | Add custom classes | - | `pimcore_select('add_classes')` |

### Configuration Parameter

- Use the `video_types` Parameter to enable/disable Video Types

**Example**  
```yaml
toolbox:
    areas:
        video:
            config_parameter:
                video_types:
                    asset:
                        active: false
                    youtube:
                        active: true
                    vimeo:
                        active: false
                    dailymotion:
                        active: false
```

# Element Config Field Overview

In short, you're able to use all the [pimcore editables](https://www.pimcore.org/docs/5.0.0/Documents/Editables/index.html).

**Example** 
```yaml
toolbox:
    custom_areas:
        container:
            config_elements:
                awesome_fields:
                    type: input # all the pimcore editables
                    config:
                        # all available configuration attributes from
                        # https://www.pimcore.org/docs/5.0.0/Documents/Editables/Input.html#page_Configuration
```

There is also the `additionalClasses` field. This Element will help you to generate some additional classes for every toolbox element.

> Note: The class gets attached to the `toolbox-*` wrapper element.

**Example** 
```yaml
toolbox:
    areas:
        container:
            config_elements:
                additional_classes:
                    type: additionalClasses
                    config:
                        store:
                            white-bg: 'White Background'
```