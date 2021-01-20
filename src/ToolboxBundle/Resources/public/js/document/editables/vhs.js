pimcore.registerNS('pimcore.document.editables.vhs');
pimcore.document.editables.vhs = Class.create(toolbox.abstract.document.editables.video, {

    initialize: function(id, name, options, data) {

        this.id = id;
        this.name = name;
        this.videoEditor = {};
        this.options = this.parseOptions(options);
        this.data = data;

        this.setupWrapper();

        var element = Ext.get('pimcore_video_' + name),
            emptyContainer = element.query('.pimcore_tag_video_empty')[0],
            buttonHolder = Ext.get(id).up('.toolbox-video').prev('.toolbox-element-edit-button'),
            videoEditbutton = new Ext.Button({
                iconCls: 'pimcore_icon_video',
                cls: 'pimcore_edit_link_button',
                text: t('settings'),
                listeners: {
                    click : this.openEditor.bind(this)
                }
            });

        videoEditbutton.render(buttonHolder);

        if(emptyContainer) {
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