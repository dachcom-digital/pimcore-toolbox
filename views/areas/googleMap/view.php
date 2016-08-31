<?php
$mapZoom = !$this->numeric('mapZoom')->isEmpty() ? $this->numeric('mapZoom')->getData() : 12;
$mapType = !$this->select('mapType')->isEmpty() ? $this->select('mapType')->getData() : 'roadmap';
?>
<?= $this->template('toolbox/googleMap.php', [ 'mapZoom' => $mapZoom, 'mapType' => $mapType ]) ?>