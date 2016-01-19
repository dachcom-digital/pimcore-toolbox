<?php

$params = $this->toolboxHelper()->getAvailableBricks( array("columns") );

foreach ($this->columns as $i => $column) { ?>

    <?php $name = "cs" . $column . "_" . $i; ?>

    <div class="col-md-<?= $column ?> col-sm-<?= $column ?> col-xs-12">

        <?= $this->areablock('c' . $name, array( 'allowed' => $params['allowed'], 'params' => $params['additional'] )); ?>

    </div>

<?php } ?>
