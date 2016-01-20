<?php

$accordionSettings = $this->toolboxHelper()->getConfigArray( 'accordion' );

$store = array();

if( !empty( $accordionSettings )&& isset( $accordionSettings['layouts'] ) ) {

    foreach( $accordionSettings['layouts'] as $key => $val ) {

        $store[] = array(0 => str_replace('_','-', $key ), 1 => $val );

    }

} else {

    $store[] = array('panel-default', 'Default');
}

if ($this->editmode) {

    if ($this->select("type")->isEmpty()) {

        $this->select("type")->setDataFromResource("panel-default");

    }

    echo $this->select("type", array("reload" => true, "store" => $store));
}

$type = $this->select("type")->getData();

$panels = $this->block('panels', array('default' => 2 ));

$id = uniqid('accordion-');

?>

<div class="toolbox-accordion">

    <div class="panel-group" id="<?= $id ?>" role="tablist" aria-multiselectable="true">

        <?php while ($panels->loop()) { ?>

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

                <div id="panel-<?= $id ?>-<?= $panels->getCurrentIndex() ?>" class="panel-collapse collapse <?= $this->editmode ? 'in' : '' ?>">

                    <div class="panel-body">
                        <?= $this->template('helper/areablock.php', [ 'name' => 'accord', 'excludeBricks' => array('accordion') ] ); ?>
                    </div>

                </div>

            </div>

        <?php } ?>

    </div>

</div>
