<?php if( isset( $this->globalStyleSets ) && !empty( $this->globalStyleSets ) ) { ?>
    <?php foreach( $this->globalStyleSets as $stylesSetName => $stylesSetData ) { ?>
        CKEDITOR.stylesSet.add('<?= $stylesSetName; ?>', <?= json_encode( $stylesSetData ); ?>);
    <?php } ?>
<?php } ?>

CKEDITOR.on( 'instanceCreated', function( event ) {

    var editor = event.editor;

    //http://ckeditor.com/latest/samples/toolbarconfigurator/index.html#advanced
    editor.on( 'configLoaded', function() {

        //remove with pimcore 4.4!
        editor.config.allowedContent = undefined;

        <?php
            if( is_array( $this->config ) ) {
               foreach( $this->config as $configName => $configData ) {
                    echo 'if( editor.config.' . $configName . ' === undefined ) {';
                        echo 'editor.config.' . $configName . ' = ' . json_encode( $configData ) . ';';
                    echo '}';
               }
            }
        ?>

    });

});
