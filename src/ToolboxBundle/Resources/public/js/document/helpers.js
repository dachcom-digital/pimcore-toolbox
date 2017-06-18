/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) 2009-2016 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

/*global localStorage */
pimcore.registerNS("pimcore.helpers.x");

pimcore.helpers.editmode.openVhsEditPanel = function (data, callback) {

    var form = null;
    var fieldPath = new Ext.form.TextField({
        fieldLabel: t('path'),
        itemId: "path",
        value: data.path,
        name: "path",
        width: 420,
        fieldCls: "pimcore_droptarget_input",
        enableKeyEvents: true,
        listeners: {
            keyup: function (el) {
                if((el.getValue().indexOf("youtu.be") >= 0 || el.getValue().indexOf("youtube.com") >= 0) && el.getValue().indexOf("http") >= 0) {
                    form.getComponent("type").setValue("youtube");
                } else if (el.getValue().indexOf("vimeo") >= 0 && el.getValue().indexOf("http") >= 0) {
                    form.getComponent("type").setValue("vimeo");
                } else if ((el.getValue().indexOf("dai.ly") >= 0 || el.getValue().indexOf("dailymotion") >= 0) && el.getValue().indexOf("http") >= 0) {
                    form.getComponent("type").setValue("dailymotion");
                }
            }.bind(this)
        }
    });

    var poster = new Ext.form.TextField({
        fieldLabel: t('poster_image'),
        value: data.poster,
        name: "poster",
        width: 420,
        fieldCls: "pimcore_droptarget_input",
        enableKeyEvents: true,
        listeners: {
            keyup: function (el) {
                //el.setValue(data.poster)
            }.bind(this)
        }
    });

    var initDD = function (el) {
        // register at global DnD manager
        new Ext.dd.DropZone(el.getEl(), {
            reference: this,
            ddGroup: "element",
            getTargetFromEvent: function(e) {
                return el.getEl();
            },

            onNodeOver : function(target, dd, e, data) {
                data = data.records[0].data;
                if (target && target.getId() == poster.getId()) {
                    if (data.elementType == "asset" && data.type == "image") {
                        return Ext.dd.DropZone.prototype.dropAllowed;
                    }
                } else {
                    if (data.elementType == "asset" && data.type == "video") {
                        return Ext.dd.DropZone.prototype.dropAllowed;
                    }
                }
                return Ext.dd.DropZone.prototype.dropNotAllowed;
            }.bind(this),

            onNodeDrop : function (target, dd, e, data) {
                if(target) {
                    data = data.records[0].data;

                    if(target.getId() == fieldPath.getId()) {
                        if (data.elementType == "asset" && data.type == "video") {
                            fieldPath.setValue(data.path);
                            form.getComponent("type").setValue("asset");
                            return true;
                        }
                    } else if (target.getId() == poster.getId()) {
                        if (data.elementType == "asset" && data.type == "image") {
                            poster.setValue(data.path);
                            return true;
                        }
                    }
                }

                return false;
            }.bind(this)
        });
    };

    fieldPath.on("render", initDD);
    poster.on("render", initDD);

    var searchButton = new Ext.Button({
        iconCls: "pimcore_icon_search",
        handler: function () {
            pimcore.helpers.itemselector(false, function (item) {
                if (item) {
                    fieldPath.setValue(item.fullpath);
                    return true;
                }
            }, {
                type: ["asset"],
                subtype: {
                    asset: ["video"]
                }
            });
        }
    });

    var updateType = function (type) {
        searchButton.enable();

        var labelEl = form.getComponent("pathContainer").getComponent("path").labelEl;
        labelEl.update(t("path"));

        if(type != "asset") {
            searchButton.disable();
        }

        if(type == "youtube") {
            labelEl.update("ID");
        }

        if(type == "vimeo") {
            labelEl.update("ID");
        }

        if(type == "dailymotion") {
            labelEl.update("ID");
        }
    };

    var videoTypeStore = new Ext.data.JsonStore({
        autoLoad: true,
        fields: ['name', 'value'],
        proxy: {
            type: 'ajax',
            url: '/toolbox/ajax/video-allowed-video-types',
            reader: {
                type: 'json'
            }
        }
    });

    form = new Ext.FormPanel({
        itemId: "form",
        bodyStyle: "padding:10px;",
        items:
        [
            {
                xtype: "combo",
                itemId: "type",
                fieldLabel: t('type'),
                displayField: 'name',
                name: 'type',
                triggerAction: 'all',
                editable: false,
                width: 270,
                mode: "local",
                store: videoTypeStore,
                value: data.type,
                listeners: {
                    select: function (combo) {
                        var type = combo.getValue();
                        updateType(type);
                    }.bind(this)
                }
            },
            {
                xtype: "fieldcontainer",
                layout: 'hbox',
                border: false,
                itemId: "pathContainer",
                items: [fieldPath, searchButton]
            },
            poster,
            {
                xtype: "checkbox",
                itemId: "showAsLightbox",
                checked: false,
                name: "showAsLightbox",
                fieldLabel: t('show in lightbox'),
                value: data.showAsLightbox
            },
            {
                xtype: "textfield",
                name: "title",
                itemId: "title",
                fieldLabel: t('title'),
                width: 420,
                value: data.title
            },
            {
                xtype: "textarea",
                itemId: "description",
                name: "description",
                fieldLabel: t('description'),
                width: 420,
                height: 50,
                value: data.description
            }
        ],
        buttons: [
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
        width: 500,
        height: 450,
        scrollable: true,
        title: t("video"),
        items: [form],
        layout: "fit",
        listeners: {
            afterrender: function () {
                updateType(data.type);
            }.bind(this)
        }
    });
    window.show();

    return window;
};