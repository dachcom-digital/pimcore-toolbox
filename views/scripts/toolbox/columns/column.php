<?php foreach ($this->columns as $column) { ?>

    <div class="col-md-<?= $column['btClass'] ?> col-sm-<?= $column['btClass'] ?><?php if (!is_null($column['offset'])) { ?> col-md-offset-<?= $column['offset'] ?> col-sm-offset-<?= $column['offset'] ?><?php } ?> col-xs-12">
        <div class="<?= $column['columnClass'] ?>">
            <?= $this->template('toolbox/columns/part/areablock.php', ['column' => $column]); ?>
        </div>
    </div>

<?php } ?>