pimcore.registerNS("pimcore.document.tags.globallink");
pimcore.document.tags.globallink = Class.create(pimcore.document.tags.link, {

    getType: function () {
        return "globallink";
    }

});