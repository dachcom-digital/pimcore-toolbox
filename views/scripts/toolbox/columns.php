<?php
    $equalHeight = $this->checkbox('equalHeight')->isChecked() && !$this->editmode;
    $type = $this->select('type')->getData();
?>

<?php if ( !empty( $type ) ) { ?>

    <?php

        $t = explode('_', $type);

        if ( $this->toolboxHelper()->templateExists($this, 'toolbox/columns/' . $type .  '.php') ) {
            $partialName = $type;
        } else {
            $partialName = $t[0];
        }

        $columns = array_splice($t, 1);
        $params = array(
            'columns' => $columns,
            'equalHeight' => $equalHeight ? ' equal-height-item' : '',
        );

    ?>

    <div class="row">
        <?= $this->template('toolbox/columns/' . $partialName . '.php', $params); ?>
    </div>

<?php } ?>