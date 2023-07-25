# Upgrade Notes

## Migrating from Version 4.x to Version 5.0.0

### Global Changes
- All folders and sub-folders in views are lowercase/underscore now (areas/accordion/accordion_tab, areas/google_map, areas/iframe, areas/link_list, areas/parallax_container, areas/parallax_container_section, areas/parallax_container_section, areas/slide_columns)
- All views are lowercase/underscore now (areas/video/type_*, areas/google_map/info_window, parallax_container/partial/*, parallax_container/wrapper/container_wrapper)
- `dynamiclink` feature has finally been removed! If you're still using it, stay on v4 and [migrate first](https://github.com/dachcom-digital/pimcore-toolbox/blob/3.x/docs/70_ConfigurationFlags.md#-use_dynamic_links-flag)

### WYSIWYG Config Changes
- CK Editor support has been removed from PIMCORE. Toolbox currently only supports the new TinyMCE Editor. Read more about it [here](./docs/13_Wysiwyg_Editor.md)
  - Config node  `toolbox.ckeditor` changed to `toolbox.wysiwyg_editor`
  - Config node `global_style_sets` has been removed (will be set via `wysiwyg_editor.config`)
  - Twig Helper `toolbox_get_wysiwyg_config_path()` has been removed (will be added globally via edit mode js file injection)


### Areas Restriction Changes
- `areas_appearance` has been renamed to `areablock_restriction`, `snippet_areas_appearance` has been renamed to `snippet_areablock_restriction`.

### Editable Changes
- `custom_areas` has been removed. Please add your custom areas to the default `areas` node.

### Editables States
Pimcore removed the extension manager, so it is not possible to enabled/disable them via pimcore anymore.
Therefor we removed all toolbox core editables by default, you need to enable every single one of them:

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
        - iframe
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

You need to change the global state via configuration now:

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

### Theme
The default theme section will be loaded, **only** if **no** `toolbox.theme.layout` has been defined.
If you're using the `Bootstrap4` layout and is explicitly configured in your project, you need to adopt the config from toolbox core `config/theme/bootstrap4_theme.yaml`

### Type Changes
TBD

### New Features
TBD

***

Toolbox 4.x Upgrade Notes: https://github.com/dachcom-digital/pimcore-toolbox/blob/4.x/UPGRADE.md
