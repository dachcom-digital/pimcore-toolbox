<?php if( $this->editmode ) { ?>
    <?= \Toolbox\Tool\ElementBuilder::buildElementConfig('googleMap', $this) ?>
<?php } ?>
<?php
$mapZoom = $this->numeric('mapZoom')->getData();
$mapType = $this->select('mapType')->getData();
?>
<?= $this->template('toolbox/googleMap.php', [ 'mapZoom' => $mapZoom, 'mapType' => $mapType ]) ?>