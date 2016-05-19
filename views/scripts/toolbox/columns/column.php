<?php foreach ($this->columns as $i => $column) { ?>

    <?php

    if ( substr($column, 0, 1) == 'o' ) {
        $offset = (int)substr($column, -1);
        continue;
    }

    $name = 'column' . $column . '_' . $i;

    ?>

    <div class="col-md-<?= $column ?> col-sm-<?= $column ?><?php if ( $offset ) {?> col-md-offset-<?=$offset?> col-sm-offset-<?=$offset?><?php } ?> col-xs-12">

        <div class="toolbox-column<?php echo $this->equalHeight ? ' equal-height-item' : '' ?>">

            <?= $this->template('helper/areablock.php', [ 'name' => $name, 'type' => 'columns' ] ); ?>

        </div>

    </div>

    <?php if ( $offset ) unset($offset); ?>

<?php } ?>