<?php

$store = array(
    array("panel-default", "Default"),
    array("panel-primary", "Primary"),
    array("panel-success", "Success"),
    array("panel-info", "Info"),
    array("panel-warning", "Warning"),
    array("panel-danger", "Danger")
);

if ($this->editmode) {
    if ($this->select("type")->isEmpty()) {
        $this->select("type")->setDataFromResource("panel-default");
    }

    echo $this->select("type", array("reload" => true, "store" => $store));
}

$type = $this->select("type")->getData();

$panels = $this->block('panels');

$id = uniqid('accordion-');
?>

<div class="panel-group" id="<?= $id ?>" role="tablist" aria-multiselectable="true">
    <?php while ($panels->loop()): ?>
        <div class="panel <?= $type ?>">
            <div class="panel-heading" role="tab">
                <h4 class="panel-title">
                    <a class="accordion-toggle collapsed" role="button"
                       data-toggle="<?= $this->editmode ? '' : 'collapse' ?>"
                       data-parent="#<?= $id ?>"
                       href="#panel-<?= $id ?>-<?= $panels->getCurrentIndex() ?>">
                        <?= $this->input('name') ?>
                    </a>
                </h4>
            </div>
            <div id="panel-<?= $id ?>-<?= $panels->getCurrentIndex() ?>"
                 class="panel-collapse collapse <?= $this->editmode ? 'in' : '' ?>">
                <div class="panel-body">
                    <?= $this->template('helper/areablock.php', [
                        'name' => 'a',
                        'excludeBricks' => ['accordion']
                    ]) ?>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>
