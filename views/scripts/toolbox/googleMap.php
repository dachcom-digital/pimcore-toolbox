<div class="toolbox-googlemap-container embed-responsive embed-responsive-16by9">
    <?= $this->googlemap('googlemap', [
        'reload' => true,
        'mapZoom' => $this->mapZoom,
        'mapType' => $this->mapType
    ]); ?>
</div>