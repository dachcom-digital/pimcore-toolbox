# Configuration Flags

Sometimes we implement new features which could break the data stream / layout after updating. 
To avoid this and keep the backward compatibility promise, we've implemented the config flags.

| Name | Type | Default
|------|------|------------|
| `strict_column_counter` | bool | false |


## strict_column_counter flag

```yaml
toolbox:
    flags:
        strict_column_counter: true
```

### Explanation
In Toolbox <= `2.1.2` the column calculator counts every element from the config. For example: `column_6_6` creates two column items: `column_0` and `column_1`.
If you're switch the column config to `column_o1_6_o1_6` the calculator will also create two columns but with different index: `column_1` and `column_3`.
As you can see, this isn't quite right. After switching the column config, the existing data from `column_0` and `column_1` is gone. 

### Solution
In Toolbox >= `2.2` the offset columns get skipped during calculation. With that your data stays with the column change. 
**Important**: You only need to add this flag if you're using some offset columns in your toolbox configuration. If you're not using offset columns, just update and ignore this flag.