<?php

if ($this->editmode) {

    $store = array(

        array("1", "Headline 1"),
        array("2", "Headline 2"),
        array("3", "Headline 3"),
        array("4", "Headline 4"),
        array("5", "Headline 5"),
        array("6", "Headline 6")

    );

    if ($this->select("headlineType")->isEmpty()) {
        $this->select("headlineType")->setDataFromResource("1");
    }

    echo $this->select("headlineType", array("store" => $store, "width" => 200, "reload" => true));

    ?>


<?php
}

?>
<h<?= $this->select("headlineType")->getData();?>>
    <?= $this->input("headlineText"); ?>
</h<?=$this->select("headlineType")->getData();?>>
