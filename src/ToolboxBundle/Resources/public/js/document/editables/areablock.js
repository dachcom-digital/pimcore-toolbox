pimcore.registerNS('pimcore.document.editables.areablock');
pimcore.document.editables.areablock = Class.create(pimcore.document.editables.areablock, {

    initialize: function ($super, id, name, options, data, inherited) {

        var i, $areaEl, $el, $editButton, $editDiv;

        $super(id, name, options, data, inherited);

        if (this.elements.length > 0) {

            for (i = 0; i < this.elements.length; i++) {

                try {

                    $areaEl = Ext.get(this.elements[i]);
                    $editDiv = $areaEl.query('.pimcore_area_edit_button[data-name="' + this.name + '"]')[0];

                    $areaEl.clearListeners();

                    if ($editDiv) {

                        $el = Ext.DomHelper.insertAfter($editDiv, {
                            'tag': 'div',
                            'class': 'toolbox-element-edit-button'
                        }, true);

                        //remove pimcore default button!
                        Ext.get($editDiv).destroy();

                        $editButton = new Ext.Button({
                            cls: 'pimcore_block_button_plus',
                            iconCls: 'pimcore_icon_edit',
                            text: t('edit'),
                            handler: this.editmodeOpen.bind(this, this.elements[i])
                        });

                        $editButton.render($el);

                    }

                } catch (e) {
                    console.log(e);
                }
            }
        }
    }
});
