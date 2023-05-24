# Upgrade Notes

## Version 4.1.0
- [FEATURE] PIMCORE 10.5 support only
- [IMPROVEMENT] Compatibility with Members 4.1 added

## Version 4.0.10
- [BUGFIX] fix case sensitivity for accordion in bootstrap3 theme `Resources/views/toolbox/bootstrap3/accordion/partial/Accordion => Resources/views/toolbox/bootstrap3/accordion/partial/accordion`

## Version 4.0.9
- [BUGFIX] Missing area layout for bricks without configuration [#182](https://github.com/dachcom-digital/pimcore-toolbox/issues/182)
- [IMPROVEMENT] Move permission command listener to maintenance task [#181](https://github.com/dachcom-digital/pimcore-toolbox/issues/181)

## Version 4.0.8
- [BUGFIX] Fix wrong query identifier for MembersBundle connector [@scrummer](https://github.com/dachcom-digital/pimcore-toolbox/pull/179)

## Version 4.0.7
- [BUGFIX] prevent type error in `DownloadExtension:getOptimizedFileSize`

## Version 4.0.6
- [BUGFIX] fix id for Accordion and Gallery: save class names

## Version 4.0.5
- [BUGFIX] fix type error in `Vhs:setDataFromResource`

## Version 4.0.4
- [BUGFIX] fix file size calculation
- [BUGFIX] add label for column adjuster [@blankse](https://github.com/dachcom-digital/pimcore-toolbox/pull/170)
- [BUGFIX] fix image thumbnail class [@NaincyKumariKnoldus](https://github.com/dachcom-digital/pimcore-toolbox/pull/168)

## Version 4.0.3
- [BUGFIX] return correct default value if editable is null

## Version 4.0.2
- [FEATURE] PIMCORE 10.2 Support

## Version 4.0.1
- [BUGFIX] Fetching edit mode state from editable instead of checking area param
 
## Migrating from Version 3.x to Version 4.0.0
⚠️ If you're still on version `2.x`, you need to install `3.x` first, then [migrate](https://github.com/dachcom-digital/pimcore-toolbox/blob/3.x/UPGRADE.md) to `3.3`. After that, you're able to update to `^4.0`.

### Global Changes
- All deprecations have been removed: 
  - Legacy Service Alias `toolbox.area.brick.base_brick` has been removed 
  - Config Node `toolbar.x`, `toolbar.y`, `toolbar.xAlign`, `toolbar.yAlign`, `toolbar.title` has been removed
  - Config Node `area_block_configuration.controlsAlign`, `area_block_configuration.controlsTrigger` has added
  - Config Node `disallowed_subareas`: `areas_appearance`
  - Config Node `disallowed_content_snippet_areas`: `snippet_areas_appearance`
  - Config Node `ToolboxBundle\Calculator\*`: `column_calculator|slide_calculator`
  - ⚠️ Element `pimcore_dynamiclink` has been removed: If you're still using this element in your project, you need to [migrate](https://github.com/dachcom-digital/pimcore-toolbox/blob/3.x/docs/70_ConfigurationFlags.md#-use_dynamic_links-flag) to pimcore_link first (via TB 3.x)!
- All Folders in `views` are lowercase/dashed now (`areas/google-map`, `areas/iframe`, `areas/link-list`, `areas/paralax-container`, `areas/slide-columns`, `toolbox/bootstrap4`, ...)

### Editable Changes
- Instead of `pimcore.area.brick` you need to use the `toolbox.area.brick` tag to register your brick. Also remove `setAreaBrickType` call from your service definition 
- Always use `$info->setParam(key, value)` instead of `$info->setParams()`, otherwise you'll overwrite given parameters from the toolbox abstract brick
- Remove `{{ elementConfigBar|raw }}` in your templates
- Conditional Logic feature for editable has been removed
- Reload property in node `config.reload` has been removed. Use `config_parameter.reload_on_close: true` instead
- Custom view has been removed, TB is now using the pimcore defaults dialog box configuration
   - Config Node `col_class` (In`[BRICKNAME].config_elements.[ELEMENT]` has been removed

### Type Changes
- `StoreProviderInterface::getValues():array` needs to return an array (return type declaration added)
- `ContextResolverInterface::getCurrentContextIdentifier():?string` needs to return null|string (return type declaration added)

### New Features
- ⚠️ [Editable Permissions](https://github.com/dachcom-digital/pimcore-toolbox/issues/161) have been added. Non-Admin Users will **NOT** be able to add editables until you enabled specific permissions for them! 
- Google Maps Improved: 
  - Only call API if address has changed
  - Better Error Reporting: Display some notes (only in editmode), if something went wrong during API call
- Simple Area Brick: Create a simple editable without creating any php classes. Read more about it [here](./docs/10_CustomBricks.md#simple-brick))
***

Toolbox 3.x Upgrade Notes: https://github.com/dachcom-digital/pimcore-toolbox/blob/3.x/UPGRADE.md
