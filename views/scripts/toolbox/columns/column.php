<?php foreach ($this->columns as $i => $column) { ?>

    <?php $name = 'column' . $column . '_' . $i; ?>

    <div class="col-md-<?= $column ?> col-sm-<?= $column ?> col-xs-12<?php echo $this->equalHeight ? ' equal-height-item' : '' ?>">

        <div class="toolbox-column">

            <?= $this->template('helper/areablock.php', [ 'name' => $name, 'excludeBricks' => array('columns') ] ); ?>

        </div>

    </div>

<?php } ?>