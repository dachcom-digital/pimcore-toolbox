<?php if ($this->editmode) { ?>

    <div class="container">
    <div class="row">
    <div class="col-xs-12">

<?php } ?>

<?= $this->areablock(
    'content',
    \Toolbox\Tool\Area::getAreaBlockConfiguration( NULL, TRUE )
); ?>

<?php if ($this->editmode) { ?>

    </div>
    </div>
    </div>

<?php } ?>