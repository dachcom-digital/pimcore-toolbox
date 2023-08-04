pimcore.registerNS('pimcore.document.editables.googlemap');
pimcore.document.editables.googlemap = Class.create(pimcore.document.editable, {

    form: null,
    element: null,
    previewData: null,

    getType: function () {
        return 'googlemap';
    },

    getValue: function () {
        return this.data;
    },

    reload: function () {
        if (this.config.reload) {
            this.reloadDocument();
        }
    },

    initialize: function (id, name, config, data) {

        if (typeof google === 'undefined') {
            console.warn('toolbox googleMap: google js api is not reachable.');
            return false;
        }

        this.id = id;
        this.name = name;
        this.data = data.locations;
        this.previewData = data;
        this.config = this.parseConfig(config);
    },

    subRender: function () {

        var errorCollector = [], mapEditButton;

        if (typeof google === 'undefined') {
            return;
        }

        this.setupWrapper();

        this.element = Ext.get(this.id);

        mapEditButton = new Ext.Button({
            iconCls: 'pimcore_icon_geopoint',
            cls: 'googlemap_edit_locations_button',
            text: t('locations'),
            listeners: {
                'click': this.openEditor.bind(this)
            }
        });

        mapEditButton.render(this.getButtonHolder());

        if (this.previewData['hasValidKey'] === false) {

            Ext.DomHelper.append(this.element, {
                'tag': 'div',
                'class': 'tb-alert-info',
                'html': 'No valid API Key defined'
            }, true);

        }

        if (this.data && this.data.length > 0) {
            Ext.Array.each(this.data, function (location, i) {
                if (typeof location.status !== 'undefined' && location.status !== null) {
                    errorCollector.push('[' + i + ']: ' + location.status)
                }
            });
        }

        if (errorCollector.length > 0) {
            this.element.dom.setAttribute('data-fetch-error', errorCollector.join(','));
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
        return Ext.get(this.id).up('.toolbox-google-map').prev('.toolbox-element-edit-button');
    },

    openEditor: function () {
        this.window = this.openGoogleMapEditPanel(this.data, {
            cancel: this.cancel.bind(this),
            save: this.save.bind(this)
        });
    },

    openGoogleMapEditPanel: function (locations, callback) {

        var window, addLocation;

        locations = locations || {};

        window = new Ext.Window({
            modal: true,
            autoScroll: true,
            width: 900,
            height: 500,
            title: t('edit_locations'),
            layout: 'fit'
        });

        addLocation = function (ev, buttonEv, location) {

            var compositeField;

            if (typeof location === 'undefined') {
                location = {
                    title: '',
                    street: '',
                    zip: '',
                    city: '',
                    country: '',
                    hideInfoWindow: 0,
                    add: ''
                }
            }

            compositeField = new Ext.form.FormPanel({
                hideLabel: true,
                layout: 'form',
                style: 'margin-bottom: 15px; padding: 5px; background: rgba(214, 221, 230, 0.45);',
                items: [
                    {
                        xtype: 'container',
                        layout: 'hbox',
                        hideLabel: true,
                        style: 'padding-bottom:5px;',
                        border: false,
                        items: [
                            {
                                xtype: 'textfield',
                                name: 'location_title',
                                emptyText: t('location_title'),
                                summaryDisplay: true,
                                allowBlank: false,
                                blankText: t('field_mandatory'),
                                value: location.title,
                                flex: 1,
                                msgTarget: 'qtip'
                            }
                        ]
                    },
                    {
                        xtype: 'container',
                        layout: 'hbox',
                        hideLabel: true,
                        style: 'padding-bottom:5px;',
                        border: false,
                        items: [
                            {
                                xtype: 'textfield',
                                name: 'location_street',
                                fieldLabel: t('location_street'),
                                labelAlign: 'top',
                                style: 'margin: 0 5px 0 0',
                                summaryDisplay: true,
                                allowBlank: false,
                                blankText: t('field_mandatory'),
                                value: location.street,
                                flex: 1,
                                msgTarget: 'qtip'
                            },
                            {
                                xtype: 'textfield',
                                name: 'location_zip',
                                fieldLabel: t('location_zip'),
                                labelAlign: 'top',
                                style: 'margin: 0 5px 0 0',
                                summaryDisplay: true,
                                allowBlank: false,
                                blankText: t('field_mandatory'),
                                value: location.zip,
                                flex: 1,
                                msgTarget: 'qtip'
                            },
                            {
                                xtype: 'textfield',
                                name: 'location_city',
                                fieldLabel: t('location_city'),
                                labelAlign: 'top',
                                style: 'margin: 0 5px 0 0',
                                summaryDisplay: true,
                                allowBlank: false,
                                blankText: t('field_mandatory'),
                                value: location.city,
                                flex: 1,
                                msgTarget: 'qtip'
                            },
                            {
                                xtype: 'textfield',
                                name: 'location_country',
                                fieldLabel: t('location_country'),
                                labelAlign: 'top',
                                anchor: '100%',
                                summaryDisplay: true,
                                allowBlank: false,
                                blankText: t('field_mandatory'),
                                value: location.country,
                                flex: 1,
                                msgTarget: 'qtip'
                            }]
                    },
                    {
                        xtype: 'container',
                        layout: 'hbox',
                        hideLabel: true,
                        style: 'padding-bottom:5px;',
                        border: false,
                        items: [{
                            xtype: 'checkbox',
                            name: 'location_hide_info_window',
                            labelAlign: 'left',
                            fieldLabel: t('location_hide_info_window'),
                            value: location.hideInfoWindow,
                            anchor: '100%',
                            labelWidth: false,
                            style: 'width: 190px;',
                            labelStyle: 'width: 190px;'
                        }]
                    },
                    {
                        xtype: 'container',
                        layout: 'hbox',
                        hideLabel: true,
                        style: 'padding-bottom:5px;',
                        border: false,
                        items: [{
                            xtype: 'textarea',
                            name: 'location_add',
                            fieldLabel: t('location_add'),
                            labelAlign: 'top',
                            anchor: '100%',
                            summaryDisplay: true,
                            allowBlank: true,
                            value: location.add,
                            flex: 1,
                            grow: false
                        }]
                    },
                    {
                        xtype: 'hidden',
                        name: 'location_checksum',
                        value: typeof location.checksum !== 'undefined' ? location.checksum : null,
                    },
                    {
                        xtype: 'hidden',
                        name: 'location_lat',
                        value: typeof location.lat !== 'undefined' ? location.lat : null,
                    },
                    {
                        xtype: 'hidden',
                        name: 'location_lng',
                        value: typeof location.lng !== 'undefined' ? location.lng : null,
                    }
                ]
            });

            compositeField.add([{
                xtype: 'button',
                iconCls: 'pimcore_icon_delete',
                text: t('remove'),
                handler: function (compositeField, el) {
                    this.form.remove(compositeField);
                    this.form.updateLayout();
                }.bind(this, compositeField)
            }, {
                xtype: 'box',
                style: 'clear:both;'
            }]);

            this.form.add(compositeField);
            this.form.updateLayout();

        }.bind(this);

        this.form = new Ext.FormPanel({
            itemId: 'form',
            scrollable: 'y',
            layout: 'form',
            tbar: ['->', {
                xtype: 'button',
                text: t('location_add_location'),
                iconCls: 'pimcore_icon_add',
                handler: addLocation.bind(this)
            }],
            buttons: [
                {
                    text: t('save'),
                    listeners: {
                        'click': callback['save']
                    },
                    iconCls: 'pimcore_icon_save'
                },
                {
                    text: t('cancel'),
                    listeners: {
                        'click': callback['cancel']
                    },
                    iconCls: 'pimcore_icon_cancel'
                }
            ],
            listeners: {
                afterrender: function () {

                    if (locations && locations.length > 0) {
                        locations.forEach(function (location) {
                            addLocation(null, null, location);
                        });
                    }

                }
            }
        });

        document.body.classList.add('toolbox-modal-open');

        window.add(this.form);

        window.show();

        return window;
    },

    cancel: function () {
        document.body.classList.remove('toolbox-modal-open');
        this.window.close();
    },

    save: function () {

        var form = this.window.getComponent('form').getForm(),
            locations = [],
            location,
            values;

        if (!form.isValid()) {
            return;
        }

        values = form.getFieldValues();

        if (typeof values['location_street'] === 'string') {
            location = {
                title: values['location_title'],
                street: values['location_street'],
                zip: values['location_zip'],
                city: values['location_city'],
                country: values['location_country'],
                hideInfoWindow: values['location_hide_info_window'],
                add: values['location_add'],
                checksum: values['location_checksum'],
                lat: values['location_lat'],
                lng: values['location_lng'],
            };
            locations.push(location);
        } else {
            if (values.hasOwnProperty('location_title') && values['location_title'].length > 0) {
                values['location_title'].forEach(function (value, index) {
                    location = {
                        title: value,
                        street: values['location_street'][index],
                        zip: values['location_zip'][index],
                        city: values['location_city'][index],
                        country: values['location_country'][index],
                        hideInfoWindow: values['location_hide_info_window'][index],
                        add: values['location_add'][index],
                        checksum: values['location_checksum'][index],
                        lat: values['location_lat'][index],
                        lng: values['location_lng'][index],
                    };
                    locations.push(location);
                });
            }
        }

        this.data = locations;

        // close window
        document.body.classList.remove('toolbox-modal-open');
        this.window.close();

        this.reloadDocument();
    }
});