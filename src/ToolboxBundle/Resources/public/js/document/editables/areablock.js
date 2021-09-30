pimcore.registerNS('pimcore.document.editables.areablock');
pimcore.document.editables.areablock = Class.create(pimcore.document.editables.areablock, {

    initialize: function ($super, id, name, config, data, inherited) {

        var setupToolbar;

        this.config = this.parseConfig(config);

        setupToolbar = typeof this.config.toolbar === 'undefined' || this.config.toolbar !== false;

        // wait until we've sorted permissions out!
        this.config.toolbar = false;

        this.filterEditablesByPermission();

        $super(id, name, config, data, inherited);

        if(setupToolbar === true) {
            this.createToolBar();
        }

        this.addToolboxEditBar();
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

    hasToolboxPermissionForEditable: function(type) {

        if(!this.config.hasOwnProperty('toolbox_permissions')) {
            return true;
        }

        return this.config.toolbox_permissions.disallowed.indexOf(type) === -1;
    },

    addToolboxEditBar: function () {

        var i, $areaEl, $areaButtonsEl, $editDiv, $labelDiv, $el, $editButton;

        if (this.elements.length === 0) {
            return;
        }

        for (i = 0; i < this.elements.length; i++) {

            try {

                $areaEl = Ext.get(this.elements[i]);
                $areaButtonsEl = $areaEl.query('.pimcore_area_buttons[data-name="' + this.name + '"]')[0];
                $editDiv = $areaEl.query('.pimcore_block_dialog[data-name="' + this.name + '"]')[0];
                $labelDiv = $areaEl.query('.pimcore_block_label[data-name="' + this.name + '"] b')[0];

                // check for permission
                if (this.hasToolboxPermissionForEditable($areaEl.getAttribute('type')) === false) {
                    $areaEl.addCls('editable-blocked');
                }

                if ($editDiv && Ext.get($editDiv).isVisible() === true) {

                    //$areaEl.clearListeners();

                    $el = Ext.DomHelper.insertAfter($areaButtonsEl, {
                        'tag': 'div',
                        'class': 'toolbox-element-edit-button',
                        'data-title': $labelDiv.innerHTML
                    }, true);

                    //remove pimcore default button!
                    Ext.get($editDiv).setVisible(false);

                    $editButton = new Ext.Button({
                        cls: 'pimcore_block_button_plus',
                        iconCls: 'pimcore_icon_edit',
                        text: t('edit'),
                        handler: this.openEditableDialogBox.bind(this, this.elements[i], $editDiv),
                        listeners: {
                            afterrender: function(ev) {
                                $areaEl.fireEvent('toolbox.bar.added', $areaEl);
                            }
                        }
                    });

                    $editButton.render($el);

                }

            } catch (e) {
                console.error(e);
            }
        }

    }
});
