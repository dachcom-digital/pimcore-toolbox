<?php
if($this->editmode) {

    if ( $this->numeric('mapZoom')->isEmpty() ) {
        $this->numeric("mapZoom")->setDataFromResource(12);
    }

    if($this->select("mapType")->isEmpty()){
        $this->select("mapType")->setDataFromResource("roadmap");
    }

}
?>

<div class="toolbox-edit-overlay">

    <div class="t-row">
        <label><?= $this->translateAdmin('Map zoom') ?></label>
        <?= $this->numeric('mapZoom', [
            'width' => 100,
            'minValue' => 0,
            'maxValue' => 19,
            'decimalPrecision' => 0
        ]) ?>
    </div>
    <div class="t-row">
        <label><?= $this->translateAdmin('Map type') ?></label>
        <?= $this->select("mapType", [
            "store" => [
                ["roadmap", "ROADMAP"],
                ["satellite", "SATELLITE"],
                ["hybrid", "HYBRID"],
                ["terrain", "TERRAIN"]
            ]
        ]); ?>
    </div>

</div>