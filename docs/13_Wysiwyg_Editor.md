# Wysiwyg Editor Configuration

The Toolbox Bundle allows you to override and specify the wysiwyg configuration.
There are several reasons for that:

- Keep the editor simple, remove unwanted dangerous elements from the wysiwyg
- Define custom styles via configuration
- Use the same editor layout in every wysiwyg element (Document, Emails, Objects)

## Supported Editors
- [TinyMCE (default)](#tinymce)
- [Quill](#quill)
 
> [!IMPORTANT]  
> Depending on the installed editor bundle, 
> toolbox **automatically enables** the corresponding extension configuration!

***

## TinyMCE

### Toolbar
Checkout the [available toolbar buttons](https://www.tiny.cloud/docs/advanced/available-toolbar-buttons/).  

> [!TIP]
> Info: This is the global configuration for the TinyMCE-Editor Toolbar.
> If you need to provide a different configuration in objects or areas, use the object/area configuration (see below)

### Overriding Configuration

**Example** 
```yaml
toolbox:
    wysiwyg_editor:
        config:
            block_formats: 'Paragraph=p; Header 1=h1; Header 2=h2; Header 3=h3'
            style_formats:
                -
                    title : 'Example'
                    inline : 'span'
                    classes : 'example'
                    selector: 'a'
            toolbar2: 'blocks | styles'

```
> [!IMPORTANT]  
> If your extending style formats, you also have to extend the [html sanitize](https://pimcore.com/docs/platform/Pimcore/Documents/Editables/WYSIWYG/#extending-symfony-html-sanitizer-configuration)
> otherwise you're applied data will be removed after persisting!

#### Area Editor Configuration
If you only need to modify the toolbar configuration for document areas, add this to your configuration:

**Example** 
```yaml
toolbox:
    wysiwyg_editor:
        area_editor:
            config:
                # his will change the toolbar2 only for document editables editors
                toolbar2: 'blocks'
```

#### Object Editor Configuration
If you only need to modify the toolbar configuration for objects, add this to your configuration:

> [!NOTE]  
> The object configuration does not respect different toolbox context environments at the moment. 
> Objects are not restricted to any sites by nature which makes any context-binding quite impossible.

**Example** 
```yaml
toolbox:
    wysiwyg_editor:
        object_editor:
            config:
                # this will change the toolbar2 only for object editors
                toolbar2: 'blocks'
```

#### TinyMce Editor Plugins
Toolbox provides some additional Editor Plugins

##### Google Opt Out
![editor-google-opt-out](https://user-images.githubusercontent.com/700119/37820009-9dd6a418-2e7f-11e8-94b4-99c7a001a3a9.png)

Add a link to allow user to disable Google Analytics tracking.
There is also a [frontend Javascript Plugin](./80_Javascript.md).

##### Enable Button
```yaml
toolbox:
    wysiwyg_editor:
        config:
            toolbar2: 'tb_goo_link_button'
```

***

## Quill

### Toolbar
Checkout the [available toolbar buttons](https://quilljs.com/docs/modules/toolbar).  

> [!TIP]
> Info: This is the global configuration for the Quill-Editor Toolbar.
> If you need to provide a different configuration in objects or areas, use the object/area configuration (see below)

### Overriding Configuration

> [!CAUTION]
> Because pimcore only uses a simple `Object.assign()` method to merge the configuration,
> you have to pass the full configuration (even if you only want to change the toolbar config)

**Example** 
```yaml
toolbox:
    wysiwyg_editor:
        config:
            modules:
                table: false
                table-better:
                    language: 'en_US'
                    menus: [ 'column', 'row', 'merge', 'table', 'cell', 'wrap', 'delete' ]
                    toolbarTable: true
                keyboard:
                    bindings: QuillTableBetter.keyboardBindings
                history:
                    delay: 700
                    maxStack: 200
                    userOnly: true
                toolbar:
                    container: [
                        [ { header: [ 1, 2, false ] } ],
                        [{ 'size': ['small', false, 'large', 'huge'] }],  # custom dropdown
                        [ 'bold' ],
                        [ { align: [ ] } ],
                        [ { list: 'ordered' } ],
                        [ 'link', 'table-better' ],
                        [ 'clean' ],
                    ]

```
> [!IMPORTANT]  
> If your extending style formats (In this example the "size" dropdown), you also have to extend the [html sanitize](https://pimcore.com/docs/platform/Pimcore/Documents/Editables/WYSIWYG/#extending-symfony-html-sanitizer-configuration)
> otherwise you're applied data will be removed after persisting!

#### Area Editor Configuration
If you only need to modify the toolbar configuration for document areas, add this to your configuration:

**Example** 
```yaml
toolbox:
    wysiwyg_editor:
        area_editor:
            config:
                # his will change the modules only for document editables editors
                modules: [...]
```

#### Object Editor Configuration
If you only need to modify the toolbar configuration for objects, add this to your configuration:

> [!NOTE]  
> The object configuration does not respect different toolbox context environments at the moment. 
> Objects are not restricted to any sites by nature which makes any context-binding quite impossible.

**Example** 
```yaml
toolbox:
    wysiwyg_editor:
        object_editor:
            config:
                # this will change the modules only for object editors
                modules: 'blocks'
```

#### Quill Editor Plugins
Currently no plugins available.
