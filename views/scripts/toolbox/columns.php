
    <?php

    $store = $this->toolboxHelper()->getConfigArray( 'columnElements', TRUE );
    $equalHeight = $this->checkbox('equalHeight')->isChecked() && !$this->editmode;

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

            <div class="form-group">
                <label class="checkbox">Gleiche Höhe?</label>
            </div>
            <div class="form-group">

                <?php

                echo $this->checkbox("equalHeight");

                ?>
            </div>

        </div>

    <?php } ?>

    <?php $type = $this->select("type")->getData(); ?>

    <?php if ( !empty( $type ) ) { ?>

        <?php

            $type = explode('_', $type);
            $partialName = $type[0];
            $columns = array_splice($type, 1);
            $params = array( 'columns' => $columns, 'equalHeight' => $equalHeight  );
        ?>

        <div class="row">
            <div class="toolbox-columns<?php echo $equalHeight ? ' equal-height' : '' ?>">
                <?= $this->template('toolbox/columns/' . $partialName . '.php', $params); ?>
            </div>
        </div>

    <?php } ?>