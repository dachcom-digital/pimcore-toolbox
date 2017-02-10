<?php if( $this->editmode ) { ?>
    <?= \Toolbox\Tool\ElementBuilder::buildElementConfig('googleMap', $this) ?>
<?php } ?>
<?= $this->template('toolbox/googleMap.php',
    [
        'mapZoom' => $this->numeric('mapZoom')->getData(),
        'mapType' => $this->select('mapType')->getData()
    ]
) ?>