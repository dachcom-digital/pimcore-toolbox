pimcore.registerNS('pimcore.plugin.toolbox.vhs.editor');
pimcore.plugin.toolbox.vhs.editor = Class.create({

    data: null,
    callbacks: {},
    window: {},
    form: {},
    videoTypeStore: {},
    videoParameterStore: {},

    initialize: function (data, callbacks) {
        this.data = data;
        this.callbacks = callbacks;

        this.initVideoTypesStore();
    },

    loadWindow: function () {

        document.body.classList.add('toolbox-modal-open');

        this.loadForm();

        this.window = new Ext.Window({
            width: 500,
            maxHeight: 650,
            scrollable: 'y',
            modal: false,
            resizable: true,
            closable: false,
            title: t('video'),
            items: [this.form],
            listeners: {
                afterrender: function () {
                    this.videoTypeStore.load({
                        scope: this,
                        callback: function () {
                            this.updateVideoType(this.data.type, this.videoTypeStore.findRecord('name', this.data.type));
                            this.form.updateLayout();
                        }.bind(this)
                    });

                }.bind(this)
            }
        });

        this.window.show();
    },

    loadForm: function () {

        this.form = new Ext.FormPanel({
            itemId: 'form',
            bodyStyle: 'padding:10px;',
            items:
                [
                    {
                        xtype: 'combo',
                        itemId: 'type',
                        fieldLabel: t('type'),
                        displayField: 'name',
                        name: 'type',
                        triggerAction: 'all',
                        editable: false,
                        width: 270,
                        mode: 'local',
                        store: this.videoTypeStore,
                        value: this.data.type,
                        listeners: {
                            select: function (combo) {
                                this.updateVideoType(combo.getValue(), this.videoTypeStore.findRecord('name', combo.getValue()));
                            }.bind(this)
                        }
                    },
                    {
                        xtype: 'fieldcontainer',
                        layout: 'hbox',
                        border: false,
                        itemId: 'pathContainer',
                        items: [
                            {
                                xtype: 'textfield',
                                fieldLabel: t('path'),
                                itemId: 'path',
                                name: 'path',
                                width: 420,
                                fieldCls: 'pimcore_droptarget_input',
                                enableKeyEvents: true,
                                value: this.data.path,
                                listeners: {
                                    render: function (el) {
                                        dndManager.addDropTarget(el.getEl(), this.onNodeOver.bind(this), this.onNodeDrop.bind(this));
                                    }.bind(this),
                                    keyup: function (el) {
                                        if ((el.getValue().indexOf('youtu.be') >= 0 || el.getValue().indexOf('youtube.com') >= 0) && el.getValue().indexOf('http') >= 0) {
                                            el.up('form').getComponent('type').setValue('youtube');
                                        } else if (el.getValue().indexOf('vimeo') >= 0 && el.getValue().indexOf('http') >= 0) {
                                            el.up('form').getComponent('type').setValue('vimeo');
                                        } else if ((el.getValue().indexOf('dai.ly') >= 0 || el.getValue().indexOf('dailymotion') >= 0) && el.getValue().indexOf('http') >= 0) {
                                            el.up('form').getComponent('type').setValue('dailymotion');
                                        }
                                    }.bind(this)
                                }
                            },
                            {
                                xtype: 'button',
                                iconCls: 'pimcore_icon_search',
                                itemId: 'searchButton',
                                handler: function () {
                                    pimcore.helpers.itemselector(false, function (item) {
                                        if (item) {
                                            this.up('form').getComponent('path').setValue(item.fullpath);
                                            return true;
                                        }
                                    }, {
                                        type: ['asset'],
                                        subtype: {
                                            asset: ['video']
                                        }
                                    });
                                }
                            }
                        ]
                    },
                    {
                        xtype: 'textfield',
                        itemId: 'poster',
                        fieldLabel: t('poster_image'),
                        name: 'poster',
                        width: 420,
                        fieldCls: 'pimcore_droptarget_input',
                        enableKeyEvents: true,
                        value: this.data.poster,
                        listeners: {
                            render: function (el) {
                                dndManager.addDropTarget(el.getEl(), this.onNodeOver.bind(this), this.onNodeDrop.bind(this));
                            }.bind(this)
                        },
                    },
                    {
                        xtype: 'checkbox',
                        itemId: 'showAsLightbox',
                        checked: false,
                        name: 'showAsLightbox',
                        fieldLabel: t('vhs_show_in_lightbox'),
                        value: this.data.showAsLightbox
                    },
                    {
                        xtype: 'textfield',
                        name: 'title',
                        itemId: 'title',
                        fieldLabel: t('title'),
                        width: 420,
                        value: this.data.title
                    },
                    {
                        xtype: 'textarea',
                        itemId: 'description',
                        name: 'description',
                        fieldLabel: t('description'),
                        width: 420,
                        height: 50,
                        value: this.data.description
                    },
                ],
            buttons: [
                {
                    text: t('cancel'),
                    listeners: {
                        'click': this.callbacks['cancel']
                    },
                    iconCls: 'pimcore_icon_cancel',
                    margin: '0 10px 15px 0'
                },
                {
                    text: t('save'),
                    listeners: {
                        'click': this.callbacks['save']
                    },
                    iconCls: 'pimcore_icon_save',
                    margin: '0 20px 15px 0'
                }
            ]
        });

        this.form.add(this.getVideoParameterField());
    },

    getVideoParameterField: function () {

        this.videoParameterStore = new Ext.data.Store({
            data: this.data.videoParameter
        });

        return Ext.create('Ext.grid.Panel', {
            store: this.videoParameterStore,
            flex: 1,
            border: false,
            width: 460,
            title: t('vhs_video_parameter'),
            columns: [
                {
                    flex: 1,
                    sortable: false,
                    dataIndex: 'key',
                    editor: new Ext.form.TextField({
                        allowBlank: true
                    })
                },
                {
                    flex: 1,
                    sortable: false,
                    dataIndex: 'value',
                    editor: new Ext.form.TextField({
                        allowBlank: true
                    })
                },
                {
                    xtype: 'actioncolumn',
                    menuText: t('delete'),
                    width: 40,
                    items: [{
                        tooltip: t('delete'),
                        icon: '/bundles/pimcoreadmin/img/flat-color-icons/delete.svg',
                        handler: function (grid, rowIndex) {
                            grid.getStore().removeAt(rowIndex);
                            grid.up('grid').getView().refresh();
                        }.bind(this)
                    }]
                }
            ],
            stripeRows: true,
            columnLines: true,
            selModel: Ext.create('Ext.selection.CellModel'),
            autoHeight: true,
            valueField: 'key',
            displayField: 'value',
            plugins: [
                Ext.create('Ext.grid.plugin.CellEditing', {
                    clicksToEdit: 1
                })
            ],
            tbar: [
                {
                    iconCls: 'pimcore_icon_table_row pimcore_icon_overlay_add',
                    handler: function (btn) {
                        var newRow,
                            grid = btn.up('grid'),
                            modelClass = grid.getStore().getModel();

                        newRow = new modelClass({
                            key: null,
                            value: null
                        });

                        this.videoParameterStore.add(newRow);
                    }.bind(this),
                }
            ]
        });

    },

    initVideoTypesStore: function () {
        this.videoTypeStore = new Ext.data.JsonStore({
            autoLoad: true,
            fields: ['name', 'value', 'config'],
            proxy: {
                type: 'ajax',
                url: '/toolbox/ajax/video-allowed-video-types',
                reader: {
                    type: 'json'
                }
            }
        });
    },

    updateVideoType: function (type, typeInfo) {

        if (typeInfo === null) {
            return;
        }

        var lightBoxEl = this.form.getComponent('showAsLightbox'),
            pathContainer = this.form.getComponent('pathContainer'),
            searchButton = pathContainer.query('button')[0],
            pathEl = pathContainer.getComponent('path'),
            typeConfig = typeInfo.get('config');

        pathEl.labelEl.update(t('path'));

        searchButton.enable();
        if (type !== 'asset') {
            searchButton.disable();
        }

        if (type === 'youtube') {
            pathEl.labelEl.update('ID');
        }

        if (type === 'vimeo') {
            pathEl.labelEl.update('ID');
        }

        if (type === 'dailymotion') {
            pathEl.labelEl.update('ID');
        }

        if (!typeConfig.allow_lightbox) {
            lightBoxEl.hide();
        } else {
            lightBoxEl.show();
        }

        if (typeConfig.id_label) {
            pathEl.labelEl.update(typeConfig.id_label);
        }
    },

    getFieldValues: function () {

        var videoParameter = [],
            values = this.window.getComponent('form').getForm().getFieldValues();

        this.videoParameterStore.each(function (record) {
            if (record.get('key') !== null && record.get('value') !== null) {
                videoParameter.push({
                    'key': record.get('key'),
                    'value': record.get('value')
                })
            }
        });

        values['videoParameter'] = videoParameter;

        return values;
    },

    hideWindow: function () {
        this.window.hide();
        this.window.destroy();
        this.window = {};
        this.form = {};

        document.body.classList.remove('toolbox-modal-open');
    },

    onNodeOver: function (target, dd, e, data) {

        var form = this.form,
            poster = form.getComponent('poster');

        data = data.records[0].data;
        if (target && target.getId() === poster.getId()) {
            if (data.elementType === 'asset' && data.type === 'image') {
                return Ext.dd.DropZone.prototype.dropAllowed;
            }
        } else {
            if (data.elementType === 'asset' && data.type === 'video') {
                return Ext.dd.DropZone.prototype.dropAllowed;
            }
        }
        return Ext.dd.DropZone.prototype.dropNotAllowed;
    },

    onNodeDrop: function (target, dd, e, data) {

        var recordData,
            form = this.form,
            pathContainer = form.getComponent('pathContainer'),
            path = pathContainer.getComponent('path'),
            poster = form.getComponent('poster');

        if (!target) {
            return false;
        }

        recordData = data.records[0].data;

        if (target.getId() === path.getId()) {
            if (recordData.elementType === 'asset' && recordData.type === 'video') {
                path.setValue(recordData.path);
                form.getComponent('type').setValue('asset');
                return true;
            }
        } else if (target.getId() === poster.getId()) {
            if (recordData.elementType === 'asset' && recordData.type === 'image') {
                poster.setValue(recordData.path);
                return true;
            }
        }

        return false;
    }
});