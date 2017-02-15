pimcore.registerNS('pimcore.document.edit');
pimcore.document.edit = Class.create(pimcore.document.edit, {

    /**
     * Because pimocore does a sloppy job in this method, we need to override it:
     * maskFrames will trigger to early => all iframe masks will have a wrong position!
     */
    maskFrames: function () {

        var _ = this, i, iFrames, iframe;

        try {

            if (typeof _.frame.Ext !== 'object') {
                return;
            }

            iFrames = _.frame.document.getElementsByTagName('iframe');

            for (i = 0; i < iFrames.length; i++) {

                iframe = iFrames[i];

                iframe.onload = function() {

                    var $frame = Ext.get(this),
                        $parentElement = $frame.parent(),
                        $element, width, height;

                    width = $frame.getWidth();
                    height = $frame.getHeight();

                    $parentElement.applyStyles({
                        position: 'relative'
                    });

                    $element = $parentElement.createChild({
                        tag: 'div',
                        id: Ext.id()
                    });

                    $element.setStyle({
                        width: width + 'px',
                        height: height + 'px',
                        left: 0,
                        top: 0
                    });

                    $element.addCls('pimcore_iframe_mask');

                };

            }
        } catch (e) {
            console.log(e);
        }
    }
});