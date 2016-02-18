<?php

$accordionSettings = $this->toolboxHelper()->getConfigArray( 'accordion/layouts', TRUE );

$store = array();

$id = uniqid('accordion-');

if( !empty( $accordionSettings ) )
{
    $store = $accordionSettings;
}
else
{
    $store[] = array('panel-default', 'Default');
}

?>

<?php if ($this->editmode) { ?>

    <div class="alert alert-info form-inline">

        <div class="form-group">
            <label>Typ:</label>
        </div>
        <div class="form-group">

        <?php

            if ($this->select("type")->isEmpty()) {
                $this->select("type")->setDataFromResource("panel-default");
            }

            echo $this->select("type", array("reload" => true, "store" => $store));

        ?>

        </div>

    </div>

<?php } ?>

<?php

$type = $this->select("type")->getData();
$panels = $this->block('panels', array('default' => 2 ));

?>

<div class="toolbox-accordion">

    <div class="panel-group" id="<?= $id ?>" role="tablist" aria-multiselectable="true">

        <?php while ($panels->loop()) { ?>

            <div class="panel <?= $type ?>">

                <div
                    class="panel-heading collapsed"
                    data-toggle="<?= $this->editmode ? '' : 'collapse' ?>"
                    data-parent="#<?= $id ?>"
                    data-target="#panel-<?= $id ?>-<?= $panels->getCurrentIndex() ?>">
                    <h4 class="panel-title">
                        <a class="accordion-toggle"
                            role="button">
                            <?= $this->input('name') ?>
                        </a>
                    </h4>

                </div>

                <div id="panel-<?= $id ?>-<?= $panels->getCurrentIndex() ?>" class="panel-collapse collapse <?= $this->editmode ? 'in' : '' ?>">

                    <div class="panel-body">
                        <?= $this->template('helper/areablock.php', [ 'name' => 'accord', 'excludeBricks' => array('accordion') ] ); ?>
                    </div>

                </div>

            </div>

        <?php } ?>

    </div>

</div>
