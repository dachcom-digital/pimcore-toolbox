# Column Adjuster

![](http://g.recordit.co/SQySgZ1Cd9.gif)

This Feature is so damn hot, it even has its own documentation page!

```yaml
toolbox:
    theme:
        grid:
            grid_size: 12
            breakpoints:
                -
                    identifier: 'xs'
                    name: 'Breakpoint: XS'
                    description: 'Your Description'
                -
                    identifier: 'sm'
                    name: 'Breakpoint: SM'
                    description: 'Your Description'
                -
                    identifier: 'md'
                    name: 'Breakpoint: MD'
                    description: 'Your Description'
                -
                    identifier: 'lg'
                    name: 'Breakpoint: LG'
                    description: 'Your Description'
```

## Parameter

| Name | Type | Description
|------|------|------------|
| `grid_size` | integer | Max Grid Columns (Default 12 in Bootstrap) |
| `breakpoints` | array | Your Grid Breakpoints. **Important (!)**: The Column Adjuster is mobile first! Always start your array with the smallest breakpoint! Otherwise the automatic class inheritance won't work! |