pimcore.registerNS('pimcore.document.tags.googlemap');

pimcore.document.tags.googlemap = Class.create(pimcore.document.tag, {

    getType: function () {
        return 'googlemap';
    },

    getValue: function () {
        return this.data;
    },

    reload : function () {
        if (this.options.reload) {
            this.reloadDocument();
        }
    },

    initialize: function(id, name, options, data, inherited) {

        if(typeof google === 'undefined') {
            console.warn('toolbox googleMap: google js api is not reachable.');
            return false;
        }

        this.id = id;
        this.name = name;
        this.data = data;

        this.options = this.parseOptions(options);
        this.geocoder = new google.maps.Geocoder();

        this.setupWrapper();

        Ext.get(id).setStyle({
            display:'inline'
        });

        var buttonHolder = Ext.get(id).up('.toolbox-google-map').prev('.toolbox-element-edit-button'),
            mapEditButton = new Ext.Button({
                iconCls: 'pimcore_icon_geopoint',
                cls: 'googlemap_edit_locations_button',
                text: t('locations'),
                listeners: {
                    'click': this.openEditor.bind(this)
                }
            }
            );

        mapEditButton.render(buttonHolder);
    },

    openEditor: function() {

        this.window = this.openGooglemapEditPanel(this.data, {
            cancel: this.cancel.bind(this),
            save: this.save.bind(this)
        });

    },

    openGooglemapEditPanel: function (data, callback) {

        data = data || [];

        var addLocation = function(location) {

            if ( typeof location == 'undefined' ) {

                location = {
                    title: '',
                    street: '',
                    zip: '',
                    city: '',
                    country: '',
                    add: ''
                }

            }

            var itemRow1 = [{
                xtype: 'textfield',
                name: 'location_title[]',
                fieldLabel: t('location_title'),
                labelAlign: 'top',
                anchor: '100%',
                summaryDisplay: true,
                allowBlank: false,
                blankText: t('field_mandatory'),
                value : location.title,
                flex: 1,
                msgTarget: 'qtip'
            }];

            var itemRow2 = [{
                xtype: 'textfield',
                name: 'location_street[]',
                fieldLabel: t('location_street'),
                labelAlign: 'top',
                anchor: '100%',
                summaryDisplay: true,
                allowBlank: false,
                blankText: t('field_mandatory'),
                value : location.street,
                flex: 1,
                msgTarget: 'qtip'
            },
                {
                    xtype: 'textfield',
                    name: 'location_zip[]',
                    fieldLabel: t('location_zip'),
                    labelAlign: 'top',
                    anchor: '100%',
                    summaryDisplay: true,
                    allowBlank: false,
                    blankText: t('field_mandatory'),
                    value : location.zip,
                    flex: 1,
                    msgTarget: 'qtip'
                },
                {
                    xtype: 'textfield',
                    name: 'location_city[]',
                    fieldLabel: t('location_city'),
                    labelAlign: 'top',
                    anchor: '100%',
                    summaryDisplay: true,
                    allowBlank: false,
                    blankText: t('field_mandatory'),
                    value : location.city,
                    flex: 1,
                    msgTarget: 'qtip'
                },
                {
                    xtype: 'textfield',
                    name: 'location_country[]',
                    fieldLabel: t('location_country'),
                    labelAlign: 'top',
                    anchor: '100%',
                    summaryDisplay: true,
                    allowBlank: false,
                    blankText: t('field_mandatory'),
                    value : location.country,
                    flex: 1,
                    msgTarget: 'qtip'
            }];

            var itemRow3 = [{
                xtype: 'textarea',
                name: 'location_add[]',
                fieldLabel: t('location_add'),
                labelAlign: 'top',
                anchor: '100%',
                summaryDisplay: true,
                allowBlank: true,
                value : location.add,
                flex: 1,
                grow: false
            }];


            var compositeField = new Ext.form.FieldContainer({
                layout: 'anchor',
                hideLabel: true,
                style: 'padding-bottom:5px;',
                items: [
                    Ext.form.FieldContainer({
                        layout: 'hbox',
                        hideLabel: true,
                        style: 'padding-bottom:5px;',
                        border: false,
                        items: itemRow1
                    }),
                    Ext.form.FieldContainer({
                        layout: 'hbox',
                        hideLabel: true,
                        style: 'padding-bottom:5px;',
                        border: false,
                        items: itemRow2
                    }),
                    Ext.form.FieldContainer({
                        layout: 'hbox',
                        hideLabel: true,
                        style: 'padding-bottom:5px;',
                        border: false,
                        items: itemRow3
                    })
                ]
            });

            compositeField.add([{
                xtype: 'button',
                iconCls: 'pimcore_icon_delete',
                text: t('remove'),
                handler: function (compositeField, el) {
                    selector.remove(compositeField);
                    selector.updateLayout();
                }.bind(this, compositeField)
            },{
                xtype: 'box',
                style: 'clear:both;'
            }]);

            selector.add(compositeField);
            selector.updateLayout();

        }

        var selector = new Ext.form.FieldSet({

            title: t('locations'),
            collapsible: false,
            autoHeight: true,
            style: '',
            items: [{
                xtype: 'toolbar',
                style: 'margin-bottom: 10px;',
                items: ['->', {
                    xtype: 'button',
                    text: t('add'),
                    iconCls: 'pimcore_icon_add',
                    handler: addLocation,
                    tooltip: {
                        title:'',
                        text: t('add_metadata')
                    }
                }]
            }]
        });

        if ( data && data.length > 0 ) {

            data.forEach(function(location) {

                addLocation(location);

            });

        }

        var form = new Ext.FormPanel({
            itemId: 'form',
            scrollable: true,
            items: [
                selector
            ],
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
                    listeners:  {
                        'click': callback['cancel']
                    },
                    iconCls: 'pimcore_icon_cancel'
                }
            ]
        });


        var window = new Ext.Window({
            modal: false,
            width: 900,
            height: 500,
            title: t('edit_locations'),
            items: [form],
            layout: 'fit'
            //autoScroll: true
        });

        window.show();

        return window;

    },

    cancel: function () {

        this.window.close();

    },

    save: function() {

        var form = this.window.getComponent('form').getForm(),
            locations = [],
            location;

        if ( form.isValid() ) {

            var values = form.getFieldValues();

            if (typeof values['location_street[]'] === 'string') {

                location = {
                    title: values['location_title[]'],
                    street: values['location_street[]'],
                    zip: values['location_zip[]'],
                    city: values['location_city[]'],
                    country: values['location_country[]'],
                    add: values['location_add[]']
                };

                locations.push(location);

            } else {

                if ( values.hasOwnProperty('location_title[]') && values['location_title[]'].length > 0 ) {

                    values['location_title[]'].forEach(function(value, index) {

                        location = {
                            title: value,
                            street: values['location_street[]'][index],
                            zip: values['location_zip[]'][index],
                            city: values['location_city[]'][index],
                            country: values['location_country[]'][index],
                            add: values['location_add[]'][index]
                        };

                        locations.push(location);

                    });

                }

            }

            this.data = locations;

            // close window
            this.window.close();
            this.reload();

        }

    }

});