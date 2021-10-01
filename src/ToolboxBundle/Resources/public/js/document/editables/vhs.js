pimcore.registerNS('pimcore.document.editables.vhs');
pimcore.document.editables.vhs = Class.create(pimcore.document.editables.video, {

    initialize: function (id, name, config, data) {
        this.id = id;
        this.name = name;
        this.videoEditor = {};
        this.config = this.parseConfig(config);
        this.data = data;
    },

    subRender: function () {

        var element, emptyContainer, videoEditButton;

        this.setupWrapper();

        this.element = Ext.get(this.id);

        element = Ext.get('pimcore_video_' + this.name);
        emptyContainer = element.query('.pimcore_editable_video_empty')[0];
        videoEditButton = new Ext.Button({
                iconCls: 'pimcore_icon_video',
                cls: 'pimcore_edit_link_button',
                text: t('settings'),
                listeners: {
                    click: this.openEditor.bind(this)
                }
            });

        videoEditButton.render(this.getButtonHolder());

        if (this.inherited) {
            button.hide();
        }

        if (emptyContainer) {
            emptyContainer = Ext.get(emptyContainer);
            emptyContainer.on('click', this.openEditor.bind(this));
        }

    },

    render: function () {

        // this is a sub render element (append button to toolbox edit bar).
        if (this.hasButtonHolder() === false) {
            Ext.get(this.id).up('.pimcore_area_entry').addListener('toolbox.bar.added', this.subRender.bind(this));
            return;
        }

        this.subRender();

    },

    hasButtonHolder: function () {
        return this.getButtonHolder() !== null;
    },

    getButtonHolder: function () {
        return Ext.get(this.id).up('.toolbox-video').prev('.toolbox-element-edit-button');
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