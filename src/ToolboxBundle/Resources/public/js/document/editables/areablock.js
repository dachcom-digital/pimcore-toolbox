pimcore.registerNS('pimcore.document.editables.areablock');
pimcore.document.editables.areablock = Class.create(pimcore.document.editables.areablock, {

    initialize: function ($super, id, name, options, data, inherited) {

        $super(id, name, options, data, inherited);

        this.addToolboxEditBar();

    },

    refresh: function ($super) {

        $super();

        this.addToolboxEditBar();
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
