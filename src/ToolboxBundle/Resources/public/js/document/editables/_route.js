// deprecated. remove with toolbox 4.0
pimcore.registerNS('toolbox.abstract.document.editable');
pimcore.registerNS('toolbox.abstract.document.editables.link');
pimcore.registerNS('toolbox.abstract.document.editables.relations');
pimcore.registerNS('toolbox.abstract.document.editables.video');

toolbox.abstract.document.editable = Class.create(pimcore.document.editable, {});
toolbox.abstract.document.editables.link = Class.create(pimcore.document.editables.link, {});
toolbox.abstract.document.editables.relations = Class.create(pimcore.document.editables.relations, {});
toolbox.abstract.document.editables.video = Class.create(pimcore.document.editables.video, {});