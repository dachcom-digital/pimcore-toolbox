<?php foreach ($this->columns as $column) { ?>

    <div class="<?= $column['btClass'] ?> <?= $column['offset']; ?>">
        <div class="<?= $column['columnClass'] ?>">
            <?= $this->template('toolbox/columns/part/areablock.php', ['column' => $column]); ?>
        </div>
    </div>

<?php } ?>