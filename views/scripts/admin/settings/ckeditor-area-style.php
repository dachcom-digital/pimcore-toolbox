<?php if( isset( $this->globalStyleSets ) && !empty( $this->globalStyleSets ) ) { ?>
    <?php foreach( $this->globalStyleSets as $stylesSetName => $stylesSetData ) { ?>
        CKEDITOR.stylesSet.add('<?= $stylesSetName; ?>', <?= json_encode( $stylesSetData ); ?>);
    <?php } ?>
<?php } ?>

CKEDITOR.editorConfig = function( config ) {
<?php if( is_array( $this->config ) ) { ?>
    <?php foreach( $this->config as $configName => $configData ) { ?>
        config.<?= $configName; ?> = <?= json_encode( $configData ); ?>;
    <?php } ?>
<?php } ?>
};