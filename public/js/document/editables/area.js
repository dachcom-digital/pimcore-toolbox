pimcore.registerNS('pimcore.document.editables.area');
pimcore.document.editables.area = Class.create(pimcore.document.editables.area, {

    initialize: function ($super, id, name, config, data, inherited) {

        $super(id, name, config, data, inherited);

        this.parseEditable(id);
    },

    parseEditable: function (id) {

        var $areaEl, $areaDialog, $editDiv, $el, isConfigurable;

        try {

            $areaEl = Ext.get(id);

            $areaDialog = $areaEl.query('.pimcore_area_dialog[data-name="' + this.name + '"]')[0];
            $editDiv = $areaEl.query('.pimcore_block_button_dialog')[0];
            isConfigurable = typeof $editDiv !== 'undefined';

            if ($areaEl && !$areaEl.hasCls('toolbox-initialized')) {

                $areaEl.addCls('toolbox-initialized');

                $el = Ext.DomHelper.insertBefore($areaEl, {
                    'tag': 'div',
                    'class': 'toolbox-element-edit-button' + (isConfigurable ? '' : ' not-configurable'),
                    'data-title': 'area'
                }, true);

                if (isConfigurable === true) {
                    // remove pimcore default edit button!
                    Ext.get($editDiv).setVisible(false);
                    this.dispatchToolboxDialogBoxEditing($areaEl, $el, $areaDialog);
                }
            }

        } catch (e) {
            console.error(e);
        }
    },

    dispatchToolboxDialogBoxEditing: function ($areaEl, $el, $areaDialog) {

        var $editButton = new Ext.Button({
            cls: 'pimcore_block_button_plus',
            iconCls: 'pimcore_icon_edit',
            text: t('edit'),
            handler: this.openEditableDialogBox.bind(this, $areaEl, $areaDialog)
        });

        $editButton.render($el);
    }

});
