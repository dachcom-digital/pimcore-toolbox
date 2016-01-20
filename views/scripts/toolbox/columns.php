<div class="row">
    <?php

    $store = $this->toolboxHelper()->getConfigArray( 'columnElements', TRUE );

    if ($this->editmode) {

        if ($this->select("type")->isEmpty()) {
            $this->select("type")->setDataFromResource("column_12");
        } ?>

        <div class="col-xs-12">
            <?= $this->select("type", array("reload" => true, "store" => $store)); ?>
        </div>

    <?php

    }

    $type = $this->select("type")->getData();

    if ( !empty( $type ) ) {

        $type = explode('_', $type);

        $partialName = $type[0];
        $columns = array_splice($type, 1);

        $params = array(

            'columns' => $columns

        );

        $this->template('toolbox/columns/' . $partialName . '.php', $params);

    }

    ?>
</div>
