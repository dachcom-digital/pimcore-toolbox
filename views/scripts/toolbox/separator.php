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

        <?php if ($this->toolboxHelper()->hasAdditionalClasses('separator')) { ?>

            <div class="form-group">
                <label>Zusatz:</label>
            </div>
            <div class="form-group">

                <?php

                $acStore = $this->toolboxHelper()->getConfigArray( 'separator/additionalClasses', TRUE, TRUE );
                echo $this->select('separatorAdditionalClasses', array('store' => $acStore, 'width' => 200, 'reload' => true));
                ?>

            </div>


        <?php } ?>


    </div>

<?php } ?>

<div class="toolbox-separator <?= $this->select('separatorAdditionalClasses')->getData();?>">
    <hr class="<?=$this->select('space')->getData();?>">
</div>
