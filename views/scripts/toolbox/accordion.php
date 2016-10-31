<?php

$id = uniqid('accordion-');
$type = $this->select('type')->getData();
$panels = $this->block('panels', array('default' => 2 ));

?>
<div class="panel-group" id="<?= $id ?>" role="tablist" aria-multiselectable="true">

    <?php while ($panels->loop()) { ?>

        <div class="panel <?= $type ?>">

            <div
                class="panel-heading collapsed"
                data-toggle="<?= $this->editmode ? '' : 'collapse' ?>"
                data-parent="#<?= $id ?>"
                data-target="#panel-<?= $id ?>-<?= $panels->getCurrentIndex() ?>">
                <h3 class="panel-title">
                    <a class="accordion-toggle"
                        role="button">
                        <?= $this->input('name', ['width' => 500]) ?>
                    </a>
                </h3>

            </div>

            <div id="panel-<?= $id ?>-<?= $panels->getCurrentIndex() ?>" class="panel-collapse collapse <?= $this->editmode ? 'in' : '' ?>">

                <div class="panel-body">
                    <?= $this->template('helper/areablock.php', [ 'name' => 'accord', 'type' => 'accordion' ] ); ?>
                </div>

            </div>

        </div>

    <?php } ?>

</div>

