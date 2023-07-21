pimcore.registerNS('toolbox.document.editables.manager');
toolbox.document.editables.manager = Class.create({

    editables: [],
    initialized: false,

    add: function (areablock, configPanel, $editDiv) {
        this.editables.push({
            areablock: areablock,
            configPanel: configPanel,
            $editDiv: $editDiv
        });
    },

    setInitialized: function (state) {
        this.initialized = state;
        Ext.Array.each(this.editables, function (areablockData) {
            areablockData.areablock.loadInlineEditables(areablockData.configPanel, areablockData.$editDiv);
        });
    },

    isInitialized: function () {
        return this.initialized;
    }
});
