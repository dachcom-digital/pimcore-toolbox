//remove with pimcore 4.4!
CKEDITOR.on( 'instanceCreated', function( event ) {

    var editor = event.editor;

    editor.on( 'configLoaded', function() {

        editor.config.allowedContent = null;
        editor.config.extraAllowedContent = "*[pimcore_type,pimcore_id]";

    });

});