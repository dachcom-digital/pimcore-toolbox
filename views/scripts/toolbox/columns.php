
    <?php

    $store = $this->toolboxHelper()->getConfigArray( 'columnElements', TRUE );

    if ($this->editmode) {

        if ($this->select("type")->isEmpty())
        {
            $this->select("type")->setDataFromResource("column_12");
        }

        ?>

        <div class="alert alert-info form-inline">

            <div class="form-group">
                <label>Spalten: </label>
            </div>
            <div class="form-group">
                <?= $this->select("type", array("reload" => true, "store" => $store)); ?>
            </div>

        </div>

    <?php } ?>

    <?php $type = $this->select("type")->getData(); ?>

    <?php if ( !empty( $type ) ) { ?>

        <?php

            $type = explode('_', $type);
            $partialName = $type[0];
            $columns = array_splice($type, 1);
            $params = array( 'columns' => $columns  );
        ?>

        <div class="row">
            <div class="toolbox-columns">
                <?= $this->template('toolbox/columns/' . $partialName . '.php', $params); ?>
            </div>
        </div>

    <?php } ?>