# Upgrade Notes

## Migrating from Version 3.x to Version 4.0.0
⚠️ If you're still on version `2.x`, you need to install `3.x` first, then [migrate](https://github.com/dachcom-digital/pimcore-toolbox/blob/3.x/UPGRADE.md) to `3.3`. After that, you're able to update to `^4.0`.

- All deprecations have been removed (
  - Config Node `disallowed_subareas`: `areas_appearance`
  - Config Node `disallowed_content_snippet_areas`: `snippet_areas_appearance`
  - Config Node `ToolboxBundle\Calculator\*`: `column_calculator|slide_calculator`
- All Folders in `views` are lowercase/dashed now

***

Toolbox 3.x Upgrade Notes: https://github.com/dachcom-digital/pimcore-toolbox/blob/3.x/UPGRADE.md
