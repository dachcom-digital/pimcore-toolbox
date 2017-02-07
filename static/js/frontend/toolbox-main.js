/*
        __           __                                ___       _ __        __
   ____/ /___ ______/ /_  _________  ____ ___     ____/ (_)___ _(_) /_____ _/ /
  / __  / __ `/ ___/ __ \/ ___/ __ \/ __ `__ \   / __  / / __ `/ / __/ __ `/ /
 / /_/ / /_/ / /__/ / / / /__/ /_/ / / / / / /  / /_/ / / /_/ / / /_/ /_/ / /
 \__,_/\__,_/\___/_/ /_/\___/\____/_/ /_/ /_/   \__,_/_/\__, /_/\__/\__,_/_/
                                                       /____/
 copyright © dachcom digital

 */
var DachcomToolbox = (function () {

    'use strict';

    var self = {

        $doc: $ !== undefined ? $(document) : null,

        isBusy : false,

        editMode : false,

        init: function() {

            self.editMode = typeof _PIMCORE_EDITMODE !== 'undefined' && _PIMCORE_EDITMODE === true;
        }

    };

    return {
        init: self.init
    };

})();

if( $ !== undefined) {

    $(function() {
        'use strict';
        DachcomToolbox.init();
    });

}