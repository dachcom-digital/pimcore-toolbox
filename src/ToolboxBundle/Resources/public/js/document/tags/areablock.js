pimcore.registerNS('pimcore.document.tags.areablock');
pimcore.document.tags.areablock = Class.create(pimcore.document.tags.areablock, {

    /**
     * Override Pimcore Button Placement for Website Blocks.
     *
     * @param $super
     * @param id
     * @param name
     * @param options
     * @param data
     * @param inherited
     */
    initialize: function($super, id, name, options, data, inherited) {

        var i, $el, $editButton, $editDiv;

        $super(id, name, options, data, inherited);

        if (this.elements.length > 0) {

            for (i = 0; i < this.elements.length; i++) {

                try {

                    $editDiv = Ext.get(this.elements[i]).query('.pimcore_area_edit_button[data-name="' + this.name + '"]')[0];
                    if($editDiv) {

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
