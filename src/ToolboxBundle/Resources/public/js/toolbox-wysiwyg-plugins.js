tinymce.PluginManager.add('tb_goo_link', function (editor, url) {

    let createOptOutLink = function (data) {
        return '<a href="#" class="google-opt-out-link" name="' + data.gac + '">' + data.title + '</a>';
    };

    editor.ui.registry.addButton('tb_goo_link_button', {
        text: 'Google Opt-Out Link',
        disabled: true,
        onAction: function (_) {

            editor.windowManager.open({
                title: 'Basic Settings',
                body: {
                    type: 'panel',
                    items: [
                        {
                            label: 'Google Analytics Code',
                            name: 'gac',
                            type: 'input'
                        },
                        {
                            label: 'Text',
                            name: 'title',
                            type: 'input'
                        }
                    ]
                },
                buttons: [
                    {
                        type: 'cancel',
                        name: 'cancel',
                        text: 'Cancel'
                    },
                    {
                        type: 'submit',
                        name: 'save',
                        text: 'Save',
                        primary: true
                    }
                ],
                initialData: {
                    preview: 'some html url'
                },
                onSubmit: (dialogApi) => {
                    editor.insertContent(createOptOutLink(dialogApi.getData()));
                    editor.windowManager.close();
                }
            });

        },
        onSetup: function (buttonApi) {

            let editorEventCallback = function (eventApi) {
                buttonApi.setEnabled(!eventApi.element.classList.contains('google-opt-out-link'));
            };

            editor.on('NodeChange', editorEventCallback);

            return function (buttonApi) {
                editor.off('NodeChange', editorEventCallback);
            };
        }
    });

    return {

        getMetadata: function () {
            return {
                name: 'Toolbox Google Opt-Out Link',
                url: 'https://github.com/dachcom-digital/pimcore-toolbox'
            };
        }
    }
});
