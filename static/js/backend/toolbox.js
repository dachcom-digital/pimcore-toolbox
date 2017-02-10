pimcore.registerNS('pimcore.plugin.toolbox.main');
pimcore.plugin.toolbox.main = Class.create({

    editWindow: null,

    needReload: false,

    initialize: function() {

        var _ = this;

        try {

            Ext.each(Ext.query('div[class="toolbox-element-edit-button"]'), function (item) {

                var editButton = new Ext.Button({
                    cls: 'pimcore_block_button_plus',
                    iconCls: 'pimcore_icon_edit',
                    text: t('edit'),
                    handler: _.openElementConfig.bind(_, this, item)
                });

                editButton.render(item);

            });

        } catch (e) {
            console.log(e);
        }

    },

    openElementConfig: function(element, item) {

        var _ = this,
            content = Ext.get(item).parent().down('.toolbox-element-window');

        if( content === null && element.getAttribute('data-editmmode-button-ref') !== null) {
            content = Ext.getBody().down( '#' + element.getAttribute('data-editmmode-button-ref' ) );
        }

        this.editmodeWindow = new Ext.Window({
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

                    content.removeCls('toolbox-element-window-hidden');
                    win.body.down('.x-autocontainer-innerCt').insertFirst(content);

                    if( content.query( 'div.toolbox-element[data-reload=true]' ).length > 0 ) {
                        _.needReload = true;
                    }

                }.bind(this)
            },
            buttons: [{
                text: t('save'),
                listeners: {
                    'click': this.editmodeSave.bind(this, content, element)
                },
                iconCls: 'pimcore_icon_save'
            },{
                text: t('cancel'),
                listeners: {
                    'click': this.editmodeClose.bind(this, content, element)
                },
                iconCls: 'pimcore_icon_cancel'
            }]
        });

        this.editmodeWindow.show();

    },

    editmodeSave: function (content, element) {

        if( !this.needReload ) {
            this.editmodeClose(content, element);
            return;
        }

        this.editmodeWindow.close();
        window.editWindow.reload();

    },

    editmodeClose: function(content, element) {
        content.addCls('toolbox-element-window-hidden');
        element.setAttribute('data-editmmode-button-ref', content.getAttribute('id') );
        this.editmodeWindow.close();
    }

});

Ext.onReady(function () {
    new pimcore.plugin.toolbox.main();
});