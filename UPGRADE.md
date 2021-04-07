# Upgrade Notes
![upgrade](https://user-images.githubusercontent.com/700119/31535145-3c01a264-affa-11e7-8d86-f04c33571f65.png)  

***

After every update you should check the pimcore extension manager. 
Just click the "update" button or execute the migration command to finish the bundle update.

#### Update from Version 3.2.4 to Version 3.2.5
- **[BUG FIX]**: Fix column adjuster column_store availability check

#### Update from Version 3.2.3 to Version 3.2.4
- **[IMPROVEMENT]**: Assert correct order in download listing when members join is active

#### Update from Version 3.2.2 to Version 3.2.3
- **[NEW FEATURE]**: Pimcore 6.8.0 ready
- **[BUGFIX]**: Use `config` property on Pimcore >= 6.8 [#146](https://github.com/dachcom-digital/pimcore-toolbox/issues/146)

#### Update from Version 3.2.1 to Version 3.2.2
- **[NEW FEATURE]** : UIkit3 theme added [@AndiKeiser](https://github.com/dachcom-digital/pimcore-toolbox/pull/138)

#### Update from Version 3.2.0 to Version 3.2.1
- **[NEW FEATURE]**: Pimcore 6.6.0 ready
- **[NEW FEATURE]** : Fix rendering of multiple t-col-half elements [@christopher-siegel](https://github.com/dachcom-digital/pimcore-toolbox/pull/135)

#### Update from Version 3.1.x to Version 3.2.0
- **[NEW FEATURE]**: Pimcore 6.4.0 and Pimcore 6.5.0 ready
- **[NEW FEATURE]**: Store Provider added (https://github.com/dachcom-digital/pimcore-toolbox/pull/128)
- **[IMPROVEMENT]**: Better Google API Key Fetching: Introduces `toolbox.google_maps.browser_api_key` and `toolbox.google_maps.simple_api_key`. Read more about it [here](./docs/11_ElementsOverview.md#google-map)
- **[BUG FIX]**: Video Autoplay Fix (https://github.com/dachcom-digital/pimcore-toolbox/issues/129)

#### Update from Version 3.x to Version 3.1.0
- **[NEW FEATURE]**: Pimcore 6.3.0 ready
- **[BUG FIX]**: [Fix wrong index in bootstrap tabs for active element](https://github.com/dachcom-digital/pimcore-toolbox/issues/119)

***

#### Update from Version 2.x to Version 3.0.0
- **[NEW FEATURE]**: Pimcore 6.0.0 ready
- **[BC BREAK]**: All Controllers are registered as services now!
- **[ATTENTION]**: All `href`, `multihref` elements has been replaced by `relation`, `relations`

***

Toolbox 2.x Upgrade Notes: https://github.com/dachcom-digital/pimcore-toolbox/blob/2.8/UPGRADE.md
