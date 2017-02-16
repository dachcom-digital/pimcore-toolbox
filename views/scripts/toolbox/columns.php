<?php if (!empty($this->type)) { ?>
    <div class="row">
        <?= $this->template('toolbox/columns/' . $this->partialName . '.php'); ?>
    </div>
<?php } ?>