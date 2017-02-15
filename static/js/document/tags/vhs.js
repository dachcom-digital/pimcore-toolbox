pimcore.registerNS('pimcore.document.tags.vhs');
pimcore.document.tags.vhs = Class.create(pimcore.document.tags.video, {

    initialize: function(id, name, options, data, inherited) {

        this.id = id;
        this.name = name;
        this.data = {};

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

        // disable the global dnd handler in this editmode/frame
        window.dndManager.disable();

        this.window = pimcore.helpers.editmode.openVhsEditPanel(this.data, {
            save: this.save.bind(this),
            cancel: this.cancel.bind(this)
        });
    },

    getType: function () {
        return 'vhs';
    }
});