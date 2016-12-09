CKEDITOR.editorConfig = function( config ) {
    <?php if( is_array( $this->config ) ) { ?>
        <?php foreach( $this->config as $configName => $configData ) { ?>
            config.<?= $configName; ?> = <?= json_encode( $configData ); ?>;
        <?php } ?>
    <?php } ?>
};