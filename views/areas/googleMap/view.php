<?php if ($this->editmode) { ?>
    <?= \Toolbox\Tool\ElementBuilder::buildElementConfig('googleMap', $this) ?>
<?php } ?>
<div class="toolbox-element toolbox-google-map <?= $this->select('googleMapAdditionalClasses')->getData(); ?>">
    <?= $this->template('toolbox/googleMap.php',
        [
            'mapZoom' => $this->numeric('mapZoom')->getData(),
            'mapType' => $this->select('mapType')->getData()
        ]
    ) ?>
</div>
