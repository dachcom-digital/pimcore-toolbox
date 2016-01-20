<?php if( $this->editmode) { ?>

    <div class="alert alert-info form-inline">

        <div class="form-group">
            <label>Abstand vor & nach Trennline:</label>
        </div>
        <div class="form-group">

            <?php

            if ($this->select("space")->isEmpty()) {

                $this->select("space")->setDataFromResource("default");

            }

            echo $this->select("space", [

                "width" => 100,
                "reload" => true,
                "store" => [["default",$this->translate('Default')], ["medium",$this->translate('Medium')], ["large",$this->translate('Large')]]

            ]);

            ?>
        </div>

    </div>

<?php } ?>

<div class="toolbox-separator">
    <hr class="<?=$this->select('space')->getData();?>">
</div>
