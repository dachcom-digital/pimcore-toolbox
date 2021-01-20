pimcore.registerNS('pimcore.document.editables.dynamiclink');
pimcore.document.editables.dynamiclink = Class.create(toolbox.abstract.document.editables.link, {

    getType: function () {
        return 'dynamiclink';
    },

    openEditor: function () {

        this.window = this.openDynamicLinkEditPanel(this.data, {
            empty: this.empty.bind(this),
            cancel: this.cancel.bind(this),
            save: this.save.bind(this)
        });

    },

    openDynamicLinkEditPanel : function (data, callback) {

        var fieldPath = new Ext.form.TextField({
            fieldLabel: t('path'),
            value: data.path,
            name: "path",
            width: 520,
            fieldCls: "pimcore_droptarget_input",
            enableKeyEvents: true,
            listeners: {
                keyup: function (el) {
                    if(el.getValue().match(/^www\./)) {
                        el.setValue("http://" + el.getValue());
                    }
                }
            }
        });

        fieldPath.on("render", function (el) {

            if (typeof dndManager !== 'undefined') {
                dndManager.addDropTarget(
                    el.getEl(),
                    function(target, dd, e, data) {
                        return Ext.dd.DropZone.prototype.dropAllowed;
                    },
                    function(target, dd, e, data) {

                        var record = data.records[0];
                        if (record.data.elementType == "asset" || record.data.elementType == "document" || record.data.elementType == "object") {
                            if( record.data.elementType == "object") {
                                fieldPath.setValue(record.data.className + '::' + record.data.path);
                            } else {
                                fieldPath.setValue(record.data.path);
                            }
                            return true;
                        }

                        return false;
                    });
            }

        }.bind(this));

        var form = new Ext.FormPanel({
            itemId: "form",
            items: [
                {
                    xtype:'tabpanel',
                    deferredRender: false,
                    defaults:{autoHeight:true, bodyStyle:'padding:10px'},
                    border: false,
                    items: [
                        {
                            title:t('basic'),
                            layout:'vbox',
                            border: false,
                            defaultType: 'textfield',
                            items: [
                                {
                                    fieldLabel: t('text'),
                                    name: 'text',
                                    value: data.text
                                },
                                {
                                    xtype: "fieldcontainer",
                                    layout: 'hbox',
                                    border: false,
                                    items: [fieldPath, {
                                        xtype: "button",
                                        iconCls: "pimcore_icon_search",
                                        style: "margin-left: 5px",
                                        handler: function () {
                                            pimcore.helpers.itemselector(false, function (item) {
                                                if (item) {
                                                    if( item.type == 'object') {
                                                        fieldPath.setValue(item.classname + "::" + item.fullpath);
                                                    } else {
                                                        fieldPath.setValue(item.fullpath);
                                                    }

                                                    return true;
                                                }
                                            }, {
                                                type: ["asset","document","object"]
                                            });
                                        }
                                    }]
                                },
                                {
                                    xtype:'fieldset',
                                    layout: 'vbox',
                                    title: t('properties'),
                                    collapsible: false,
                                    defaultType: 'textfield',
                                    width: '100%',
                                    defaults: {
                                        width: 250
                                    },
                                    items :[
                                        {
                                            xtype: "combo",
                                            fieldLabel: t('target'),
                                            name: 'target',
                                            triggerAction: 'all',
                                            editable: true,
                                            mode: "local",
                                            store: ["","_blank","_self","_top","_parent"],
                                            value: data.target,
                                            width: 300
                                        },
                                        {
                                            fieldLabel: t('parameters'),
                                            name: 'parameters',
                                            value: data.parameters
                                        },
                                        {
                                            fieldLabel: t('anchor'),
                                            name: 'anchor',
                                            value: data.anchor
                                        },
                                        {
                                            fieldLabel: t('title'),
                                            name: 'title',
                                            value: data.title
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            title: t('advanced'),
                            layout:'form',
                            defaultType: 'textfield',
                            border: false,
                            items: [
                                {
                                    fieldLabel: t('accesskey'),
                                    name: 'accesskey',
                                    value: data.accesskey
                                },
                                {
                                    fieldLabel: t('relation'),
                                    name: 'rel',
                                    width: 300,
                                    value: data.rel
                                },
                                {
                                    fieldLabel: ('tabindex'),
                                    name: 'tabindex',
                                    value: data.tabindex
                                },
                                {
                                    fieldLabel: t('class'),
                                    name: 'class',
                                    width: 300,
                                    value: data["class"]
                                },
                                {
                                    fieldLabel: t('attributes') + ' (key="value")',
                                    name: 'attributes',
                                    width: 300,
                                    value: data["attributes"]
                                }
                            ]
                        }
                    ]
                }
            ],
            buttons: [
                {
                    text: t("empty"),
                    listeners:  {
                        "click": callback["empty"]
                    }
                },
                {
                    text: t("cancel"),
                    listeners:  {
                        "click": callback["cancel"]
                    }
                },
                {
                    text: t("save"),
                    listeners: {
                        "click": callback["save"]
                    },
                    iconCls: "pimcore_icon_save"
                }
            ]
        });

        var window = new Ext.Window({
            modal: false,
            width: 600,
            height: 470,
            title: t("edit_link"),
            items: [form],
            layout: "fit"
        });

        window.show();

        return window;
    }
});