# Configuration Flags

Sometimes we implement new features which could break the data stream / layout after updating. 
To avoid this and keep the backward compatibility promise, we've implemented the config flags.

| Name | Type | Default
|------|------|------------|
| `strict_column_counter` | bool | false |

## 🚩 strict_column_counter flag

> Note: This is only necessary if want to update from TB <= `2.1.2` and there are offset columns (for example `column_o1_6_o1_6`) in your configuration to migrate!
> if you don't have any offset columns, just skip this.

```yaml
toolbox:
    flags:
        strict_column_counter: true
```

### Explanation
In Toolbox <= `2.1.2` the column calculator counts every element from the config. For example: `column_6_6` creates two column items: `column_0` and `column_1`.
If you're going to switch the column config to `column_o1_6_o1_6` the calculator will also create two columns but with different indexes: `column_1` and `column_3`.
As you can see, this isn't quite right. After switching the column config, the existing data from `column_0` and `column_1` is gone. 

### Solution
In Toolbox >= `2.2` the offset columns get skipped during calculation. With that your data stays with the column change. 
**Important**: You only need to add this flag if you're using some offset columns in your toolbox configuration. If you're not using offset columns, just update and ignore this flag.

***

## 🚩 use_dynamic_links flag
The dynamic link flag is set to `false` by default. If you want to change it, you need to add this to your app config:

```yaml
toolbox:
    flags:
        use_dynamic_links: true
```

### Explanation
In Pimcore 4 there was no way to add object paths to the link tag so we implemented the dynamic link element.
Now, with Pimcore 5 we're able to add dynamic object links via the  [linkGenerator service](https://pimcore.com/docs/5.x/Development_Documentation/Objects/Object_Classes/Class_Settings/Link_Generator.html).
Since Toolbox `2.5.0` we're using the `pimcore_link` element instead of `pimcore_dynamiclink`.

### Migration
The Migration is very easy:

1. If you have added the `use_dynamic_links` flag, remove it since it's disabled by default anyway
2. Check your templates for `pimcore_dynamiclink` and replace it with `pimcore_link`
3. Remove all your event listeners with tag `toolbox.dynamiclink.object.url`
3. Run the migration script: `$ bin/console toolbox:migrate-dynamic-link`
4. Implement a `LinkGenerator` service for each object type like described in the pimcore docs (you're might be able to adapt some code from step 3).
4. Check your frontend and backend, all your links should be transformed without loosing any data