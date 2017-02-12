pimcore.registerNS('pimcore.plugin.toolbox.main');
pimcore.plugin.toolbox.main = Class.create({

    editWindows: {},

    initialize: function() {

        var _ = this;

        try {

            Ext.each(Ext.query('div[class="toolbox-element-edit-button"]'), function (item) {

                var editButton = new Ext.Button({
                    cls: 'pimcore_block_button_plus',
                    iconCls: 'pimcore_icon_edit',
                    text: t('edit'),
                    handler: _.openElementConfig.bind(_, this)
                });

                editButton.render(item);

            });

        } catch (e) {
            console.log(e);
        }

    },

    openElementConfig: function(element) {

        var _ = this,
            content;

        if( element.getAttribute('editor-id') !== null ) {

            editWindow  = this.editWindows[ element.getAttribute('editor-id' ) ]['editor'];
            content     = this.editWindows[ element.getAttribute('editor-id' ) ]['content'];

        } else {

            content = Ext.get(element).parent().down('.toolbox-element-window');

            var editWindow = new Ext.Window({
                modal: true,
                width: 600,
                height: 400,
                title: 'Edit Toolbox Element Configuration',
                closeAction: 'hide',
                bodyStyle: 'padding: 10px;',
                closable: false,
                autoScroll: true,
                listeners: {
                    afterrender: function (win) {

                        var needReload = false;

                        content.removeCls('toolbox-element-window-hidden');
                        win.body.down('.x-autocontainer-innerCt').insertFirst(content);

                        if( content.query( 'div.toolbox-element[data-reload=true]' ).length > 0 ) {
                            needReload = true;
                        }

                        var id = win.id.replace('#', '');
                        element.setAttribute('editor-id', id);

                        this.editWindows[ id ] = {
                            editor : win,
                            element : element,
                            needReload : needReload
                        };

                    }.bind(this)
                },
                buttons: [{
                    text: t('save'),
                    listeners: {
                        click: {
                            scope: _,
                            fn: _.editmodeSave,
                            args: [editWindow]
                        }
                    },
                    iconCls: 'pimcore_icon_save'
                },{
                    text: t('cancel'),
                    listeners: {
                        click: {
                            scope: _,
                            fn: _.editmodeClose,
                            args: [editWindow]
                        }
                    },
                    iconCls: 'pimcore_icon_cancel'
                }]
            });

        }

        editWindow.show();

    },

    editmodeSave: function (scope, button) {

        var editWindow = button.up('window'),
            data = this.editWindows[ editWindow.id ];

        if( !data.needReload ) {
            data.editor.close();
            return;
        }

        data.editor.close();
        window.editWindow.reload();

    },

    editmodeClose: function(scope, button) {

        var editWindow = button.up('window'),
            data = this.editWindows[ editWindow.id ];

        //content.addCls('toolbox-element-window-hidden');
        data.editor.close();
    }

});

Ext.onReady(function () {
    new pimcore.plugin.toolbox.main();
});