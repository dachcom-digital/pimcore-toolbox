# Upgrade Notes

## Migrating from Version 4.x to Version 5.0.0

### Global Changes
- CK Editor support has been removed from PIMCORE. Toolbox currently only supports the new TinyMCE Editor. Read more about it [here](./docs/13_Wysiwyg_Editor.md)
  - Config node  `toolbox.ckeditor` changed to `toolbox.wysiwyg_editor`
  - Config node `global_style_sets` has been removed (will be set via `wysiwyg_editor.config`)
  - Twig Helper `toolbox_get_wysiwyg_config_path()` has been removed (will be added globally via edit mode js file injection)

- All deprecations have been removed:
  - TBD

### Editable Changes
Pimcore removed the extension manager, so it is not possible to enabled/disable them via pimcore anymore.

You need to change the global state via configuration now:

```yaml
toolbox:
    areas:
        accordion:
            enabled: false
```

If you want to disable any area from third party bundles (for example the members brick):
```yaml
    custom_areas:
        members_login:
            enabled: false
```

### Type Changes
TBD

### New Features
TBD

***

Toolbox 4.x Upgrade Notes: https://github.com/dachcom-digital/pimcore-toolbox/blob/4.x/UPGRADE.md
