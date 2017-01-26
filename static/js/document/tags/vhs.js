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

pimcore.registerNS("pimcore.document.tags.vhs");
pimcore.document.tags.vhs = Class.create(pimcore.document.tags.video, {

    openEditor: function () {

        // disable the global dnd handler in this editmode/frame
        window.dndManager.disable();

        this.window = pimcore.helpers.editmode.openVhsEditPanel(this.data, {
            save: this.save.bind(this),
            cancel: this.cancel.bind(this)
        });
    },

    getType: function () {
        return "vhs";
    }
});