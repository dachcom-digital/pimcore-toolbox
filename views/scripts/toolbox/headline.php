<?php if ($this->editmode) { ?>

    <div class="alert alert-info form-inline">

        <div class="form-group">
            <label>Grösse:</label>
        </div>
        <div class="form-group">

            <?php

            $store = $this->toolboxHelper()->getConfigArray( 'headlines', TRUE );

            if ($this->select("headlineType")->isEmpty()) {

                $this->select("headlineType")->setDataFromResource("h3");

            }

            echo $this->select("headlineType", array("store" => $store, "width" => 200, "reload" => true));

            ?>

        </div>

    </div>

<?php } ?>

<div class="toolbox-headline">
    <<?= $this->select("headlineType")->getData();?>><?= $this->input("headlineText"); ?></<?=$this->select("headlineType")->getData();?>>
</div>