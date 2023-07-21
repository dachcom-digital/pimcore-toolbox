pimcore.registerNS('pimcore.document.editables.areablock');
pimcore.document.editables.areablock = Class.create(pimcore.document.editables.areablock, {

    enableInlineEditableConfig: false,

    initialize: function ($super, id, name, config, data, inherited) {

        var setupToolbar;

        this.config = this.parseConfig(config);

        setupToolbar = typeof this.config.toolbar === 'undefined' || this.config.toolbar !== false;

        // wait until we've sorted permissions out!
        this.config.toolbar = false;

        this.filterEditablesByPermission();

        $super(id, name, config, data, inherited);

        if (setupToolbar === true) {
            this.createToolBar();
        }
    },

    filterEditablesByPermission: function () {

        var filteredTypes = [],
            filteredGroups = {};

        if (Ext.isArray(this.config.types)) {
            Ext.Array.each(this.config.types, function (brick) {
                if (this.hasToolboxPermissionForEditable(brick.type) === true) {
                    filteredTypes.push(brick);
                }
            }.bind(this));
        }

        if (Ext.isObject(this.config.group)) {
            Ext.Object.each(this.config.group, function (groupName, groupData) {
                var groupIsValid = false;
                if (Ext.isArray(groupData)) {
                    Ext.Array.each(groupData, function (typeName) {
                        if (Ext.Array.map(filteredTypes, function (brick) {
                            return brick.type;
                        }).indexOf(typeName) !== -1) {
                            groupIsValid = true;
                            return false;
                        }
                    });
                }

                if (groupIsValid === true) {
                    filteredGroups[groupName] = groupData;
                }

            });

            this.config.group = filteredGroups;
        }

        this.config.types = filteredTypes;

    },

    refresh: function ($super) {
        $super();
        this.addToolboxEditBar();
    },

    hasToolboxPermissionForEditable: function (type) {

        if (!this.config.hasOwnProperty('toolbox_permissions')) {
            return true;
        }

        return this.config.toolbox_permissions.disallowed.indexOf(type) === -1;
    },

    addToolboxEditBar: function () {

        var i;

        if (this.elements.length === 0) {
            return;
        }

        for (i = 0; i < this.elements.length; i++) {
            this.parseEditable(i);
        }
    },

    parseEditable: function (i) {

        var $areaEl, $areaButtonsEl, $editDiv, $labelDiv, $el, isConfigurable;

        try {

            $areaEl = Ext.get(this.elements[i]);
            $areaButtonsEl = $areaEl.query('.pimcore_area_buttons[data-name="' + this.name + '"]')[0];
            $editDiv = $areaEl.query('.pimcore_block_dialog[data-name="' + this.name + '"]')[0];
            $labelDiv = $areaEl.query('.pimcore_block_label[data-name="' + this.name + '"] b')[0];
            isConfigurable = typeof $editDiv !== 'undefined';

            // check for permission
            if (this.hasToolboxPermissionForEditable($areaEl.getAttribute('type')) === false) {
                $areaEl.addCls('editable-blocked');
            }

            if ($labelDiv && !$areaEl.hasCls('toolbox-initialized')) {

                $areaEl.addCls('toolbox-initialized');

                $el = Ext.DomHelper.insertAfter($areaButtonsEl, {
                    'tag': 'div',
                    'class': 'toolbox-element-edit-button' + (isConfigurable ? '' : ' not-configurable'),
                    'data-title': $labelDiv.innerHTML
                }, true);

                if (isConfigurable === true) {

                    // remove pimcore default edit button!
                    Ext.get($editDiv).setVisible(false);

                    if (this.enableInlineEditableConfig === true) {
                        this.dispatchToolboxInlineEditing(i, $areaEl, $el, $editDiv);
                    } else {
                        this.dispatchToolboxDialogBoxEditing(i, $areaEl, $el, $editDiv);
                    }
                }
            }

        } catch (e) {
            console.error(e);
        }
    },

    dispatchToolboxDialogBoxEditing: function (index, $areaEl, $el, $editDiv) {

        var $editButton = new Ext.Button({
            cls: 'pimcore_block_button_plus',
            iconCls: 'pimcore_icon_edit',
            text: t('edit'),
            handler: this.openEditableDialogBox.bind(this, this.elements[index], $editDiv),
            listeners: {
                afterrender: function (ev) {
                    $areaEl.fireEvent('toolbox.bar.added', $areaEl);
                }
            }
        });

        $editButton.render($el);
    },

    dispatchToolboxInlineEditing: function (i, $areaEl, $el, $editDiv) {

        let configPanel = new Ext.Panel({
            header: false,
            autoSize: true,
            listeners: {
                afterrender: function (panel) {
                    $areaEl.fireEvent('toolbox.bar.added', $areaEl, true);
                }.bind(this)
            }
        });

        configPanel.render($areaEl);

        if (toolboxEditableManager.isInitialized()) {
            this.loadInlineEditables(configPanel, $editDiv);
        } else {
            toolboxEditableManager.add(this, configPanel, $editDiv);
        }

    },

    loadInlineEditables: function (configPanel, $editDiv) {

        let id = $editDiv.dataset.dialogId;
        let jsonConfig = document.getElementById('dialogBoxConfig-' + id).innerHTML;
        let config = JSON.parse(jsonConfig);

        let editablesInBox = this.getEditablesInDialogBox(id);
        let items = this.buildEditableDialogLayout(config.items, editablesInBox, 1);

        items = this.adjustInlineEditables(items);

        configPanel.add(items);

        Object.keys(editablesInBox).forEach(function (editableName) {

            if (typeof editablesInBox[editableName]['renderInDialogBox'] === "function") {
                editablesInBox[editableName].renderInDialogBox();
            } else {
                editablesInBox[editableName].render();
            }

            editablesInBox[editableName].setInherited(editablesInBox[editableName].inherited);
        }.bind(this));

        configPanel.updateLayout();
    },

    adjustInlineEditables: function (component) {

        if (Ext.isObject(component)) {
            component.autoSize = true;
            component.layout = 'fit';
            if (component.hasOwnProperty('items')) {
                Ext.Array.each(component.items, function (item, i) {
                    component.items[i] = this.adjustInlineEditables(item);
                }.bind(this))
            }
        }

        return component;
    }

});
