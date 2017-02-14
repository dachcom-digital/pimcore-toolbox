<?php if( $this->editmode ) { ?>
    <?= \Toolbox\Tool\ElementBuilder::buildElementConfig('accordion', $this) ?>
<?php }?>

<div class="toolbox-element toolbox-accordion <?= $this->select('accordionAdditionalClasses')->getData();?>">
    <?= $this->template('toolbox/accordion.php') ?>
</div>