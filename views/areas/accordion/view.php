<?php if( $this->editmode ) { ?>
    <?= \Toolbox\Tools\ElementBuilder::buildElementConfig('accordion', $this) ?>
<?php }?>

<div class="toolbox-accordion <?= $this->select('accordionAdditionalClasses')->getData();?>">
    <?= $this->template('toolbox/accordion.php') ?>
</div>