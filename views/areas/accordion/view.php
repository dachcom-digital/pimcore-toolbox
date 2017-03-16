<?php if( $this->editmode ) { ?>
    <?= \Toolbox\Tool\ElementBuilder::buildElementConfig('accordion', $this) ?>
<?php }?>

<?php
    $id = uniqid('accordion-');
    $panel = $this->block('panels', ['default' => 2]);
    $type = $this->select('type')->getData();
?>
<div class="toolbox-element toolbox-accordion component-<?= $this->select('component')->getData() ?> <?= $this->select('accordionAdditionalClasses')->getData();?>">
    <?= $this->template('toolbox/accordion.php', ['id' => $id, 'type' => $type, 'panel' => $panel ]) ?>
</div>