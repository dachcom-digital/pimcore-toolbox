<?php
    $equalHeight = $this->checkbox('equalHeight')->isChecked() && !$this->editmode;
    $type = $this->select('type')->getData();
?>

<?php if ( !empty( $type ) ) { ?>

    <?php

        $type = explode('_', $type);
        $partialName = $type[0];
        $columns = array_splice($type, 1);
        $params = array( 'columns' => $columns, 'equalHeight' => $equalHeight  );

    ?>

    <div class="row">
        <?= $this->template('toolbox/columns/' . $partialName . '.php', $params); ?>
    </div>

<?php } ?>