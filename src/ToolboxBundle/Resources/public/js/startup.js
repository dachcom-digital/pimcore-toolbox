pimcore.registerNS('pimcore.plugin.toolbox');

pimcore.plugin.toolbox = Class.create(pimcore.plugin.admin, {
    getClassName: function() {
        return 'pimcore.plugin.toolbox';
    },
    initialize: function() {
        pimcore.plugin.broker.registerPlugin(this);
    }

});

var toolboxPlugin = new pimcore.plugin.toolbox();