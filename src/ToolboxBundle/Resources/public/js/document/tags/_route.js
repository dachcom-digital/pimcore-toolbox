// deprecated. remove with toolbox 4.0
pimcore.registerNS('toolbox.abstract.document.editable');
pimcore.registerNS('toolbox.abstract.document.editables.link');
pimcore.registerNS('toolbox.abstract.document.editables.relations');
pimcore.registerNS('toolbox.abstract.document.editables.video');

toolbox.abstract.document.editable = Class.create(pimcore.document.tag, {});
toolbox.abstract.document.editables.link = Class.create(pimcore.document.tags.link, {});
toolbox.abstract.document.editables.relations = Class.create(pimcore.document.tags.relations, {});
toolbox.abstract.document.editables.video = Class.create(pimcore.document.tags.video, {});