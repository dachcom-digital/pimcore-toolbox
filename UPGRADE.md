# Upgrade Notes

## Migrating from Version 3.x to Version 4.0.0
⚠️ If you're still on version `2.x`, you need to install `3.x` first, then [migrate](https://github.com/dachcom-digital/pimcore-toolbox/blob/3.x/UPGRADE.md) to `3.3`. After that, you're able to update to `^4.0`.

### Global Changes
- All deprecations have been removed:
  - Config Node `disallowed_subareas`: `areas_appearance`
  - Config Node `disallowed_content_snippet_areas`: `snippet_areas_appearance`
  - Config Node `ToolboxBundle\Calculator\*`: `column_calculator|slide_calculator`
  - ⚠️ Element `pimcore_dynamiclink` has been removed: If you're still using this element in your project, you need to [migrate](https://github.com/dachcom-digital/pimcore-toolbox/blob/3.x/docs/70_ConfigurationFlags.md#-use_dynamic_links-flag) to pimcore_link first (via TB 3.x)!
- All Folders in `views` are lowercase/dashed now

### Editable Changes
- Always use `$info->setParam(key, value)` instead of `$info->setParams()`, otherwise you'll overwrite given parameters from the toolbox abstract brick.
- Remove `{{ elementConfigBar|raw }}` in your templates
- Conditional Logic Feature for editable has been removed
- Reload Property in node `config.reload` has been removed. use `config_parameter.reload_on_close: true` instead
- Custom view has been removed, TB is now using the pimcore defaults dialog box configuration



***

Toolbox 3.x Upgrade Notes: https://github.com/dachcom-digital/pimcore-toolbox/blob/3.x/UPGRADE.md
