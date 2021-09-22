pimcore.registerNS('pimcore.document.editables.vhs');
pimcore.document.editables.vhs = Class.create(pimcore.document.editables.video, {

    initialize: function (id, name, config, data) {
        this.id = id;
        this.name = name;
        this.videoEditor = {};
        this.config = this.parseConfig(config);
        this.data = data;
    },

    render: function () {

        this.setupWrapper();

        var element = Ext.get('pimcore_video_' + this.name),
            emptyContainer = element.query('.pimcore_editable_video_empty')[0],
            videoEditbutton = new Ext.Button({
                iconCls: 'pimcore_icon_video',
                cls: 'pimcore_edit_link_button',
                text: t('settings'),
                listeners: {
                    click: this.openEditor.bind(this)
                }
            });

        videoEditbutton.render(element.insertHtml("afterBegin", '<div class="pimcore_video_edit_button"></div>'));
        if (this.inherited) {
            button.hide();
        }

        if (emptyContainer) {
            emptyContainer = Ext.get(emptyContainer);
            emptyContainer.on('click', this.openEditor.bind(this));
        }
    },

    openEditor: function () {
        this.videoEditor = new pimcore.plugin.toolbox.vhs.editor(this.data, {
            save: this.save.bind(this),
            cancel: this.cancel.bind(this)
        });

        this.videoEditor.loadWindow();
    },

    save: function () {
        this.data = this.videoEditor.getFieldValues();
        this.videoEditor.hideWindow();
        this.reloadDocument();
    },

    cancel: function () {
        this.videoEditor.hideWindow();
    },

    getType: function () {
        return 'vhs';
    }
});