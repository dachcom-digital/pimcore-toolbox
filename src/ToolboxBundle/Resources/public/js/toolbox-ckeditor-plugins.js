CKEDITOR.plugins.add('tbGoogleOptOutLink', {
    init: function (editor) {
        editor.addCommand('tbGoogleOptOutLink', new CKEDITOR.dialogCommand('tbGoogleOptOutLinkDialog'));

        editor.on('selectionChange', function (e) {
            if (e.data.selection.getStartElement().hasClass('google-opt-out-link'))
                editor.getCommand('tbGoogleOptOutLink').setState(CKEDITOR.TRISTATE_DISABLED);
            else
                editor.getCommand('tbGoogleOptOutLink').setState(CKEDITOR.TRISTATE_ENABLED);
        });

        editor.ui.addButton('GoogleOptOutLink', {
            label: 'Toolbox Google Opt-Out Link',
            command: 'tbGoogleOptOutLink',
            icon: '/bundles/toolbox/images/ckeditor/gOptOut.png'
        });
    }
});

CKEDITOR.dialog.add('tbGoogleOptOutLinkDialog', function (editor) {
    return {
        title: 'Google Opt Out Properties',
        minWidth: 400,
        minHeight: 200,
        contents: [
            {
                id: 'tab-basic',
                label: 'Basic Settings',
                elements: [
                    {
                        type: 'text',
                        id: 'gac',
                        label: 'Google Analytics Code',
                        validate: CKEDITOR.dialog.validate.notEmpty('Google Analytics Code field cannot be empty.'),
                        setup: function (element) {
                            this.data(element.getAttribute('name'));
                        },
                        commit: function (element) {
                            var gac = this.getValue();
                            element.setAttribute('name', gac);
                        }
                    },
                    {
                        type: 'text',
                        id: 'title',
                        label: 'Text',
                        validate: CKEDITOR.dialog.validate.notEmpty('Text field cannot be empty.'),
                        setup: function (element) {
                            this.data(element.getText());
                        },
                        commit: function (element) {
                            var text = this.getValue();
                            element.setText(text);
                        }
                    },
                ]
            }
        ],
        onShow: function () {

            var selection = editor.getSelection();
            var element = selection.getStartElement();

            if (element) {
                element = element.getAscendant('a', true);
            }

            if (!element || element.getName() != 'a' || !element.hasClass('google-opt-out-link')) {
                element = editor.document.createElement('a');
                element.setAttribute('class', 'google-opt-out-link');
                element.setAttribute('href', '#');
                element.setText('click here to opt-out of google analytics');
                this.insertMode = true;
            } else {
                this.insertMode = false;
            }

            this.element = element;
            if (!this.insertMode) {
                this.setupContent(this.element);
            }
        },

        onOk: function () {
            var dialog = this;
            var aLink = this.element;
            this.commitContent(aLink);

            if (this.insertMode)
                editor.insertElement(aLink);
        }
    };
});